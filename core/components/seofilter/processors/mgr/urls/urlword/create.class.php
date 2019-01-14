<?php

class sfUrlWordCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfUrlWord';
    public $classKey = 'sfUrlWord';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
//        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
//        $field = trim($this->getProperty('field_id'));
//        if (empty($field)) {
//            $this->modx->error->addField('field_id', $this->modx->lexicon('seofilter_fieldids_err_field_id'));
//        } elseif ($this->modx->getCount($this->classKey, array('field_id' => $field))) {
//            $this->modx->error->addField('field_id', $this->modx->lexicon('seofilter_fieldids_err_ae'));
//        }

        return parent::beforeSet();
    }

    public function logManagerAction()
    {

    }

}

return 'sfUrlWordCreateProcessor';