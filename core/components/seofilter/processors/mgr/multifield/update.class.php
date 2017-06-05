<?php

class sfMultiFieldUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfMultiField';
    public $classKey = 'sfMultiField';
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

        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $processorProps = $this->getProperties();
        $processorProps['multi_id'] = $processorProps['id'];
        if($processorProps['id'] = $processorProps['seo_id']) {
            $action = 'mgr/seometa/update';
        } else {
            $action = 'mgr/seometa/create';
        }
            $otherProps = array('processors_path' => $path . 'processors/');
            $response = $this->modx->runProcessor($action, $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
            }

       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($_POST,1));
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));



       // $where = 0;


//        $url = '';
//        $q = $this->modx->newQuery('sfFieldIds');
//        $q->sortby('priority', 'ASC');
//        if($links = $this->object->getMany('Link',$q)){
//            $count = count($links);
//            foreach($links as $key => $link) {
//                if($link->get('where')) {
//                    $where = 1;
//                }
//                if($field = $link->getOne('Field')) {
//                    if($alias = $field->get('alias')) {
//                        if($field->get('hideparam')) {
//                            $url .= '/{$'.$alias.'}';
//                        } else {
//                            $url .= '/' . $alias . '-{$'.$alias.'}';
//                        }
//
//                    }
//                }
//                //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($link->getOne('Field')->toArray(),1));
//                //link->getOne('Field')->toArray()
//            }
//            $this->object->set('url',$url);
//        }
        $this->object->set('url',$this->object->makeUrl());
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->makeUrl(),1));

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
       // $name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_multifield_err_ns');
        }

//        if (empty($name)) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_multifield_err_name'));
//        } elseif ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $id))) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_multifield_err_ae'));
//        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
       // $muiltifield = $this->object;
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($muiltifield->makeUrl(),1));
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
        return parent::afterSave();
    }
}

return 'sfMultiFieldUpdateProcessor';
