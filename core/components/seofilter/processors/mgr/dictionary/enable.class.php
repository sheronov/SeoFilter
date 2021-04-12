<?php

class sfFieldEnableProcessor extends modObjectProcessor
{
    public $objectType      = 'seofilter.word';
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

        /*** @var SeoFilter $SeoFilter */
        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');

        $total_message = '';
        foreach ($ids as $id) {
            /** @var sfDictionary $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_dictionary_err_nf'));
            }

            $object->set('active', true);
            $object->save();

            $response = $SeoFilter->generateUrlsByWord($object->toArray());
            if($response) {
                foreach ($response as $rule_id => $resp) {
                    $resp['rule_id'] = $rule_id;
                    $total_message .= $SeoFilter->pdo->parseChunk('@INLINE ' . $this->modx->lexicon('seofilter_word_add_info'), $resp);
                }
            }

        }

        return $this->success($total_message);
    }

}

return 'sfFieldEnableProcessor';
