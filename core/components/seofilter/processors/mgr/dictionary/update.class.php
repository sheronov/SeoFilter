<?php

class sfDictionaryUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
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

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        $name = trim($this->getProperty('value'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_dictionary_err_ns');
        }

        if (empty($name)) {
            $this->modx->error->addField('value', $this->modx->lexicon('seofilter_dictionary_err_name'));
        } elseif ($this->modx->getCount($this->classKey, array('value' => $name, 'id:!=' => $id))) {
            $this->modx->error->addField('value', $this->modx->lexicon('seofilter_dictionary_err_ae'));
        }

        return parent::beforeSet();
    }
}

return 'sfDictionaryUpdateProcessor';
