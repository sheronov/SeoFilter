<?php

class sfDictionaryCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));

        $input = trim($this->getProperty('input'));
        $field_id = $this->getProperty('field_id');
        if (empty($input)) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_seometa_err_input'));
        } elseif ($this->modx->getCount($this->classKey, array('input' => $input,'field_id'=>$field_id))) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_seometa_err_ae'));
        }


        return parent::beforeSet();
    }

    public function beforeSave()
    {
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
        if($this->object->get('value') && !$this->object->get('alias')) {
            $this->object->set('alias', modResource::filterPathSegment($this->modx, $this->object->get('value')));
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {

        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
       // if($value = $this->object->get('value')) {
            //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->translit($value),1));
       // }
        return parent::afterSave();
    }

}

return 'sfDictionaryCreateProcessor';