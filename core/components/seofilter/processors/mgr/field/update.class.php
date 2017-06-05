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
        if($this->getProperty('priority') == 1) {
            $this->object->set('translit',1);
            $this->object->set('translate',0);
        } elseif($this->getProperty('priority') == 2) {
            $this->object->set('translate',1);
            $this->object->set('translit',0);
        } else {
            $this->object->set('translit',0);
            $this->object->set('translate',0);
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

        return parent::beforeSet();
    }
}

return 'sfFieldUpdateProcessor';
