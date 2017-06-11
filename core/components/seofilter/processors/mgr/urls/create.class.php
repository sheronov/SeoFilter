<?php

class sfUrlsCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('old_url'));
        $multi_id = trim($this->getProperty('multi_id'));
        if (empty($name)) {
            $this->modx->error->addField('old_url', $this->modx->lexicon('seofilter_url_err_url'));
        } elseif ($this->modx->getCount($this->classKey, array('old_url' => $name,'multi_id'=>$multi_id))) {
            $this->modx->error->addField('old_url', $this->modx->lexicon('seofilter_url_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'sfUrlsCreateProcessor';