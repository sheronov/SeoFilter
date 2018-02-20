<?php

class sfRuleOptionUpdateProcessor extends modProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


    public function process()
    {
//        $this->modx->log(1,print_r($this->getProperties(),1));

        return $this->success('',$this->getProperties());
    }

}

return 'sfRuleOptionUpdateProcessor';
