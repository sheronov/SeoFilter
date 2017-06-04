<?php

class sfFieldCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'sfField';
    public $classKey = 'sfField';
    public $languageTopics = array('seofilter');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_name'));
        } elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_field_err_ae'));
        }

        return parent::beforeSet();
    }

    public  function afterSave()
    {
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
        if($class = $this->getProperty('class') && $key = $this->getProperty('key')) {
            $q = $this->modx->newQuery($class);
            $q->limit(0);
            $q->select(array('DISTINCT '.$class.'.'.$key));
            if ($q->prepare() && $q->stmt->execute()) {
                while($word = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                    if($this->getProperty('translit')) {
                        //$this->object->translit($word,1);
                    } else {
                        echo $word;
                    }
                    echo '<br>';
                }
            }
        }
//
//        $fields = $modx->getCollection('sfField');
//        foreach($fields as $field) {
//            print_r($field->toArray());
//            $class = $field->get('class');
//            $key = $field->get('key');
//            $q = $modx->newQuery($class);
//            $q->limit(0);
//            $q->select(array('DISTINCT '.$class.'.'.$key));
//            if ($q->prepare() && $q->stmt->execute()) {
//                while($word = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
//                    if($field->get('translit')) {
//                        echo $field->translit($word,1);
//                    } else {
//                        echo $word;
//                    }
//                    echo '<br>';
//                }
//            }
//        }
        return true;
    }

}

return 'sfFieldCreateProcessor';