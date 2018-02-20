<?php

class sfRuleDuplicateProcessor extends modObjectDuplicateProcessor
{
    public $objectType = 'sfRule';
    public $classKey = 'sfRule';
    public $languageTopics = array('seofilter');
    public $nameField = 'name';


    public function beforeSave()
    {
        $this->newObject->set('page',(int)$this->getProperty('page'));
        return parent::beforeSave();
    }

    public function alreadyExists($name) {
        return $this->modx->getCount($this->classKey,array(
                $this->nameField => $name,
                'page'=>(int)$this->getProperty('page')
            )) > 0;

    }
    /**
     *
     */
    public function afterSave()
    {
       // $this->modx->log(modx::LOG_LEVEL_ERROR,print_r($this->getProperties(),1));
        $this->duplicateFields();
        $this->generateUrls();
    }



    public function duplicateFields()
    {
        if($this->getProperty('copy_fields',false)) {
            $links = $this->object->getMany('Links');
            if(is_array($links) && !empty($links)) {
                foreach($links as $link) {
                    $newLink = $this->modx->newObject('sfFieldIds');
                    $newLink->fromArray($link->toArray());
                    $newLink->set('multi_id',$this->newObject->get('id'));
                    $newLink->save();
                }
            }
        }
    }

    public function generateUrls()
    {
        if((int)$this->getProperty('active')) {
            $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
            $multi_id = $this->newObject->get('id');
            $page_id = $this->newObject->get('page');
            $urls_array = $this->newObject->generateUrl(1);
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
        }
    }


}

return 'sfRuleDuplicateProcessor';