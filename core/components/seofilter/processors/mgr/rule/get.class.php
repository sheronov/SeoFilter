<?php

class sfRuleGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter:default');
    //public $permission = 'view';
    public $proMode = 0;

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
        $this->proMode = (int)$this->modx->getOption('seofilter_pro_mode', null, 0);

        return parent::process();
    }

    public function beforeOutput()
    {
        if($this->proMode) {
            $pages = $this->object->get('pages');
            if(empty($pages)) {
                $this->object->set('pages',$this->object->get('page'));
            }
        }
        parent::beforeOutput();
    }


}

return 'sfRuleGetProcessor';