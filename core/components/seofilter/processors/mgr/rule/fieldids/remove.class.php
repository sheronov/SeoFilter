<?php

class sfFieldIdsRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'sfFieldIds';
    public $classKey = 'sfFieldIds';
    public $languageTopics = array('seofilter');
    //public $permission = 'remove';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seofilter_fieldids_err_ns'));
        }
        $rule_id = 0;
        foreach ($ids as $id) {
            /** @var sfField $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_fieldids_err_nf'));
            }

            $rule_id = $object->get('multi_id');

            $object->remove();
        }

        if($rule_id && $rule = $this->modx->getObject('sfRule',$rule_id)) {
            $url = $rule->makeUrl();
        } else {
            $url = $this->makeUrl($rule_id);
        }


        return $this->success($url);
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

return 'sfFieldIdsRemoveProcessor';