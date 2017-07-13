<?php

class sfRuleRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
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

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seofilter_field_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var sfField $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_field_err_nf'));
            }

            $object->remove();
        }

        return $this->success();
    }

}

return 'sfRuleRemoveProcessor';