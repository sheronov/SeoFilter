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

//        if(($this->getProperty('hideparam') != $this->object->get('hideparam')) || ($this->getProperty('valuefirst') != $this->object->get('valuefirst'))) {
//
//            $this->object->set('hideparam',(int)$this->getProperty('hideparam')); //fix
//            $this->object->set('valuefirst',(int)$this->getProperty('valuefirst')); //fix
//            $this->object->save();
//            if($fieldids = $this->object->getMany('Links')) {
//                foreach ($fieldids as $fieldid) {
//                    if ($rule = $fieldid->getOne('Rule')) {
//                        $rule->set('url',$rule->generateUrl());
//                        $rule->save();
//                    }
//                }
//            }
//        }


        return parent::beforeSet();
    }
}

return 'sfFieldUpdateProcessor';
