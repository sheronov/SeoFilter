<?php

class sfRuleDuplicateProcessor extends modObjectDuplicateProcessor
{
    public $objectType = 'seofilter.rule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    public $nameField = 'name';
    /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;

    public function beforeSave()
    {
        $this->newObject->set('page',(int)$this->getProperty('page'));
        $this->newObject->set('pages',$this->getProperty('pages'));
        $this->newObject->set('active',(int)$this->getProperty('active'));
        return parent::beforeSave();
    }

    public function alreadyExists($name) {
        $page = (int)$this->getProperty('page');
        $pages = $this->getProperty('pages','');
        $proMode = $this->SeoFilter->config['proMode'];

        $exists =  ((!$proMode && $this->modx->getCount($this->classKey, array('name' => $name,'page'=>$page))) || ($proMode && $this->modx->getCount($this->classKey, array('name' => $name,'pages'=>$pages))));

        return $exists > 0;

    }

    public function initialize()
    {
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
            $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');
        return parent::initialize();
    }

    /**
     *
     */
    public function afterSave() {
        if($this->getProperty('copy_fields',false)) {
            $this->duplicateFields();

            /*** @var sfRule $object */
            $object = $this->newObject;
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
        }

       // $this->modx->log(modx::LOG_LEVEL_ERROR,print_r($this->getProperties(),1));

    }



    public function duplicateFields()
    {
            $links = $this->object->getMany('Links');
            if(is_array($links) && !empty($links)) {
                foreach($links as $link) {
                    $newLink = $this->modx->newObject('sfFieldIds');
                    $newLink->fromArray($link->toArray());
                    $newLink->set('multi_id',$this->newObject->get('id'));
                    $newLink->save();
                }
            }
    }

    public function generateUrls()
    {
        /*** @var sfRule $object */
        $object = $this->newObject;
        if($this->getProperty('copy_fields',false)) {
            $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
            $multi_id = $object->get('id');
            $page_id = $object->get('page');
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

        }
    }


}

return 'sfRuleDuplicateProcessor';