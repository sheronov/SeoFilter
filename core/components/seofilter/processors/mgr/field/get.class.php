<?php

class sfFieldGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'seofilter.field';
    public $classKey = 'sfField';
    public $languageTopics = array('seofilter:default');
    //public $permission = 'view';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }


        return parent::process();
    }

}

return 'sfFieldGetProcessor';