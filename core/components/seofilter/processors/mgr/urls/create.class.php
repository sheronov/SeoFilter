<?php

class sfUrlsCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'url';
    public $classKey = 'sfUrls';
    public $languageTopics = array('seofilter');
    public $beforeSaveEvent = 'sfOnBeforeUrlAdd';
    public $afterSaveEvent = 'sfOnUrlAdd';
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $url = trim($this->getProperty('old_url'));
        $multi_id = trim($this->getProperty('multi_id'));
        $page_id = (int)$this->getProperty('page_id');
        $from_rule = (int)$this->getProperty('from_rule');
        if (empty($url)) {
            if($from_rule) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_url_err_url'));
            } else {
                $this->modx->error->addField('old_url', $this->modx->lexicon('seofilter_url_err_url'));
            }
        } elseif(empty($page_id)) {
            if($from_rule) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_url_err_page'));
            } else {
                $this->modx->error->addField('page_id', $this->modx->lexicon('seofilter_url_err_page'));
            }
        }
        elseif ($this->modx->getCount($this->classKey, array('old_url' => $url,'page_id'=>$page_id))) {
            $this->modx->error->errors[] = 'double';
            if($from_rule) {
                $this->modx->error->failure( $this->modx->lexicon('seofilter_url_err_ae').' = '.$url);
            } else {
                $this->modx->error->addField('old_url', $this->modx->lexicon('seofilter_url_err_ae'));
            }
        }


        return parent::beforeSet();
    }

    public function afterSave()
    {
        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        //$this->modx->log(modX::LOG_LEVEL_ERROR,'ID: '.$this->object->get('id').print_r($this->getProperties(),1));
        $url_id = $this->object->get('id');
        $field_words = $this->getProperty('field_word');
        foreach($field_words as $fkey => $field_word) {
            $processorProps = array(
                'url_id' => $url_id,
                'field_id' => $field_word['field_id'],
                'word_id' => $field_word['word_id'],
                'priority' => $fkey,
            );
            $otherProps = array('processors_path' => $path . 'processors/');
            $response = $this->modx->runProcessor('mgr/urls/urlword/create', $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] ' . $response->getMessage());
                $this->modx->error->reset();
            }
        }

        return parent::afterSave();
    }


}

return 'sfUrlsCreateProcessor';