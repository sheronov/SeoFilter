<?php

class sfCountHandler
{
    /* @var modX $modx */
    public $modx;
    public $config = [];

    /* @var pdoFetch|pdoTools $pdoTools */
    public $pdoTools;

    /**
     * @param  modX  $modx
     * @param  array  $config
     */
    public function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('seofilter_core_path', $config,
            $this->modx->getOption('core_path').'components/seofilter/'
        );
        $customPath = $this->modx->getOption('seofilter_custom_path', $config, $corePath.'custom/');

        $count_results = $this->modx->getOption('seofilter_count', null, 0, true);
        $count_choose = $this->modx->getOption('seofilter_choose', null, '', true);
        $count_select = $this->modx->getOption('seofilter_select', null, '', true);
        $values_delimeter = $this->modx->getOption('seofilter_values_delimeter', null, ',', true);

        $this->config = array_merge([
            'corePath'   => $corePath,
            'customPath' => $customPath,
            'modelPath'  => $corePath.'model/',

            'count_choose'     => $count_choose,
            'count_select'     => $count_select,
            'count_results'    => $count_results,
            'values_delimeter' => $values_delimeter,
            'base_class'       => 'modResource'
        ], $config);

        $this->pdoTools = $this->modx->getService('pdoFetch');
        $this->pdoTools->setConfig(['loadModels' => 'seofilter']);

