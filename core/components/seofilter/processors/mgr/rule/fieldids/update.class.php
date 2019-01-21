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

return 'sfFieldIdsUpdateProcessor';
