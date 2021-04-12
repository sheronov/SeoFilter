<?php

class sfUrlsGetProcessor extends modObjectGetProcessor
{
    public $objectType     = 'seofilter.url';
    public $classKey       = 'sfUrls';
    public $languageTopics = ['seofilter:default'];
    //public $permission = 'view';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        if (!$this->object->get('custom')) {
            if ($rule = $this->modx->getObject('sfRule', $this->object->get('multi_id'))) {
                $this->object->set('title', $rule->get('title'));
                $this->object->set('h1', $rule->get('h1'));
                $this->object->set('h2', $rule->get('h2'));
                $this->object->set('description', $rule->get('description'));
                $this->object->set('introtext', $rule->get('introtext'));
                $this->object->set('keywords', $rule->get('keywords'));
                $this->object->set('text', $rule->get('text'));
                $this->object->set('content', $rule->get('content'));
            }
        }

        return parent::process();
    }

}

return 'sfUrlsGetProcessor';