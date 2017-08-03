<?php

class sfRuleUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


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

        $this->object->set('url',$this->object->generateUrl());

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
       // $name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_rule_err_ns');
        }

//        if (empty($name)) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_rule_err_name'));
//        } elseif ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $id))) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_rule_err_ae'));
//        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $multi_id = $this->object->get('id');
        $page_id = $this->object->get('page');
        $urls_array = $this->object->generateUrl(1);
        $urls = array();
        foreach($urls_array as $ukey => $uarr) {
            $urls[$ukey] = $uarr['url'];
        }
        $url_objs = $this->modx->getCollection('sfUrls',array('multi_id'=>$multi_id));
        if(count($url_objs)) {
            $old_urls = array();
            foreach($url_objs as $url_obj) {
                $old_urls[] = $url_obj->get('old_url');
            }
            $del_urls = array_diff($old_urls,$urls);
            //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К удалению: '. print_r($del_urls,1));
            if($del_urls) {
                $removed = $this->modx->removeCollection('sfUrls',array('old_url:IN'=>$del_urls));
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.$removed.' urls deleted: '. print_r($del_urls,1));
            }
            $urls = array_diff($urls,$old_urls);
            //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($urls,1));
        }
        foreach($urls_array as $url) {
            if(in_array($url['url'],$urls)) {
                //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($url,1));
                $processorProps = array(
                    'multi_id' => $multi_id,
                    'old_url' => $url['url'],
                    'page_id' => $page_id,
                    'field_word' => $url['field_word']
                );
                $otherProps = array('processors_path' => $path . 'processors/');
                $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                if ($response->isError()) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                }
            }
        }
        return parent::afterSave();
    }
}

return 'sfRuleUpdateProcessor';
