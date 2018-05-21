<?php

class sfDictionaryDeclineProcessor extends modObjectProcessor
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
            return $this->failure($this->modx->lexicon('seofilter_url_err_ns'));
        }
        $total_message = '';
        $recount = (int)$this->getProperty('recount');
        if($recount) {
            /* @var SeoFilter $SeoFilter */
            $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                    $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
            $SeoFilter->loadHandler();
            foreach ($ids as $id) {
                if($counts = $SeoFilter->countHandler->countByWord($id)) {
                    $counts['word_id'] = $id;
                    $total_message .= $SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_word_recount_message'), $counts);
                }
            }

        } else {
            foreach ($ids as $id) {
                /** @var sfUrls $object */
                if (!$object = $this->modx->getObject($this->classKey, $id)) {
                    return $this->failure($this->modx->lexicon('seofilter_url_err_nf'));
                }

                $object->set('update', true);
                $object->save();
            }
        }

        return $this->success($total_message);
    }

}

return 'sfDictionaryDeclineProcessor';