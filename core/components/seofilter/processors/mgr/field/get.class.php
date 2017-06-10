<?php

class sfFieldGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'sfField';
    public $classKey = 'sfField';
    public $languageTopics = array('seofilter:default');
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

        if($seo = $this->modx->getObject('sfSeoMeta',array('field_id'=>$this->object->get('id')))) {
            $seo_array = $seo->toArray();
            $seo_array['seo_id'] = $seo_array['id'];
            unset($seo_array['id']);
            foreach($seo_array as $seo_key => $seo_value) {
                $this->object->set($seo_key,$seo_value);
            }
        }
        return parent::process();
    }

}

return 'sfFieldGetProcessor';