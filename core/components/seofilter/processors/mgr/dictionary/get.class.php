<?php

class sfDictionaryGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
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

        if((int)$field_id = $this->object->get('field_id')) {
            $q = $this->modx->newQuery('sfField');
            $q->where(array('id'=>$field_id,'relation'=>1));
            $q->select('relation_field');
            $relation_field = (int)$this->modx->getValue($q->prepare());
            $this->object->set('field_relation',$relation_field);
        }

        return parent::process();
    }

}

return 'sfDictionaryGetProcessor';