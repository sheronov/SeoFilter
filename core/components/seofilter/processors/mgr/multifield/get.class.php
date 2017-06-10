<?php

class sfMultiFieldGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'sfMultiField';
    public $classKey = 'sfMultiField';
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

       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->object->toArray(),1));
        if($seo = $this->modx->getObject('sfSeoMeta',array('multi_id'=>$this->object->get('id')))) {
            $seo_array = $seo->toArray();
            $seo_array['seo_id'] = $seo_array['id'];
            unset($seo_array['id']);
            foreach($seo_array as $seo_key => $seo_value) {
                $this->object->set($seo_key,$seo_value);
            }
        }
        //$obj = $this->object->getMany('Seo');
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($obj->toArray(),1));

        //$c->leftJoin('sfSeoMeta', 'sfSeoMeta', $this->classKey.'.id = sfSeoMeta.multi_id');
        //$c->select($this->modx->getSelectColumns($this->classKey,$this->classKey));
        //$c->select($this->modx->getSelectColumns('sfSeoMeta','sfSeoMeta','',array('title','h1')));

        return parent::process();
    }


}

return 'sfMultiFieldGetProcessor';