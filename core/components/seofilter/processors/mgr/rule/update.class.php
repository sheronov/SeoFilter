<?php

class sfRuleUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';

     /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;


    public function initialize()
    {
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
            $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/');

        return parent::initialize();
    }

    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {

        $id = (int)$this->getProperty('id');
        $name = trim($this->getProperty('name'));
        $page = (int)$this->getProperty('page');
        $pages = (int)$this->getProperty('pages');
        $proMode = (int)$this->SeoFilter->config['proMode'];
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_rule_err_ns');
        }

        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_rule_err_name'));
        } elseif (
            (!$proMode && $this->modx->getCount($this->classKey, array('name' => $name,'page'=>$page,'id:!=' => $id)))
            || ($proMode && $this->modx->getCount($this->classKey, array('name' => $name,'pages'=>$pages,'id:!=' => $id)))
        ) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_rule_err_ae'));
        }
//        else {
//            if(!$proMode && ($this->getProperty('page') != $this->object->get('page'))) {
//                if($urls = $this->modx->getIterator('sfUrls',array('multi_id'=>$this->object->get('id')))) {
//                    foreach($urls as $url) {
//                        $url->set('page_id',(int)$this->getProperty('page'));
//                        $url->save();
//                    }
//                }
//            }
//        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
        /*** @var sfRule $object */
        $object = $this->object;
        $url_mask = $object->updateUrlMask(); //обновление маски
        $recount = (int)$this->getProperty('recount');
        $rename = (int)$this->getProperty('relinks');

        if($object->get('active')) {

            if($this->SeoFilter->config['proMode']) {
                $pages = $object->get('pages');
            } else {
                $pages = $object->get('page');
            }
            $response = $this->SeoFilter->generateUrls($object->get('id'),$pages,$object->get('link_tpl'),$url_mask,array(),$rename);
            $total_message = $this->SeoFilter->pdo->parseChunk('@INLINE '.$this->modx->lexicon('seofilter_rule_information'),$response);

            if($recount) {
                $this->SeoFilter->loadHandler();
                if($counts = $this->SeoFilter->countHandler->countByRule($object->id)) {
                    $counts['rule_id'] = $object->id;
                    $total_message .= $this->SeoFilter->pdo->parseChunk('@INLINE '.$this->modx->lexicon('seofilter_rule_recount_message'),$counts);
                }
            }
            $object->set('total_message',$total_message);
        }

        return parent::afterSave();
    }
}

return 'sfRuleUpdateProcessor';
