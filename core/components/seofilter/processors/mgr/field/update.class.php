<?php

class sfFieldUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfField';
    public $classKey = 'sfField';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::beforeSave();
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        $name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_field_err_ns');
        }

        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $id))) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }

        if(($this->getProperty('alias') != $this->object->get('alias'))
            || ($this->getProperty('hideparam') != $this->object->get('hideparam'))
            || ($this->getProperty('valuefirst') != $this->object->get('valuefirst'))) {
            $new_alias = $this->getProperty('alias');
            $old_alias = $this->object->get('alias');
            $this->object->set('alias',$new_alias); //fix
            $this->object->set('hideparam',(int)$this->getProperty('hideparam')); //fix
            $this->object->set('valuefirst',(int)$this->getProperty('valuefirst')); //fix
            $this->object->save();

            if($urlwords = $this->modx->getCollection('sfUrlWord',array('field_id'=>$id))) {
                foreach ($urlwords as $urlword) {
                    if ($url = $urlword->getOne('Url')) {
                        $priorities = array();
                        if ($rule = $url->getOne('Rule')) {
                            $q = $this->modx->newQuery('sfFieldIds');
                            $q->sortby('priority', 'ASC');
                            $q->where(array('multi_id' => $rule->get('id')));
                            //  $q->leftJoin('sfField','sfField','sfFieldIds.field_id = sfField.id');
                            $q->select('sfFieldIds.field_id,sfFieldIds.priority');
                            if ($q->prepare() && $q->stmt->execute()) {
                                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $priorities[$row['priority']] = $row['field_id'];
                                }
                            }
                        }
                        $new_url = $url->updateUrl($priorities);
                        // $this->modx->log(modX::LOG_LEVEL_ERROR, 'SEOFILTER URL: '.$url->updateUrl($priorities));
                    }
                }
            }

            if($fieldids = $this->object->getMany('Links')) {
                foreach ($fieldids as $fieldid) {
                    if ($rule = $fieldid->getOne('Rule')) {
                        $rule_array = $rule->toArray();
                        foreach ($rule_array as $key => $value) {
                            $new_value = str_replace(
                                array('{$' . $old_alias . '}', '[[+' . $old_alias . ']]','-'.$old_alias,$old_alias . '-',$old_alias . '_'),
                                array('{$' . $new_alias . '}', '[[+' . $new_alias . ']]','-'.$new_alias,$new_alias . '-',$new_alias . '_'),
                                $value);
                            $rule->set($key, $new_value);
                            $rule->save();
                        }
                        $rule->set('url',$rule->generateUrl());
                        $rule->save();
                    }
                }
            }
        }

        return parent::beforeSet();
    }

    public  function afterSave()
    {
        // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
        if($this->object->get('active') && !$this->object->get('slider')) {
            $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
            $field = $this->object;
            $class = $field->get('class');
            $key = $field->get('key');
            $values = array();

            if ($class && $key) {
                $processorProps = array(
                    'class' => $class,
                    'key' => $key,
                    'field_id' => $field->get('id'),
                );
                $otherProps = array('processors_path' => $path . 'processors/');

                if ($field->get('xpdo')) {
                    $xpdo_id = $field->get('xpdo_id');
                    $xpdo_name = $field->get('xpdo_name');
                    if ($xpdo_class = $field->get('xpdo_class')) {
                        if ($package = $field->get('xpdo_package')) {
                            $this->modx->addPackage($package, $this->modx->getOption('core_path') . 'components/' . $package . '/model/');
                        }
                        $q = $this->modx->newQuery($xpdo_class);
                        $q->select($xpdo_id . ',' . $xpdo_name);
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($row,1));
                                $values[$row[$xpdo_id]] = $row[$xpdo_name];
                            }
                        }

                    }
                }


                if ($class == 'modTemplateVar') {
                    $tvvalues = $pre = array();
                    $q = $this->modx->newQuery($class, array('name' => $key));
                    $q->limit(1);
                    $q->select('id,elements');
                    if ($q->prepare() && $q->stmt->execute()) {
                        $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row['elements']) {
                            if (strpos($row['elements'], '||')) {
                                $pre = array_map('trim', explode('||', $row['elements']));
                            } elseif (strpos($row['elements'], ',')) {
                                $pre = array_map('trim', explode(',', $row['elements']));
                            } else {
                                $pre = array($row['elements']);
                            }
                        }
                        if ($tv_id = $row['id']) {
                            $q = $this->modx->newQuery('modTemplateVarResource');
                            $q->where(array('tmplvarid' => $tv_id, 'value:!=' => ''));
                            $q->select(array('DISTINCT modTemplateVarResource.value'));
                            if ($q->prepare() && $q->stmt->execute()) {
                                while ($row = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                                    if (strpos($row, '||')) {
                                        $row_arr = array_map('trim', explode('||', $row));
                                    } elseif (strpos($row, ',')) {
                                        $row_arr = array_map('trim', explode(',', $row));
                                    } else {
                                        $row_arr = array($row);
                                    }
                                    $tvvalues = array_unique(array_merge($tvvalues, $row_arr));
                                }
                            }
                        }
                    }
                    $words = array_unique(array_merge($tvvalues, $pre));
                    foreach ($words as $word) {
                        if ($field->get('xpdo')) {
                            $value = $values[$word];
                        } else {
                            $value = $word;
                        }
                        $processorProps['input'] = $word;
                        $processorProps['value'] = $value;
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                        }
                    }
                } else {

                    $q = $this->modx->newQuery($class);
                    $q->limit(0);
                    if ($class == 'msProductOption') {
                        $q->where(array('key' => $key, 'value:!=' => ''));
                        $q->select(array('DISTINCT ' . $class . '.value'));
                        $key = 'value';
                    } elseif ($class == 'msVendor') {
                        $q->select('id,name');
                    } else {
                        $q->select(array('DISTINCT ' . $class . '.' . $key));
                    }
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($input = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($field->get('xpdo')) {
                                $value = $values[$input[$key]];
                            } else {
                                $value = $input[$key];
                            }
                            if ($class == 'msVendor') {
                                $value = $input['name'];
                            }
                            if (strpos($value, '||')) {
                                $value_arr = array_map('trim', explode('||', $value));
                            } elseif (strpos($value, ',')) {
                                $value_arr = array_map('trim', explode(',', $value));
                            } else {
                                $value_arr = array($value);
                            }
                            foreach ($value_arr as $value) {
                                //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($input, 1));
                                $processorProps['input'] = $input[$key];
                                $processorProps['value'] = $value;
                                if ($input[$key] && $value) {
                                    $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                                    if ($response->isError()) {
                                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return parent::afterSave();
    }
}


return 'sfFieldUpdateProcessor';
