<?php

class sfRuleCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';

    /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;


    public function initialize()
    {
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
            $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');

        return parent::initialize();
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        $page = trim($this->getProperty('page'));
        $pages = trim($this->getProperty('pages'));
        $proMode = $this->SeoFilter->config['proMode'];
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif(($proMode && $this->modx->getCount($this->classKey, array('name' => $name,'pages'=>$pages)))
            || (!$proMode && $this->modx->getCount($this->classKey, array('name' => $name,'page'=>$page)))) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }
        return parent::beforeSet();
    }


    /**
     * для полей добавленных при создании правила
     */
    public function fieldsToRule() {
        $rule_id = $this->object->get('id');
        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>0));
        $ruleFields = $this->modx->getIterator('sfFieldIds',$q);
        foreach($ruleFields as $ruleField) {
            $ruleField->set('multi_id',$rule_id);
            $ruleField->save();
        }
    }


    public function afterSave() {
        $this->fieldsToRule(); //привязка полей к правилу
        /*** @var sfRule $object */
        $object = $this->object;
        if($this->SeoFilter->config['edit_url_mask'] && $object->get('url')) {
            $url_mask = $object->get('url');
        } else {
            $url_mask = $object->updateUrlMask(); //обновление маски
        }
        $recount = (int)$this->getProperty('recount');

        if($object->get('active')) {
            if($this->SeoFilter->config['proMode']) {
                $pages = $object->get('pages');
            } else {
                $pages = $object->get('page');
            }
            if($response = $this->SeoFilter->generateUrls($object->get('id'),$pages,$object->get('link_tpl'),$url_mask)) {
                $total_message = $this->SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_rule_information'), $response);

                if ($recount) {
                    $this->SeoFilter->loadHandler();
                    if ($counts = $this->SeoFilter->countHandler->countByRule($object->id)) {
                        $counts['rule_id'] = $object->id;
                        $total_message .= $this->SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_rule_recount_message'), $counts);
                    }
                }
                $object->set('total_message', $total_message);
            }
        }

        return parent::afterSave();
    }
}

return 'sfRuleCreateProcessor';