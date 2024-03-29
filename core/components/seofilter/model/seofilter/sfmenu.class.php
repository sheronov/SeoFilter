<?php

class sfMenu
{
    /** @var modX $modx */
    public $modx;
    /** @var  pdoFetch|pdoTools $pdoTools */
    public $pdoTools;
    /** @var array $config */
    public $config = [];
    /** @var array $tree */
    protected $tree = [];
    /** @var array $parents */
    protected $parents = [];
    /** @var int $level */
    protected $level = 1;
    /** @var int $idx */
    protected $idx = 1;
    /** @var sfCountHandler $countHandler */
    public $countHandler = null;

    public function __construct(modX $modx, $config = [])
    {
        $this->modx = &$modx;

        $config = array_merge([
            'firstClass'   => 'first',
            'lastClass'    => 'last',
            'hereClass'    => 'active',
            'parentClass'  => '',
            'rowClass'     => '',
            'outerClass'   => '',
            'innerClass'   => '',
            'levelClass'   => '',
            'selfClass'    => '',
            'webLinkClass' => '',
            'limit'        => 0,
        ], $config, [
            'return' => 'data',
        ]);

        $config['corePath'] = $this->modx->getOption('seofilter_core_path', $config,
            $this->modx->getOption('core_path').'components/seofilter/'
        );
        $config['customPath'] = $this->modx->getOption('seofilter_custom_path', $config,
            $config['corePath'].'custom/');

        $config['between_urls'] = $this->modx->getOption('seofilter_between_urls', null, '/', true);
        $config['container_suffix'] = $this->modx->getOption('container_suffix', null, '/');
        $config['url_suffix'] = $this->modx->getOption('seofilter_url_suffix', null, '', true);
        $config['possibleSuffixes'] = array_map('trim',
            explode(',', $this->modx->getOption('seofitler_possible_suffixes', null, '/,.html,.php', true)));
        $config['possibleSuffixes'] = array_unique(array_merge($config['possibleSuffixes'],
            [$config['container_suffix']]));
        $config['main_alias'] = $this->modx->getOption('seofilter_main_alias', null, 0);
        $config['site_start'] = $this->modx->context->getOption('site_start', 1);
        $config['proMode'] = $this->modx->getOption('seofilter_pro_mode', null, 0, true);

        if (empty($config['tplInner']) && !empty($config['tplOuter'])) {
            $config['tplInner'] = $config['tplOuter'];
        }

        if (!empty($config['context']) && !is_array($config['context'])) {
            $config['context'] = array_map('trim', explode(',', $config['context']));
        }

        $sf_seo_id = $modx->getPlaceholder('sf.seo_id');
        if (!isset($config['hereId'])) {
            $config['hereId'] = 0;
        }
        if (empty($config['hereId']) && !empty($sf_seo_id)) {
            $config['hereId'] = (int)$sf_seo_id;
        }

        $this->config = $config;

        $fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
        $path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH.'components/pdotools/model/', true);
        /** @var pdoFetch $pdoClass */
        if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
            $this->pdoTools = new $pdoClass($modx, $config);
        } else {
            return;
        }

        $modx->addPackage('seofilter', $config['corePath'].'model/');
        $modx->lexicon->load('seofilter:default');
    }

    public function clearSuffixes($url = '')
    {
        foreach ($this->config['possibleSuffixes'] as $possibleSuffix) {
            if (substr($url, -strlen($possibleSuffix)) === $possibleSuffix) {
                $url = substr($url, 0, -strlen($possibleSuffix));
            }
        }
        return $url;
    }

    /**
     * DEPRECATED METHOD
     * The new findParents
     *
     * @param  int  $id
     *
     * @return array
     */
    public function urlParents($id = 0)
    {
        $parents = [];
        $words = [];
        $rule_id = 0;
        $page_id = 0;

        $q = $this->modx->newQuery('sfUrls');
        $q->leftJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
        $q->where(['sfUrls.id' => $id]);
        $q->sortby('sfUrlWord.priority', 'ASC');
        $q->select('sfUrls.id,sfUrls.multi_id,sfUrls.page_id,sfUrlWord.field_id,sfUrlWord.word_id,sfUrlWord.priority');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $rule_id = $row['multi_id'];
                $page_id = $row['page_id'];
                $words[] = $row;
            }
        }
        if (count($words) > 1) {
            $q = $this->modx->newQuery('sfUrls');
            $q->where([
                'sfUrls.id:!='       => $id,
                'sfUrls.multi_id:!=' => $rule_id,
                'sfUrls.page_id'     => $page_id,
            ]);
            if (!(int)$this->config['double']) {
                $q->limit(1);
            }
            if ($this->config['sortby'] && $this->config['sortdir']) {
                $q->sortby($this->config['sortby'], $this->config['sortdir']);
            }
            $select = ['sfUrls.id,sfUrls.link,sfUrls.page_id,sfUrls.multi_id'];
            foreach ($words as $key => $word) {
                if ($key < count($words) - 1) {
                    $q->rightJoin('sfUrlWord', 'sfUrlWord'.$key, 'sfUrlWord'.$key.'.url_id = sfUrls.id');
                    $q->where([
                        'sfUrlWord'.$key.'.field_id' => $word['field_id'],
                        'sfUrlWord'.$key.'.word_id'  => $word['word_id'],
                    ]);
                }
            }
            $q->select($select);
            $q->groupby('sfUrls.id');
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($this->modx->getCount('sfUrlWord', ['url_id' => $row['id']]) === count($words) - 1) {
                        $parents[] = $row['id'];
                    }
                }
            }
            foreach ($parents as $parent) {
                $parents = array_merge($parents, $this->urlParents($parent));
            }
        }

        return array_unique($parents);
    }

    public function getTree($rules = '', $parents = '')
    {
        $tree = [];
        $time = microtime(true);
        $this->pdoTools->addTime('Tree start built');
        if (empty($rules)) {
            $rules = $this->config['rules'];
        }
        if (empty($parents)) {
            $parents = $this->config['parents'];
        }

        if ($this->config['fast']) {
            $tree = $this->fastGetLinks($rules, $parents);
            $tree = $this->prepareLinks($tree);
        } else {
            if ($pre_array = $this->rulesArray($rules, $parents)) {
                $tree = $this->getLinks($pre_array);

                $tree = $this->prepareLinks($tree);

                if ((int)$this->config['countChildren']) {
                    $tree = $this->countChildren($tree);
                }
            }
        }

        if (!empty($tree)) {
            if ((int)$this->config['nesting'] && !(int)$this->config['groupbyrule']) {
                $tree = $this->linksNesting($tree);
            }

            if ((int)$this->config['groupbyrule']) {
                $tree = $this->fastGroupByRule($tree, $rules, $parents);
            }
        }
        $this->pdoTools->addTime('Tree was built (AllTime)', microtime(true) - $time);
        return $tree;
    }

    public function prepareParents($rules, $parents, $rule_alias = 'id', $page_alias = 'page')
    {
        $where = [];
        $rules_in = $rules_out = [];
        $parents_in = $parents_out = [];
        if (!empty($parents)) {
            if (!is_array($parents)) {
                $parents = array_map('trim', explode(',', $parents));
            }
            foreach ($parents as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                if (mb_strpos($v, '-') === 0) {
                    $parents_out[] = abs($v);
                } else {
                    $parents_in[] = abs($v);
                }
            }
        }
        if (!empty($rules)) {
            if (!is_array($rules)) {
                $rules = array_map('trim', explode(',', $rules));
            }
            foreach ($rules as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                if (mb_strpos($v, '-') === 0) {
                    $rules_out[] = abs($v);
                } else {
                    $rules_in[] = abs($v);
                }
            }
        }
        if (count($rules_in)) {
            $where[$rule_alias.':IN'] = $rules_in;
        }
        if (count($rules_out)) {
            $where[$rule_alias.':NOT IN'] = $rules_out;
        }
        if ($rule_alias === 'id') {
            //условия для правил, нужно учесть proMode
            if ($this->config['proMode']) {
                if (count($parents_in)) {
                    $where_pin = [];
                    foreach ($parents_in as $p_in) {
                        $where_pin[] = ('1=1 AND FIND_IN_SET('.$p_in.',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
                    }
                    $where[] = implode(' OR ', $where_pin);
                }
                if (count($parents_out)) {
                    $where_pout = [];
                    foreach ($parents_out as $p_out) {
                        $where_pout[] = ('1=1 AND NOT FIND_IN_SET('.$p_out.',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
                    }
                    $where[] = implode(' AND ', $where_pout);
                }
            } else {
                if (count($parents_in)) {
                    $where[$page_alias.':IN'] = $parents_in;
                }
                if (count($parents_out)) {
                    $where[$page_alias.':NOT IN'] = $parents_out;
                }
            }
        } else {
            if (count($parents_in)) {
                $where[$page_alias.':IN'] = $parents_in;
            }
            if (count($parents_out)) {
                $where[$page_alias.':NOT IN'] = $parents_out;
            }
        }

        return $where;
    }

    public function rulesArray($rules = '', $parents = '')
    {
        $time = microtime(true);
        $pre_array = [];
        $system = ['field_id', 'multi_id', 'priority', 'class', 'key', 'alias', 'slider', 'exact', 'xpdo_package'];

        $q = $this->modx->newQuery('sfRule');
        $qwhere = array_merge($this->prepareParents($rules, $parents), ['active' => 1]);
        $q->where($qwhere);
        $q->leftJoin('sfFieldIds', 'sfFieldIds', 'sfFieldIds.multi_id = sfRule.id');
        $q->innerJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
        $q->select([
            'sfRule.*',
            'sfFieldIds.field_id,sfFieldIds.multi_id,sfFieldIds.priority',
            'sfField.class,sfField.key,sfField.alias,sfField.slider,sfField.exact,sfField.xpdo_package',
        ]);
        $q->sortby('sfFieldIds.priority', 'ASC');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $field = array_intersect_key($row, array_flip($system));
                $field_id = $row['field_id'];
                $row = array_diff_key($row, array_flip($system));
                if (isset($pre_array[$row['id']])) {
                    $pre_array[$row['id']] = array_merge($pre_array[$row['id']], $row);
                } else {
                    $pre_array[$row['id']] = $row;
                }
                $pre_array[$row['id']]['fields'][$field_id] = $field;
            }
        }
        $this->pdoTools->addTime('Rules array complete ', microtime(true) - $time);
        return $pre_array;
    }

    public function prepareHaving($level = 'level')
    {
        $having = '';
        $maxlevel = (int)$this->config['level'];
        $minlevel = (int)$this->config['minlevel'];
        if ($maxlevel) {
            if ($minlevel) {
                $having = $level.' >= '.$minlevel.' AND '.$level.' <= '.$maxlevel;
            } else {
                $having = $level.' <= '.$maxlevel;
            }
        } elseif ($minlevel) {
            $having = $level.' >= '.$minlevel;
        }

        return $having;
    }

    public function fastGetLinks($rules = '', $parents = '')
    {
        $links = [];
        $where = $this->prepareParents($rules, $parents, 'sfUrls.multi_id', 'sfUrls.page_id');
        $select = [
            'sfUrls.*',
            'sfUrlWord.url_id,sfUrlWord.word_id,sfUrlWord.field_id,sfUrlWord.priority',
            'sfDictionary.alias,sfDictionary.input,sfDictionary.value',
            'modResource.context_key'
        ];

        $q = $this->modx->newQuery('sfUrls');
        $where = array_merge([
            'sfUrls.active' => 1,
        ], $where);
        $q->innerJoin('modResource', 'modResource', 'sfUrls.page_id = modResource.id');
        $q->innerJoin('sfRule', 'sfRule', 'sfUrls.multi_id = sfRule.id');
        $q->innerJoin('sfUrlWord', 'sfUrlWord', 'sfUrls.id = sfUrlWord.url_id');
        $q->innerJoin('sfDictionary', 'sfDictionary', 'sfUrlWord.word_id = sfDictionary.id');
        $q->where(['sfRule.active' => 1]);
        if (!empty($this->config['context'])) {
            $q->where(['modResource.context_key:IN' => $this->config['context']]);
        }
        if ((int)$this->config['level'] || (int)$this->config['minlevel']) {
            $q->innerJoin('sfUrlWord', 'sfUrlWordX', 'sfUrlWordX.url_id = sfUrlWord.url_id');
            $select[] = 'COUNT(sfUrlWordX.id) as level';
            $q->having($this->prepareHaving());
        }
        if ($mincount = (int)$this->config['mincount']) {
            $where['sfUrls.total:>='] = $mincount;
        }
        if (!(int)$this->config['showHidden']) {
            $where['sfUrls.menu_on'] = 1;
        }
        if (!empty($this->config['urls'])) {
            if (!is_array($this->config['urls'])) {
                $this->config['urls'] = array_map('trim', explode(',', $this->config['urls']));
            }
            $where['sfUrls.id:IN'] = $this->config['urls'];
        }
        if (!empty($this->config['where'])) {
            $where = array_merge($where, $this->modx->fromJSON($this->config['where']));
        }

        if (!empty($this->config['wordWhere'])) {
            $where = array_merge($this->prepareWordWhere($this->config['wordWhere']), $where);
        }
        $q->select($select);
        $q->where($where);
        $q->groupby('sfUrlWord.id');

        $q = $this->addSort($q);

        $q->limit((int)$this->config['limit'], (int)$this->config['offset']);
        if ($q->prepare()) {
            $this->pdoTools->addTime('SQL '.$q->toSQL());
            if ($q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $url = $row['new_url'] ?: $row['old_url'];
                    $page_url = $this->modx->makeUrl($row['page_id'], $row['context_key'], '',
                        $this->config['scheme']);
                    $u_suffix = $this->config['url_suffix'];

                    $page_url = $this->clearSuffixes($page_url);

                    if ((int)$this->config['site_start'] === (int)$row['page_id']) {
                        if ($this->config['main_alias']) {
                            $qq = $this->modx->newQuery('modResource', ['id' => $row['page_id']]);
                            $qq->select('alias');
                            $malias = $this->modx->getValue($qq->prepare());
                            $url = $page_url.'/'.$malias.$this->config['between_urls'].$url.$u_suffix;
                        } else {
                            $url = $page_url.'/'.$url.$u_suffix;
                        }
                    } else {
                        $url = $page_url.$this->config['between_urls'].$url.$u_suffix;
                    }

                    $name = $row['menutitle'] ?: $row['link'];
                    $row['url'] = $url;
                    $row['name'] = $name;
                    $row['rule_id'] = $row['multi_id'];

                    $word_array = [
                        'multi_id'   => $row['multi_id'],
                        'rule_id'    => $row['multi_id'],
                        'url_id'     => $row['url_id'],
                        'field_id'   => $row['field_id'],
                        'word_id'    => $row['word_id'],
                        'word_input' => $row['input'],
                        'word_alias' => $row['alias'],
                        'word_value' => $row['value'],
                        'priority'   => $row['priority']
                    ];

                    if (isset($links[$row['url_id']])) {
                        $links[$row['url_id']] = array_merge($links[$row['url_id']], $row);
                    } else {
                        $links[$row['url_id']] = $row;
                    }
                    $links[$row['url_id']]['words'][] = $word_array;
                }
            }
        }

        return $links;
    }

    protected function addSort(xPDOQuery $q)
    {
        $sort = [];

        if ((int)$this->config['sortcount']) {
            $sort['sfUrls.count'] = 'DESC';
        }

        if (empty($this->config['sortby'])
            || (is_string($this->config['sortby']) && in_array(strtolower($this->config['sortby']), ['ids', 'urls']))) {
            if (!empty($this->config['urls'])) {
                $urls = $this->config['urls'];
                if (is_array($urls)) {
                    $urls = implode(',', $urls);
                }
                $sort['FIELD(sfUrls.id, '.$urls.')'] = '';
            } else {
                $sort['sfUrls.id'] = !empty($this->config['sortdir']) ? $this->config['sortdir'] : 'ASC';
            }
            $q->groupby('sfUrls.id');
        } elseif (is_string($this->config['sortby'])) {
            if ($this->config['sortby'][0] === '{' || $this->config['sortby'][0] === '[') {
                $sort = json_decode($this->config['sortby'], true);
            } else {
                $sort[$this->config['sortby']] = !empty($this->config['sortdir']) ? $this->config['sortdir'] : 'ASC';
            }
        } elseif(is_array($this->config['sortby'])) {
            $sort = $this->config['sortby'];
        }

        $urlFields = $this->modx->getFields('sfUrls');
        foreach ($sort as $sortBy => $sortDir) {
            if (array_key_exists($sortBy, $urlFields) && mb_strpos($sortBy, '.') === false) {
                $sortBy = 'sfUrls.'.$sortBy;
            }

            $tmp = explode(',', $sortBy);
            array_walk($tmp, function (&$value) {
                if (strpos($value, '`') === false) {
                    $value = preg_replace('#(.*?)\.(.*?)\s#', '`$1`.`$2`', $value);
                }
            });
            $sortBy = implode(',', $tmp);
            if (!in_array(strtoupper($sortDir), ['ASC', 'DESC', ''], true)) {
                $sortDir = 'ASC';
            }

            $q->sortby($sortBy, $sortDir);
            if (mb_strpos($sortBy, '.') !== false) {
                $q->groupby($sortBy);
            }
        }

        return $q;
    }

    public function prepareWordWhere($wordWhere = '')
    {
        $where = [];
        $wordWhere = $this->modx->fromJSON($wordWhere);

        if ((count($wordWhere, COUNT_RECURSIVE) - count($wordWhere)) > 0) {
            //многомерный
            foreach ($wordWhere as $wwhere) {
                foreach ($wwhere as $param => $val) {
                    $param = 'sfDictionary.'.str_replace('word_', '', $param);
                    $inKey = $param.':IN';
                    if (isset($where[$inKey])) {
                        $where[$param.':IN'][] = $val;
                    } else {
                        $where[$param.':IN'] = [$val];
                    }
                }
            }
        } else {
            foreach ($wordWhere as $param => $val) {
                $param = 'sfDictionary.'.str_replace('word_', '', $param);
                $where[$param] = $val;
            }
        }

        return $where;
    }

    public function getLinks($pre_array)
    {
        $time = microtime(true);
        $links = [];
        $q = $this->modx->newQuery('sfUrls');
        $where = [
            'sfUrls.active'      => 1,
            'sfUrls.multi_id:IN' => array_keys($pre_array)
        ];
        if (!(int)$this->config['showHidden']) {
            $where['sfUrls.menu_on'] = 1;
        }
        if ($this->config['where']) {
            $where_add = $this->modx->fromJSON($this->config['where']);
            if (!is_array($where_add)) {
                $where_add = [$where_add];
            }
            $where = array_merge($where, $where_add);
        }
        $q->innerJoin('modResource', 'modResource', 'sfUrls.page_id = modResource.id');
        $q->leftJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
        $q->innerJoin('sfDictionary', 'sfDictionary', 'sfDictionary.id = sfUrlWord.word_id');
        $q->select([
            'sfUrls.*',
            'sfUrlWord.word_id,sfUrlWord.field_id',
            'sfDictionary.alias,sfDictionary.input,sfDictionary.value',
            'modResource.context_key'
        ]);
        if (!empty($this->config['context'])) {
            $q->where(['modResource.context_key:IN' => $this->config['context']]);
        }
        $q->where($where);
        if ($this->config['sortby'] && $this->config['sortdir']) {
            $q->sortby($this->config['sortby'], $this->config['sortdir']);
        }
        $q->limit((int)$this->config['limit'], (int)$this->config['offset']);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!(int)$row['page_id']) {
                    continue;
                }
                $url = $row['new_url'] ?: $row['old_url'];
                $page_url = $this->modx->makeUrl($row['page_id'], $row['context_key'], '',
                    $this->config['scheme']);
                $u_suffix = $this->config['url_suffix'];
                $page_url = $this->clearSuffixes($page_url);
                //                $url = $page_url.$this->config['between_urls'].$url.$u_suffix;
                if ($this->config['site_start'] == $row['page_id']) {
                    if ($this->config['main_alias']) {
                        $qq = $this->modx->newQuery('modResource', ['id' => $row['page_id']]);
                        $qq->select('alias');
                        $malias = $this->modx->getValue($qq->prepare());
                        $url = $page_url.'/'.$malias.$this->config['between_urls'].$url.$u_suffix;
                    } else {
                        $url = $page_url.'/'.$url.$u_suffix;
                    }
                } else {
                    $url = $page_url.$this->config['between_urls'].$url.$u_suffix;
                }

                $name = $row['menutitle'] ?: $row['link'];
                $row['url'] = $url;
                $row['name'] = $name;
                $row['rule_name'] = $pre_array[$row['multi_id']]['name'];
                $row['rule_id'] = $row['multi_id'];
                $row['rank'] = $pre_array[$row['multi_id']]['rank'];
                $row['count_where'] = $pre_array[$row['multi_id']]['count_where'];
                $row['count_parents'] = $pre_array[$row['multi_id']]['count_parents'];
                $word_id = $row['word_id'];
                $word_array = [
                    'multi_id'     => $row['multi_id'],
                    'field_id'     => $row['field_id'],
                    'class'        => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['class'],
                    'key'          => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['key'],
                    'field_alias'  => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['alias'],
                    'priority'     => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['priority'],
                    'slider'       => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['slider'],
                    'xpdo_package' => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['xpdo_package'],
                    'exact'        => $pre_array[$row['multi_id']]['fields'][$row['field_id']]['exact'],
                    'word_id'      => $word_id,
                    'word_input'   => $row['input'],
                    'word_alias'   => $row['alias'],
                    'word_value'   => $row['value'],
                ];
                unset($row['word_id']);
                unset($row['field_id']);
                unset($row['input']);
                unset($row['alias']);
                unset($row['value']);
                if (isset($links[$row['id']])) {
                    $links[$row['id']] = array_merge($links[$row['id']], $row);
                } else {
                    $links[$row['id']] = $row;
                }
                $links[$row['id']]['words'][] = $word_array;
            }
        }
        $this->pdoTools->addTime('Get Links complete', microtime(true) - $time);
        return $links;
    }

    public function prepareLinks($links = [], $onlyWW = 0)
    {
        $tree = [];
        $time = microtime(true);

        $wordWhere = [];
        if (isset($this->config['wordWhere'])) {
            $wordWhere = $this->modx->fromJSON($this->config['wordWhere']);
        }

        foreach ($links as $key => $link) {
            if (isset($link['words'])) {
                $words = $link['words'];
                if (!$onlyWW) {
                    $link['level'] = count($words);
                }
                usort($words, function ($a, $b) {
                    if ($a['priority'] == $b['priority']) {
                        return 0;
                    } else {
                        return ($a['priority'] < $b['priority']) ? -1 : 1;
                    }
                });
                $link['words'] = $words;

                if ($wordWhere) {
                    $found = false;
                    if ((count($wordWhere, COUNT_RECURSIVE) - count($wordWhere)) > 0) {
                        //многомерный массив - несколько условий передано
                        $wheres = count($wordWhere);
                        $find = 0;
                        foreach ($wordWhere as $ww) {
                            foreach ($words as $wf) {
                                if (!array_diff_assoc($ww, $wf)) {
                                    $find++;
                                    break;
                                }
                            }
                            if ($find == $wheres) {
                                $found = true;
                                break;
                            }
                        }
                    } else {
                        foreach ($words as $wf) {
                            if (!array_diff_assoc($wordWhere, $wf)) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }
            } elseif (!$onlyWW) {
                $link['level'] = 0;
            }

            if (!$onlyWW) {
                if ($maxlevel = (int)$this->config['level']) {
                    if (!empty($this->config['minlevel'])) {
                        $minlevel = (int)$this->config['minlevel'];
                        if ($link['level'] <= $maxlevel && $link['level'] >= $minlevel) {
                            $tree[$key] = $link;
                        }
                    } else {
                        if ($link['level'] <= $maxlevel) {
                            $tree[$key] = $link;
                        }
                    }
                } elseif (!empty($this->config['minlevel'])) {
                    $minlevel = (int)$this->config['minlevel'];
                    if ($link['level'] >= $minlevel) {
                        $tree[$key] = $link;
                    }
                } else {
                    $tree[$key] = $link;
                }
            }
        }

        $this->pdoTools->addTime('Sort links complete ', microtime(true) - $time);
        return $tree;
    }

    public function loadHandler()
    {
        if (!is_object($this->countHandler)) {
            require_once('sfcount.class.php');
            $count_class = $this->modx->getOption('seofilter_count_handler_class', null, 'sfCountHandler', true);
            if ($count_class != 'sfCountHandler') {
                $this->loadCustomClasses('count');
            }
            if (!class_exists($count_class)) {
                $count_class = 'sfCountHandler';
            }
            $this->countHandler = new $count_class($this->modx, $this->config);
            if (!($this->countHandler instanceof sfCountHandler)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[SeoFilter] Could not initialize count handler class: "'.$count_class.'"');
                return false;
            }
        }
        return true;
    }

    public function countChildren($links = [])
    {
        $tree = $add_where = [];
        $time = microtime(true);

        $this->loadHandler();

        if (!empty($this->config['count_where'])) {
            $add_where = $this->modx->fromJSON($this->config['count_where']);
        }
        foreach ($links as $link) {
            $link['total'] = $this->countHandler->countByLink($link['id'], $link['page_id'], $add_where, 0);
            if ($mincount = (int)$this->config['mincount']) {
                if ($link['total'] >= $mincount) {
                    $tree[$link['id']] = $link;
                }
            } else {
                $tree[$link['id']] = $link;
            }
        }

        if ((int)$this->config['sortcount']) {
            uasort($tree, [$this, 'sortTotal']);
        }

        $this->pdoTools->addTime('Count Children complete', microtime(true) - $time);

        return $tree;
    }

    public function sortTotal($a = [], $b = [])
    {
        if ($a['total'] == $b['total']) {
            return 0;
        }
        if ($this->config['sortdir'] == 'DESC') {
            return ($a['total'] > $b['total']) ? -1 : 1;
        } else {
            return ($a['total'] < $b['total']) ? -1 : 1;
        }
    }

    /***
     * DEPRECATED METHOD
     */
    public function _countChildren($links = [])
    {
        $tree = [];
        $time = microtime(true);
        foreach ($links as $link) {
            $groupby = [];
            $total = 0;
            $addTVs = $fields_where = $innerJoin = [];
            foreach ($link['words'] as $field) {
                switch ($field['class']) {
                    case 'msProductData':
                    case 'modResource':
                    case 'msProductOption':
                        $fw = $field['class'].'.'.$field['key'];
                        if ($field['class'] === 'msProductData') {
                            $innerJoin['msProductData'] = [
                                'class' => 'msProductData',
                                'on'    => 'msProductData.id = modResource.id'
                            ];
                        }
                        if ($field['class'] === 'msProductOption') {
                            $innerJoin['msProductOption'] = [
                                'class' => 'msProductOption',
                                'on'    => 'msProductOption.product_id = modResource.id'
                            ];
                            //                            $fields_where[$field['class'] . '.key:IN'][] = $field['key'];
                            $fields_where[$field['class'].'.key'] = $field['key'];
                            $fw = $field['class'].'.value';
                        }
                        $values = explode(',', $field['word_input']);
                        if ($field['slider']) {
                            $fields_where[$fw.':>='] = $values[0];
                            if (isset($values[1])) {
                                $fields_where[$fw.':<='] = $values[1];
                            }
                        } elseif ($field['class'] === 'msProductOption') {
                            //                             $tmp = count($fields_where[$fw . ':IN']) ? $fields_where[$fw . ':IN'] : array();
                            //                             $fields_where[$fw . ':IN'] = array_merge($tmp, $values);
                            $fields_where[$fw.':IN'] = $values;
                            //                            $groupby = 'msProductOption.product_id HAVING COUNT(DISTINCT msProductOption.value) = ' . count($fields_where[$fw . ':IN']);
                        } else {
                            $fields_where[$fw.':IN'] = $values;
                        }
                        break;
                    case 'modTemplateVar':
                        $addTVs[] = $field['key'];
                        if (strtolower($field['xpdo_package']) === 'tvsuperselect') {
                            $this->pdoTools->setConfig(['loadModels' => 'tvsuperselect']);
                            $innerJoin['tvssOption'.$field['key']] = [
                                'class' => 'tvssOption',
                                'on'    => 'tvssOption'.$field['key'].'.resource_id = modResource.id'
                            ];
                            $fields_where['tvssOption'.$field['key'].'.value:LIKE'] = '%'.$field['word_input'].'%';
                        } elseif ($field['exact']) {
                            $fields_where['TV'.$field['key'].'.value'] = $field['word_input'];
                        } else {
                            $fields_where['TV'.$field['key'].'.value:LIKE'] = '%'.$field['word_input'].'%';
                        }

                        break;
                    case 'msVendor':
                        $innerJoin['msProductData'] = [
                            'class' => 'msProductData',
                            'on'    => 'msProductData.id = modResource.id'
                        ];
                        $innerJoin['msVendor'] = ['class' => 'msVendor', 'on' => 'msVendor.id = msProductData.vendor'];
                        $fields_where[$field['class'].'.id'] = $field['word_input'];
                        break;
                    default:
                        break;
                }
            }

            $addTVs = implode(',', $addTVs);

            if ($link_where = $this->modx->fromJSON($link['count_where'])) {
                $fields_where = array_merge($fields_where, $link_where);
            }
            if ($where = $this->modx->fromJSON($this->config['count_where'])) {
                $fields_where = array_merge($fields_where, $where);
            }
            foreach ($fields_where as $key => $wheres) {
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
                                //                                $this->modx->log(MODX::LOG_LEVEL_ERROR,'[SeoFilter] The key = '.$key[0].' is wrong for where condition');
                                break;
                        }
                    }
                }
            }

            if (!empty($link['count_parents'])) {
                $parents = $link['count_parents'];
            } elseif ($link['page_id']) {
                $parents = $link['page_id'];
            } else {
                $parents = $this->config['parents'];
            }

            $config = [
                'showLog'    => 0,
                'class'      => 'modResource',
                'parents'    => $parents,
                'includeTVs' => $addTVs,
                'innerJoin'  => $innerJoin,
                'where'      => $fields_where,
                'return'     => 'data',
                'select'     => [
                    'modResource' => 'COUNT(modResource.id) as count'
                ],
            ];
            // if ($groupby) $config['groupby'] = $groupby;
            $this->pdoTools->setConfig($config);

            $run = $this->pdoTools->run();
            if (count($run)) {
                if (isset($run[0]['count'])) {
                    $total = $run[0]['count'];
                }
            }
            $link['total'] = $total;

            if ($mincount = (int)$this->config['mincount']) {
                if ($link['total'] >= $mincount) {
                    $tree[$link['id']] = $link;
                }
            } else {
                $tree[$link['id']] = $link;
            }
        }

        if ((int)$this->config['sortcount']) {
            uasort($tree, [$this, 'sortTotal']);
        }

        $this->pdoTools->addTime('Count Children complete', microtime(true) - $time);

        return $tree;
    }

    /**
     * Method loads custom classes from specified directory
     *
     * @return void
     * @var string $dir Directory for load classes
     */
    public function loadCustomClasses($dir)
    {
        $customPath = $this->config['customPath'];
        $placeholders = [
            'base_path'   => MODX_BASE_PATH,
            'core_path'   => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        ];
        $pl1 = $this->pdoTools->makePlaceholders($placeholders, '', '[[+', ']]', false);
        $pl2 = $this->pdoTools->makePlaceholders($placeholders, '', '[[++', ']]', false);
        $pl3 = $this->pdoTools->makePlaceholders($placeholders, '', '{', '}', false);
        $customPath = str_replace([$pl1['pl'], $pl2['pl'], $pl3['pl']], [$pl1['vl'], $pl2['vl'], $pl3['vl']],
            $customPath);
        if (mb_strpos($customPath, MODX_BASE_PATH) === false && mb_strpos($customPath, MODX_CORE_PATH) === false) {
            $customPath = MODX_BASE_PATH.ltrim($customPath, '/');
        }
        $customPath = rtrim($customPath, '/').'/'.ltrim($dir, '/');
        if (file_exists($customPath) && $files = scandir($customPath)) {
            foreach ($files as $file) {
                if (preg_match('#\.class\.php$#i', $file)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once $customPath.'/'.$file;
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[SeoFilter] Custom path is not exists: \"{$customPath}\"");
        }
    }

    public function recursiveNesting($links = [])
    {
        $tree = [];
        $double = (int)$this->config['double'];
        $roots = [];
        $firstlevel = [];

        if ($links && $levels = $this->recursiveLevels($links)) {
            foreach ($levels as $level => &$linkss) {
                if ($level > 1) {
                    foreach ($linkss as $key => &$link) {
                        if (isset($link['words'])) {
                            $words = $link['words'];
                            $parents = [];
                            $parent_find = 0;
                            if (isset($levels[$level - 1])) {
                                foreach ($levels[$level - 1] as $pid => &$parent_link) {
                                    $find = 0;
                                    $childs = [];
                                    if (isset($parent_link['words'])) {
                                        foreach ($words as $word) {
                                            foreach ($parent_link['words'] as $word_find) {
                                                if (((int)$word_find['field_id'] === (int)$word['field_id'])
                                                    && ((int)$word_find['word_id'] === (int)$word['word_id'])) {
                                                    $find++;
                                                    break;
                                                }
                                            }
                                            if ($find === $level - 1) {
                                                break;
                                            }
                                        }
                                    }
                                    if ($find === $level - 1) {
                                        $parents[] = $parent_link['id'];
                                        $parent_find++;

                                        if (isset($parent_link['childrens'])) {
                                            $parent_link['childrens'][] = $link['id'];
                                        } else {
                                            $parent_link['childrens'] = [$link['id']];
                                        }
                                        if (isset($parent_link['inner'])) {
                                            $parent_link['inner'][] = $link;
                                        } else {
                                            $parent_link['inner'] = [$link];
                                        }

                                        if (!$double) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $tree = $levels[1];
        }

        return $tree;
    }

    public function recursiveLevels($links = [])
    {
        $levels = [];
        foreach ($links as $link) {
            $levels[$link['level']][$link['id']] = $link;
        }
        krsort($levels);
        return $levels;
    }

    public function linksNesting($links = [])
    {
        $time = microtime(true);

        $tree = $this->recursiveNesting($links);

        if ($tree = $this->multiSort($tree)) {
            foreach ($tree as $key => $link) {
                if (isset($link['inner'])) {
                    $tree[$key]['childs'] = count($link['inner']);
                } else {
                    $tree[$key]['childs'] = 0;
                }
            }
        }

        $this->pdoTools->addTime('Nesting links complete', microtime(true) - $time);
        return $tree;
    }

    public function fastGroupByRule($links = [], $rules = '', $parents = '')
    {
        $time = microtime(true);
        $tree = [];
        $groupsort = $this->config['groupsort'];
        $groupdir = $this->config['groupdir'];
        $where = $this->prepareParents($rules, $parents);

        $q = $this->modx->newQuery('sfRule');
        //        $select = array('sfRule.*');
        $select = [$this->modx->getSelectColumns('sfRule', 'sfRule')];
        if ($groupsort === 'level') {
            $q->leftJoin('sfFieldIds', 'sfFieldIds', 'sfFieldIds.multi_id = sfRule.id');
            $select[] = 'COUNT(sfFieldIds.id) as level';
            $q->groupby('sfRule.id');
        }
        if ($groupsort === 'total' || $groupsort === 'children' || $groupsort === 'count') {
            $q->leftJoin('sfUrls', 'sfUrls', 'sfUrls.multi_id = sfRule.id');
            $select[] = 'COUNT(sfUrls.id) as '.$groupsort;
            $q->groupby('sfRule.id');
            $where['sfUrls.id:IN'] = array_keys($links);
        }

        $q->where(array_merge($where, ['active' => 1]));
        if ($groupsort && $groupdir) {
            $q->sortby($groupsort, $groupdir);
        } elseif ($groupsort) {
            $q->sortby($groupsort);
        } elseif ($rules) {
            $q->sortby('FIELD(sfRule.id,'.$rules.')');
        }
        $q->select($select);
        $rules = [];
        if ($q->prepare()) {
            $this->pdoTools->addTime('GROUP SQL:'.$q->toSQL());
            if ($q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    //                $row['links'] = array();
                    $row['level'] = 0;
                    $rules[$row['id']] = $row;
                }
            }
        }

        foreach ($links as $key => $link) {
            $rules[$link['multi_id']]['links'][] = $link;
            $rules[$link['multi_id']]['level'] = $link['level'];
        }

        return $rules;
    }

    public function groupByRule($links = [], $pre_array = [])
    {
        $time = microtime(true);
        $tree = [];
        $groupsort = $this->config['groupsort'];
        $groupdir = $this->config['groupdir'];

        foreach ($links as $key => $link) {
            $pre_array[$link['multi_id']]['links'][] = $link;
            $pre_array[$link['multi_id']]['level'] = count($pre_array[$link['multi_id']]['fields']);
        }

        foreach ($pre_array as $key => $pre) {
            if (isset($pre['links'])) {
                $tree[$key] = $pre;
                $tree[$key]['total'] = count($pre['links']);
            }
        }

        if ($groupsort) {
            uasort($tree, [$this, 'sortingGroups']);
        }
        $this->pdoTools->addTime('Group by Rule complete', microtime(true) - $time);
        return $tree;
    }

    public function multiSort($links = [])
    {
        if ($links) {
            uasort($links, [$this, 'sortingLinks']);
            foreach ($links as $key => $link) {
                if (isset($link['inner'])) {
                    $inner = $this->multiSort($link['inner']);
                    $links[$key]['inner'] = $inner;
                }
            }
        }
        return $links;
    }

    public function sortingGroups($a, $b)
    {
        $groupsort = $this->config['groupsort'];
        $groupdir = $this->config['groupdir'];

        if ($a[$groupsort] === $b[$groupsort]) {
            if ((int)$this->config['userank']) {
                if ($a['rank'] == $b['rank']) {
                    return 0;
                }
                return ($a['rank'] < $b['rank']) ? -1 : 1;
            } else {
                return 0;
            }
        } elseif ($groupdir == 'DESC') {
            return ($a[$groupsort] > $b[$groupsort]) ? -1 : 1;
        } else {
            return ($a[$groupsort] < $b[$groupsort]) ? -1 : 1;
        }
    }

    public function sortingLinks($a, $b)
    {
        $sortby = $this->config['sortby'];
        $sortdir = $this->config['sortdir'];
        $sortcount = (int)$this->config['sortcount'];
        $countChildren = (int)$this->config['countChildren'];
        if ($sortcount && $countChildren) {
            $sortby = 'total';
        }
        if ((int)$this->config['userank']) {
            if ($a['rank'] === $b['rank']) {
                if ($sortby) {
                    if ($a[$sortby] === $b[$sortby]) {
                        return 0;
                    }

                    if (mb_strtolower($sortdir) === 'desc') {
                        return ($a[$sortby] > $b[$sortby]) ? -1 : 1;
                    }

                    return ($a[$sortby] < $b[$sortby]) ? -1 : 1;
                }

                return 0;
            }
            return ($a['rank'] < $b['rank']) ? -1 : 1;
        }

        if ($sortby) {
            if ($a[$sortby] === $b[$sortby]) {
                return 0;
            }

            if (mb_strtolower($sortdir) === 'desc') {
                return ($a[$sortby] > $b[$sortby]) ? -1 : 1;
            }

            return ($a[$sortby] < $b[$sortby]) ? -1 : 1;
        }

        return 0;
    }

    public function linkTemplate($row = [])
    {
        $children = '';
        $row['level'] = $this->level;
        $relative = (int)$this->config['relative'];
        $active = (int)$this->config['hereId'];
        $find = 0;

        if (!empty($row['inner']) && ($this->isHere($row['id']) || empty($this->config['hideSubMenus']))) {
            $idx = $this->idx;
            $this->level++;
            $count = count($row['inner']);
            foreach ($row['inner'] as $v) {
                $v['idx'] = $idx++;
                $v['last'] = (int)$v['idx'] === $count;
                $children .= $this->linkTemplate($v);
            }
            $this->level--;
            $row['children'] = $count;
        } else {
            $row['children'] = isset($row['inner']) ? count($row['inner']) : 0;
        }

        if (!empty($children)) {
            $pls = [
                'wrapper'    => $children,
                'classes'    => ' class="'.$this->config['innerClass'].'"',
                'classnames' => $this->config['innerClass'],
                'classNames' => $this->config['innerClass'],
                'level'      => $this->level,
            ];
            $row['wrapper'] = $this->pdoTools->getChunk($this->config['tplInner'], $pls);
        } else {
            $row['wrapper'] = '';
        }

        $classes = $this->getClasses($row);

        if (!empty($classes)) {
            $row['classNames'] = $row['classnames'] = $classes;
            $row['classes'] = ' class="'.$classes.'"';
        } else {
            $row['classNames'] = $row['classnames'] = $row['classes'] = '';
        }

        $tpl = $this->getTpl($row);

        $row = $this->prepareRow($row, $tpl);

        return $this->pdoTools->getChunk($tpl, $row, (int)$this->config['fastMode']);
    }

    public function prepareRow($row = [], $tpl = '')
    {
        if (!empty($this->config['prepareSnippet'])) {
            $name = trim($this->config['prepareSnippet']);
            array_walk_recursive($row, function (&$value) {
                $value = str_replace(
                    ['[', ']', '{', '}'],
                    ['*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'],
                    $value
                );
            });
            $tmp = $this->modx->runSnippet($name, array_merge($this->config, [
                'row'      => serialize($row),
                'tpl'      => $tpl,
                'sfMenu'   => $this,
                'pdoTools' => $this->pdoTools,
            ]));
            $tmp = (mb_strpos($tmp, '[') === 0 || mb_strpos($tmp, '{') === 0)
                ? json_decode($tmp, true)
                : unserialize($tmp);
            if (!is_array($tmp)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[SeoFilter]: Preparation snippet must return an array, instead of "'.gettype($tmp).'"');
            } else {
                $row = array_merge($row, $tmp);
            }
            array_walk_recursive($row, function (&$value) {
                $value = str_replace(
                    ['*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'],
                    ['[', ']', '{', '}'],
                    $value
                );
            });
        }
        return $row;
    }

    public function getClasses($row = [])
    {
        $classes = [];

        if (!empty($this->config['rowClass'])) {
            $classes[] = $this->config['rowClass'];
        }
        if ((int)$row['idx'] === 1 && !empty($this->config['firstClass'])) {
            $classes[] = $this->config['firstClass'];
        } elseif (!empty($row['last']) && !empty($this->config['lastClass'])) {
            $classes[] = $this->config['lastClass'];
        }
        if (!empty($this->config['levelClass'])) {
            $classes[] = $this->config['levelClass'].$row['level'];
        }
        if ($row['children'] && !empty($this->config['parentClass']) && ($row['level'] < $this->config['level'] || empty($this->config['level']))) {
            $classes[] = $this->config['parentClass'];
        }
        $row_id = $row['id'];
        if ($this->isHere($row_id) && !empty($this->config['hereClass'])) {
            $classes[] = $this->config['hereClass'];
        }
        if ((int)$row_id === (int)$this->config['hereId'] && !empty($this->config['selfClass'])) {
            $classes[] = $this->config['selfClass'];
        }

        return implode(' ', $classes);
    }

    public function isHere($id = 0)
    {
        return in_array($id, $this->parents, true);
    }

    public function getTpl($row = [])
    {
        if ($row['children'] && $row['id'] == $this->config['hereId'] && !empty($this->config['tplParentRowHere'])) {
            $tpl = 'tplParentRowHere';
        } elseif ($row['level'] > 1 && (int)$row['id'] === (int)$this->config['hereId'] && !empty($this->config['tplInnerHere'])) {
            $tpl = 'tplInnerHere';
        } elseif ((int)$row['id'] === (int)$this->config['hereId'] && !empty($this->config['tplHere'])) {
            $tpl = 'tplHere';
        } elseif ($row['children'] && $this->isHere($row['id']) && !empty($this->config['tplParentRowActive'])) {
            $tpl = 'tplParentRowActive';
        } elseif ($row['children'] && !empty($this->pdoTools->config['tplParentRow'])) {
            $tpl = 'tplParentRow';
        } elseif ($row['level'] > 1 && !empty($this->pdoTools->config['tplInnerRow'])) {
            $tpl = 'tplInnerRow';
        } else {
            $tpl = 'tpl';
        }

        return $this->config[$tpl];
    }

    public function getTemplate($tree = [])
    {
        $idx = $this->idx;
        $count = count($tree);
        $output = '';
        foreach ($tree as $row) {
            if (empty($row['id'])) {
                continue;
            }
            $this->level = 1;
            $row['idx'] = $idx++;
            $row['last'] = (int)$row['idx'] === $count;

            $row['children'] = $row['total'];

            $output .= $this->linkTemplate($row);
        }

        if (!empty($output)) {
            $output = $this->pdoTools->parseChunk($this->config['tplOuter'], [
                    'wrapper'    => $output,
                    'classes'    => ' class="'.$this->config['outerClass'].'"',
                    'classnames' => $this->config['outerClass'],
                    'classNames' => $this->config['outerClass'],
                ]
            );
        }

        return $output;
    }

    public function findParents($links = [], $id = 0)
    {
        $parents = [];
        foreach ($links as $row) {
            if ((int)$row['id'] === (int)$id) {
                $parents[] = $row['id'];
                continue;
            }
            if (isset($row['childrens']) && in_array($id, $row['childrens'], true)) {
                $parents[] = $row['id'];
            }
            if (isset($row['inner']) && $inner = $this->findParents($row['inner'], $id)) {
                $parents = array_merge($parents, $inner);
                $parents[] = $row['id'];
            }
        }
        return array_unique($parents);
    }

    public function makeMenu($tree = [], $par = 0)
    {
        $output = '';
        $time = microtime(true);
        $this->pdoTools->addTime('Start template menu');

        $total = count($tree);
        if ((int)$this->config['groupbyrule']) {
            $idx = 0;
            foreach ($tree as $group) {
                if (isset($group['links'])) {
                    if (!empty($this->config['hereId'])) {
                        $parents = $this->findParents($group['links'], (int)$this->config['hereId']);
                        $this->parents = array_merge($this->parents, $parents);
                    }

                    $idx++;
                    if ($total === $idx) {
                        $last = 1;
                    } else {
                        $last = 0;
                    }
                    if ($idx === 1) {
                        $first = 1;
                    } else {
                        $first = 0;
                    }

                    $output .= $this->pdoTools->getChunk($this->config['tplGroup'], array_merge($group, [
                            'idx'      => $idx,
                            'last'     => $last,
                            'first'    => $first,
                            'total'    => count($group['links']),
                            'children' => count($group['links']),
                            'wrapper'  => $this->getTemplate($group['links'])
                        ])
                    );
                }
            }
        } else {
            if (!empty($this->config['hereId'])) {
                $parents = $this->findParents($tree, (int)$this->config['hereId']);
                $this->parents = $parents;
            }
            if ((int)$this->config['relative'] && $this->config['hereId']) {
                $find = 0;
                foreach ($tree as $row) {
                    if (in_array($row['id'], $this->parents, true) && isset($row['inner'])) {
                        $find++;
                        $output = $this->getTemplate($row['inner']);
                    }
                }
            } else {
                $output = $this->getTemplate($tree);
            }
        }
        $this->pdoTools->addTime('End template menu', microtime(true) - $time);
        return $output;
    }

}