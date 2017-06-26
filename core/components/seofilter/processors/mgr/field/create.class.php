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
       // $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($this->getProperties(),1));


        $path = $this->modx->getOption('seofilter_core_path', null, $this->modx->getOption('core_path') . 'components/seofilter/');
        $field = $this->object;
        $class = $field->get('class');
        $key = $field->get('key');
        $values = array();

        if($class && $key) {

            $processorProps = array(
                'class' => $class,
                'key' => $key,
                'field_id' => $field->get('id'),
            );
            $otherProps = array('processors_path' => $path . 'processors/');

            if($class == 'modTemplateVar') {
                $tvvalues = $pre = array();
                $q = $this->modx->newQuery($class, array('name' => $key));
                $q->limit(1);
                $q->select('id,elements');
                if ($q->prepare() && $q->stmt->execute()) {
                    $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
                    if($row['elements']) {
                        $pre = explode('||',$row['elements']);
                    }
                    if($tv_id = $row['id']) {
                        $q = $this->modx->newQuery('modTemplateVarResource');
                        $q->where(array('tmplvarid'=>$tv_id,'value:!='=>''));
                        $q->select(array('DISTINCT modTemplateVarResource.value'));
                        if ($q->prepare() && $q->stmt->execute()) {
                            while($row = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                                $tvvalues = array_unique(array_merge($tvvalues,explode('||',$row)));
                            }
                        }
                    }
                }
                $words = array_unique(array_merge($tvvalues,$pre));
                foreach($words as $word) {
                    $processorProps['input'] = $word;
                    $processorProps['value'] = $word;
                    $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
                    }
                }
            } else {

                if($field->get('xpdo')) {
                    $xpdo_id = $field->get('xpdo_id');
                    $xpdo_name = $field->get('xpdo_name');
                    if($xpdo_class = $field->get('xpdo_class')) {
                        if($package = $field->get('xpdo_package')) {
                            $this->modx->addPackage($package, $this->modx->getOption('core_path').'components/'.$package.'/model/');
                        }
                        $q = $this->modx->newQuery($xpdo_class);
                        $q->select($xpdo_id.','.$xpdo_name);
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($row,1));
                                $values[$row[$xpdo_id]] = $row[$xpdo_name];
                            }
                        }

                    }
                }

                $q = $this->modx->newQuery($class);
                $q->limit(0);
                if ($class == 'msProductOption') {
                    $q->where(array('key' => $key, 'value:!=' => ''));
                    $q->select(array('DISTINCT ' . $class . '.value'));
                    $key = 'value';
                } elseif ($class == 'msVendor') {
                    $q->select('id,name');
                } else {
                    $q->select(array('DISTINCT ' . $class . '.' . $key));
                }
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($input = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($field->get('xpdo')) {
                            $value = $values[$input[$key]];
                        } else {
                            $value = $input[$key];
                        }
                        if ($class == 'msVendor') {
                            $value = $input['name'];
                        }
                        //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($input, 1));
                        $processorProps['input'] = $input[$key];
                        $processorProps['value'] = $value;
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
                        }
                    }
                }
            }
        }

        return parent::afterSave();
    }

}

return 'sfFieldCreateProcessor';