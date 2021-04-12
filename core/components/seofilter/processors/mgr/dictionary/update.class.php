<?php

class sfDictionaryUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'seofilter.word';
    public $classKey       = 'sfDictionary';
    public $languageTopics = ['seofilter'];
    public $afterSaveEvent = 'sfOnAfterUpdateWord';
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

        $alias = $this->object->get('alias');
        if ($this->object->get('value') && empty($alias)) {
            $this->object->set('alias', modResource::filterPathSegment($this->modx, $this->object->get('value')));
        }

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        $input = trim($this->getProperty('input'));
        $field_id = (int)$this->getProperty('field_id');
        $from_field = (int)$this->getProperty('from_field');
        $alias = $this->getProperty('alias');

        if (empty($id)) {
            return $this->modx->lexicon('seofilter_dictionary_err_ns');
        }

        if (!isset($input)) {
            if ($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_name'));
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_name'));
            }
        } elseif ($this->modx->getCount($this->classKey,
            ['input' => $input, 'field_id' => $field_id, 'id:!=' => $id])) {
            if ($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_err_ae'));
            } else {
                $this->modx->error->addField('input', $this->modx->lexicon('seofilter_dictionary_err_ae'));
            }
        } elseif ($this->modx->getCount($this->classKey,
            ['field_id' => $field_id, 'alias' => $alias, 'id:!=' => $id])) {
            if ($from_field) {
                $this->modx->error->failure($this->modx->lexicon('seofilter_dictionary_alias_double').' Field_id = '.$field_id.' Input = '.$input);
            } else {
                $this->modx->error->addField('alias', $this->modx->lexicon('seofilter_dictionary_alias_double'));
            }
        }

        $change_title = $change_alias = $change_relation = 0;
        if (($this->getProperty('value') != $this->object->get('value')) || $this->getProperty('update')) {
            $this->setProperty('update', 1); //чтоб просклонять изменения
            $change_title = 1; //чтобы перегенерировать названия ссылок
        }

        if (isset($alias) && ($alias != $this->object->get('alias'))) {
            $change_alias = 1;
        }

        if ($this->getProperty('relation_word') != $this->object->get('relation_word')) {
            $change_relation = 1;
        }

        $this->setProperty('change_alias', $change_alias);
        $this->setProperty('change_title', $change_title);
        $this->setProperty('change_relation', $change_relation);


        return parent::beforeSet();
    }

    public function afterSave()
    {
        $id = (int)$this->object->get('id');
        $path = $this->modx->getOption('seofilter_core_path', null,
            $this->modx->getOption('core_path').'components/seofilter/');
        $otherProps = ['processors_path' => $path.'processors/'];

        if ($this->getProperty('change_relation')) {
            /*** @var SeoFilter $SeoFilter */
            $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
                $this->modx->getOption('seofilter_core_path', null,
                    $this->modx->getOption('core_path').'components/seofilter/').'model/seofilter/');
            $total_message = '';
            $response = $SeoFilter->generateUrlsByWord($this->object->toArray());
            if ($response) {
                foreach ($response as $rule_id => $resp) {
                    $resp['rule_id'] = $rule_id;
                    $total_message .= $SeoFilter->pdo->parseChunk('@INLINE '.$this->modx->lexicon('seofilter_word_update_info'),
                        $resp);
                }
            }
            $this->object->set('total_message', $total_message);
        }

        if ($this->getProperty('change_alias') || $this->getProperty('change_title')) {
            $pdoTools = $this->modx->getService('pdoTools');

            $q = $this->modx->newQuery('sfUrls');
            $q->leftJoin('sfRule', 'sfRule', 'sfRule.id = sfUrls.multi_id');
            $q->rightJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
            $q->where(['sfUrlWord.word_id' => $id, 'sfUrls.id:!=' => '']);
            $q->groupby('sfUrls.id');
            $q->select([
                'sfUrls.*',
                'sfRule.url as url_mask,sfRule.link_tpl as link_tpl',
            ]);
            $urls = $this->modx->getIterator('sfUrls', $q);
            foreach ($urls as $url) {
                $url_mask = $url->get('url_mask');
                $link_tpl = $url->get('link_tpl');
                $link = $old_url = '';

                $s = $this->modx->newQuery('sfUrlWord');
                $s->where(['url_id' => $url->get('id')]);
                $s->leftJoin('sfDictionary', 'sfDictionary', 'sfUrlWord.word_id = sfDictionary.id');
                $s->innerJoin('sfField', 'sfField', 'sfField.id = sfDictionary.field_id');
                $s->select([
                    'sfUrlWord.id as uw_id',
                    'sfDictionary.*',
                    'sfField.alias as field_alias',
                ]);
                if ($s->prepare() && $s->stmt->execute()) {
                    if ($words = $s->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                        $word_array = $url_array = [];
                        $countWords = count($words);
                        foreach ($words as $word) {
                            $alias = $word['field_alias'];
                            $url_array[$alias] = $word['alias'];
                            if ($countWords == 1) {
                                $word_array = $word;
                            }
                            foreach ($word as $key => $value) {
                                if (strpos($key, 'value') !== false) {
                                    $word_array[str_replace('value', $alias, $key)] = $value;
                                } elseif (!isset($word_array[$key])) {
                                    $word_array[$key] = $value;
                                }
                            }
                            $word_array[$alias.'_input'] = $word['input'];
                            $word_array[$alias.'_alias'] = $word['alias'];
                            $word_array['m_'.$alias] = $word_array['m_'.$alias.'_i'];
                        }
                        if (!empty($link_tpl) && $this->getProperty('change_title')) {
                            $link = $pdoTools->getChunk('@INLINE '.$link_tpl, $word_array);
                        }
                        if (!empty($url_mask) && $this->getProperty('change_alias')) {
                            $to_replace = [];
                            foreach ($url_array as $ukey => $uval) {
                                $to_replace[] = '{$'.$ukey.'}';
                            }
                            $old_url = str_replace($to_replace, array_values($url_array), $url_mask);
                        }
                    }
                }

                if ($link || $old_url) {
                    $processorProps = $url->toArray();
                    if ($link) {
                        $processorProps['link'] = $link;
                    }
                    if ($old_url) {
                        $processorProps['old_url'] = $old_url;
                    }
                    $response = $this->modx->runProcessor('mgr/urls/update', $processorProps, $otherProps);
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]'.$response->getMessage());
                        $this->modx->error->reset();
                    }
                }
            }
        }


        return parent::afterSave();
    }
}

return 'sfDictionaryUpdateProcessor';
