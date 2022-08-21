<?php

class sfDictionaryUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType     = 'sfDictionary';
    public $classKey       = 'sfDictionary';
    public $languageTopics = ['seofilter'];
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

        return true;
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        $name = trim($this->getProperty('value'));
        $field_id = (int)$this->getProperty('field_id');
        $alias = $this->getProperty('alias');
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_dictionary_err_ns');
        }

        if (empty($name)) {
            $this->modx->error->addField('value', $this->modx->lexicon('seofilter_dictionary_err_name'));
        } elseif (!empty($alias) && $this->modx->getCount($this->classKey,
                ['alias' => $alias, 'field_id' => $field_id, 'id:!=' => $id])) {
            $this->modx->error->addField('alias', $this->modx->lexicon('seofilter_dictionary_err_ae'));
        } elseif (empty($alias) && $this->modx->getCount($this->classKey,
                ['value' => $name, 'field_id' => $field_id, 'id:!=' => $id])) {
            $this->modx->error->addField('value', $this->modx->lexicon('seofilter_dictionary_err_ae'));
        }

        $this->setProperty('changed_alias', $alias !== $this->object->get('alias'));
        $this->setProperty('changed_field', $field_id !== (int)$this->object->get('field_id'));

        return parent::beforeSet();
    }

    public function afterSave()
    {
        if ($this->getProperty('changed_field')) {
            $this->modx->addPackage('seofilter', $this->modx->getOption('seofilter_core_path', null,
                    $this->modx->getOption('core_path').'components/seofilter/', true).'model/');
            $urlWords = $this->modx->getIterator('sfUrlWord', ['word_id' => $this->object->get('id')]);
            foreach ($urlWords as $urlWord) {
                $urlWord->set('field_id', $this->object->get('field_id'));
                $urlWord->save();
            }
        }

        if ($this->getProperty('changed_alias')) {
            $this->object->set('alias', $this->getProperty('alias')); //fix
            $this->object->save();
            $urlwords = $this->object->getMany('UrlWords');
            foreach ($urlwords as $urlword) {
                if ($url = $urlword->getOne('Url')) {
                    $priorities = [];
                    if ($rule = $url->getOne('Rule')) {
                        $q = $this->modx->newQuery('sfFieldIds');
                        $q->sortby('priority', 'ASC');
                        $q->where(['multi_id' => $rule->get('id')]);
                        //  $q->leftJoin('sfField','sfField','sfFieldIds.field_id = sfField.id');
                        $q->select('sfFieldIds.field_id,sfFieldIds.priority');
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                $priorities[$row['priority']] = $row['field_id'];
                            }
                        }
                    }
                    $new_url = $url->updateUrl($priorities);
                    //$this->modx->log(modX::LOG_LEVEL_ERROR, 'SEOFILTER URL: '.$new_url);
                }
            }
        }

        return parent::afterSave();
    }
}

return 'sfDictionaryUpdateProcessor';
