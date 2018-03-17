<?php

class sfRuleCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        $page = trim($this->getProperty('page'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, array('name' => $name,'page'=>$page))) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }

        return parent::beforeSet();
    }



    public function afterSave()
    {
        /*** @var sfRule $object */
        $object = $this->object;

        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $multi_id = $object->get('id');
        $page_id = $object->get('page');

        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>0));
        $ruleFields = $this->modx->getIterator('sfFieldIds',$q);
        foreach($ruleFields as $ruleField) {
            $ruleField->set('multi_id',$multi_id);
            $ruleField->save();
        }

        $this->object->set('url',$this->object->generateUrl());
        $this->object->save();

        if($urls_array = $object->generateUrl(1)) {
            if(is_array($urls_array)) {
                foreach ($urls_array as $url) {
                    //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($url,1));
                    $processorProps = array(
                        'multi_id' => $multi_id,
                        'old_url' => $url['url'],
                        'page_id' => $page_id,
                        'link' => $url['link'],
                        'field_word' => $url['field_word'],
                        'from_rule' => 1,
                    );
                    $otherProps = array('processors_path' => $path . 'processors/');
                    $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                        $this->modx->error->reset();
                    }
                }
            }
        }


        return parent::afterSave();
    }
}

return 'sfRuleCreateProcessor';