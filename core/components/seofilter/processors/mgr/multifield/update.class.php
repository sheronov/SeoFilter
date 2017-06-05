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
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($_POST,1));

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
}

return 'sfMultiFieldUpdateProcessor';
