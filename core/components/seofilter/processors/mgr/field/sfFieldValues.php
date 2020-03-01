<?php

/**
 * @property sfField $object
 * @property modX $modx
 * @property SeoFilter $SeoFilter
 * Trait sfFieldValues
 */
trait sfFieldValues
{
    protected $otherProps;

    public function initialize()
    {
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter',
            $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path').'components/seofilter/').'model/seofilter/');
        $this->otherProps = [
            'processors_path' => $this->modx->getOption('seofilter_core_path', null,
                    $this->modx->getOption('core_path').'components/seofilter/').'processors/'
        ];

        return parent::initialize();
    }

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
        return parent::beforeSave();
    }

    /**
     * Сбор значений при создании и редактировании поля
     *
     * @param  array  $existedValues
     */
    protected function collectValues($existedValues = [])
    {
        /** @var sfField $field */
        $field = $this->object;
        $values = [];

        if ($field->get('class') && trim($field->get('key'))) {
            if ($package = $field->get('xpdo_package')) {
                $this->modx->addPackage(strtolower($package),
                    $this->modx->getOption('core_path').'components/'.strtolower($package).'/model/');
            }

            if ($field->get('xpdo')) {
                $values = $this->valuesFromExternalTables($field);
            }

            switch (mb_strtolower($field->get('class'))) {
                case 'modtemplatevar':
                    $this->processTvField($field, $values, $existedValues);
                    break;
                case 'tagger':
                    $this->processTaggerField($field, $existedValues);
                    break;
                default:
                    $this->processField($field, $values, $existedValues);
            }
        }
    }

    /**
     * @param  sfField  $field
     *
     * @return array
     */
    protected function valuesFromExternalTables($field)
    {
        $values = [];
        $xpdoId = $field->get('xpdo_id');
        $xpdoName = $field->get('xpdo_name');
        $xpdoClass = $field->get('xpdo_class');
        if ($xpdoClass && $xpdoId && $xpdoName) {
            $q = $this->modx->newQuery($xpdoClass);
            if ($field->get('relation')) {
                $relation_column = $field->get('relation_column');
                if ($relation_column) {
                    $q->select($xpdoId.','.$xpdoName.','.$relation_column);
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $values[$row[$xpdoId]] = [
                                'value'    => $row[$xpdoName],
                                'relation' => $row[$relation_column]
                            ];
                        }
                    }
                } else {
                    $q->select($xpdoId.','.$xpdoName);
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $values[$row[$xpdoId]] = $row[$xpdoName];
                        }
                    }
                }
            } else {
                $q->select($xpdoId.','.$xpdoName);
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $values[$row[$xpdoId]] = $row[$xpdoName];
                    }
                }
            }
        }
        return $values;
    }

    protected function getResourceJoinCondition($class = '')
    {
        if (!$class) {
            $class = $this->object->get('class');
        }
        $condition = '';
        switch ($class) {
            case 'msProductData':
                $condition = 'msProductData.id = modResource.id';
                break;
            case 'msProductOption':
                $condition = 'msProductOption.product_id = modResource.id';
                break;
            case 'modTemplateVar':
                $condition = 'modTemplateVarResource.contentid = modResource.id';
                break;
            case 'Tagger':
                $condition = 'TaggerTagResource.resource = modResource.id';
                break;
        }
        return $condition;
    }

    protected function getProcessorProps()
    {
        return [
            'class'      => $this->object->get('class'),
            'key'        => trim($this->object->get('key')),
            'field_id'   => $this->object->get('id'),
            'from_field' => 1,
        ];
    }

    /**
     * @param  sfField  $field
     * @param  array  $values
     * @param  array  $existedValues
     */
    protected function processTvField($field, $values = [], $existedValues = [])
    {
        $tvValues = $pre = [];
        $q = $this->modx->newQuery($field->get('class'), ['name' => $field->get('key')]);
        $q->limit(1);
        $q->select('id,elements');
        if ($q->prepare() && $q->stmt->execute()) {
            $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['elements']) {
                if (mb_strpos($row['elements'], '||') !== false) {
                    $pre = array_map('trim', explode('||', $row['elements']));
                } elseif (mb_strpos($row['elements'], ',') !== false) {
                    $pre = array_map('trim', explode(',', $row['elements']));
                } else {
                    $pre = [$row['elements']];
                }
            }
            if ($tv_id = $row['id']) {
                $q = $this->modx->newQuery('modTemplateVarResource');
                $q->where(['tmplvarid' => $tv_id, 'value:!=' => '']);
                $q->select(['DISTINCT modTemplateVarResource.value']);
                if ($field->get('xpdo_where')) {
                    $q = $this->prepareQueryConditions($q, $field);
                }
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                        $result = json_decode($row, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (is_array($result)) {
                                $row_arr = $result;
                            } else {
                                $row_arr = [$result];
                            }
                        } elseif (mb_strpos($row, '||')) {
                            $row_arr = array_map('trim', explode('||', $row));
                        } elseif (mb_strpos($row, ',')) {
                            $row_arr = array_map('trim', explode(',', $row));
                        } else {
                            $row_arr = [$row];
                        }
                        $tvValues = array_unique(array_merge($tvValues, $row_arr));
                    }
                }
            }
        }
        $words = array_unique(array_merge($pre, $tvValues));
        if (!empty($existedValues)) {
            $words = array_diff($words, $existedValues);
        }
        $dictionary = []; // промежуточный массив для проверки дубликатов
        $processorProps = $this->getProcessorProps();
        foreach ($words as $word) {
            $value = null;
            $relation_id = $relation_value = '';
            if ($field->get('xpdo')) {
                if (is_array($values[$word])) {
                    $relation_value = $values[$word]['relation'];
                    $value = $values[$word]['value'];
                } else {
                    $value = $values[$word];
                }
            } elseif (mb_strpos($word, '==') !== false) {
                $word_exp = array_map('trim', explode('==', $word));
                $value = $word_exp[0];
                if (isset($word_exp[1])) {
                    $word = $word_exp[1];
                } else {
                    $word = $value;
                }
            }

            if ($value === null) {
                $value = $word;
            }

            if (empty($word) || in_array($word, $existedValues, true)) {
                continue;
            }

            if ($relation_value) {
                $relation_field = $field->get('relation_field');
                $s = $this->modx->newQuery('sfDictionary');
                $s->where(['input' => $relation_value, 'field_id' => $relation_field]);
                $s->select('id');
                $relation_id = $this->modx->getValue($s->prepare());
            }

            if (isset($dictionary[$word]) && $dictionary[$word] === $value) {
                continue;
            }
            $dictionary[$word] = $value;

            $processorProps['relation_word'] = $relation_id;
            $processorProps['input'] = $word;
            $processorProps['value'] = $value;

            $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $this->otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]'.$response->getMessage());
                $this->modx->error->reset();
            }
        }
    }

    /**
     * @param  sfField  $field
     * @param  array  $existedValues
     */
    protected function processTaggerField($field, $existedValues = [])
    {
        $key = $field->get('key');
        $taggerPath = $this->modx->getOption('tagger.core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH).'components/tagger/');
        /** @var Tagger $tagger */
        $tagger = $this->modx->getService('tagger', 'Tagger', $taggerPath.'model/tagger/',
            ['core_path' => $taggerPath]);
        if (!($tagger instanceof Tagger)) {
            return;
        }
        $q = $this->modx->newQuery('TaggerTag');
        $q->innerJoin('TaggerGroup', 'Group', 'Group.id = TaggerTag.group');
        $q->groupby('TaggerTag.id');
        $q->where(['Group.id = "'.$key.'" OR Group.alias = "'.$key.'"']);
        $q->limit(0);
        $q->select([
            'TaggerTag.tag as input,TaggerTag.label as value,TaggerTag.alias'
        ]);
        if ($field->get('xpdo_where')) {
            $q->innerJoin('TaggerTagResource', 'TaggerTagResource', 'TaggerTagResource.tag = TaggerTag.id');
            $q = $this->prepareQueryConditions($q, $field);
        }

        if ($q->prepare() && $q->stmt->execute()) {
            $processorProps = $this->getProcessorProps();
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ((empty($row['input']) && $row['input'] !== 0) || in_array($row['input'], $existedValues, true)) {
                    continue;
                }

                $processorProps['value'] = $row['value'];
                if (empty($processorProps['value'])) {
                    $processorProps = $row['input'];
                }
                $processorProps['input'] = $row['input'];
                $processorProps['alias'] = '';
                if ($row['alias'] && $row['alias'] !== '-1') {
                    $processorProps['alias'] = $row['alias'];
                }
                if ($processorProps['input'] && $processorProps['value']) {
                    $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $this->otherProps);
                    if ($response->isError()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]'.$response->getMessage());
                        $this->modx->error->reset();
                    }
                }
            }
        }
    }

    /**
     * @param  sfField  $field
     * @param  array  $values
     * @param  array  $existedValues
     */
    protected function processField($field, $values = [], $existedValues = [])
    {
        $class = $field->get('class');
        $key = $field->get('key');
        $q = $this->modx->newQuery($class);
        $q->limit(0);
        if ($class === 'msProductOption') {
            $q->where(['key' => $key, 'value:!=' => '']);
            $q->select(['DISTINCT '.$class.'.value']);
            $key = 'value';
        } elseif ($class === 'msVendor') {
            $q->select('id,name');
        } else {
            $q->select(['DISTINCT '.$class.'.'.$key]);
        }
        if ($field->get('xpdo_where')) {
            $q = $this->prepareQueryConditions($q, $field);
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $processorProps = $this->getProcessorProps();
            while ($input = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $relation_id = $relation_value = '';

                if ($field->get('xpdo')) {
                    if (is_array($values[$input[$key]])) {
                        $relation_value = $values[$input[$key]]['relation'];
                        $value = $values[$input[$key]]['value'];
                    } else {
                        $value = $values[$input[$key]];
                    }
                } else {
                    $value = $input[$key];
                }

                if ($relation_value) {
                    $relation_field = $field->get('relation_field');
                    $s = $this->modx->newQuery('sfDictionary');
                    $s->where(['input' => $relation_value, 'field_id' => $relation_field]);
                    $s->select('id');
                    $relation_id = $this->modx->getValue($s->prepare());
                }

                $processorProps['relation_word'] = $relation_id;

                if ($class === 'msVendor') {
                    $value = $input['name'];
                }
                if ($field->get('exact')) {
                    $value_arr = [$value];
                } elseif (mb_strpos($value, '||') !== false) {
                    $value_arr = array_map('trim', explode('||', $value));
                } elseif (mb_strpos($value, ',') !== false) {
                    $value_arr = array_map('trim', explode(',', $value));
                } else {
                    $value_arr = [$value];
                }
                foreach ($value_arr as $value) {
                    if (in_array($input[$key], $existedValues, true)) {
                        continue;
                    }
                    $processorProps['input'] = $input[$key];
                    $processorProps['value'] = $value;
                    if ($input[$key] && $value) {
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps,
                            $this->otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]'.$response->getMessage());
                            $this->modx->error->reset();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param  xPDOQuery  $q
     * @param  sfField  $field
     *
     * @return xPDOQuery
     */
    protected function prepareQueryConditions($q, $field)
    {
        $toConfig = ['where' => 'where', 'join' => 'innerJoin', 'leftjoin' => 'leftJoin'];
        $this->SeoFilter->loadHandler();
        $conditions = $this->SeoFilter->countHandler->prepareWhere($this->modx->fromJSON($field->get('xpdo_where')));
        if (!empty($conditions['where'])) {
            if ($field->get('class') !== 'modResource') {
                $q->innerJoin('modResource', 'modResource', $this->getResourceJoinCondition());
            }
            foreach ($conditions['where'] as $where_key => $where_arr) {
                if (mb_strpos($where_key, '.') === false) {
                    $conditions['where']['modResource.'.$where_key] = $where_arr;
                    unset($conditions['where'][$where_key]);
                }
            }
        }
        foreach ($toConfig as $prop => $propConfig) {
            if (!empty($conditions[$prop])) {
                if (in_array($propConfig, ['leftJoin', 'innerJoin'])) {
                    foreach ($conditions[$prop] as $join_alias => $join_array) {
                        $q->$propConfig($join_array['class'], $join_alias, $join_array['on']);
                    }
                } else {
                    $q->$propConfig($conditions[$prop]);
                }
            }
        }
        return $q;
    }
}