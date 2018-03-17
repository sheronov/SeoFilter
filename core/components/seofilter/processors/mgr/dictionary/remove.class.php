<?php

class sfDictionaryRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
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

        $deleteLinks = (int)$this->getProperty('deleteLinks');
        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seofilter_dictionary_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var sfDictionary $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_dictionary_err_nf'));
            }

            if($deleteLinks) {
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
                $links = $this->modx->getIterator('sfUrls',$q);
                foreach($links as $link) {
                    $url = $link->get('new_url');
                    if(!$url) {
                        $url = $link->get('old_url');
                    }
                    $del_urls[] = $link->get('link').' '.$url;
                    $link->remove();
                }
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.count($del_urls).' urls deleted when word Id:'.$object->get('id').' Input:'.$object->get('input').' Value:'.$object->get('value').' Alias:'.$object->get('alias').' FieldID:'.$object->get('field_id').' delete: '. print_r($del_urls,1));
            }

            $object->remove();
        }

        return $this->success();
    }

}

return 'sfDictionaryRemoveProcessor';