<?php

class sfUrlsClearProcessor extends modProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';

    public function process()
    {
        $q = $this->modx->newQuery($this->classKey);
        $q->where(array('count:>'=>0));
        $urls = $this->modx->getIterator('sfUrls',$q);
        foreach($urls as $url) {
            $url->set('count',0);
            $url->set('ajax',0);
            $url->save();
        }
        return $this->success();
    }


}

return 'sfUrlsClearProcessor';