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

}

return 'sfMultiFieldCreateProcessor';