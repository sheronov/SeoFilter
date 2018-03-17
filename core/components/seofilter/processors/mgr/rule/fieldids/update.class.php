<?php

class sfFieldIdsUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfFieldIds';
    public $classKey = 'sfFieldIds';
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

return 'sfFieldIdsUpdateProcessor';
