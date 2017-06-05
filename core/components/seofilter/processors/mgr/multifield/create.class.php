<?php

class sfMultiFieldCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfMultiField';
    public $classKey = 'sfMultiField';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
//        $name = trim($this->getProperty('name'));
//        if (empty($name)) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
//        } elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
//        }

        return parent::beforeSet();
    }



    public function afterSave()
    {
//        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
//        $processorProps = $this->getProperties();
//        $processorProps['multi_id'] = $this->object->get('id');
//        $action = 'mgr/seometa/create';
//        $otherProps = array('processors_path' => $path . 'processors/');
//        $response = $this->modx->runProcessor($action, $processorProps, $otherProps);
//        if ($response->isError()) {
//            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
//        }

        return parent::afterSave();
    }
}

return 'sfMultiFieldCreateProcessor';