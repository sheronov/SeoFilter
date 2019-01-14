<?php

class sfDictionaryCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'word';
    public $classKey = 'sfDictionary';
    public $languageTopics = array('seofilter');
    public $beforeSaveEvent = 'sfOnBeforeWordAdd';
    public $afterSaveEvent = 'sfOnWordAdd';
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {

        $input = trim($this->getProperty('input'));
        $field_id = (int)$this->getProperty('field_id');
        $from_field = (int)$this->getProperty('from_field');
        $alias = $this->getProperty('alias');
        if (!isset($input)) {
            if($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_input'));
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_input'));
            }
        } elseif ($this->modx->getCount($this->classKey, array('input' => $input,'field_id'=>$field_id))) {
            if($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_ae'). ' Field_id = '.$field_id.' Input = '.$input);
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_ae'));
            }
        } elseif ($this->modx->getCount($this->classKey, array('field_id'=>$field_id,'alias'=>$alias))) {
            if($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_alias_double'). ' Field_id = '.$field_id.' Input = '.$input);
            } else {
                $this->modx->error->addField('alias', $this->modx->lexicon('seofilter_dictionary_alias_double'));
            }
        }


        return parent::beforeSet();
    }

    public function beforeSave()
    {
        $alias = $this->object->get('alias');
        if($this->object->get('value') && empty($alias)) {
            $this->object->set('alias', modResource::filterPathSegment($this->modx, $this->object->get('value')));
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {
        /*** @var SeoFilter $SeoFilter */
        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');

        $total_message = '';
        $response = $SeoFilter->generateUrlsByWord($this->object->toArray());
        if($response) {
            foreach ($response as $rule_id => $resp) {
                $resp['rule_id'] = $rule_id;
                $total_message .= $SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_word_add_info'), $resp);
            }
        }

        $this->object->set('total_message',$total_message);

        return parent::afterSave();
    }

}

return 'sfDictionaryCreateProcessor';