<?php

class sfDictionaryCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfDictionary';
    public $classKey = 'sfDictionary';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {

        $input = trim($this->getProperty('input'));
        $field_id = (int)$this->getProperty('field_id');
        $from_field = (int)$this->getProperty('from_field');
        $alias = $this->getProperty('alias');
        if (!isset($input)) {
            if($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_input'));
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_input'));
            }
        } elseif ($this->modx->getCount($this->classKey, array('input' => $input,'field_id'=>$field_id))) {
            if($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_ae'). ' Field_id = '.$field_id.' Input = '.$input);
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_ae'));
            }
        }


        return parent::beforeSet();
    }

    public function beforeSave()
    {
        $alias = $this->object->get('alias');
        if($this->object->get('value') && empty($alias)) {
            $this->object->set('alias', modResource::filterPathSegment($this->modx, $this->object->get('value')));
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {
        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $otherProps = array('processors_path' => $path . 'processors/');

        if($field = $this->object->getOne('Field')) {
            if($links = $field->getMany('Links')) {
                foreach ($links as $link) {
                    /* @var sfFieldIds $link */
                    if ($rule = $link->getOne('Rule')) {
                        /* @var sfRule $rule */
                        $urls_array = $rule->generateUrl(1,$this->object->toArray());
                        /* @var array $urls_array */
                        $reurls = $urls = array();
                        foreach($urls_array as $ukey => $uarr) {
                            $urls[$ukey] = $uarr['url'];
                        }
                        $url_objs = $this->modx->getCollection('sfUrls',array('multi_id'=>$rule->get('id')));
                        if(count($url_objs)) {
                            $old_urls = array();
                            foreach($url_objs as $url_obj) {
                                $old_url = $url_obj->get('old_url');
                                $old_urls[] = $old_url;
                                if($this->getProperty('relinks') && $this->getProperty('link_tpl')) {
                                    $link = '';
                                    foreach ($urls_array as $key => $val) {
                                        if ($val['url'] == $old_url) {
                                            $link = $val['link'];
                                            break;
                                        }
                                    }
                                    if($link) {
                                        $url_obj->set('link',$link);
                                        $url_obj->save();
                                    }
                                }
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
                            if(!empty($url['url']) && in_array($url['url'],$urls)) {
                                //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($url,1));
                                $processorProps = array(
                                    'multi_id' => $rule->get('id'),
                                    'old_url' => $url['url'],
                                    'page_id' => $rule->get('page'),
                                    'link' => $url['link'],
                                    'field_word' => $url['field_word'],
                                    'from_rule' => 1,
                                );
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

        return parent::afterSave();
    }

}

return 'sfDictionaryCreateProcessor';