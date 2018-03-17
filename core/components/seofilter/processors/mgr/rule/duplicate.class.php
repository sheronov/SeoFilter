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
        $this->newObject->set('active',(int)$this->getProperty('active'));
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
        /*** @var sfRule $object */
        $object = $this->newObject;
        if($this->getProperty('copy_fields',false)) {
            $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
            $multi_id = $object->get('id');
            $page_id = $object->get('page');
            if($urls_array = $object->generateUrl(1)) {
                if(is_array($urls_array)) {
                    foreach ($urls_array as $url) {
                        //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($url,1));
                        $processorProps = array(
                            'multi_id' => $multi_id,
                            'old_url' => $url['url'],
                            'page_id' => $page_id,
                            'link' => $url['link'],
                            'field_word' => $url['field_word'],
                            'from_rule' => 1,
                        );
                        $otherProps = array('processors_path' => $path . 'processors/');
                        $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                            $this->modx->error->reset();
                        }
                    }
                }
            }

        }
    }


}

return 'sfRuleDuplicateProcessor';