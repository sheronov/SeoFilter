<?php

require_once 'sfFieldValues.php';

class sfFieldCreateProcessor extends modObjectCreateProcessor
{
    use sfFieldValues;

    public $objectType     = 'sfField';
    public $classKey       = 'sfField';
    public $languageTopics = ['seofilter'];
    //public $permission = 'create';

    /*** @var SeoFilter $SeoFilter */
    protected $SeoFilter;


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        $alias = trim($this->getProperty('alias'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }

        return parent::beforeSet();
    }


    public function afterSave()
    {
        if ($this->object->get('active') && !$this->object->get('slider')) {
            $this->collectValues();
        }

        return parent::afterSave();
    }

}

return 'sfFieldCreateProcessor';