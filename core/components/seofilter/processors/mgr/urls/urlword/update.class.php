<?php

class sfUrlWordUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfUrlWord';
    public $classKey = 'sfUrlWord';
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
        //$name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_fieldids_err_ns');
        }

//        if (empty($name)) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_fieldids_err_name'));
//        } elseif ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $id))) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_fieldids_err_ae'));
//        }

        return parent::beforeSet();
    }
}

return 'sfUrlWordUpdateProcessor';
