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
        $field_id = $this->getProperty('field_id');
        $alias = $this->getProperty('alias');
        if (empty($input)) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_input'));
        } elseif (!empty($alias) && $this->modx->getCount($this->classKey, ['alias' => $alias, 'field_id' => $field_id])) {
            $this->modx->error->addField('alias', $this->modx->lexicon('seofilter_dictionary_err_ae'));
        } elseif (empty($alias) && $this->modx->getCount($this->classKey,
                ['input' => $input, 'field_id' => $field_id])) {
            $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_ae'));
        }


        return parent::beforeSet();
    }

    public function beforeSave()
    {
        if($this->object->get('value') && !$this->object->get('alias')) {
            $this->object->set('alias', modResource::filterPathSegment($this->modx, $this->object->get('value')));
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {
        if($field = $this->object->getOne('Field')) {
            if($links = $field->getMany('Links')) {
                $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
                foreach ($links as $link) {
                    if ($rule = $link->getOne('Rule')) {
                        $urls_array = $rule->generateUrl(1);

                        $reurls = $urls = array();
                        foreach($urls_array as $ukey => $uarr) {
                            $urls[$ukey] = $uarr['url'];
                        }
                        //                        $url_objs = $this->modx->getCollection('sfUrls',array('multi_id'=>$rule->get('id')));
                        //                        if(count($url_objs)) {
                        //                            $old_urls = array();
                        //                            foreach($url_objs as $url_obj) {
                        //                                $old_url = $url_obj->get('old_url');
                        //                                $old_urls[] = $old_url;
                        //                                if($this->getProperty('relinks') && $this->getProperty('link_tpl')) {
                        //                                    $link = '';
                        //                                    foreach ($urls_array as $key => $val) {
                        //                                        if ($val['url'] == $old_url) {
                        //                                            $link = $val['link'];
                        //                                            break;
                        //                                        }
                        //                                    }
                        //                                    if($link) {
                        //                                        $url_obj->set('link',$link);
                        //                                        $url_obj->save();
                        //                                    }
                        //                                }
                        //                            }
                        //                            $del_urls = array_diff($old_urls,$urls);
                        //                            //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К удалению: '. print_r($del_urls,1));
                        //                            if($del_urls) {
                        //                                $removed = $this->modx->removeCollection('sfUrls',array('old_url:IN'=>$del_urls));
                        //                                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.$removed.' urls deleted: '. print_r($del_urls,1));
                        //                            }
                        //
                        //                            $urls = array_diff($urls,$old_urls);
                        //                            //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($urls,1));
                        //                        }
                        //                        foreach($urls_array as $url) {
                        //                            if(in_array($url['url'],$urls)) {
                        //                                //$this->modx->log(modX::LOG_LEVEL_ERROR, 'К добавлению: '. print_r($url,1));
                        //                                $processorProps = array(
                        //                                    'multi_id' => $rule->get('id'),
                        //                                    'old_url' => $url['url'],
                        //                                    'page_id' => $rule->get('page'),
                        //                                    'link' => $url['link'],
                        //                                    'field_word' => $url['field_word']
                        //                                );
                        //                                $otherProps = array('processors_path' => $path . 'processors/');
                        //                                $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                        //                                if ($response->isError()) {
                        //                                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                        //                                }
                        //                            }
                        //                        }

                    }
                }
            }
        }

        return parent::afterSave();
    }

}

return 'sfDictionaryCreateProcessor';
