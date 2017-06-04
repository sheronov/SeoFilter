<?php

class sfDictionaryCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('input'));
        if (empty($input)) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_seometa_err_input'));
        } elseif ($this->modx->getCount($this->classKey, array('input' => $name))) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_seometa_err_ae'));
        }


        return parent::beforeSet();
    }

}

return 'sfDictionaryCreateProcessor';