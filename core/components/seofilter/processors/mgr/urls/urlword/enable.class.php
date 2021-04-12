<?php

class sfFieldEnableProcessor extends modObjectProcessor
{
    public $objectType = 'seofilter.url_word';
    public $classKey = 'sfUrlWord';
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
            return $this->failure($this->modx->lexicon('seofilter_fieldids_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var sfUrlWord $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_fieldids_err_nf'));
            }

            $object->set('active', true);
            $object->save();
        }

        return $this->success();
    }

}

return 'sfFieldEnableProcessor';
