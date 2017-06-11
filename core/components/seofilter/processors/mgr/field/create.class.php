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

    public function beforeSave()
    {
        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));
        return parent::beforeSave();
    }

    public  function afterSave()
    {
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));


        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $field = $this->object;
        $class = $field->get('class');
        $key = $field->get('key');
        if($class && $key) {
            $q = $this->modx->newQuery($class);
            $q->limit(0);
            $q->select(array('DISTINCT ' . $class . '.' . $key));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($input = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                    if ($field->get('xpdo')) {
                        $value = '';
                    } else {
                        $value = $input;
                    }
                    $processorProps = array(
                        'input' => $input,
                        'value' => $value,
                        'translit' => $field->get('translit'),
                        'xpdo' => $field->get('xpdo'),
                        'class' => $class,
                        'key' => $key,
                        'field_id' => $field->id
                    );
                    $otherProps = array('processors_path' => $path . 'processors/');
                    $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
                    }
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
        return parent::afterSave();
    }

}

return 'sfFieldCreateProcessor';