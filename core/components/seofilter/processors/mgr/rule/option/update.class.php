<?php

class sfRuleOptionUpdateProcessor extends modProcessor
{
    public $objectType = 'seofilter.rule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


    public function process()
    {
        return $this->success('',$this->getProperties());
    }

}

return 'sfRuleOptionUpdateProcessor';
