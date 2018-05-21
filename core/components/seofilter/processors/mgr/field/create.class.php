<?php

class sfFieldCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfField';
    public $classKey = 'sfField';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        $alias = trim($this->getProperty('alias'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
//        } elseif ($this->modx->getCount($this->classKey, array('alias' => $alias))) {
//            $this->modx->error->addField('alias', $this->modx->lexicon('seofilter_field_err_ae'));
        }

        return parent::beforeSet();
    }

    public function beforeSave()
    {
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
        return parent::beforeSave();
    }

    public  function afterSave()
    {
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
        if($this->object->get('active') && !$this->object->get('slider')) {
            $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
            $field = $this->object;
            $class = $field->get('class');
            $key = trim($field->get('key'));
            $values = array();

            if ($class && $key) {
                $processorProps = array(
                    'class' => $class,
                    'key' => $key,
                    'field_id' => $field->get('id'),
                    'from_field' => 1,
                );
                $otherProps = array('processors_path' => $path . 'processors/');

                if ($package = $field->get('xpdo_package')) {
                    $this->modx->addPackage(strtolower($package), $this->modx->getOption('core_path') . 'components/' . strtolower($package) . '/model/');
                }
                if ($field->get('xpdo')) {
                    $xpdo_id = $field->get('xpdo_id');
                    $xpdo_name = $field->get('xpdo_name');
                    if ($xpdo_class = $field->get('xpdo_class')) {
                        $q = $this->modx->newQuery($xpdo_class);
                        if($field->get('relation')) {
                            $relation_column = $field->get('relation_column');
                            if($relation_column) {
                                $q->select($xpdo_id . ','.$xpdo_name.','.$relation_column);
                                if($q->prepare() && $q->stmt->execute()) {
                                    while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $values[$row[$xpdo_id]] = array('value'=>$row[$xpdo_name],'relation'=>$row[$relation_column]);
                                    }
                                }
                            } else {
                                $q->select($xpdo_id . ',' . $xpdo_name);
                                if ($q->prepare() && $q->stmt->execute()) {
                                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($row,1));
                                        $values[$row[$xpdo_id]] = $row[$xpdo_name];
                                    }
                                }
                            }
                        } else {
                            $q->select($xpdo_id . ',' . $xpdo_name);
                            if ($q->prepare() && $q->stmt->execute()) {
                                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                    //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($row,1));
                                    $values[$row[$xpdo_id]] = $row[$xpdo_name];
                                }
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
                                    $result = json_decode($row) ;
                                    if(json_last_error() === JSON_ERROR_NONE) {
                                        if(is_array($result)) {
                                            $row_arr = $result;
                                        } else {
                                            $row_arr = array($result);
                                        }
                                    } else {
                                        if (strpos($row, '||')) {
                                            $row_arr = array_map('trim', explode('||', $row));
                                        } elseif (strpos($row, ',')) {
                                            $row_arr = array_map('trim', explode(',', $row));
                                        } else {
                                            $row_arr = array($row);
                                        }
                                    }
                                    $tvvalues = array_unique(array_merge($tvvalues, $row_arr));
                                }
                            }
                        }
                    }
                    $words = array_unique(array_merge($tvvalues, $pre));
                    foreach ($words as $word) {
                        $relation_id = $relation_value = '';
                        if ($field->get('xpdo')) {
                            if(is_array($values[$word])) {
                                $relation_value = $values[$word]['relation'];
                                $value = $values[$word]['value'];
                            } else {
                                $value = $values[$word];
                            }
                        } elseif(strpos($word,'==') !== false) {
                            $word_exp = array_map('trim',explode('==',$word));
                            $value = $word_exp[0];
                            $word = $word_exp[1];
                        } else {
                            $value = $word;
                        }

                        if($relation_value) {
                            $relation_field = $field->get('relation_field');
                            $s = $this->modx->newQuery('sfDictionary');
                            $s->where(array('input'=>$relation_value,'field_id'=>$relation_field));
                            $s->select('id');
                            $relation_id = $this->modx->getValue($s->prepare());
                        }

                        $processorProps['relation_word'] = $relation_id;
                        $processorProps['input'] = $word;
                        $processorProps['value'] = $value;
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                            $this->modx->error->reset();
                        }
                    }
                } elseif(strtolower($class) == 'tagger') {
                    $taggerPath = $this->modx->getOption('tagger.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tagger/');
                    /** @var Tagger $tagger */
                    $tagger = $this->modx->getService('tagger', 'Tagger', $taggerPath . 'model/tagger/', array('core_path' => $taggerPath));
                    if(!($tagger instanceof Tagger)) {
                        return parent::afterSave();
                    }
                    $q = $this->modx->newQuery('TaggerTag');
                    $q->innerJoin('TaggerGroup','Group','Group.id = TaggerTag.group');
                    $q->groupby('TaggerTag.id');
                    $q->where(array('Group.id = "'.$key.'" OR Group.alias = "'.$key.'"'));
                    $q->limit(0);
                    $q->select(array(
                       'TaggerTag.tag as input,TaggerTag.label as value,TaggerTag.alias'
                    ));
                    if($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $processorProps['value'] = $row['value'];
                            if(empty($processorProps['value'])) {
                                $processorProps = $row['input'];
                            }
                            $processorProps['input'] = $row['input'];
                            $processorProps['alias'] = '';
                            if($row['alias'] && $row['alias'] != '-1') {
                                $processorProps['alias'] = $row['alias'];
                            }
                            if ($processorProps['input'] && $processorProps['value']) {
                                $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                                if ($response->isError()) {
                                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                                    $this->modx->error->reset();
                                }
                            }
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
                            $relation_id = $relation_value = '';

                            if ($field->get('xpdo')) {
                                if(is_array($values[$input[$key]])) {
                                    $relation_value = $values[$input[$key]]['relation'];
                                    $value = $values[$input[$key]]['value'];
                                } else {
                                    $value = $values[$input[$key]];
                                }
                            } else {
                                $value = $input[$key];
                            }

                            if($relation_value) {
                                $relation_field = $field->get('relation_field');
                                $s = $this->modx->newQuery('sfDictionary');
                                $s->where(array('input'=>$relation_value,'field_id'=>$relation_field));
                                $s->select('id');
                                $relation_id = $this->modx->getValue($s->prepare());
                            }

                            $processorProps['relation_word'] = $relation_id;

                            if ($class == 'msVendor') {
                                $value = $input['name'];
                            }
                            if($field->get('exact')) {
                                $value_arr = array($value);
                            } else {
                                if (strpos($value, '||')) {
                                    $value_arr = array_map('trim', explode('||', $value));
                                } elseif (strpos($value, ',')) {
                                    $value_arr = array_map('trim', explode(',', $value));
                                } else {
                                    $value_arr = array($value);
                                }
                            }
                            foreach ($value_arr as $value) {
                                //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($input, 1));
                                $processorProps['input'] = $input[$key];
                                $processorProps['value'] = $value;
                                if ($input[$key] && $value) {
                                    $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                                    if ($response->isError()) {
                                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                                        $this->modx->error->reset();
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

return 'sfFieldCreateProcessor';