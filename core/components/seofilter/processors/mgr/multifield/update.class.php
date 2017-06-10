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

        $this->object->set('url',$this->object->generateUrl());

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
        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $multi_id = $this->object->get('id');
        $urls = $this->object->generateUrl(1);
        foreach($urls as $url) {
            $processorProps = array(
                'multi_id' => $multi_id,
                'old_url' => $url
            );
            $otherProps = array('processors_path' => $path . 'processors/');
            $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
            }
        }
       // $muiltifield = $this->object;
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($muiltifield->makeUrl(),1));
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
        return parent::afterSave();
    }
}

return 'sfMultiFieldUpdateProcessor';
