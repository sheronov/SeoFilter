<?php

class SeoFilter
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = [];
    /** @var array $initialized */
    public $initialized = [];

    public $pdo;

    /**
     * @param  modX  $modx
     * @param  array  $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('seofilter_core_path', $config,
            $this->modx->getOption('core_path').'components/seofilter/'
        );
        $assetsUrl = $this->modx->getOption('seofilter_assets_url', $config,
            $this->modx->getOption('assets_url').'components/seofilter/'
        );
        $actionUrl = $assetsUrl.'action.php';
        $connectorUrl = $assetsUrl.'connector.php';
        $ajax = $this->modx->getOption('seofilter_ajax', null, 1, true);
        $replace = $this->modx->getOption('seofilter_replace', null, 1, true);
        $separator = $this->modx->getOption('seofilter_separator', null, '-', true);
        $redirect = $this->modx->getOption('seofilter_redirect', null, 1, true);
        $base_get = $this->modx->getOption('seofilter_base_get', null, '', true);
        $site_start = $this->modx->context->getOption('site_start', 1);
        $charset = $this->modx->context->getOption('modx_charset', 'UTF-8');

        $title = $this->modx->getOption('seofilter_title', null, '', true);
        $description = $this->modx->getOption('seofilter_description', null, '', true);
        $introtext = $this->modx->getOption('seofilter_introtext', null, '', true);
        $link = $this->modx->getOption('seofilter_link', null, '', true);
        $h1 = $this->modx->getOption('seofilter_h1', null, '', true);
        $h2 = $this->modx->getOption('seofilter_h2', null, '', true);
        $text = $this->modx->getOption('seofilter_text', null, '', true);
        $content = $this->modx->getOption('seofilter_content', null, '', true);
        $pagetpl = $this->modx->getOption('seofilter_pagetpl', null, '', true);

        $count_childrens = $this->modx->getOption('seofilter_count', null, 0, true);
        $count_choose = $this->modx->getOption('seofilter_choose', null, '', true);
        $count_select = $this->modx->getOption('seofilter_select', null, '', true);
        $prepareSnippet = $this->modx->getOption('seofilter_snippet', null, '', true);

        $replacebefore = $this->modx->getOption('seofilter_replacebefore', null, 1, true);
        $replaceseparator = $this->modx->getOption('seofilter_replaceseparator', null, ' / ', true);
        $jtitle = $this->modx->getOption('seofilter_jtitle', null, '', true);
        $jlink = $this->modx->getOption('seofilter_jlink', null, '', true);
        $jdescription = $this->modx->getOption('seofilter_jdescription', null, '', true);
        $jintrotext = $this->modx->getOption('seofilter_jintrotext', null, '', true);
        $jh1 = $this->modx->getOption('seofilter_jh1', null, '', true);
        $jh2 = $this->modx->getOption('seofilter_jh2', null, '', true);
        $jtext = $this->modx->getOption('seofilter_jtext', null, '', true);
        $jcontent = $this->modx->getOption('seofilter_jcontent', null, '', true);

        $this->pdo = $this->modx->getService('pdoFetch');
        $this->pdo->setConfig(['loadModels' => 'seofilter']);

        $this->config = array_merge([
            'assetsUrl'     => $assetsUrl,
            'cssUrl'        => $assetsUrl.'css/',
            'jsUrl'         => $assetsUrl.'js/',
            'imagesUrl'     => $assetsUrl.'images/',
            'connectorUrl'  => $connectorUrl,
            'actionUrl'     => $actionUrl,
            'json_response' => true,

            'corePath'       => $corePath,
            'modelPath'      => $corePath.'model/',
            'chunksPath'     => $corePath.'elements/chunks/',
            'templatesPath'  => $corePath.'elements/templates/',
            'chunkSuffix'    => '.chunk.tpl',
            'snippetsPath'   => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',

            'params'     => [],
            'ajax'       => $ajax,
            'replace'    => $replace,
            'separator'  => $separator,
            'redirect'   => $redirect,
            'site_start' => $site_start,
            'charset'    => $charset,
            'base_get'   => $base_get,

            'count_childrens' => $count_childrens,
            'count_choose'    => $count_choose,
            'count_select'    => $count_select,
            'prepareSnippet'  => $prepareSnippet,

            'title'       => $title,
            'description' => $description,
            'introtext'   => $introtext,
            'link'        => $link,
            'h1'          => $h1,
            'h2'          => $h2,
            'text'        => $text,
            'content'     => $content,
            'pagetpl'     => $pagetpl,

            'replacebefore'    => $replacebefore,
            'replaceseparator' => $replaceseparator,
            'jtitle'           => $jtitle,
            'jdescription'     => $jdescription,
            'jintrotext'       => $jintrotext,
            'jlink'            => $jlink,
            'jh1'              => $jh1,
            'jh2'              => $jh2,
            'jtext'            => $jtext,
            'jcontent'         => $jcontent,

        ], $config);

        $this->modx->addPackage('seofilter', $this->config['modelPath']);
        $this->modx->lexicon->load('seofilter:default');
    }

    /**
     * Initializes component into different contexts.
     *
     * @param  string  $ctx  The context to load. Defaults to web.
     * @param  array  $scriptProperties  Properties for initialization.
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = [], $loadVideos = false)
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;

        if ($this->config['ajax']) {
            $config = $this->makePlaceholders($this->config);
            if ($js = trim($this->modx->getOption('seofilter_frontend_js', null,
                $this->config['jsUrl'].'web/default.js', true))) {
                //                $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));

                if ($this->config['page']) {
                    $aliases = $this->fieldsAliases($this->config['page'], 1);
                    $this->config['aliases'] = $aliases;
                    $this->config['url'] = $this->modx->makeUrl($this->config['page'], $ctx, '', 'full');

                    $q = $this->modx->newQuery('sfFieldIds');
                    $q->rightJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id');
                    $q->rightJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
                    $q->where(['sfField.slider' => 1, 'sfRule.page' => $this->config['page']]);
                    $this->config['slider'] = $this->modx->getCount('sfField', $q);
                }

                $data = json_encode([
                    'jsUrl'            => $this->config['jsUrl'].'web/',
                    'actionUrl'        => $this->config['actionUrl'],
                    'ctx'              => $ctx,
                    'page'             => $this->config['page'],
                    'params'           => $this->config['params'],
                    'aliases'          => $this->config['aliases'],
                    'slider'           => $this->config['slider'],
                    'separator'        => $this->config['separator'],
                    'redirect'         => $this->config['redirect'],
                    'url'              => $this->config['url'],
                    //'pagetpl' => str_replace(array('[[+', ']]', '{$'), array('{', '}', '{'), $this->pdo->getChunk($this->config['pagetpl'])),
                    'replacebefore'    => $this->config['replacebefore'],
                    'replaceseparator' => $this->config['replaceseparator'],
                    'jtitle'           => $this->config['jtitle'],
                    'jlink'            => $this->config['jlink'],
                    'jdescription'     => $this->config['jdescription'],
                    'jintrotext'       => $this->config['jintrotext'],
                    'jh1'              => $this->config['jh1'],
                    'jh2'              => $this->config['jh2'],
                    'jtext'            => $this->config['jtext'],
                    'jcontent'         => $this->config['jcontent'],
                ], true);

                $this->modx->regClientStartupScript(
                    '<script type="text/javascript">seoFilterConfig = '.$data.';</script>', true
                );
            }
        }
        $this->initialized[$ctx] = true;
        if (!$this->modx->getPlaceholder('sf.videos') && $loadVideos) {
            $this->modx->setPlaceholder('sf.videos', $this->modx->runSnippet('getVideos', ['category' => null]));
        }
        return true;
    }

    public function pageAliases($page_id = 0, $first_params = [])
    {
        $field_id = $rule_id = 0;
        $rule_ids = $aliases = [];
        foreach ($first_params as $alias) {
            $q = $this->modx->newQuery('sfField');
            $q->where(['alias' => $alias]);
            $q->limit(1);
            $q->select('id');
            if ($q->prepare() && $q->stmt->execute()) {
                $field_id = $q->stmt->fetch(PDO::FETCH_COLUMN);
            }

            $q = $this->modx->newQuery('sfFieldIds');
            $q->innerJoin('sfRule', 'sfRule',
                'sfRule.id = sfFieldIds.multi_id AND sfRule.active = 1 AND sfRule.page = '.$page_id);
            $q->where(['sfFieldIds.field_id' => $field_id]);
            $q->select(['sfFieldIds.multi_id', 'sfRule.rank', 'sfRule.base']);
            $q->sortby('sfFieldIds.priority', 'ASC');
            $q->limit(0);
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rule_ids[$row['multi_id']] = ['rank' => $row['rank'], 'base' => $row['base']];
                }
            }

            foreach ($rule_ids as $rule_id => $rarray) {
                $q = $this->modx->newQuery('sfFieldIds');
                $q->innerJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
                $q->where(['sfFieldIds.multi_id' => $rule_id]);
                $q->select(['sfFieldIds.*', 'sfField.alias as alias']);
                $q->sortby('sfFieldIds.priority', 'ASC');
                $q->limit(0);
                if ($q->prepare() && $q->stmt->execute()) {
                    if ($row = $q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                        if ($row[0]['field_id'] == $field_id) {
                            $row['sort'] = $rule_ids[$rule_id]['rank'];
                            $row['base'] = $rule_ids[$rule_id]['base'];
                            $aliases[$rule_id] = $row;
                        }
                    }
                }
            }
        }

        uasort($aliases, function ($a, $b) {
            if ($a['sort'] === $b['sort']) {
                return 0;
            }
            return $a['sort'] > $b['sort'] ? 1 : -1;
        });

        return $aliases;
    }

    public function fieldsAliases($page = 0, $firstAliases = 0, $count = 0)
    {
        $aliases = $rules_id = [];
        $q = $this->modx->newQuery('sfRule');
        $q->limit(0);
        //$q->where(array('1 = 1 AND FIND_IN_SET('.$this->config['page'].',pages)'));
        if ($page) {
            $q->where(['active' => 1, 'page' => $page]);
        }
        $q->select('id,base');
        if ($q->prepare() && $q->stmt->execute()) {
            $fields = $delfields = [];
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $rules_id[$row['id']] = $row['base'];
            }

            foreach ($rules_id as $rule_id => $rule_base) {
                $q = $this->modx->newQuery('sfFieldIds');
                $q->where(['multi_id' => $rule_id]);
                if ($firstAliases) {
                    $q->sortby('priority', 'ASC');
                    $q->limit(1);
                }

                $q->select('field_id');
                $fcount = $this->modx->getCount('sfFieldIds', $q);
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($row,1));
                        if (($fcount < $count) && !$rule_base) {
                            $delfields[] = $row['field_id'];
                        } else {
                            $fields[] = $row['field_id'];
                        }
                    }
                }
                //$this->modx->log(modx::LOG_LEVEL_ERROR, print_r($fields,1));
            }
            $fields = array_diff($fields, $delfields);
            if (count($fields)) {
                $q = $this->modx->newQuery('sfField');
                $q->limit(0);
                $q->where(['id:IN' => array_unique($fields)]);
                $q->select('id,alias');
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $aliases[$row['id']] = $row['alias'];
                    }
                }
            }
        }
        return $aliases;
    }

    /**
     * Method for transform array to placeholders
     *
     * @return array $array Two nested arrays With placeholders and values
     * @var string $prefix
     * @var array $array With keys and values
     */
    public function makePlaceholders(array $array = [], $prefix = '')
    {
        $result = [
            'pl' => [],
            'vl' => [],
        ];
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result, $this->makePlaceholders($v, $prefix.$k.'.'));
            } else {
                $result['pl'][$prefix.$k] = '[[+'.$prefix.$k.']]';
                $result['vl'][$prefix.$k] = $v;
            }
        }

        return $result;
    }

    public function str_replace_once($search, $replace, $text)
    {
        $pos = strpos($text, $search);
        return $pos !== false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    public function getHashUrl($params)
    {
        $urls = [];

        foreach ($params as $param => $value) {
            $urls[] = $param.'='.$value;
        }

        return '?'.implode('&', $urls);
    }

    public function process($action, $data = [])
    {
        $diff = $original_params = [];
        $params = $copyparams = $data['data'];
        $pageId = $data['pageId'];
        $aliases = $data['aliases'];
        $base_get = array_map('trim', explode(',', $this->config['base_get']));

        if ($params) {
            $original_params = array_diff_key($params, array_flip($base_get));
        }

        if (count($params)) {
            $diff = array_flip(array_diff(array_keys($params), $aliases));
        }
        if (count($diff)) {
            foreach ($diff as $dif => $dff) {
                unset($copyparams[$dif]);
            }
            $diff = array_diff_key($params, $copyparams);
            $params = array_intersect_key($params, $copyparams);
        }
        //нахождение первичного параметра

        switch ($action) {
            case 'getmeta':
                $find = 0;
                $rule_count = 0;
                $meta = [];
                if (count($params)) { //тут проверяет, были ли переданы первичные алиасы в правилах. если их нет, то и правил нет)
                    $base_params = $params;
                    $diff_params = array_diff_key($diff, array_flip($base_get));
                    $diff = array_diff_key($diff, $diff_params);

                    foreach ($base_params as $param => $value) {
                        if (count(array_map('trim', explode(',', $value))) > 1) {
                            $q = $this->modx->newQuery('sfDictionary');
                            $q->innerJoin('sfField', 'sfField', 'sfField.id = sfDictionary.field_id');
                            $value_array = array_map('trim', explode(',', $value));
                            $andConditions = [];
                            foreach ($value_array as $key => $val) {
                                $andConditions[] = ['sfDictionary.input' => $val, 'AND:sfField.alias:=' => $param];
                            }
                            $q->orCondition($andConditions);

                            if (!$this->modx->getCount('sfDictionary', $q)) {
                                $find_range = 0;

                                $c = $this->modx->newQuery('sfField');
                                $c->where(['sfField.alias' => $param, 'sfField.slider' => 1]);
                                if ($this->modx->getCount('sfField', $c)) {
                                    $values = array_map('trim', explode(',', $value));
                                    $c->leftJoin('sfDictionary', 'sfDictionary', 'sfDictionary.field_id = sfField.id');
                                    $c->select('sfField.id,sfDictionary.input');
                                    if ($c->prepare() && $c->stmt->execute()) {
                                        foreach ($c->stmt->fetchAll(PDO::FETCH_ASSOC) as $inp) {
                                            $i_values = array_map('trim', explode(',', $inp['input']));
                                            if ($values[0] >= $i_values[0] && $values[1] <= $i_values[1]) {
                                                $find_range = 1;
                                                // unset($base_params[$param]);
                                                //  $base_params[$param] = $inp['input'];
                                                break;
                                            }
                                        }
                                    }
                                }

                                if (!$find_range) {
                                    $diff[$param] = $value;
                                    unset($base_params[$param]);
                                }
                            }
                        }
                    }

                    foreach ($diff_params as $param => $value) {
                        if (count(array_map('trim', explode(',', $value))) > 1) {
                            $q = $this->modx->newQuery('sfDictionary');
                            $q->innerJoin('sfField', 'sfField', 'sfField.id = sfDictionary.field_id');
                            $value_array = array_map('trim', explode(',', $value));
                            $andConditions = [];
                            foreach ($value_array as $key => $val) {
                                $andConditions[] = ['sfDictionary.input' => $val, 'AND:sfField.alias:=' => $param];
                            }
                            $q->orCondition($andConditions);

                            if (!$this->modx->getCount('sfDictionary', $q)) {
                                $find_range = 0;
                                $c = $this->modx->newQuery('sfField');
                                $c->where(['sfField.alias' => $param, 'sfField.slider' => 1]);
                                if ($this->modx->getCount('sfField', $c)) {
                                    $values = array_map('trim', explode(',', $value));
                                    $c->leftJoin('sfDictionary', 'sfDictionary', 'sfDictionary.field_id = sfField.id');
                                    $c->select('sfField.id,sfDictionary.input');
                                    if ($c->prepare() && $c->stmt->execute()) {
                                        foreach ($c->stmt->fetchAll(PDO::FETCH_ASSOC) as $inp) {
                                            $i_values = array_map('trim', explode(',', $inp['input']));
                                            if ($values[0] >= $i_values[0] && $values[1] <= $i_values[1]) {
                                                $find_range = 1;
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (!$find_range) {
                                    $diff[$param] = $value;
                                    unset($diff_params[$param]);
                                }
                            }
                        }
                    }
                    if ($rule_id = $this->findRuleId($pageId, array_merge($base_params, $diff_params), $base_params,
                        $diff_params)) {
                        $rule_fields = $this->ruleFields($rule_id);
                        $diff_fields = array_diff_key(array_merge($base_params, $diff_params),
                            array_flip($rule_fields));
                        $rule_array = $this->pdo->getArray('sfRule', ['id' => $rule_id, 'active' => 1]);
                        $rule_base = $rule_array['base'];
                        if ((count($diff_fields) && $rule_base) || !count($diff_fields)) {
                            $meta = $this->getRuleMeta(array_merge(array_intersect_key(array_merge($base_params,
                                $diff_params), array_flip($rule_fields)), ['servicePage' => $data['data']['page']]),
                                $rule_id, $pageId, 1, 0, $original_params);
                            if (count($meta['diff'])) {
                                $diff = array_merge($diff, $meta['diff']);
                            }
                            $meta['find'] = $find = 1;
                        } else {
                            $meta['find'] = $find = 0;
                            $diff = $data['data'];
                        }
                        $diff = array_merge($diff, $diff_fields);
                    } else {
                        $diff = array_merge($diff, array_merge($base_params, $diff_params));
                    }
                }
                if (!$find) {
                    $meta = $this->getPageMeta($pageId);
                    $meta['find'] = 0;
                }

                if (count($diff)) {
                    if (strpos($meta['url'], '?')) {
                        $meta['url'] = $meta['url'].str_replace('?', '&', $this->getHashUrl($diff));
                    } else {
                        $meta['url'] = $meta['url'].$this->getHashUrl($diff);
                    }
                }

                if (!empty($meta['content'])) {
                    $parser = $this->modx->getParser();
                    $maxIterations = (integer)$this->modx->getOption('parser_max_iterations', null, 10);
                    $parser->processElementTags('', $meta['content'], false, false, '[[', ']]', [], $maxIterations);
                    $parser->processElementTags('', $meta['content'], true, true, '[[', ']]', [], $maxIterations);
                }

                $response = [
                    'success' => true,
                    'data'    => $meta,
                ];
                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            default:
                return $this->error('sf_err_ajax_nf', [], ['action' => $action]);
        }
    }

    public function error($message = '', $data = [], $placeholders = [])
    {
        $response = [
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data'    => $data,
        ];
        return $this->config['json_response']
            ? $this->modx->toJSON($response)
            : $response;
    }

    public function ruleFields($rule_id = 0)
    {
        $fields = [];

        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(['sfFieldIds.multi_id' => $rule_id]);
        $q->leftJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id');
        $q->select('sfField.alias');
        if ($q->prepare() && $q->stmt->execute()) {
            $fields = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $fields;
    }

    public function findRuleId($page_id = 0, $params = [], $first_params = [], $other_params = [])
    {
        if (!count($first_params)) {
            $copyparams = $params;
            $aliases = $this->fieldsAliases($this->config['page'], 1);
            if (count($params)) {
                $other_params = array_flip(array_diff(array_keys($params), $aliases));
            }
            if (count($other_params)) {
                foreach ($other_params as $dif => $dff) {
                    unset($copyparams[$dif]);
                }
                $other_params = array_diff_key($params, $copyparams);
                $first_params = array_intersect_key($params, $copyparams);
            }
        } else {
            $params = array_merge($first_params, $other_params);
        }

        $rule_id = $rid = $rid_count = 0;
        $diff = $find = $rule_aliases = [];
        $params_keys = array_keys(array_merge($first_params, $other_params));

        $page_aliases = $this->pageAliases($page_id, array_keys($first_params));
        if (count($params) > 1) {
            return 28;
        } else {
            if (count($params) == 1) {
                $tmpParams = array_flip($params);
                if (($field = $this->modx->getObject('sfField', ['alias' => $tmpParams[0]])) &&
                    ($slider = $field->get('slider')) &&
                    (count(explode(',', array_values($params)[0])) > 1)) {
                    return 28;
                }
            }
        }

        foreach ($page_aliases as $rule => $ralias) {
            $sort = $ralias['sort'];
            $base = $ralias['base'];
            unset($ralias['sort']);
            unset($ralias['base']);
            foreach ($ralias as $ra) {
                $rule_aliases[$rule]['sort'] = $sort;
                $rule_aliases[$rule]['base'] = $base;
                $rule_aliases[$rule]['fields'][$ra['alias']] = [
                    'where'   => $ra['where'],
                    'compare' => $ra['compare'],
                    'value'   => $ra['value']
                ];
            }
        }

        $count = count($rule_aliases);

        foreach ($rule_aliases as $rule => $rarray) {
            $fields = $rarray['fields'];
            if ($rarray['base']) {
                if (count(array_diff(array_keys($fields), $params_keys))) {
                    continue;
                }
            } else {
                if (count($params_keys) != count(array_keys($fields))) {
                    continue;
                }
                if (count(array_diff(array_keys($fields), $params_keys))) {
                    continue;
                }
            }
            if (count($fields) > $rid_count) {
                $check = 0;
                foreach ($fields as $alias => $row) {
                    if (!in_array($alias, $params_keys)) {
                        continue;
                    }
                    if ($row['where'] && $row['compare']) {
                        $value = $row['value'];
                        $values = explode(',', $value);
                        $get_param = $params[$alias];
                        switch ($row['compare']) { //Обратный механизм поиска
                            case 1:
                                if (in_array($get_param, $values)) {
                                    $check++;
                                }
                                break;
                            case 2:
                                if (!in_array($get_param, $values)) {
                                    $check++;
                                }
                                break;
                            case 3:
                                if ($get_param < $value) {
                                    $check++;
                                }
                                break;
                            case 4:
                                if ($get_param < $value) {
                                    $check++;
                                }
                                break;
                            case 5:
                                if ($get_param > $values[0] && $get_param < $values[1]) {
                                    $check++;
                                }
                                break;
                        }
                    } else {
                        $check++;
                    }
                }
                if ($check == count($fields)) {
                    $rid_count = count($fields);
                    $rule_id = $rule;
                } else {
                    $rid_count = $rule_id = 0;
                }
            }
        }

        return $rule_id ? $rule_id : 28;
    }

    /**
     * DEPRECATED METHOD
     */
    public function findRule($params_keys = [], $page_id = 0)
    {
        //$this->modx->log(modx::LOG_LEVEL_ERROR, 'SEOFILTER: '.print_r($params_keys,1));
        $rule_id = 0;
        $params = $params_keys;
        $find_params = [];
        $q = $this->modx->newQuery('sfFieldIds');
        $q->limit(0);
        $shift = array_shift($params);
        $q->innerJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id AND sfField.alias = "'.$shift.'"');
        if (count($params)) {
            foreach ($params as $key => $alias) {
                $q->innerJoin('sfFieldIds', 'sfFieldIds'.$key, 'sfFieldIds.multi_id = sfFieldIds'.$key.'.multi_id ');
                $q->innerJoin('sfField', 'sfField'.$key,
                    'sfFieldIds'.$key.'.field_id = sfField'.$key.'.id AND sfField'.$key.'.alias = "'.$alias.'"');
            }
        }
        $q->innerJoin('sfRule', 'sfRule',
            'sfFieldIds.multi_id = sfRule.id AND sfRule.active = 1 AND sfRule.page = '.$page_id);
        $q->sortby('sfRule.rank', 'ASC');
        $q->select(['sfFieldIds.*', 'sfField.alias', 'sfRule.base']);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $find_params[] = $row['alias'];
                if ($this->countRuleFields($row['multi_id']) == count($params_keys)) {
                    $rule_id = $row['multi_id'];
                }
            }
        }

        $diff = array_diff($params_keys, $find_params);

        return $rule_id;
    }

    public function countRuleFields($rule_id = 0)
    {
        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(['multi_id' => $rule_id]);
        return $this->modx->getCount('sfFieldIds', $q);
    }

    public function getRuleMeta(
        $params = [],
        $rule_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $original_params = [],
        $url = ''
    ) {
        $seo_system = ['id', 'field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'key'];
        $seo_array = ['title', 'h1', 'h2', 'description', 'introtext', 'text', 'content', 'link'];
        $meta = $fields = $word_array = $aliases = $fields_key = $field_word = [];
        $countFields = $this->countRuleFields($rule_id);
        $diff_params = [];
        $check = 0;
        $fieldWordIds = [];

        foreach ($params as $param => $input) {
            if ($field = $this->modx->getObject('sfField', ['alias' => $param])) {
                $field_id = $field->get('id');
                $slider = $field->get('slider');
                $alias = $field->get('alias');
                $fields[] = $field_id;
                $words = $this->getWordArray($input, $field_id, $slider);

                if ($slider) {
                    $i = 0;
                    $word = $words;
                    foreach (array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                        if ($countFields == 1) {
                            $word_array[$i][$tmp_key] = $tmp_array;
                        }
                        $word_array[$i][str_replace('value', $alias, $tmp_key)] = $tmp_array;
                        $word_array[$i][$alias.'_input'] = $word_array[$i]['input'];
                        $word_array[$i][$alias.'_alias'] = $word_array[$i]['alias'];
                        $word_array[$i]['m_'.$alias] = $word_array[$i]['m_'.$alias.'_i'];
                    }

                    $aliases[$param][$i] = $word['alias'];
                    $fields_key[$i][$alias]['class'] = $field->get('class');
                    $fields_key[$i][$alias]['key'] = $field->get('key');
                    $fields_key[$i][$alias]['exact'] = $field->get('exact');
                    $fields_key[$i][$alias]['slider'] = $field->get('slider');

                    $field_word[$i][$field_id] = $word['id'];
                } else {
                    foreach ($words as $i => $word) {
                        foreach (array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                            if ($countFields == 1) {
                                $word_array[$i][$tmp_key] = $tmp_array;
                            }
                            $word_array[$i][str_replace('value', $alias, $tmp_key)] = $tmp_array;
                            $word_array[$i][$alias.'_input'] = $word_array[$i]['input'];
                            $word_array[$i][$alias.'_alias'] = $word_array[$i]['alias'];
                            $word_array[$i]['m_'.$alias] = $word_array[$i]['m_'.$alias.'_i'];
                        }

                        $aliases[$param][$i] = $word['alias'];
                        $fields_key[$i][$alias]['class'] = $field->get('class');
                        $fields_key[$i][$alias]['key'] = $field->get('key');
                        $fields_key[$i][$alias]['exact'] = $field->get('exact');
                        $fields_key[$i][$alias]['slider'] = $field->get('slider');

                        $field_word[$i][$field_id] = $word['id'];
                    }
                }

                $q = $this->modx->newQuery('sfFieldIds');
                $q->sortby('priority', 'ASC');
                $q->where(['multi_id' => $rule_id, 'field_id' => $field_id]);
                $q->select(['sfFieldIds.*']);
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['where'] && $row['compare'] && $row['value']) {
                            $c = $this->modx->newQuery('sfDictionary');
                            $c->select(['sfDictionary.*']);
                            $c->where(['field_id' => $row['field_id'], 'input' => $input]);
                            $value = $row['value'];
                            $values = explode(',', $value);
                            switch ($row['compare']) { //Обратный механизм поиска
                                case 1:
                                    $c->where(['input:NOT IN' => $values]);
                                    break;
                                case 2:
                                    $c->where(['input:IN' => $values]);
                                    break;
                                case 3:
                                    $c->where(['input:<' => $value]);
                                    break;
                                case 4:
                                    $c->where(['input:>' => $value]);
                                    break;
                                case 5:
                                    $c->where(['input:<' => $values[0], 'AND:input:>' => $values[1]]);
                                    break;
                            }
                            if ($c->prepare() && $c->stmt->execute()) {
                                while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $check++;
                                    $diff_params[$param] = $row['input'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $fieldWordIds = $field_word[0];
        // $this->modx->log(1,'field_word '.print_r($field_word,1));

        if ($check) {
            //когда найдено слово в параметрах, которое подлежит к исключению
            $aliases = array_diff_key($aliases, $diff_params);
            $params = array_diff_key($params, $diff_params);
            $params_keys = array_keys($params);
            // if($rule_id = $this->findRule($params_keys,$page_id))
            if ($rule_id = $this->findRuleId($page_id, $params_keys)) {
                $meta = $this->getRuleMeta($params, $rule_id, $page_id, 1, $new, $original_params);
                $meta['diff'] = $diff_params;
                return $meta;
            }
        }
        $url_array = $this->multiUrl($aliases, $rule_id, $page_id, $ajax, $new, $field_word);

        if ($seo = $this->pdo->getArray('sfRule', ['id' => $rule_id, 'active' => 1])) {
            if ($seo['count_parents']) {
                $parents = $seo['count_parents'];
            } else {
                $parents = $page_id;
            }

            if ($this->config['count_choose'] && $this->config['count_select']) {
                $min_max_array = $this->getRuleCount($original_params, $fields_key, $parents, $seo['count_where'], 1);
                $word_array = array_merge($min_max_array, $word_array);
                $word_array['count'] = $this->getRuleCount($original_params, $fields_key, $parents,
                    $seo['count_where']);
            } elseif ($this->config['count_childrens']) {
                $word_array['count'] = $this->getRuleCount($original_params, $fields_key, $parents,
                    $seo['count_where']);
            }

            $word_array = $this->prepareRow($word_array, $parents, $rule_id);

            if ($url_array['nourl']) {
                // $seo_array = $this->getPageMeta($page_id);
            } else {
                if ($url_array['custom']) {
                    $seo_array = array_intersect_key($url_array, array_flip($seo_array));
                } else {
                    $seo_array = array_intersect_key($seo, array_flip($seo_array));
                }
            }

            foreach ($seo_array as $tag => $text) {
                if ($text) {
                    if ($rule_id == 28) {
                        $result = $this->getSeoTitle($original_params);
                        $meta[$tag] = str_replace('крым', 'Крым', str_replace('{$value}', $result, $text));
                    } elseif ($rule_id == 29 || $rule_id == 47 || $rule_id == 48) {
                        $result = [];
                        foreach ($word_array as $word) {
                            foreach ($word as $key => $value) {
                                if (strpos($key, '_d') !== false && $key != 'value_d' && !empty($value)) {
                                    if (key_exists($key, $result)) {
                                        $result[$key] .= ', '.$value;
                                    } else {
                                        $result[$key] = $value;
                                    }
                                }
                            }
                        }
                        $result = implode(', ', $result);
                        $meta[$tag] = str_replace('крым', 'Крым', str_replace('{$value_r}', $result, $text));
                    } elseif ($rule_id == 44 || $rule_id == 49 || $rule_id == 45 || $rule_id == 46) {
                        $result = [];
                        foreach ($word_array as $word) {
                            foreach ($word as $key => $value) {
                                if (strpos($key, '_d') !== false && $key != 'value_d' && !empty($value)) {
                                    if (key_exists($key, $result)) {
                                        $result[$key] .= ' и '.$value;
                                    } else {
                                        $result[$key] = $value;
                                    }
                                }
                            }
                        }
                        $result = implode(', ', $result);
                        $meta[$tag] = str_replace('крым', 'Крым', str_replace('{$value_r}', $result, $text));
                    } else {
                        if ($word_array) {
                            $tpl = '@INLINE '.$text;
                            $meta[$tag] = $this->pdo->getChunk($tpl, $word_array[0]);
                        } /*else {
                          	return $this->getRuleMeta($params, 28, $page_id, $ajax, $new, $original_params, $url);
                        }*/
                    }
                }
            }
            $meta['rule_id'] = $rule_id;
            $meta['filterTopText'] = (isset($params['servicePage']) && $params['servicePage'] > 1 && !empty($meta['text'])) ? $meta['text'] : $meta['introtext'];
            $this->modx->setPlaceholder('filterTopText', $meta['filterTopText']);
        }
        $diff = [];
        if (count($url_array['diff'])) {
            foreach ($url_array['diff'] as $param => $alias) {
                if ($diff_arr = $this->pdo->getArray('sfDictionary', ['alias' => $alias])) {
                    $diff[$param] = $diff_arr['input'];
                }
            }
            $meta['diff'] = $diff;
        }
        $meta['url'] = $url_array['url'];
        $meta['seo_id'] = $url_array['id'];
        $meta['link'] = $url_array['link'];

        $url = $url
            ?: 'homes/'.(strpos($url_array['url'], '?') === false ? $url_array['url']
                : substr($url_array['url'], 0, strpos($url_array['url'], '?')));
        if (substr($url, 0, 1) == '/') {
            $url = substr($url, 1);
        }
        $master = $this->modx->getObject('msProduct',
            [['parent' => 308], ['alias:LIKE' => $url, 'OR:alias:LIKE' => '/'.$url]]);
        if (is_object($master)) {
            $masterId = $master->get('id');
            $q = $this->modx->query('SELECT `Slave`.`alias`, `Link`.`title` FROM `modx_ms2_product_links` `Link` LEFT JOIN `modx_site_content` `Slave` ON `Slave`.`id`=`Link`.`slave` WHERE `Link`.`link`=2 AND `Slave`.`deleted`=0 AND `Slave`.`parent`=308 AND `Link`.`master`='.$masterId);
            $linksData = $q->fetchAll(PDO::FETCH_ASSOC);

            foreach ($linksData as $linkData) {
                $meta['links'] .= '<a href="/'.$linkData['alias'].'" class="tag-text">'.$linkData['title'].'</a>';
            }
        }
        $this->modx->setPlaceholder('sf.links', $meta['links']);

        // TODO: это не будет работать без названий ссылок
        // if ($nested = $this->findNestedCrumbs($fieldWordIds, $page_id, $meta['seo_id'])) {
        //     $meta['nested'] = [];
        //     foreach ($nested as $nestedLink) {
        //         $nestedLink['url'] = $nestedLink['new_url'] ?: $nestedLink['old_url'];
        //         $nestedLink['sflink'] = $nestedLink['link'];
        //         $nestedLink['sfurl'] = $nestedLink['url'].$this->config['url_suffix'];
        //         $meta['nested'][] = $nestedLink;
        //     }
        //     $meta['nested'] = $this->config['json_response']
        //         ? $meta['nested']
        //         : $this->modx->toJSON($meta['nested']);
        // }

        return $meta;
    }

    protected function findNestedCrumbs($field_word = [], $page_id = 0, $url_id = 0)
    {
        $links = [];
        $count = count($field_word);
        $for_find = [];
        foreach ($field_word as $field_id => $word_id) {
            $for_find[] = [
                'field_id' => $field_id,
                'word_id'  => $word_id
            ];
        }
        $for_find = array_reverse($for_find, 0);

        //$this->modx->log(1,print_r($for_find,1));
        for ($i = 0; $i < $count - 1; $i++) {
            $link = $this->recursiveLinkFind($for_find, $page_id, 0, $url_id);
            if ($link['find']) {
                $for_find = array_values($link['field_word']); //сброс ключей
                $links[] = $link['link'];
            } else {
                array_shift($for_find);
            }
        }

        $links = array_reverse($links);
        //$links[] = $this->findSeoLink($for_find,$page_id);

        return $links;
    }

    public function recursiveLinkFind($field_word = [], $page_id = 0, $skip = 0, $url_id = 0)
    {
        $result = [
            'find' => false,
            'link' => []
        ];
        $copy = $field_word;
        foreach ($copy as $index => $row) {
            if ($index == $skip) {
                unset($copy[$index]);
            }
        }
        if ($link = $this->findSeoLink($copy, $page_id, $url_id)) {
            //ссылка найдена
            $result['link'] = $link;
            $result['find'] = true;
            $result['field_word'] = $copy;
            $result['skip'] = $skip;
        } elseif ($skip < count($field_word)) {
            $skip++;
            $result = $this->recursiveLinkFind($field_word, $page_id, $skip, $url_id);
        }

        return $result;
    }

    public function findSeoLink($field_word = [], $page_id = 0, $url_id = 0)
    {
        $link = [];
        $q = $this->modx->newQuery('sfUrls');
        $q->where(['page_id' => $page_id]);
        if ($url_id) {
            $q->where(['id:!=' => $url_id]);
        }
        $q->groupby('sfUrls.id');
        $q->select($this->modx->getSelectColumns('sfUrls', 'sfUrls', ''));
        $index = 0;
        foreach ($field_word as $i => $fw) {
            $q->rightJoin('sfUrlWord', "sfUrlWord{$index}", "sfUrls.id = sfUrlWord{$index}.url_id");
            $q->where([
                "sfUrlWord{$index}.word_id"  => $fw['word_id'],
                "sfUrlWord{$index}.field_id" => $fw['field_id']
            ]);
            if (!$index) {
                $q->innerJoin('sfUrlWord', 'sfUrlCount', "sfUrlWord{$index}.url_id = sfUrlCount.url_id");
                $q->select("COUNT(sfUrlWord{$index}.id) as levels");
            }
            $index++;
            //$q->sortby('sfUrlWord.priority','ASC');
        }
        if (count($field_word)) {
            $q->having('levels = '.count($field_word));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['field_word'] = $field_word;
                $link = $row;
            }
        }

        return $link;
    }

    public function getRuleCount($params = [], $fields_key = [], $parents, $count_where = [], $min_max = 0)
    {
        $count = 0;
        $innerJoin = [];
        $addTVs = [];
        $fields_where = [];
        $params_keys = array_diff(array_keys($params), array_keys($fields_key));

        foreach ($params_keys as $param) {
            if ($field = $this->pdo->getArray('sfField', ['alias' => $param])) {
                $alias = $field['alias'];
                $fields_key[$alias]['class'] = $field['class'];
                $fields_key[$alias]['key'] = $field['key'];
                $fields_key[$alias]['exact'] = $field['exact'];
                $fields_key[$alias]['slider'] = $field['slider'];
            }
        }

        if (count(array_diff(array_keys($params), array_keys($fields_key)))) {
            $this->modx->log(modx::LOG_LEVEL_ERROR,
                "[SeoFilter] don't known this fields. Please add this fields to the first tab in component (Fields)".print_r(array_diff(array_keys($params),
                    array_keys($fields_key)), 1));
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
                        $innerJoin['msProductOption'] = [
                            'class' => 'msProductOption',
                            'on'    => 'msProductOption.product_id = modResource.id'
                        ];
                        $fields_where[$field['class'].'.key'] = $field['key'];
                        $fw = $field['class'].'.value';
                    }
                    if ($field['slider']) {
                        $slider = explode(',', $params[$field_alias]);
                        $fields_where[$fw.':>='] = $slider[0];
                        if ($slider[1]) {
                            $fields_where[$fw.':<='] = $slider[1];
                        }
                    } else {
                        $values = explode(',', $params[$field_alias]);
                        $fields_where[$fw.':IN'] = $values;
                    }
                    break;
                case 'modTemplateVar':
                    $addTVs[] = $field['key'];
                    if ($field['exact']) {
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

        //$this->modx->log(modx::LOG_LEVEL_ERROR,print_r($where,1));

        if ($min_max) {
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
                        $choose_alias = explode('=', $choose)[1];
                        $choose = explode('=', $choose)[0];
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
                        $choose_alias = explode('=', $choose)[1];
                        $choose = explode('=', $choose)[0];
                    } else {
                        $choose_alias = $choose;
                    }
                    $sortby = 'modResource.'.$choose_alias;
                }

                foreach (['max' => 'DESC', 'min' => 'ASC'] as $m => $sort) {
                    if ($m == 'min') {
                        $where = array_merge($where, [$sortby.':>' => 0]);
                    }
                    $this->pdo->setConfig([
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

                    if ($run = $this->pdo->run()) {
                        foreach ($run[0] as $key => $value) {
                            $min_max_array[$m.'_'.$choose_alias.'_'.$key] = $value;
                        }
                    }
                }
            }

            return $min_max_array;
        } else {
            $this->pdo->setConfig([
                'showLog'    => 0,
                'class'      => 'modResource',
                'parents'    => $parents,
                'includeTVs' => $addTVs,
                'innerJoin'  => $innerJoin,
                'where'      => $where,
                'return'     => 'data',
                'select'     => [
                    'modResource' => 'COUNT(modResource.id) as count'
                ]
            ]);

            $run = $this->pdo->run();
            if (count($run)) {
                $count = $run[0]['count'];
            }
            return $count;
        }
    }

    public function getWordArray($input = '', $field_id = 0, $slider = 0)
    {
        $word = [];
        if ($slider) {
            $q = $this->modx->newQuery('sfDictionary');
            $values = array_map('trim', explode(',', $input));
            $q->where(['field_id' => $field_id]);
            $q->sortby('input', 'ASC');
            $q->limit(0);
            if ($this->modx->getCount('sfDictionary', $q)) {
                $q->select(['sfDictionary.*']);
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $i_values = array_map('trim', explode(',', $row['input']));
                        if ($field_id === 25) {
                            if ($values[0] == $i_values[0] && $values[1] == $i_values[1]) {
                                $word = $row;
                                break;
                            }
                        } else {
                            if ($values[0] >= $i_values[0] && $values[1] <= $i_values[1]) {
                                $word = $row;
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            foreach (explode(',', $input) as $val) {
                $q = $this->modx->newQuery('sfDictionary');
                $q->limit(1);
                $q->where(['field_id' => $field_id, 'input' => $val]);
                if ($this->modx->getCount('sfDictionary', $q)) {
                    $q->select(['sfDictionary.*']);
                    if ($q->prepare() && $q->stmt->execute()) {
                        $word[] = $q->stmt->fetch(PDO::FETCH_ASSOC);
                    }
                } else {
                    if ($field = $this->modx->getObject('sfField', $field_id)) {
                        if ($input && $value = $field->getValueByInput($input)) {
                            $processorProps = [
                                'class'    => $field->get('class'),
                                'key'      => $field->get('key'),
                                'field_id' => $field->get('id'),
                                'value'    => $value,
                                'input'    => $input,
                            ];
                            $otherProps = ['processors_path' => $this->config['corePath'].'processors/'];
                            $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps,
                                $otherProps);
                            if ($response->isError()) {
                                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.print_r($response->response, 1));
                            } else {
                                $word[] = $response->response['object'];
                            }
                        }
                    }
                }
            }
        }

        return $word;
    }

    public function getPageMeta($page_id)
    {
        $system = [
            'title'       => $this->config['title'],
            'description' => $this->config['description'],
            'introtext'   => $this->config['introtext'],
            'h1'          => $this->config['h1'],
            'h2'          => $this->config['h2'],
            'text'        => $this->config['text'],
            'content'     => $this->config['content'],
            'link'        => $this->config['link'],
        ];
        $meta = [];

        if ($page = $this->modx->getObject('modResource', $page_id)) {
            $page_keys = array_keys($page->toArray());

            $variables = $this->prepareRow($meta, $page_id);

            $array_diff = array_diff($system, $page_keys);
            foreach ($array_diff as $tag => $tvname) {
                if ($tvvalue = $page->getTVValue($tvname)) {
                    $tpl = '@INLINE '.$tvvalue;
                    $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
                    unset($system[$tag]);
                }
            }
            foreach ($system as $tag => $tagname) {
                if (strpos($tagname, '[') || strpos($tagname, '{')) {
                    $tpl = '@INLINE '.$tagname;
                } else {
                    $tpl = '@INLINE '.$page->get($tagname);
                }
                $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
            }
        }

        return $meta;
    }

    public function prepareRow($row = [], $page_id = 0, $rule_id = 0)
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
                'row'       => serialize($row),
                'input'     => $row['input'] ? $row['input'] : 0,
                'seoFilter' => $this,
                'pdoTools'  => $this->pdo,
                'pdoFetch'  => $this->pdo,
                'rule_id'   => $rule_id,
                'page_id'   => $page_id,
            ]));
            $tmp = ($tmp[0] == '[' || $tmp[0] == '{')
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

    public function findMultiId($url = '')
    {
        if ($url_array = $this->pdo->getArray('sfUrls', ['old_url' => $url, 'OR:new_url:=' => $url])) {
            return $url_array['multi_id'];
        } else {
            return 0;
        }
    }

    public function findUrlArray($url = '', $page = 0)
    {
        if ($url_array = $this->pdo->getArray('sfUrls',
            ['page_id' => $page, 'old_url' => $url, 'OR:new_url:=' => $url])) {
            return $url_array;
        } else {
            return [];
        }
    }

    public function newUrl($old_url = '', $multi_id = 0, $page_id = 0, $ajax = 0, $new = 0, $field_word = [])
    {
        $url = [];
        if ($ajax) {
            $new = $ajax;
        }
        $processorProps = [
            'old_url'    => $old_url,
            'multi_id'   => $multi_id,
            'page_id'    => $page_id,
            'ajax'       => $ajax,
            'count'      => $new,
            'field_word' => $field_word
        ];
        $otherProps = ['processors_path' => $this->config['corePath'].'processors/'];
        $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]: '.print_r($response->getMessage(), 1));
        } else {
            $url = $response->response['object'];
        }

        return $url;
    }

    public function addUrlCount($url_id = 0, $filter = 0)
    {
        if ($url = $this->modx->getObject('sfUrls', ['active' => 1, 'id' => $url_id])) {
            $count = $url->get('count') + 1;
            $url->set('count', $count);
            if ($filter) {
                $ajax = $url->get('ajax') + 1;
                $url->set('ajax', $ajax);
            }
            $url->save();
        }
    }

    /**
     * DEPRECATED METHOD
     */
    public function fieldUrl($value = '', $field = [])
    {
        if (!$alias = $field['alias']) {
            $alias = $field['key'];
        }
        if ($field['hideparam']) {
            $url = $value;
        } else {
            if ($field['valuefirst']) {
                $url = $value.$this->config['separator'].$alias;
            } else {
                $url = $alias.$this->config['separator'].$value;
            }
        }
        return $url;
    }

    public function multiUrl($aliases = [], $multi_id = 0, $page_id = 0, $ajax = 0, $new = 0, $field_word = [])
    {
        $url = [];
        if ($multi_id) {
            if ($rule = $this->pdo->getArray('sfRule', ['id' => $multi_id, 'active' => 1])) {
                $tmp_link = $rule['url'];
                $tmp_link = array_flip(explode('/', $tmp_link));
                foreach ($tmp_link as $key => $value) {
                    $tmp_link[$key] = $key;
                }
                $url_link = [];
                $j = 0;
                $k = 0;

                foreach ($aliases as $key => $alias) {
                    foreach ($alias as $i => $value) {
                        if ($j > 0) {
                            $url_link[$k] = $url_link[$k].'-i-';
                            $j = 0;
                        }

                        if (key_exists($key.'-{$'.$key.'}', $tmp_link)) {
                            $url_link[$k] = $url_link[$k].str_replace('{$'.$key.'}', $value,
                                    $tmp_link[$key.'-{$'.$key.'}']);
                        } else {
                            if (key_exists('{$'.$key.'}-'.$key, $tmp_link)) {
                                $url_link[$k] = $url_link[$k].str_replace('{$'.$key.'}', $value,
                                        $tmp_link['{$'.$key.'}-'.$key]);
                            } else {
                                if (key_exists('{$'.$key.'}', $tmp_link)) {
                                    $url_link[$k] = $url_link[$k].str_replace('{$'.$key.'}', $value,
                                            $tmp_link['{$'.$key.'}']);
                                }
                            }
                        }
                        $j++;
                    }
                    $j = 0;
                    $k++;
                }
                $url_link = implode('/', $url_link);

                if ($url_array = $this->pdo->getArray('sfUrls', ['page_id' => $page_id, 'old_url' => $url_link])) {
                    if ($url_array['active']) {
                        $url = $url_array;
                        if ($url_array['new_url']) {
                            $url['url'] = $url_array['new_url'];
                        } else {
                            $url['url'] = $url_link;
                        }
                        $this->addUrlCount($url_array['id'], $ajax);
                    } else {
                        $url['custom'] = $url_array['custom'];
                        $url['id'] = $url_array['id'];
                        $url['nourl'] = 1;
                    }
                    $url['link'] = $url_array['link'];
                } else {
                    $field_words = [];
                    foreach ($field_word as $i => $word) {
                        foreach ($word as $field_id => $word_id) {
                            $field_words[] = ['field_id' => $field_id, 'word_id' => $word_id];
                        }
                    }
                    $url = $this->newUrl($url_link, $multi_id, $page_id, $ajax, $new, $field_words);
                    $url['url'] = $url_link;
                    //$this->modx->log(modx::LOG_LEVEL_ERROR, 'SeoFilter: сработало условие и создан УРЛ '.$url_link);
                }
            }
        } else {
            $total = 1;
            $count = count($aliases);
            foreach ($aliases as $param => $alias) {
                if ($total == 1) {
                    $url['url'] .= '?';
                }
                $url['url'] .= $param.'='.$alias;
                if ($total != $count) {
                    $url['url'] .= '&';
                }
                $total++;
            }
        }

        return $url;
    }

    protected function getSeoTitle($params)
    {
        $result = [];

        $request = array_map(function ($item) {
            return explode(',', $item);
        }, $params);
        $houses = ' домов';
        if (isset($request['purpose'])) {
            if (count($request['purpose']) > 1) {
                $houses = [];
                foreach ($request['purpose'] as $purpose) {
                    $houses[] = $this->getPurpose($purpose);
                }
                $houses = implode(',', $houses);
            } else {
                $houses = $this->getPurpose($request['purpose'][0]);
            }
        }
        if (isset($request['form'])) {
            if (count($request['form']) > 1) {
                $forms = [];
                foreach ($request['form'] as $form) {
                    $forms[] = $this->getForm($form);
                }
                $forms = implode(',', $forms);
            } else {
                $forms = $this->getForm($request['form'][0]);
            }

            $result[] = $forms;
        }

        if (isset($request['steni']) || isset($request['etagi'])) {
            if (isset($request['steni']) && isset($request['etagi'])) {
                if (count($request['steni']) > 1) {
                    $steni = [];
                    foreach ($request['steni'] as $stena) {
                        if ($stena == 'Каркас') {
                            $steni[] = 'каркасных';
                        } elseif ($stena == 'Бревно') {
                            $steni[] = 'из бревна';
                        } else {
                            $steni[] = 'из '.mb_strtolower($stena).'а';
                        }
                    }

                    $steni = implode(', ', $steni);
                    if ($request['etagi'][0] < $request['etagi'][1]) {
                        $result[] = $houses.' '.$steni;
                        $result[] = ' на '.$request['etagi'][0].'-'.$request['etagi'][1].' этажа';
                    } else {
                        switch ($request['etagi'][0]) {
                            case '1':
                                $result[] = " одноэтажных $houses ".$steni;
                                break;
                            case '2':
                                $result[] = " двухэтажных $houses ".$steni;
                                break;
                            case '3':
                                $result[] = " трехэтажных $houses ".$steni;
                                break;
                            case '4':
                                $result[] = " четырехэтажных $houses ".$steni;
                                break;
                            default:
                                $result[] = '';
                        }
                    }
                } else {
                    if ($request['etagi'][0] < $request['etagi'][1]) {
                        if ($request['steni'][0] == 'Каркас') {
                            $result[] = ' каркасных'.$houses;
                        } elseif ($request['steni'][0] == 'Бревно') {
                            $result[] = $houses.' из бревна';
                        } else {
                            $result[] = $houses.' из '.mb_strtolower($request['steni'][0]).'а';
                        }
                        $result[] = ' на '.$request['etagi'][0].'-'.$request['etagi'][1].' этажа';
                    } else {
                        switch ($request['etagi'][0]) {
                            case '1':
                                $result[] = ' одноэтажных ';
                                break;
                            case '2':
                                $result[] = ' двухэтажных ';
                                break;
                            case '3':
                                $result[] = ' трехэтажных ';
                                break;
                            case '4':
                                $result[] = ' четырехэтажных ';
                                break;
                            default:
                                $result[] = '';
                        }
                        if ($request['steni'][0] == 'Каркас') {
                            $result[] = ' каркасных'.$houses;
                        } elseif ($request['steni'][0] == 'Бревно') {
                            $result[] = $houses.' из бревна';
                        } else {
                            $result[] = $houses.' из '.mb_strtolower($request['steni'][0]).'а';
                        }
                    }
                }
            } elseif (isset($request['steni'])) {
                if (count($request['steni']) > 1) {
                    $steni = [];
                    foreach ($request['steni'] as $stena) {
                        if ($stena == 'Каркас') {
                            $steni[] = 'каркасных';
                        } elseif ($stena == 'Бревно') {
                            $steni[] = 'из бревна';
                        } else {
                            $steni[] = 'из '.mb_strtolower($stena).'а';
                        }
                    }
                    $steni = implode(', ', $steni);
                    $result[] = $houses.' '.$steni;
                } else {
                    if ($request['steni'][0] == 'Каркас') {
                        $result[] = ' каркасных'.$houses;
                    } elseif ($request['steni'][0] == 'Бревно') {
                        $result[] = $houses.' из бревна';
                    } else {
                        $result[] = $houses.' из '.mb_strtolower($request['steni'][0]).'а';
                    }
                }
            } elseif (isset($request['etagi'])) {
                if ($request['etagi'][0] < $request['etagi'][1]) {
                    $result[] = $houses.' на '.$request['etagi'][0].'-'.$request['etagi'][1].' этажа';
                } else {
                    switch ($request['etagi'][0]) {
                        case '1':
                            $result[] = ' одноэтажных'.$houses;
                            break;
                        case '2':
                            $result[] = ' двухэтажных'.$houses;
                            break;
                        case '3':
                            $result[] = ' трехэтажных'.$houses;
                            break;
                        case '4':
                            $result[] = ' четырехэтажных'.$houses;
                            break;
                        default:
                            $result[] = '';
                    }
                }
            }
        } else {
            $result[] = $houses;
        }

        if (isset($request['parent'])) {
            $this->getByParent($result, $request['parent']);
        }

        $options = [];

        if (isset($request['dopetagi'])) {
            if (count($request['dopetagi']) > 1) {
                $options[] = ' '.mb_strtolower($request['dopetagi'][0]).' и '.mb_strtolower($request['dopetagi'][1]);
            } else {
                $options[] = ' '.mb_strtolower($request['dopetagi'][0]);
            }
        }

        if (isset($request['spalen'])) {
            if ($request['spalen'][0] < $request['spalen'][1]) {
                if ($request['spalen'][1] == '1' || $request['spalen'][1] == '21') {
                    $options[] = ' на '.$request['spalen'][0].'-'.$request['spalen'][1].' спальню';
                } elseif ($request['spalen'][1] == '2' || $request['spalen'][1] == '3' || $request['spalen'][1] == '4'
                    || $request['spalen'][1] == '22' || $request['spalen'][1] == '23' || $request['spalen'][1] == '24') {
                    $options[] = ' на '.$request['spalen'][0].'-'.$request['spalen'][1].' спальни';
                } else {
                    $options[] = ' на '.$request['spalen'][0].'-'.$request['spalen'][1].' спален';
                }
            } else {
                if ($request['spalen'][0] == '1' || $request['spalen'][0] == '21') {
                    $options[] = ' на '.$request['spalen'][0].' спальню';
                } elseif ($request['spalen'][0] == '2' || $request['spalen'][0] == '3' || $request['spalen'][0] == '4'
                    || $request['spalen'][0] == '22' || $request['spalen'][0] == '23' || $request['spalen'][0] == '24') {
                    $options[] = ' на '.$request['spalen'][0].' спальни';
                } else {
                    $options[] = ' на '.$request['spalen'][0].' спален';
                }
            }
        }

        if (isset($request['obschayaploschad'])) {
            if ($request['obschayaploschad'][1] == '99999') {
                $options[] = ' площадью от '.$request['obschayaploschad'][0].' м&sup2';
            } elseif ($request['obschayaploschad'][0] < $request['obschayaploschad'][1]) {
                $options[] = ' площадью '.$request['obschayaploschad'][0].'-'.$request['obschayaploschad'][1].' м&sup2';
            } else {
                $options[] = ' площадью '.$request['obschayaploschad'][0].' м&sup2';
            }
        }

        if (isset($request['dlina'])) {
            if ($request['dlina'][0] < $request['dlina'][1]) {
                $options[] = ' длиной '.$request['dlina'][0].'-'.$request['dlina'][1].' м';
            } else {
                $options[] = ' длиной '.$request['dlina'][0].' м';
            }
        }

        if (isset($request['shirina'])) {
            if ($request['shirina'][0] < $request['shirina'][1]) {
                $options[] = ' шириной '.$request['shirina'][0].'-'.$request['shirina'][1].' м';
            } else {
                $options[] = ' шириной '.$request['shirina'][0].' м';
            }
        }

        if (isset($request['stoimost'])) {
            if ($request['stoimost'][0] < $request['stoimost'][1]) {
                $options[] = ' стоимостью постройки от&nbsp;'.round($request['stoimost'][0] / 1000000, 1)
                    .' до&nbsp;'.round($request['stoimost'][1] / 1000000, 1).'&nbsp;млн.&nbsp;руб.';
            } else {
                $options[] = ' стоимостью постройки '.round($request['stoimost'][0] / 1000000,
                        1).'&nbsp;млн.&nbsp;руб.';
            }
        }

        if (isset($request['doppom'])) {
            if (count($request['doppom']) > 1) {
                foreach ($request['doppom'] as $pom) {
                    $options[] = ' '.mb_strtolower($pom);
                }
            } else {
                $options[] = ' '.mb_strtolower($request['doppom'][0]);
            }
        }

        if (isset($request['archelem'])) {
            if (count($request['archelem']) > 1) {
                foreach ($request['archelem'] as $pom) {
                    $options[] = ' '.mb_strtolower($pom);
                }
            } else {
                $options[] = ' '.mb_strtolower($request['archelem'][0]);
            }
        }

        if (isset($request['kolichestvosemej'])) {
            $options[] = $this->getSemya($request['kolichestvosemej'][0]);
        }

        if (isset($request['krisha'])) {
            $options[] = $this->getKrisha($request['krisha'][0]);
        }

        if (isset($request['tipstroeniya'])) {
            if (count($request['tipstroeniya']) > 1) {
                foreach ($request['tipstroeniya'] as $tip) {
                    if ($tip === 'Для Крыма') {
                        $options[] = ' для Крыма';
                    } else {
                        $options[] = ' '.mb_strtolower($tip);
                    }
                }
            } else {
                $options[] = ' '.mb_strtolower($request['tipstroeniya'][0]);
            }
        }

        $options = implode(',', $options);

        $result[] = $options;

        return implode($result);
    }

    private function getForm($form)
    {
        return ' '.mb_substr(mb_strtolower($form), 0, -1).'х';
    }

    private function getByParent(&$result, $parents)
    {
        $tmp = [];
        foreach ($parents as $parent) {
            if (in_array($parent,
                [2151, 2152, 2153, 2154, 2193, 180, 181, 183, 184, 185, 186, 188, 190, 192, 193, 194, 379])) {
                $parentTitle = explode('x', $this->modx->getObject('modResource', ['id' => $parent])->pagetitle);
                $tmp[] = ' '.$parentTitle[0].' на '.$parentTitle[1].' метров';
            } else {
                $obj = $this->modx->getObject('modResource', ['id' => $parent]);
                if ($obj->description === '0' || $obj->description === '1') {
                    array_unshift($result, ' '.$obj->longtitle);
                } else {
                    $tmp[] = ' '.$obj->longtitle;
                }
            }
        }

        if ($tmp) {
            $result[] = implode(',', $tmp);
        }
    }

    private function getPurpose($purpose)
    {
        switch ($purpose) {
            case 'Дом / коттедж':
                return ' домов и коттеджей';
            case 'Усадьбы / особняки':
                return ' усадеб и особняков';
            case 'Гостевые дома':
                return ' гостевых домов';
            case 'Таунхаусы':
                return ' таунхаусов';
            case 'Бани':
                return ' бань';
            case 'Гаражи':
                return ' гаражей';
            case 'Беседки':
                return ' беседок';
            default:
                return ' домов';
        }
    }

    private function getSemya($semya)
    {
        if ($semya == 'Нежилой') {
            return ' нежилого';
        } elseif ($semya == 'На 3 и более') {
            return ' на 3 и более семьи';
        }

        return ' '.mb_strtolower($semya);
    }

    private function getKrisha($krisha)
    {
        switch ($krisha) {
            case 'Плоская':
                return 'с плоской кровлей';
            case 'Односкатная':
                return ' с односкатной кровлей';
            case 'Двускатная':
                return ' с двускатной кровлей';
            case 'Четырехскатная':
                return ' с четырехскатной кровлей';
            case 'Сложная':
                return ' со сложной кровлей';
            default:
                return '';
        }
    }
}
