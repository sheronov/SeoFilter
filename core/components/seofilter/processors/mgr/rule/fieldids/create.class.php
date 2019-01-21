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
        /* @var sfFieldIds $object */
        $object = $this->object;
        $rule_id = (int)$object->get('multi_id');
        if($rule_id && $rule = $this->modx->getObject('sfRule',$rule_id)) {
            $url = $rule->updateUrlMask();
        } else {
            $url = $object->updateUrlMask($rule_id);
        }
        $q = $this->modx->newQuery('sfField',array('id'=>$object->get('field_id')));
        $q->select('alias');
        $object->set('alias','{$'.$this->modx->getValue($q->prepare()).'}');
        return $this->success($url,$object);
    }

}

return 'sfFieldIdsCreateProcessor';