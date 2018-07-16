<?php

class sfDictionaryDisableProcessor extends modObjectProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


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
            return $this->failure($this->modx->lexicon('seofilter_dictionary_err_ns'));
        }
        $message = '';
        /*** @var SeoFilter $SeoFilter */
        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');


        $total_dels = array();
        foreach ($ids as $id) {
            /** @var sfDictionary $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_dictionary_err_nf'));
            }

            $q = $this->modx->newQuery('sfUrls');
            $q->innerJoin('sfUrlWord','sfUrlWord','sfUrlWord.url_id = sfUrls.id');
            $q->where(array(
                'sfUrlWord.word_id'=>$id
            ));
            $q->groupby('sfUrls.id');
            $q->select(array(
                'sfUrls.*',
                'sfUrlWord.word_id'
            ));

            $del_urls = array();
            $dels = array();
            $links = $this->modx->getIterator('sfUrls',$q);
            foreach($links as $link) {
                $rule_id = $link->get('multi_id');
                if(isset($dels[$rule_id])) {
                    $dels[$rule_id]++;
                } else {
                    $dels[$rule_id] = 1;
                }
                $url = $link->get('new_url');
                if(!$url) {
                    $url = $link->get('old_url');
                }
                $del_urls[] = $link->get('link').' '.$url;
                $link->remove();
            }

//            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.count($del_urls).' urls deleted when word Id:'.$object->get('id').' Input:'.$object->get('input').' Value:'.$object->get('value').' Alias:'.$object->get('alias').' FieldID:'.$object->get('field_id').' disabled: '. print_r($del_urls,1));

            foreach ($dels as $rid => $count) {
                if(isset($total_dels[$rid])) {
                    $total_dels[$rid] += $count;
                } else {
                    $total_dels[$rid] = $count;
                }
            }

            $object->set('active', false);
            $object->save();
        }

        foreach ($total_dels as $rid => $count) {
            $message .= $SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_word_disable_info'), array('rule_id'=>$rid,'total'=>$count));
        }

        return $this->success($message);
    }

}

return 'sfDictionaryDisableProcessor';
