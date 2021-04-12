<?php

require_once 'sfFieldValues.php';

class sfFieldUpdateProcessor extends modObjectUpdateProcessor
{
    use sfFieldValues;

    public $objectType     = 'seofilter.field';
    public $classKey       = 'sfField';
    public $languageTopics = ['seofilter'];
    //public $permission = 'save';

    /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        $name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_field_err_ns');
        }

        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name, 'id:!=' => $id])) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }


        if (($this->getProperty('alias') !== $this->object->get('alias'))
            || ((bool)$this->getProperty('hideparam') !== (bool)$this->object->get('hideparam'))
            || ((bool)$this->getProperty('valuefirst') !== (bool)$this->object->get('valuefirst'))) {
            $this->replaceFieldAliasInSeoTexts($id);
        }

        return parent::beforeSet();
    }

    public function afterSave()
    {
        if ($this->object->get('active') && !$this->object->get('slider')) {
            $base_words = [];
            $q = $this->modx->newQuery('sfDictionary');
            $q->where(['field_id' => $this->object->get('id')]);
            $q->select('input');
            if ($q->prepare() && $q->stmt->execute()) {
                $base_words = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            $this->collectValues($base_words);
        }
        return parent::afterSave();
    }

    protected function replaceFieldAliasInSeoTexts($id)
    {
        $new_alias = $this->getProperty('alias');
        $old_alias = $this->object->get('alias');
        $this->object->set('alias', $new_alias); //fix
        $this->object->set('hideparam', (int)$this->getProperty('hideparam')); //fix
        $this->object->set('valuefirst', (int)$this->getProperty('valuefirst')); //fix
        $this->object->save();

        if ($urlWords = $this->modx->getCollection('sfUrlWord', ['field_id' => $id])) {
            foreach ($urlWords as $urlWord) {
                /* @var sfUrlWord $urlWord */
                /* @var sfUrls $url */
                if ($url = $urlWord->getOne('Url')) {
                    $priorities = [];
                    if ($rule = $url->getOne('Rule')) {
                        $q = $this->modx->newQuery('sfFieldIds');
                        $q->sortby('priority', 'ASC');
                        $q->where(['multi_id' => $rule->get('id')]);
                        $q->select('sfFieldIds.field_id,sfFieldIds.priority');
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                $priorities[$row['priority']] = $row['field_id'];
                            }
                        }
                    }
                    $url->updateUrl($priorities);
                }
            }
        }

        if ($fieldIds = $this->object->getMany('Links')) {
            foreach ($fieldIds as $fieldId) {
                /* @var sfRule $rule */
                /* @var sfFieldIds $fieldId */
                if ($rule = $fieldId->getOne('Rule')) {
                    $rule_array = $rule->toArray();
                    foreach ($rule_array as $key => $value) {
                        $new_value = str_replace(
                            [
                                '{$'.$old_alias.'}',
                                '[[+'.$old_alias.']]',
                                '-'.$old_alias,
                                $old_alias.'-',
                                $old_alias.'_'
                            ],
                            [
                                '{$'.$new_alias.'}',
                                '[[+'.$new_alias.']]',
                                '-'.$new_alias,
                                $new_alias.'-',
                                $new_alias.'_'
                            ],
                            $value);
                        $rule->set($key, $new_value);
                    }
                    if (!$this->SeoFilter->config['edit_url_mask']) {
                        $rule->set('url', $rule->generateUrl());
                    }
                    $rule->save();
                }
            }
        }
    }
}


return 'sfFieldUpdateProcessor';
