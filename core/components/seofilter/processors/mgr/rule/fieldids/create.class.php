<?php

class sfFieldIdsCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfFieldIds';
    public $classKey = 'sfFieldIds';
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



    public function cleanup() {

        $rule_id = (int)$this->object->get('multi_id');
        if($rule_id && $rule = $this->modx->getObject('sfRule',$rule_id)) {
            $url = $rule->makeUrl();
        } else {
            $url = $this->makeUrl($rule_id);
        }

        return $this->success($url,$this->object);
    }

    public function makeUrl($rule_id = 0) {
        $url = array();
        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>$rule_id));
        $q->sortby('priority','ASC');
        $q->innerJoin('sfField','Field','Field.id = sfFieldIds.field_id');
        $q->select(array(
            'Field.*',
            'sfFieldIds.id as fid,sfFieldIds.priority'
        ));
        $fields = $this->modx->getIterator('sfField',$q);
        foreach($fields as $field) {
            /*** @var sfField $field */
            $url[] = $field->makeUrl();
        }

        return implode('/',$url);
    }



}

return 'sfFieldIdsCreateProcessor';