        $this->modx->addPackage('seofilter', $this->config['modelPath']);
    }

    public function countByRule($rule_id = 0)
    {
        $links = $all_links = 0; // количество ссылок связанное с этим словом
        $total = 0; // количество результатов связанное с этим правилом (не уникальных)

        $q = $this->modx->newQuery('sfUrls');
        $q->where([
            'multi_id' => $rule_id
        ]);
        $urls = $this->modx->getIterator('sfUrls', $q);
        foreach ($urls as $url) {
            $url_id = $url->get('id');
            $old_total = $url->get('total');
            $page_id = $url->get('page_id');
            $all_links++;
            if ($link_total = $this->countByLink($url_id, $page_id, [], 0)) {
                $total += $link_total;
                $links++;
            }
            if ($link_total != $old_total) {
                $url->set('total', $link_total);
                $url->set('editedon', strtotime(date('Y-m-d H:i:s')));
                $url->save();
            }
        }
        //
        //        $q->select(array('sfUrls.id'));
        //        if($q->prepare() && $q->stmt->execute()) {
        //            while ($url_id = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
        //                $all_links++;
        //                if($link_total = $this->countByLink($url_id)) {
        //                    $total += $link_total;
        //                    $links++;
        //                }
        //            }
        //        }

        return ['all_links' => $all_links, 'links' => $links, 'total' => $total];
    }

    public function countBySlider($field_id = 0, $field = [])
    {
        $links = $all_links = 0; // количество ссылок связанное с этим словом
        $total = 0; // количество результатов связанное с этим словом (не уникальных)

        $q = $this->modx->newQuery('sfDictionary');
        $q->where(['field_id' => $field_id]);
        $q->select('id');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($word_id = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                if ($recount = $this->countByWord($word_id)) {
                    $links += $recount['links'];
                    $all_links += $recount['all_links'];
                    $total += $recount['total'];
                }
            }
        }

        return ['all_links' => $all_links, 'links' => $links, 'total' => $total];
    }

    public function countByWord($word_id = 0, $field_id = 0)
    {
        $links = $all_links = 0; // количество ссылок связанное с этим словом
        $total = 0; // количество результатов связанное с этим словом (не уникальных)

        $q = $this->modx->newQuery('sfUrls');
        $q->innerJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
        $q->where([
            'sfUrlWord.word_id' => $word_id
        ]);
        $urls = $this->modx->getIterator('sfUrls', $q);
        foreach ($urls as $url) {
            $url_id = $url->get('id');
            $old_total = $url->get('total');
            $page_id = $url->get('page_id');
            $all_links++;
            if ($link_total = $this->countByLink($url_id, $page_id, [], 0)) {
                $total += $link_total;
                $links++;
            }
            if ($link_total != $old_total) {
                $url->set('total', $link_total);
                $url->set('editedon', strtotime(date('Y-m-d H:i:s')));
                $url->save();
            }
        }
        // версия без объектов
        //        $q->select(array('sfUrls.id,sfUrls.total'));
        //        if($q->prepare() && $q->stmt->execute()) {
        //            while ($url_id = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
        //                $all_links++;
        //                if($link_total = $this->countByLink($url_id)) {
        //                    $total += $link_total;
        //                    $links++;
        //                }
        //            }
        //        }

        return ['all_links' => $all_links, 'links' => $links, 'total' => $total];
    }

    public function countByLink($url_id = 0, $page_id = 0, $additional_params = [], $update = 1, $min_max = 0)
    {
        $total = $old_total = 0;
        $params = []; //параметры, из которых состоит ссылка
        $fields = []; //поля, которые задействованы
        $parents = 0; //parents для поиска его потомков
        $rule_where = []; //дополнительные условия, которые заданы в правиле/ссылке
        $rule_id = 0; //пригодится, чтобы исходя из правила менять class для подсчёта

        $q = $this->modx->newQuery('sfUrlWord');
        $q->innerJoin('sfField', 'Field', 'Field.id = sfUrlWord.field_id');
        $q->innerJoin('sfDictionary', 'Word', 'Word.id = sfUrlWord.word_id');
        $q->innerJoin('sfUrls', 'Url', 'Url.id = sfUrlWord.url_id');
        $q->innerJoin('sfRule', 'Rule', 'Rule.id = Url.multi_id');
        $q->where([
            'sfUrlWord.url_id' => $url_id
        ]);
        $q->select($this->modx->getSelectColumns('sfUrlWord', 'sfUrlWord'));
        $q->select($this->modx->getSelectColumns('sfField', 'Field', 'field.'));
        $q->select($this->modx->getSelectColumns('sfDictionary', 'Word', '', ['input']));
        $q->select($this->modx->getSelectColumns('sfUrls', 'Url', 'old_', ['total']));
        $q->select($this->modx->getSelectColumns('sfRule', 'Rule', 'rule.',
            ['id', 'page', 'count_where', 'count_parents']));
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $field = [];
                $field_alias = '';
                foreach ($row as $k => $v) {
                    if (strpos($k, 'field.') === 0) {
                        $new_k = str_replace('field.', '', $k);
                        if ($new_k === 'alias') {
                            $field_alias = $v;
                        }
                        $field[$new_k] = $v;
                        continue;
                    }
                }

                if ($field_alias) {
                    $fields[$field_alias] = $field;
                    if (!empty($row['input'])) {
                        $params[$field_alias] = $row['input'];
                    }
                }

                if (!empty($row['rule.count_where'])) {
                    $result = json_decode($row['rule.count_where'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $rule_where = $result;
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR,
                            '[SeoFilter] Additional where error. It is not JSON "'.$row['rule.count_where'].'". Please fix it and try again');
                    }
                }
                if (!empty($row['rule.count_parents']) || $row['rule.count_parents'] === 0 || $row['rule.count_parents'] === '0') {
                    $parents = $row['rule.count_parents'];
                } elseif ($page_id) {
                    $parents = $page_id;
                } else {
                    $parents = $row['rule.page'];
                }

                $old_total = $row['old_total'];

                $rule_id = $row['rule.id'];
            }
        }
        if ($params) {
            if ($additional_params) {
                if (is_array($additional_params)) {
                    $rule_where = array_merge($additional_params, $rule_where);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,
                        '[SeoFilter] Additional params must be array! Please fix it "'.$additional_params.'"');
                }
            }
            $class = $this->getClassForCount($rule_id, $fields);

            $total = $this->countByParams($params, $fields, $parents, $rule_where, $class, $min_max);
        }

        if ($update && (int)$total !== (int)$old_total) {
            if ($url = $this->modx->getObject('sfUrls', $url_id)) {
                $url->set('total', $total);
                $url->set('editedon', strtotime(date('Y-m-d H:i:s')));
                $url->save();
            }
        }

        return (int)$total;
    }

    /**
     * The user method for changing the class by the rule id or fields for counting results
     *
     * @param  int  $rule_id
     *
     * @return string
     */
    public function getClassForCount($rule_id = 0, $fields = [])
    {
        $class = 'modResource';

        return $class;
    }

    public function getResourceConditions($value = '', $field = [], $class_key = '')
    {
        return $this->getModResourceConditions($value, $field, $class_key);
    }

    public function getModResourceConditions($value = '', $field = [], $class_key = '')
    {
        $where = [];

        if (!$class_key) {
            $class_key = $this->modx->escape($field['class']).'.'.$this->modx->escape($field['key']);
        }

        if ($field['slider']) {
            if (strpos($value, $this->config['values_delimeter']) === false) {
                $where[] = $class_key." = ".(float)$value;
            } else {
                $slider = explode($this->config['values_delimeter'], $value);
                $slider_arr = [];
                $slider_arr[] = "$class_key >=  ".str_replace(',', '.', (float)$slider[0]);
                if (isset($slider[1])) {
                    $slider_arr[] = "$class_key <= ".str_replace(',', '.', (float)$slider[1]);
                }
                $where[] = implode(' AND ', $slider_arr);
            }
        } else {
            if (strpos($value, $this->config['values_delimeter']) !== false) {
                $values = explode($this->config['values_delimeter'], $value);
                if ($field['exact']) {
                    $where[$class_key.':IN'] = $values;
                } else {
                    $where[$class_key.':REGEXP'] = implode('|', $values);
                }
            } elseif (strpos($value, '||') !== false) {
                $values = explode('||', $value);
                if ($field['exact']) {
                    $where[$class_key.':IN'] = $values;
                } else {
                    $where[$class_key.':REGEXP'] = implode('|', $values);
                }
            } elseif ($field['exact']) {
                $where[$class_key] = $value;
            } else {
                $where[$class_key.':LIKE'] = '%'.$value.'%';
            }
        }

        $conditions = ['where' => $where];

        if ($field['class'] == 'modResource' && $field['key'] == 'parent' && $this->checkMS2()) {
            $conditions['leftjoin'] = [
                'msCategoryMember' => [
                    'class' => 'msCategoryMember',
                    'on'    => 'msCategoryMember.product_id = modResource.id'
                ]
            ];

            $sql_where = '(';
            foreach ($where as $pre => $val) {
                $sql_where .= str_replace(':', ' ', $pre).' ';
                if (is_array($val)) {
                    $sql_where .= "('".implode("', '", $val)."')";
                } else {
                    $sql_where .= '= '.$val;
                }
            }

            if (strpos($value, $this->config['values_delimeter']) !== false) {
                $values = explode($this->config['values_delimeter'], $value);
                $sql_where .= " OR msCategoryMember.category_id IN ('".implode("', '", $values)."')";
            } elseif (strpos($value, '||') !== false) {
                $values = explode('||', $value);
                $sql_where .= " OR msCategoryMember.category_id IN ('".implode("', '", $values)."')";
            } else {
                $sql_where .= ' OR msCategoryMember.category_id = '.$value;
            }
            $sql_where .= ' )';

            $conditions['where'] = [$sql_where];
        }

        return $conditions;
    }

    public function getDataConditions($value = '', $field = [], $includeWhere = 1)
    {
        return $this->getMsProductDataConditions($value, $field, $includeWhere);
    }

    public function getMsProductDataConditions($value = '', $field = [], $includeWhere = 1)
    {
        $conditions = [];
        $conditions['join'] = [
            'msProductData' => ['class' => 'msProductData', 'on' => 'msProductData.id = modResource.id']
        ];

        if ($includeWhere) {
            $conditions = array_merge($conditions, $this->getModResourceConditions($value, $field));
        }

        return $conditions;
    }

    public function getVendorConditions($value = '', $field = [], $includeWhere = 1)
    {
        $this->getMsVendorConditions($value, $field, $includeWhere);
    }

    public function getMsVendorConditions($value = '', $field = [], $includeWhere = 1)
    {
        $conditions = [];
        $conditions['join'] = [
            'msProductData' => ['class' => 'msProductData', 'on' => 'msProductData.id = modResource.id'],
            'msVendor'      => ['class' => 'msVendor', 'on' => 'msVendor.id = msProductData.vendor']
        ];

        if ($includeWhere) {
            $conditions = array_merge($conditions, $this->getModResourceConditions($value, $field));
        }

        return $conditions;
    }

    public function getOptionConditions($value = '', $field = [], $includeWhere = 1)
    {
        $this->getMsProductOptionConditions($value, $field, $includeWhere);
    }

    public function getMsProductOptionConditions($value = '', $field = [], $includeWhere = 1)
    {
        $conditions = [];

        $option = ucfirst($field['key']);
        $tableKey = $this->modx->escape('option'.$option);

        $conditions['join'] = [
            'msProductData' => ['class' => 'msProductData', 'on' => 'msProductData.id = modResource.id'],
            $tableKey       => [
                'class' => 'msProductOption',
                'on'    => "{$tableKey}.product_id = msProductData.id AND {$tableKey}.key = \"{$field['key']}\""
            ]
        ];

        $class_key = $tableKey.'.value';

        if ($includeWhere) {
            $conditions = array_merge($conditions, $this->getModResourceConditions($value, $field, $class_key));
        }

        return $conditions;
    }

    public function getTaggerConditions($value = '', $field = [], $includeWhere = 1)
    {
        $group = ucfirst($field['key']);

        $tableKey = $this->modx->escape('TaggerTag'.$group);
        $groupKey = $this->modx->escape('TaggerGroup'.$group);
        $resourceKey = $this->modx->escape('TaggerTagResource'.$group);

        $conditions = [
            'models' => ['tagger'],
            'join'   => [
                $groupKey    => [
                    'class' => 'TaggerGroup',
                    'on'    => "{$groupKey}.id = \"{$group}\" OR {$groupKey}.alias = \"{$group}\""
                ],
                $tableKey    => [
                    'class' => 'TaggerTag',
                    'on'    => "{$tableKey}.group = {$groupKey}.id"
                ],
                $resourceKey => [
                    'class' => 'TaggerTagResource',
                    'on'    => "{$resourceKey}.tag = {$tableKey}.id AND modResource.id = {$resourceKey}.resource"
                ]
            ]
        ];

        if ($includeWhere) {
            $class_key = $tableKey.'.tag';
            $conditions = array_merge($conditions, $this->getModResourceConditions($value, $field, $class_key));
        }

        //        $this->modx->log(1,$value.print_r($field,1));
        //        $this->modx->log(1,'Conditions '.print_r($conditions,1));

        return $conditions;
    }

    public function getTVConditions($value = '', $field = [], $includeWhere = 1)
    {
        $this->getModTemplateVarConditions($value, $field, $includeWhere);
    }

    public function getModTemplateVarConditions($value = '', $field = [], $includeWhere = 1)
    {
        $conditions = [
            'tvs'  => [$field['key']],
            'join' => []
        ];

        $tv = ucfirst($field['key']);
        if (isset($field['xpdo_package']) && strtolower($field['xpdo_package']) == 'tvsuperselect') {
            //            $this->pdoTools->setConfig(array('loadModels' => 'tvsuperselect'));
            $conditions['models'][] = 'tvsuperselect';
            $tvssKey = $this->modx->escape('tvssOption'.$tv);
            $conditions['join'][$tvssKey] = [
                'class' => 'tvssOption',
                'on'    => "{$tvssKey}.resource_id = modResource.id"
            ];
            $class_key = $tvssKey.'.value';
        } else {
            $class_key = $this->modx->escape('TV'.strtolower($field['key'])).'.value';
        }

        if ($includeWhere) {
            $conditions = array_merge($conditions, $this->getModResourceConditions($value, $field, $class_key));
        }

        return $conditions;
    }

    public function getConditions($value = '', $field = [])
    {
        $where = $join = $leftjoin = $tvs = [];
        $conditions = ['where' => $where, 'join' => $join, 'leftjoin' => $leftjoin, 'tvs' => $tvs];

        if (!empty($field)) {
            $method_name = 'get'.ucfirst($field['class']).'Conditions';
            if (method_exists($this, $method_name)) {
                $conditions = call_user_func_array([$this, $method_name], [$value, $field]);
            } elseif ($this->config['count_results']) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[SeoFilter] Counting error: Method "'.$method_name.'" not exists in class "'.get_class($this).' ".');
            }
        }

        return $conditions;
    }

    public function getMinMaxConditions($class = 'modResource', $count_select = '', $count_choose = '')
    {
        $conditions = ['select' => [], 'choose' => []];
        if (!$count_select) {
            $count_select = $this->config['count_select'];
        }
        if (!$count_choose) {
            $count_choose = $this->config['count_choose'];
        }

        $count_select = array_map('trim', explode(',', $this->config['count_select']));
        $count_choose = array_map('trim', explode(',', $this->config['count_choose']));

        foreach ($count_select as $cselect) {
            if (strpos($cselect, '.') !== false) {
                $cselect = explode('.', $cselect);
                $sclass = array_shift($cselect);
                $cselect = implode('.', $cselect);
            } else {
                $sclass = $class;
            }

            $sclass = $this->classCanonical($sclass);

            if ($sclass === $class) {
                $conditions['select'][$sclass][] = $cselect;
                continue;
            }

            $clear_key = explode(' ', $cselect);
            $clear_key = array_shift($clear_key);
            $method_name = 'get'.ucfirst($sclass).'Conditions';
            if (method_exists($this, $method_name)) {
                if ($add_conditions = $this->$method_name('', ['key' => $clear_key], 0)) {
                    foreach ($add_conditions as $add => $vals) {
                        if ($add === 'join') {
                            switch ($sclass) {
                                case 'msVendor':
                                case 'msProductOption':
                                    $vals = array_filter($vals, function ($value, $table) {
                                        return $table !== 'msProductData';
                                    }, ARRAY_FILTER_USE_BOTH);
                                    $conditions['join']['msProductData'] = [
                                        'class' => 'msProductData',
                                        'on'    => 'msProductData.id = modResource.id'
                                    ];
                                    $add = 'leftJoin';
                                    break;
                            }
                        }
                        if (isset($conditions[$add])) {
                            $conditions[$add] = array_merge($conditions[$add], $vals);
                        } else {
                            $conditions[$add] = $vals;
                        }
                    }
                    switch ($sclass) {
                        case 'msProductOption':
                            $option = ucfirst($clear_key);
                            $table = $this->modx->escape('option'.$option);
                            $column = $table.'.`value`';
                            if (mb_strpos($cselect, ' ') !== false) {
                                $tmpSelect = explode(' ', $cselect);
                                $column .= ' as '.array_pop($tmpSelect);
                            } else {
                                $column .= ' as '.$clear_key;
                            }
                            $conditions['select'][$table][] = $column;
                            break;
                        default:
                            $conditions['select'][$sclass][] = $cselect;
                            break;
                    }
                }
            } else {
                $this->modx->log(modx::LOG_LEVEL_ERROR,
                    '[SeoFilter] Method '.$method_name.' is not exists in this class');
            }
        }

        foreach ($count_choose as $choose) {
            if (strpos($choose, '.') !== false) {
                $choose = explode('.', $choose);
                $sclass = array_shift($choose);
                $choose = implode('.', $choose);

                if ($sclass != $class) {
                    $method_name = 'get'.ucfirst($sclass).'Conditions';
                    if (method_exists($this, $method_name)) {
                        if ($add_conditions = call_user_func_array([$this, $method_name],
                            ['', ['key' => $clear_key], 0])) {
                            foreach ($add_conditions as $add => $vals) {
                                if (isset($conditions[$add])) {
                                    $conditions[$add] = array_merge($conditions[$add], $vals);
                                } else {
                                    $conditions[$add] = $vals;
                                }
                            }
                        }
                    } else {
                        $this->modx->log(modx::LOG_LEVEL_ERROR,
                            '[SeoFilter] Method '.$method_name.' is not exists in this class');
                    }
                }
            } else {
                $sclass = $class;
            }

            $sclass = $this->classCanonical($sclass);

            if (strpos($choose, '=') !== false) {
                $chooses = explode('=', $choose);
                $choose = array_shift($chooses);
                $choose_alias = implode('=', $chooses);
            } else {
                $choose_alias = $choose;
            }
            $conditions['choose'][$sclass.'.'.$choose] = $choose_alias;
        }

        return $conditions;
    }

    public function classCanonical($class = '')
    {
        switch ($class) {
            case 'msProductData':
            case 'Data':
                $class = 'msProductData';
                break;
            case 'msProductOption':
            case 'Option':
                $class = 'msProductOption';
                break;
            case 'msVendor':
            case 'Vendor':
                $class = 'msVendor';
                break;
            case 'modTemplateVar':
            case 'TV':
                $class = 'modTemplateVar';
                break;
            case 'Resource':
            case 'modResource':
                $class = 'modResource';
                break;
        }

        return $class;
    }

    public function countByParams(
        $params = [],
        $fields = [],
        $parents = '',
        $rule_where = [],
        $class = '',
        $min_max = 0,
        $returnConfig = 0
    ) {
        $total = 0; //количество результатов удовлетворяющее условиям

        if (!$class) {
            $class = 'modResource';
        }
        $where = $join = $leftjoin = $tvs = $models = [];
        $to_config = [
            'where'    => 'where',
            'join'     => 'innerJoin',
            'leftjoin' => 'leftJoin',
            'tvs'      => 'includeTVs',
            'models'   => 'loadModels'
        ];

        foreach ($params as $alias => $param) {
            if (isset($fields[$alias])) {
                $conditions = $this->getConditions($param, $fields[$alias]);
                foreach ($to_config as $prop => $propConfig) {
                    if (isset($conditions[$prop])) {
                        ${$prop} = array_merge(${$prop}, $conditions[$prop]);
                    }
                }
            } else {
                //$this->modx->log(modX::LOG_LEVEL_ERROR,'[SeoFilter] ERROR: The field by alias = '.$alias.' is not set to fields array. Param = '.$param);
            }
        }

        if ($rule_where) {
            $conditions = $this->prepareWhere($rule_where, $fields);
            foreach ($to_config as $prop => $propConfig) {
                if (isset($conditions[$prop])) {
                    ${$prop} = array_merge(${$prop}, $conditions[$prop]);
                }
            }
        }

        $config = [
            'class'  => $class,
            'return' => 'data',
            'limit'  => 1,
            'select' => [
                $class => 'COUNT(DISTINCT '.$class.'.id) as count'
            ]
        ];

        if (!empty($parents) || $parents === 0 || $parents === '0') {
            $config['parents'] = $parents;
        }

        $to_return = [];
        foreach ($to_config as $prop => $propConfig) {
            if (!empty(${$prop})) {
                if (in_array($prop, ['tvs', 'models'], true)) {
                    $config[$propConfig] = $to_return[$propConfig] = implode(',', ${$prop});
                } else {
                    $config[$propConfig] = $to_return[$propConfig] = ${$prop};
                }
            }
        }

        if ($returnConfig) {
            return $to_return;
        }

        $check = true;

        $p_key = '`modResource`.`parent`';
        if (isset($config['where'][$p_key])) {
            $parentsWhere = $config['where'][$p_key];
        } elseif (isset($config['where'][0]) && mb_strpos($config['where'][0], $p_key) !== false) {
            $parentsWhere = $config['where'][0];
            $p_key = 0;
        }
        if (isset($parentsWhere, $config['parents']) && (string)$config['parents'] !== (string)$parentsWhere) {
            $this->pdoTools->setConfig($config);
            $where = $this->pdoTools->additionalConditions();
            $check = false;
            foreach ($where as $key => $vals) {
                if ($this->config['proMode'] && mb_strpos($parentsWhere, '||') !== false) {
                    $inp_parents = explode('||', $parentsWhere);
                    foreach ($inp_parents as $inp_parent) {
                        if (is_array($vals) && in_array($inp_parent, $vals)) {
                            $check = true;
                            $config['where'][$p_key] = $inp_parent;
                        }
                        foreach ($vals as $vkey => $vvals) {
                            if (mb_strpos($vkey, 'modResource.parent') !== false
                                && (is_array($vvals) && in_array($inp_parent, $vvals))) {
                                $check = true;
                                $config['where'][$p_key] = $inp_parent;
                            }
                        }
                    }
                } else {
                    if (mb_stripos($parentsWhere, 'in') !== false) {
                        $tmpWhere = explode('in', mb_strtolower($parentsWhere));
                        $lastPart = trim(array_pop($tmpWhere), ' \t\n\r\0\x0B()');
                        $parentIds = array_map('trim', explode(',', $lastPart));
                    } else {
                        $tmpWhere = explode('=', mb_strtolower($parentsWhere));
                        $parentIds = [trim(array_pop($tmpWhere), ' \t\n\r\0\x0B()')];
                    }

                    foreach ($parentIds as $parentId) {
                        if (is_array($vals) && in_array($parentId, $vals)) {
                            $check = true;
                        }
                        foreach ($vals as $vkey => $vvals) {
                            if (mb_strpos($vkey, 'modResource.parent') !== false
                                && (is_array($vvals) && in_array($parentId, $vvals))) {
                                $check = true;
                            }
                        }
                    }
                }
            }
            if (!$check) {
                $total = 0;
            }
        }

        if ($check && $run = $this->run($config)) {
            if (is_array($run) && isset($run[0]['count'])) {
                $total = $run[0]['count'];
            }
            // $this->modx->log(1,print_r($config,1));
            // $this->modx->log(1,print_r($run,1));
            // если проблема с подсчётами - проверить здесь в первую очередь
        }

        $min_max_array = ['total' => $total];
        if ($total && $min_max && $this->config['count_select'] && $this->config['count_choose']) {
            $conditions = $this->getMinMaxConditions($class);

            if (!empty($conditions['choose']) && !empty($conditions['select'])) {
                unset($config['select']);
                if (!empty($conditions['select'])) {
                    foreach ($conditions['select'] as $table => $selectFields) {
                        $config['select'][$table] = implode(',', $selectFields);
                    }
                }

                if (!empty($conditions['tvs']) && is_array($conditions['tvs'])) {
                    if (!empty($config['includeTVs'])) {
                        $config['includeTVs'] .= ','.implode(',', $conditions['tvs']);
                    } else {
                        $config['includeTVs'] = implode(',', $conditions['tvs']);
                    }
                    unset($config['select']['modTemplateVar']);
                }

                if (!empty($conditions['join'])) {
                    if (isset($config['innerJoin'])) {
                        $config['innerJoin'] = array_merge($config['innerJoin'], $conditions['join']);
                    } else {
                        $config['innerJoin'] = $conditions['join'];
                    }
                }

                if (!empty($conditions['leftJoin'])) {
                    if (isset($config['leftJoin'])) {
                        $config['leftJoin'] = array_merge($config['leftJoin'], $conditions['leftJoin']);
                    } else {
                        $config['leftJoin'] = $conditions['leftJoin'];
                    }
                }

                foreach ($conditions['choose'] as $sortBy => $alias) {
                    $config_where = [];
                    if (!empty($config['where'])) {
                        $config_where = $config['where'];
                    }
                    foreach (['max' => 'DESC', 'min' => 'ASC'] as $m => $sortDir) {
                        if (mb_strpos($sortBy, 'modTemplateVar.') !== false) {
                            $sortBy = str_replace('modTemplateVar.', '', $sortBy);
                            if ($m === 'min') {
                                $config['tvFilters'] = $sortBy.'>0';
                            }
                            $config['sortbyTV'] = $sortBy;
                            $config['sortdirTV'] = $sortDir;
                        } else {
                            if ($m === 'min') {
                                $config['where'] = array_merge($config_where, [$sortBy.':>' => 0]);
                            }
                            $config['sortby'] = $sortBy;
                            $config['sortdir'] = $sortDir;
                        }

                        if (!empty($config['leftJoin'])) {
                            foreach ($config['leftJoin'] as $table => $column) {
                                if (isset($config['innerJoin'][$table])) {
                                    unset($config['leftJoin'][$table]);
                                }
                            }
                        }

                        if ($run = $this->run($config)) {
                            if (!empty($run[0])) {
                                foreach ($run[0] as $key => $val) {
                                    $min_max_array[$m.'_'.$alias.'_'.$key] = $val;
                                }
                            }
                        }
                    }
                }
            }

            return $min_max_array;
        }

        return $total;
    }

    public function run($config = [], $count = 1)
    {
        $this->pdoTools->setConfig($config);
        return $this->pdoTools->run();
    }

    public function prepareWhere($where = [], $fields = [])
    {
        $conditions = [
            'where'    => [],
            'join'     => [],
            'leftjoin' => [],
            'tvs'      => []
        ];
        foreach ($where as $key => $cond) {
            if (strpos($key, '.') !== false) {
                $add_key = '';
                $keys = explode('.', $key);
                $table = array_shift($keys);
                $key = implode('.', $keys);
                $clear_key = explode(':', $key);
                if (!empty($clear_key[1])) {
                    $add_key = ':'.$clear_key[1];
                }
                $clear_key = array_shift($clear_key);

                switch ($table) {
                    case 'msProductData':
                    case 'Data':
                        if ($add_conditions = $this->getMsProductDataConditions('', [], 0)) {
                            foreach ($add_conditions as $add => $vals) {
                                if (isset($conditions[$add])) {
                                    $conditions[$add] = array_merge($conditions[$add], $vals);
                                }
                            }
                        }
                        $conditions['where']['msProductData.'.$key] = $cond;
                        break;
                    case 'msProductOption':
                    case 'Option':
                        if (strpos($clear_key, '.value') !== false) {
                            $clear_key = str_replace('.value', '', $clear_key);
                        }
                        if ($add_conditions = $this->getMsProductOptionConditions('', ['key' => $clear_key], 0)) {
                            foreach ($add_conditions as $add => $vals) {
                                if (isset($conditions[$add])) {
                                    $conditions[$add] = array_merge($conditions[$add], $vals);
                                }
                            }
                        }
                        $conditions['where']['option'.ucfirst($clear_key).'.value'.$add_key] = $cond;
                        break;
                    case 'msVendor':
                    case 'Vendor':
                        if ($add_conditions = $this->getMsVendorConditions('', [], 0)) {
                            foreach ($add_conditions as $add => $vals) {
                                if (isset($conditions[$add])) {
                                    $conditions[$add] = array_merge($conditions[$add], $vals);
                                }
                            }
                        }
                        $conditions['where']['msVendor.'.$key] = $cond;
                        break;
                    case 'modTemplateVar':
                    case 'TV':
                        if (strpos($clear_key, '.value') !== 0) {
                            $clear_key = str_replace('.value', '', $clear_key);
                        }
                        if ($add_conditions = $this->getModTemplateVarConditions('', ['key' => $clear_key], 0)) {
                            foreach ($add_conditions as $add => $vals) {
                                if (isset($conditions[$add])) {
                                    $conditions[$add] = array_merge($conditions[$add], $vals);
                                }
                            }
                        }
                        $conditions['where']['TV'.$clear_key.'.value'.$add_key] = $cond;
                        break;
                    case 'Resource':
                    case 'modResource':
                        $conditions['where'][$key] = $cond;
                        break;
                    default:
                        $method_name = 'get'.ucfirst($table).'Conditions';
                        if (method_exists($this, $method_name)) {
                            if ($add_conditions = call_user_func_array([$this, $method_name],
                                ['', ['key' => $clear_key], 0])) {
                                foreach ($add_conditions as $add => $vals) {
                                    if (isset($conditions[$add])) {
                                        $conditions[$add] = array_merge($conditions[$add], $vals);
                                    }
                                }
                            }
                        } else {
                            $this->modx->log(MODX::LOG_LEVEL_ERROR,
                                '[SeoFilter] The table = '.$table.' is unknown for counting in where condition '.print_r($where,
                                    1));
                        }

                        $conditions['where'][$table.'.'.$key] = $cond;

                        break;
                }
            } else {
                //
                $conditions['where'][$key] = $cond;
            }
        }

        return $conditions;
    }

    /*
     * Deprecated method
     */
    public function getRuleCount($params = [], $fields_key = [], $parents, $count_where = [], $min_max = 0)
    {
        $count = 0;
        $innerJoin = [];
        $addTVs = [];
        $fields_where = [];
        $params_keys = array_diff(array_keys($params), array_keys($fields_key));

        foreach ($params_keys as $param) {
            if ($field = $this->pdoTools->getArray('sfField', ['alias' => $param])) {
                $alias = $field['alias'];
                $fields_key[$alias]['class'] = $field['class'];
                $fields_key[$alias]['key'] = $field['key'];
                $fields_key[$alias]['exact'] = $field['exact'];
                $fields_key[$alias]['slider'] = $field['slider'];
                $fields_key[$alias]['xpdo_package'] = $field['xpdo_package'];
            }
        }

        if (count(array_diff(array_keys($params), array_keys($fields_key)))) {
            //            $this->modx->log(modx::LOG_LEVEL_ERROR,"[SeoFilter] don't known this fields. Please add this fields to the first tab in component (Fields)".print_r(array_diff(array_keys($params), array_keys($fields_key)),1));
        }

        foreach ($fields_key as $field_alias => $field) {
            switch ($field['class']) {
                case 'msProductData':
                case 'modResource':
                case 'msProductOption':
                    $fw = $field['class'].'.'.$field['key'];
                    if ($field['class'] == 'msProductData') {
                        $innerJoin['msProductData'] = [
                            'class' => 'msProductData',
                            'on'    => 'msProductData.id = modResource.id'
                        ];
                    }
                    if ($field['class'] == 'msProductOption') {
                        $innerJoin['msOption'.$field['key']] = [
                            'class' => 'msProductOption',
                            'on'    => 'msOption'.$field['key'].'.product_id = modResource.id'
                        ];
                        $fields_where['msOption'.$field['key'].'.key'] = $field['key'];
                        $fw = 'msOption'.$field['key'].'.value';
                    }
                    if ($field['slider']) {
                        $slider = explode($this->config['values_delimeter'], $params[$field_alias]);
                        $slider_arr = [];
                        $slider_arr[] = "$fw >=  ".(float)$slider[0];
                        //                      $where[$class_key.':>='] = (float)$slider[0];
                        if (isset($slider[1])) {
                            //                      $where[$class_key.':<='] = (float)$slider[1];
                            $slider_arr[] = "$fw <= ".(float)$slider[1];
                        }
                        $fields_where[] = implode(' AND ', $slider_arr);
                        //                        $fields_where[$fw.':>='] = (float)$slider[0];
                        //                        if($slider[1]) {
                        //                            $fields_where[$fw.':<='] = (float)$slider[1];
                        //                        }
                    } else {
                        $values = explode($this->config['values_delimeter'], $params[$field_alias]);
                        if (!isset($count_where[$fw])) {
                            $fields_where[$fw.':IN'] = $values;
                        }
                    }
                    break;
                case 'modTemplateVar':
                    $addTVs[] = $field['key'];
                    if (strtolower($field['xpdo_package']) == 'tvsuperselect') {
                        $this->pdoTools->setConfig(['loadModels' => 'tvsuperselect']);
                        $innerJoin['tvssOption'.$field['key']] = [
                            'class' => 'tvssOption',
                            'on'    => 'tvssOption'.$field['key'].'.resource_id = modResource.id'
                        ];
                        $fields_where['tvssOption'.$field['key'].'.value:LIKE'] = '%'.$params[$field_alias].'%';
                    } elseif ($field['exact']) {
                        $fields_where['TV'.$field['key'].'.value'] = $params[$field_alias];
                    } else {
                        $fields_where['TV'.$field['key'].'.value:LIKE'] = '%'.$params[$field_alias].'%';
                    }
                    break;
                case 'msVendor':
                    $innerJoin['msProductData'] = [
                        'class' => 'msProductData',
                        'on'    => 'msProductData.id = modResource.id'
                    ];
                    $innerJoin['msVendor'] = ['class' => 'msVendor', 'on' => 'msVendor.id = msProductData.vendor'];
                    $fields_where[$field['class'].'.id'] = $params[$field_alias];
                    break;
                default:
                    break;
            }
        }

        $addTVs = implode(',', $addTVs);

        if ($count_where) {
            $where = array_merge($count_where, $fields_where);
        } else {
            $where = $fields_where;
        }
        foreach ($where as $key => $wheres) {
            if (strpos($key, '.') !== false) {
                $key = explode('.', $key);
                if (!isset($innerJoin[$key[0]])) {
                    switch ($key[0]) {
                        case 'msProductData':
                        case 'Data':
                            $innerJoin['msProductData'] = [
                                'class' => 'msProductData',
                                'on'    => 'msProductData.id = modResource.id'
                            ];
                            break;
                        case 'msProductOption':
                            $innerJoin['msProductOption'] = [
                                'class' => 'msProductOption',
                                'on'    => 'msProductOption.product_id = modResource.id'
                            ];
                            break;
                        case 'msVendor':
                            $innerJoin['msProductData'] = [
                                'class' => 'msProductData',
                                'on'    => 'msProductData.id = modResource.id'
                            ];
                            $innerJoin['msVendor'] = [
                                'class' => 'msVendor',
                                'on'    => 'msVendor.id = msProductData.vendor'
                            ];
                            break;
                        case 'modResource':
                            break;
                        default:
                            //                            $this->modx->log(MODX::LOG_LEVEL_ERROR,'[SeoFilter] The key = '.$key[0].' is wrong for where condition '.print_r($where,1));
                            break;
                    }
                }
            }
        }

        $select = $min_max_array = $count_choose = $count_select = [];
        $sortby = '';
        if ($this->config['count_choose']) {
            $count_choose = array_map('trim', explode(',', $this->config['count_choose']));
        }
        if ($this->config['count_select']) {
            $count_select = array_map('trim', explode(',', $this->config['count_select']));
            foreach ($count_select as $cselect) {
                if (strpos($cselect, '.')) {
                    $cselect = explode('.', $cselect);
                    $class = $cselect[0];
                    if ($class == 'msProduct') {
                        $class = 'modResource';
                    }
                    if ($class == 'msProductData') {
                        $innerJoin['msProductData'] = [
                            'class' => 'msProductData',
                            'on'    => 'msProductData.id = modResource.id'
                        ];
                    }
                    $select[$class][] = $cselect[1];
                } else {
                    $select['modResource'][] = $cselect;
                }
            }
            foreach ($select as $class => $sel) {
                $select[$class] = implode(',', $sel);
            }
        }

        foreach ($count_choose as $choose) {
            if (strpos($choose, '.')) {
                $choose = explode('.', $choose);
                $class = $choose[0];
                $choose = $choose[1];
                if (strpos($choose, '=')) {
                    $chooses = explode('=', $choose);
                    $choose = array_shift($chooses);
                    $choose_alias = implode('=', $chooses);
                } else {
                    $choose_alias = $choose;
                }
                if ($class == 'msProduct') {
                    $class = 'modResource';
                }
                if ($class == 'msProductData') {
                    $innerJoin['msProductData'] = [
                        'class' => 'msProductData',
                        'on'    => 'msProductData.id = modResource.id'
                    ];
                }
                if ($class == 'msProductOption') {
                    $innerJoin['msProductOption'] = [
                        'class' => 'msProductOption',
                        'on'    => 'msProductOption.product_id = modResource.id'
                    ];
                }
                $sortby = $class.'.'.$choose_alias;
            } else {
                if (strpos($choose, '=')) {
                    $chooses = explode('=', $choose);
                    $choose = array_shift($chooses);
                    $choose_alias = implode('=', $chooses);
                } else {
                    $choose_alias = $choose;
                }
                $sortby = 'modResource.'.$choose_alias;
            }

            foreach (['max' => 'DESC', 'min' => 'ASC'] as $m => $sort) {
                if ($m == 'min') {
                    $where = array_merge($where, [$sortby.':>' => 0]);
                }
                $this->pdoTools->setConfig([
                    'showLog'    => 0,
                    'class'      => 'modResource',
                    'parents'    => $parents,
                    'includeTVs' => $addTVs,
                    'innerJoin'  => $innerJoin,
                    'where'      => $where,
                    'limit'      => 1,
                    'sortby'     => $sortby,
                    'sortdir'    => $sort,
                    'return'     => 'data',
                    'select'     => $select
                ]);

                if ($run = $this->pdoTools->run()) {
                    foreach ($run[0] as $key => $value) {
                        $min_max_array[$m.'_'.$choose_alias.'_'.$key] = $value;
                    }
                }
            }
        }

        return $min_max_array;
    }

    public function checkMS2()
    {
        return file_exists(MODX_CORE_PATH.'components/minishop2/model/minishop2/msproduct.class.php');
    }

}