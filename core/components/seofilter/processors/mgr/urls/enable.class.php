<?php

class sfUrlsEnableProcessor extends modObjectProcessor
{
    public $objectType = 'seofilter.url';
    public $classKey = 'sfUrls';
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

        foreach ($ids as $id) {
            /** @var sfUrls $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_url_err_nf'));
            }

            $object->set('active', true);
            $object->save();
        }

        return $this->success();
    }

}

return 'sfUrlsEnableProcessor';
