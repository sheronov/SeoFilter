<?php

class SeoFilter
{
    public $version = '1.9.4';
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = [];
    /** @var array $initialized */
    public $initialized = [];
    /** @var pdoFetch $pdo */
    public $pdo;
    /** @var sfCountHandler $countHandler */
    public $countHandler = null;

    /** @var modResource */
    protected $object = null;


    /**
     * @param  modX  $modx
     * @param  array  $config
     */
    public function __construct(modX $modx, array $config = [])
    {
        $this->modx =& $modx;

        $this->config = $this->prepareConfig($config);
        $this->pdo = $this->modx->getService('pdoFetch');
        $this->pdo->setConfig(['loadModels' => 'seofilter']);

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
    public function initialize($ctx = 'web', $scriptProperties = [])
    {
        if (isset($this->initialized[$ctx]) && $this->initialized[$ctx]) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);

        if ($this->config['ajax']) {
            $config = $this->makePlaceholders($this->config);
            $js = trim($this->modx->getOption('seofilter_frontend_js', null, $this->config['jsUrl'].'web/default.js',
                false));
            if (!empty($js) && preg_match('/\.js/i', $js)) {
                $js_file = str_replace($config['pl'], $config['vl'], $js);

                if ($this->config['admin_version'] && preg_match('/\.js$/i', $js_file)) {
                    $js_file .= '?v='.mb_substr(md5($this->version), 0, 10);
                }

                if (!isset($this->modx->loadedjscripts[$js_file])) {
                    $this->modx->regClientScript($js_file);
                }
            }

            if ($this->config['page']) {
                $page_url = $this->modx->makeUrl($this->config['page'], $ctx, '', $this->config['scheme']);

                if ($this->config['replace_host']) {
                    $site_url = explode('://', $this->config['site_url']);
                    $site_url = array_pop($site_url);
                    $site_url = trim($site_url, '/');
                    $page_url = str_replace($site_url, $_SERVER['HTTP_HOST'], $page_url);
                }

                $c_suffix = $this->config['container_suffix'];
                if ($c_suffix && $page_url) {
                    if (strpos($page_url, $c_suffix, strlen($page_url) - strlen($c_suffix)) !== false) {
                        $page_url = substr($page_url, 0, -strlen($c_suffix));
                    }
                }
                $page_url = $this->clearSuffixes($page_url);

                $this->config['url'] = $page_url;

                $q = $this->modx->newQuery('sfFieldIds');
                $q->rightJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id');
                $q->rightJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
                $q->where(['sfField.slider' => 1]);
                if ($this->config['proMode']) {
                    $q->where('1=1 AND FIND_IN_SET('.$this->config['page'].',REPLACE(IFNULL(NULLIF(sfRule.pages,""),sfRule.page)," ",""))');
                } else {
                    $q->where(['sfRule.page' => $this->config['page']]);
                }
                $this->config['slider'] = $this->modx->getCount('sfFieldIds', $q);
            }

            $data = json_encode([
                'jsUrl'            => $this->config['jsUrl'].'web/',
                'actionUrl'        => $this->config['actionUrl'],
                'ctx'              => $ctx,
                'page'             => $this->config['page'],
                'params'           => $this->config['params'],
                'hash'             => $this->config['hash'],
                //                    'aliases' => $this->config['aliases'],
                'slider'           => $this->config['slider'],
                'crumbs'           => $this->config['crumbsReplace'],
                'separator'        => $this->config['separator'],
                'redirect'         => $this->config['redirect'],
                'url'              => $this->config['url'],
                'between'          => $this->config['between_urls'],
                'replacebefore'    => $this->config['replacebefore'],
                'replaceseparator' => $this->config['replaceseparator'],
                'jtitle'           => $this->config['jtitle'],
                'jlink'            => $this->config['jlink'],
                'jdescription'     => $this->config['jdescription'],
                'jintrotext'       => $this->config['jintrotext'],
                'jkeywords'        => $this->config['jkeywords'],
                'jh1'              => $this->config['jh1'],
                'jh2'              => $this->config['jh2'],
                'jtext'            => $this->config['jtext'],
                'jcontent'         => $this->config['jcontent'],
                'delimeter'        => $this->config['values_delimeter']
            ], true);

            $this->modx->regClientStartupScript(
                '<script type="text/javascript">seoFilterConfig = '.$data.';</script>', true
            );
        }
        $this->initialized[$ctx] = true;
        return true;
    }

    public function getFieldsKey($key = 'key', $resourceId = 0)
    {
        $fields = [];
        $q = $this->modx->newQuery('sfField');
        //$q->where(array('active'=>1));
        $q->select(['sfField.*']);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($resourceId && !empty($row['xpdo_where'])
                    && !$this->checkResourceCondition($resourceId, $row['xpdo_where'])) {
                    continue;
                }

                if (mb_strtolower($row['class']) === 'modtemplatevar') {
                    if (mb_strtolower($row['xpdo_package']) === 'tvsuperselect') {
                        $fields['tvss'][$row[$key]] = $row;
                    } else {
                        $fields['tvs'][$row[$key]] = $row;
                    }
                } elseif (mb_strtolower($row['class']) === 'tagger') {
                    $fields['tagger'][$row[$key]] = $row;
                } elseif (mb_strtolower($row['class']) === 'msvendor') {
                    $fields['data']['vendor'] = $row;
                } else {
                    $fields['data'][$row[$key]] = $row;
                }
            }
        }
        return $fields;
    }

    public function explodeValue($input = '')
    {
        $values = [];
        if (strpos($input, '||') !== false) {
            $values = array_map('trim', explode('||', $input));
        } elseif (strpos($input, ',') !== false) {
            $values = array_map('trim', explode(',', $input));
        } else {
            $values = [$input];
        }

        return $values;
    }

    public function returnChanges($after = [], $before = [], $type = '', $double = 1)
    {
        $changes = [];
        if (is_array($after)) {
            foreach ($after as $param => $val) {
                if (is_array($val)) {
                    if (isset($before[$param])) {
                        if ($type = 'tvs') {
                            $tv_changes = ['after' => [], 'before' => []];
                            foreach ($val as $vals) {
                                if ($tv_change = $this->explodeValue($vals)) {
                                    foreach ($tv_change as $tvc) {
                                        $tv_changes['after'][] = $tvc;
                                    }
                                }
                            }
                            foreach ($before[$param] as $vals) {
                                if ($tv_change = $this->explodeValue($vals)) {
                                    foreach ($tv_change as $tvc) {
                                        $tv_changes['before'][] = $tvc;
                                    }
                                }
                            }
                            if ($am = array_unique(array_merge(array_diff($tv_changes['after'], $tv_changes['before']),
                                array_diff($tv_changes['before'], $tv_changes['after'])))) {
                                $changes[$param] = $am;
                            }
                        } else {
                            if ($am = array_unique(array_merge(array_diff($val, $before[$param]),
                                array_diff($before[$param], $val)))) {
                                $changes[$param] = $am;
                            }
                        }
                    } else {
                        if ($type = 'tvs') {
                            foreach ($val as $vals) {
                                if ($tv_change = $this->explodeValue($vals)) {
                                    foreach ($tv_change as $tvc) {
                                        $changes[$param][] = $tvc;
                                    }
                                }
                            }
                        } else {
                            $changes[$param] = $val;
                        }
                    }
                } else {
                    if (isset($before[$param])) {
                        if ($val != $before[$param]) {
                            if ($val) {
                                $changes[$param][] = $val;
                            }
                            if ($before[$param]) {
                                $changes[$param][] = $before[$param];
                            }
                        }
                    } elseif ($val) {
                        $changes[$param][] = $val;
                    }
                }
                if (!empty($changes[$param])) {
                    $changes[$param] = array_unique($changes[$param]);
                }
            }
        }

        //        метод дополнительный
        //        if(!empty($before) && $double) {
        //            $changes = array_merge_recursive($changes,$this->returnChanges($before,$after,$type, 0));
        //        }

        return $changes;
    }

    public function checkResourceCondition($resource_id = 0, $where = '')
    {
        $check = true;
        if (empty($where)) {
            return $check;
        }

        $q = $this->modx->newQuery('modResource', ['id' => $resource_id]);
        $to_config = ['where' => 'where', 'join' => 'innerJoin', 'leftjoin' => 'leftJoin'];
        $this->loadHandler();
        $conditions = $this->countHandler->prepareWhere($this->modx->fromJSON($where));
        if (!empty($conditions['where'])) {
            foreach ($conditions['where'] as $where_key => $where_arr) {
                if (strpos($where_key, '.') === false) {
                    $conditions['where']['modResource.'.$where_key] = $where_arr;
                    unset($conditions['where'][$where_key]);
                }
            }
        }
        foreach ($to_config as $prop => $propConfig) {
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

        return $this->modx->getCount('modResource', $q);
    }

    /**
     * The method from miniShop2:
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param       $eventName
     * @param  array  $params
     * @param       $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = [], $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return [
            'success' => empty($message),
            'message' => $message,
            'data'    => $params,
        ];
    }

    public function getResourceData($resource_id = 0, $fields = [])
    {
        $data = [];
        foreach (['tvs' => 'modTemplateVarResource', 'tvss' => 'tvssOption'] as $var => $class) {
            if (empty($fields[$var])) {
                continue;
            }

            if ($var === 'tvss') {
                $this->modx->addPackage('tvsuperselect',
                    $this->modx->getOption('core_path').'components/tvsuperselect/model/');
            }
            // foreach ($fields[$var] as $f_key => $field) {
            //     if (!empty($field['xpdo_where']) && !$this->checkResourceCondition($resource_id,
            //             $field['xpdo_where'])) {
            //         unset($fields[$var][$f_key]);
            //     }
            // }

            if (!empty($fields[$var])) {
                $q = $this->modx->newQuery($class);
                if ($var === 'tvss') {
                    $q->innerJoin('modTemplateVar', 'TV', 'TV.id = tvssOption.tv_id');
                    $q->where([
                        'resource_id' => $resource_id,
                        'TV.name:IN'  => array_unique(array_column($fields[$var], 'key'))
                    ]);
                } else {
                    $q->innerJoin('modTemplateVar', 'TV', 'TV.id = modTemplateVarResource.tmplvarid');
                    $q->where([
                        'contentid'  => $resource_id,
                        'TV.name:IN' => array_unique(array_column($fields[$var], 'key'))
                    ]);
                }
                $q->select([
                    'DISTINCT '.$class.'.value',
                    'TV.id,TV.name'
                ]);
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $data[$var][$row['name']][] = $row['value'];
                    }
                }
            }
        }

        if (array_key_exists('tagger', $fields)) {
            // foreach ($fields['tagger'] as $f_key => $field) {
            //     if ($field['xpdo_where'] && !$this->checkResourceCondition($resource_id, $field['xpdo_where'])) {
            //         unset($fields['tagger'][$f_key]);
            //     }
            // }
            if (!empty($fields['tagger'])) {
                $taggerPath = $this->modx->getOption('tagger.core_path', null,
                    $this->modx->getOption('core_path', null, MODX_CORE_PATH).'components/tagger/');
                /** @var Tagger $tagger */
                $tagger = $this->modx->getService('tagger', 'Tagger', $taggerPath.'model/tagger/',
                    ['core_path' => $taggerPath]);
                if (($tagger instanceof Tagger)) {
                    $q = $this->modx->newQuery('TaggerTagResource', ['resource' => $resource_id]);
                    $q->innerJoin('TaggerTag', 'Tag', 'Tag.id = TaggerTagResource.tag');
                    $q->innerJoin('TaggerGroup', 'Group', 'Group.id = Tag.group');
                    $q->where([
                        'Group.id:IN'       => array_unique(array_column($fields['tagger'], 'key')),
                        'OR:Group.alias:IN' => array_unique(array_column($fields['tagger'], 'key'))
                    ]);
                    $q->select($this->modx->getSelectColumns('TaggerTag', 'Tag', ''));
                    $q->select($this->modx->getSelectColumns('TaggerGroup', 'Group', 'group_'));
                    if ($this->modx->getCount('TaggerTagResource')) {
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                if (array_key_exists($row['group_id'], array_column($fields['tagger'], 'key'))) {
                                    $data['tagger'][$row['group_id']][] = $row['tag'];
                                } else {
                                    $data['tagger'][$row['group_alias']][] = $row['tag'];
                                }
                            }
                        }
                    }
                }
            }
        }


        if (!empty($fields['data']) && $resource = $this->modx->getObject('modResource', $resource_id)) {
            $resourceData = $resource->toArray();
            foreach ($fields['data'] as $field) {
                if (array_key_exists($field['key'], $resourceData)) {
                    // if (!empty($field['xpdo_where'])) {
                    //     if ($this->checkResourceCondition($resource_id, $field['xpdo_where'])) {
                    //         $data['data'][$field['key']] = $resourceData[$field['key']];
                    //     }
                    // } else {
                    $data['data'][$field['key']] = $resourceData[$field['key']];
                    // }
                }
            }
        }

        return $data;
    }


    public function loadHandler()
    {
        if (!is_object($this->countHandler)) {
            require_once 'sfcount.class.php';
            $count_class = $this->config['count_class'];
            if ($count_class !== 'sfCountHandler') {
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
        $pl1 = $this->pdo->makePlaceholders($placeholders, '', '[[+', ']]', false);
        $pl2 = $this->pdo->makePlaceholders($placeholders, '', '[[++', ']]', false);
        $pl3 = $this->pdo->makePlaceholders($placeholders, '', '{', '}', false);
        $customPath = str_replace($pl1['pl'], $pl1['vl'], $customPath);
        $customPath = str_replace($pl2['pl'], $pl2['vl'], $customPath);
        $customPath = str_replace($pl3['pl'], $pl3['vl'], $customPath);
        if (strpos($customPath, MODX_BASE_PATH) === false && strpos($customPath, MODX_CORE_PATH) === false) {
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

    public function fastPageAliases($pageId = 0, $fieldIds = [])
    {
        $aliases = $rule_ids = [];
        foreach ($fieldIds as $fieldId) {
            $q = $this->modx->newQuery('sfFieldIds');
            $q->innerJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id');
            $q->where([
                'sfFieldIds.field_id' => $fieldId,
                'sfRule.active'       => 1
            ]);
            if ($this->config['proMode']) {
                $q->where('1=1 AND FIND_IN_SET('.$pageId.',REPLACE(IFNULL(NULLIF(sfRule.pages,""),sfRule.page)," ",""))');
            } else {
                $q->where(['sfRule.page' => $pageId]);
            }
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
                        if ((int)$row[0]['field_id'] === (int)$fieldId) {
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
            $q->innerJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id AND sfRule.active = 1');
            $q->where(['sfFieldIds.field_id' => $field_id]);
            if ($this->config['proMode']) {
                $q->where('1=1 AND FIND_IN_SET('.$page_id.',REPLACE(IFNULL(NULLIF(sfRule.pages,""),sfRule.page)," ",""))');
            } else {
                $q->where(['sfRule.page' => $page_id]);
            }
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
        if ($page) {
            $q->where(['active' => 1]);
            if ($this->config['proMode']) {
                $q->where('1=1 AND FIND_IN_SET('.$page.',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
                // $q->where(array('(page = '.$page.') OR (1 = 1 AND FIND_IN_SET('.$page.',pages))'));
            } else {
                $q->where(['page' => $page]);
            }
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

        $specialChars = [
            '%' => '%25',
            '+' => '%2B',
            '&' => '%26'
        ];
        foreach ($params as $param => $value) {
            //замены для корректности URL, как и в JS для хэша
            $value = str_replace(array_keys($specialChars), array_values($specialChars), $value);
            $urls[] = $param.'='.$value;
        }

        return '?'.implode('&', $urls);
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


    public function process($action, $data = [])
    {
        if (isset($data['data']['hash'])) {
            $this->config['pdopage_hash'] = $data['data']['hash'];
            unset($data['data']['hash']);
        }
        switch ($action) {
            //TODO: переделать
            case 'metabyurl':
                $meta = [];
                $delArray = ['http://www.', 'https://www.', 'http://', 'https://'];
                $pageId = (int)$data['pageId'];
                $findUrl = '';
                if (!empty($data['data'])) {
                    $fullUrl = explode('?', str_replace($delArray, '', $data['data']['full_url']));
                    $fullUrl = $this->clearSuffixes(array_shift($fullUrl));
                    $pageUrl = $this->clearSuffixes(str_replace($delArray, '', $data['data']['page_url']));
                    if ($this->config['main_alias'] && $pageId === $this->config['site_start']) {
                        $q = $this->modx->newQuery('modResource');
                        $q->where(['id' => $pageId]);
                        $q->select('alias');
                        $alias = $this->modx->getValue($q->prepare());
                        if ($pageUrl !== $fullUrl) {
                            $pageUrl .= '/'.$alias;
                        }
                    }
                    $findUrl = $this->clearSuffixes(trim(str_replace($pageUrl, '', $fullUrl), '/'));
                    $findUrl = trim($findUrl, $this->config['between_urls']);

                    if (!empty($this->config['page_tpl'])) {
                        $pageTplMask = str_replace([
                            '/',
                            '[[+pagevarkey]]',
                            '{$pagevarkey}',
                            '[[+'.$this->config['page_key'].']]',
                            '{$'.$this->config['page_key'].'}',

                        ], [
                            '\/',
                            $this->config['page_key'],
                            $this->config['page_key'],
                            '(\d+)',
                            '(\d+)',

                        ], mb_strtolower($this->config['page_tpl']));

                        if (preg_match('/^(.*)'.$pageTplMask.'$/', $findUrl, $matches)) {
                            $findUrl = $matches[1];
                            $this->config['page_number'] = (int)$matches[2];
                        }
                    }
                }
                if ($findUrl) {
                    if ($url_words = $this->getParamsByUrl($findUrl, $pageId)) {
                        $rule_id = 0;
                        $params = [];
                        foreach ($url_words as $row) {
                            $params[$row['field_alias']] = $row['word_input'];
                            $rule_id = $row['rule_id'];
                        }

                        $q = $this->modx->newQuery('sfFieldIds');
                        $q->where(['multi_id' => $rule_id]);
                        $url_fields = $this->modx->getCount('sfFieldIds', $q);

                        if ((count($params) === $url_fields)) { //Доп проверка на изменения в базе
                            $meta = $this->getRuleMeta($params, $rule_id, $pageId, 1, 0, $params);
                            $meta['find'] = 1;
                            $meta['params'] = $params;
                        }
                    }
                } else {
                    $meta = $this->getPageMeta($pageId);
                    $meta['full_url'] = $this->modx->makeUrl($pageId, '', '', '-1');
                    $meta['find'] = 0;
                }


                if ($this->config['crumbsReplace']) {
                    $crumb_array = $this->getCrumbs($pageId);
                    if ($findUrl) {
                        $meta['link_url'] = $meta['url'].$this->config['url_suffix'];
                        $crumb_array['sflink'] = $meta['link'];
                        $crumb_array['sfurl'] = $meta['link_url'];
                    }
                    if (!empty($meta['nested'])) {
                        $crumb_array['sfnested'] = $meta['nested'];
                    }
                    $crumbs = $this->pdo->getChunk($this->config['crumbsCurrent'], $crumb_array);
                    $meta['crumbs'] = $crumbs;
                }

                $plugin_response = $this->invokeEvent('sfOnReturnMeta',
                    ['action' => $action, 'page' => $pageId, 'meta' => $meta, 'SeoFilter' => $this]);
                if ($plugin_response['success']) {
                    $meta = $plugin_response['data']['meta'];
                }

                $response = [
                    'success' => true,
                    'data'    => $meta,
                ];
                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            case 'meta_results':
                $json_response = $this->config['json_response'];
                $this->config['json_response'] = false;
                $response = $this->process('getmeta', $data);
                $this->config['json_response'] = $json_response;
                $config = [];
                if (isset($response['data']['config'])) {
                    $config = $response['data']['config'];
                    unset($response['data']['config']);
                }

                $response['data']['pdopage_change'] = $this->changePdoPageSession($config);

                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            case 'getmetatm':
            case 'getmeta':
                $params = [];
                $pageId = (int)$data['pageId'];

                if ($action === 'getmetatm') {
                    $guard = $this->config['tm2_tags_guard'];
                    foreach ($data['data'] as $tmVal) {
                        if (!isset($tmVal['name'], $tmVal['value']) || $tmVal['name'] === 'page_id') {
                            continue;
                        }
                        $value = $tmVal['value'];
                        $fieldKey = $tmVal['name'];

                        //значит поле фильтра
                        if ($pos = mb_strpos($fieldKey, '[like][]')) {
                            $fieldKey = mb_substr($fieldKey, 0, $pos);
                            if (isset($params[$fieldKey])) {
                                $params[$fieldKey] .= $this->config['values_delimeter'].$guard.$value.$guard;
                            } else {
                                $params[$fieldKey] = $guard.$value.$guard;
                            }
                        } elseif ($pos = mb_strpos($fieldKey, '[from]')) {
                            $fieldKey = mb_substr($fieldKey, 0, $pos);
                            if (isset($params[$fieldKey])) {
                                $params[$fieldKey] = $value.$this->config['values_delimeter'].$params[$fieldKey];
                            } else {
                                $params[$fieldKey] = $value;
                            }
                        } elseif ($pos = mb_strpos($fieldKey, '[to]')) {
                            $fieldKey = mb_substr($fieldKey, 0, $pos);
                            if (isset($params[$fieldKey])) {
                                $params[$fieldKey] .= $this->config['values_delimeter'].$value;
                            } else {
                                $params[$fieldKey] = $value;
                            }
                        } elseif ($pos = mb_strpos($fieldKey, '[]')) {
                            $fieldKey = mb_substr($fieldKey, 0, $pos);
                            if (isset($params[$fieldKey])) {
                                $params[$fieldKey] .= $this->config['values_delimeter'].$value;
                            } else {
                                $params[$fieldKey] = $value;
                            }
                        } else {
                            $params[$fieldKey] = $value;
                        }
                    }
                } else {
                    $params = $data['data'];
                }

                if (!$result = $this->findSeoPageByParams($pageId, $params)) {
                    return $this->error('no_results', [], ['action' => $action]);
                }

                $meta = $this->preparePageMeta($result, $action, $params);

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

    /**
     * Основной метод для поиска SEO-страниц по параметрам с фронта
     *
     * @param  int  $pageId
     * @param  array  $params
     * @param  string  $objectClass
     *
     * @return null|array
     */
    public function findSeoPageByParams($pageId, $params, $objectClass = 'modResource')
    {
        if (!is_array($params)) {
            $params = [];
        }

        // 0. Сохранить данные по странице в память
        if (!$this->object = $this->modx->getObject($objectClass, $pageId)) {
            return null;
        }

        // 1. Получить поля для страницы с ключом-псевдонимом
        $fields = $this->fieldsForPage($pageId, 'alias');

        if (empty($fields)) {
            return $this->metaForExistedPage($pageId, $params);
        }

        // 1.1. Оставим только параметры, для которых у нас есть поля
        $fieldsParams = array_intersect_key($params, $fields);
        $diffParams = array_diff_key($params, $fieldsParams);

        if (empty($fieldsParams)) {
            return $this->metaForExistedPage($pageId, $diffParams);
        }

        $usageFields = array_intersect_key($fields, $fieldsParams);

        // 2. Получить массив слов из словаря (если надо - добавить)
        $words = $this->wordsByParamsAndFields($fieldsParams, $usageFields);

        $fieldsParams = array_intersect_key($fieldsParams, $words); //не все слова могли быть, усечём ещё раз
        $diffParams = array_diff_key($params, $fieldsParams); //diff на этом этапе мог увеличиться

        $usageFields = array_intersect_key($usageFields, $fieldsParams);

        if (empty($fieldsParams) || empty($usageFields)) {
            return $this->metaForExistedPage($pageId, $diffParams);
        }

        // 3. Найти правила для страницы по полям (с учётом условий)
        $rules = $this->rulesByPageFieldsParams($pageId, $usageFields, $fieldsParams);
        if (empty($rules)) {
            return $this->metaForExistedPage($pageId, $params);
        }

        // 4. Найти SEO страницу с учётом найденных слов, правил

        $links = $this->linksByRulesWordsIds($pageId, array_column($rules, 'id'), array_column($words, 'id'));

        if (empty($links)) {
            return $this->metaForExistedPage($pageId, $params);
        }

        $linkFound = null;

        foreach ($links as $link) {
            // 5. Пересчитать результаты, если их 0.
            if ($this->config['count_childrens'] && (!(int)$link['total'] || $this->config['ajax_recount'])) {
                $this->loadHandler();
                $link['total'] = $this->countHandler->countByLink($link['id']); //внутри объект ссылки обновится
            }

            if ((int)$link['total'] || !$this->config['hideEmpty']) {
                $linkFound = $link;
                break;
            }
        }

        // 6. Вернуть ссылку, если есть результаты, подставив мета-теги
        if ($linkFound) {
            $sortForParams = [];
            if (!empty($rules[$linkFound['multi_id']]['url'])) {
                foreach ($fieldsParams as $alias => $input) {
                    if (mb_strpos($rules[$linkFound['multi_id']]['url'], $alias) === false) {
                        $diffParams[$alias] = $input;
                        unset($words[$alias], $fieldsParams[$alias]);
                    } else {
                        $sortForParams[$alias] = mb_strpos($rules[$linkFound['multi_id']]['url'], $alias);
                    }
                }
            }

            uksort($words, function ($a, $b) use ($sortForParams) {
                $indexA = 0;
                $indexB = 0;
                if (isset($sortForParams[$a])) {
                    $indexA = $sortForParams[$a];
                }
                if (isset($sortForParams[$b])) {
                    $indexB = $sortForParams[$b];
                }
                if ($indexA === $indexB) {
                    return 0;
                }
                return ($indexA < $indexB) ? -1 : 1;
            });

            $counterUpdate = "UPDATE {$this->modx->getTableName('sfUrls')} SET count = count + 1";
            if ($this->config['json_response']) {
                $counterUpdate .= ', ajax = ajax + 1';
            }
            $counterUpdate .= " WHERE id = {$linkFound['id']}";
            if ($q = $this->modx->prepare($counterUpdate)) {
                $q->execute();
            }

            return $this->metaForSeoPage($linkFound, $words, $rules[$linkFound['multi_id']], $diffParams);
        }


        // 6.1. Если нет результатов, то вернуть содержимое страницы
        return $this->metaForExistedPage($pageId, $diffParams);
    }

    /**
     * @param  array  $meta
     * @param  string  $action
     * @param  array  $params
     *
     * @return array
     */
    public function preparePageMeta($meta, $action, $params)
    {
        if ($meta['find'] && !empty($meta['diff'])) {
            $hash = [];
            foreach ($meta['diff'] as $tmVal) {
                if (!isset($tmVal['name'], $tmVal['value']) || $tmVal['name'] === 'page_id') {
                    continue;
                }
                foreach ($params as $param => $value) {
                    if (mb_strpos($tmVal['name'], $param) === 0) {
                        $hash[] = $tmVal['name'].'='.$tmVal['value'];
                    }
                }
            }
            $meta['hash'] = implode('&', $hash);
        }

        if (!empty($meta['url'])) {
            if ((int)$this->object->id === (int)$this->config['site_start']) {
                if ($this->config['main_alias']) {
                    $alias = $this->object->alias;
                    $meta['url'] = '/'.$alias.$this->config['between_urls'].$meta['url'].$this->config['url_suffix'];
                } else {
                    $meta['url'] = '/'.$meta['url'].$this->config['url_suffix'];
                }
            } else {
                $meta['url'] = $this->config['between_urls'].$meta['url'].$this->config['url_suffix'];
            }
            $meta['full_url'] = $this->clearSuffixes($this->modx->makeUrl($this->object->id, $this->object->context_key,
                    '', '-1')).$meta['url'];
        } elseif ((int)$this->object->id === (int)$this->config['site_start']) {
            $meta['url'] = '';
        } else {
            $meta['url'] = isset($this->config['this_page_suffix'])
                ? $this->config['this_page_suffix']
                : $this->config['container_suffix'];
        }

        if (!empty($meta['diff'])) {
            if ($this->config['page_tpl'] && array_key_exists($this->config['page_key'], $meta['diff'])) {
                if ((int)$meta['diff'][$this->config['page_key']] === 1) {
                    unset($meta['diff'][$this->config['page_key']]);
                } else {
                    $page_part = $this->pdo->parseChunk('@INLINE '.$this->config['page_tpl'], [
                        'pageVarKey'              => $this->config['page_key'],
                        'pagevarkey'              => $this->config['page_key'],
                        $this->config['page_key'] => $meta['diff'][$this->config['page_key']]
                    ]);

                    if (mb_strpos($page_part, mb_substr($meta['url'], -1, 1)) === 0) {
                        $page_part = mb_substr($page_part, 1);
                    }

                    $meta['url'] .= $page_part;
                    $meta['full_url'] .= $page_part;

                    unset($meta['diff'][$this->config['page_key']]);
                }
            }


            if ($action === 'getmetatm') {
                $hash_part = $this->getHashUrlForTM2($meta['diff'], $params);
            } else {
                $hash_part = $this->getHashUrl($meta['diff']);
            }
            if (mb_strpos($meta['url'], '?') !== false) {
                $meta['url'] .= str_replace('?', '&', $hash_part);
                $meta['full_url'] .= str_replace('?', '&', $hash_part);
            } else {
                $meta['url'] .= $hash_part;
                $meta['full_url'] .= $hash_part;
            }
        }

        if ($this->config['crumbsReplace']) {
            $crumb_array = $this->getCrumbs($this->object->id);
            if ($meta['find']) {
                if (mb_strpos($meta['url'], '?') === false) {
                    $meta['link_url'] = $meta['url'].$this->config['url_suffix'];
                } else {
                    $meta['link_url'] = $meta['url'];
                }
                $crumb_array['sflink'] = $meta['link'];
                $crumb_array['sfurl'] = $meta['link_url'];
            }
            if (!empty($meta['nested'])) {
                $crumb_array['sfnested'] = $meta['nested'];
            }
            $crumbs = $this->pdo->getChunk($this->config['crumbsCurrent'], $crumb_array);
            $meta['crumbs'] = $crumbs;
        }

        $pluginResponse = $this->invokeEvent('sfOnReturnMeta',
            ['action' => $action, 'page' => $this->object->id, 'meta' => $meta, 'SeoFilter' => $this]);

        if ($pluginResponse['success']) {
            $meta = $pluginResponse['data']['meta'];
        }

        return $meta;
    }

    /**
     * Подбор мета-тегов для обычной страницы (без параметров, влияющих на SEO)
     *
     * @param  int  $pageId
     * @param  array  $params
     *
     * @return array
     */
    protected function metaForExistedPage($pageId, $params)
    {
        if ($params && array_key_exists($this->config['page_key'], $params)) {
            $this->config['page_number'] = $params[$this->config['page_key']];
        }

        $meta = [
            'find'     => 0,
            'full_url' => $this->modx->makeUrl($pageId, $this->object->context_key, '', '-1'),
            'page_id'  => $pageId,
            'id'       => $pageId,
            'diff'     => $params
        ];

        $system = [
            'title'       => $this->config['title'],
            'description' => $this->config['description'],
            'introtext'   => $this->config['introtext'],
            'keywords'    => $this->config['keywords'],
            'h1'          => $this->config['h1'],
            'h2'          => $this->config['h2'],
            'text'        => $this->config['text'],
            'content'     => $this->config['content'],
            'link'        => $this->config['link'],
        ];


        $page_arr = $this->object->toArray();
        $page_keys = array_keys($page_arr);

        $meta = array_merge($meta, $page_arr);
        $variables = $this->prepareRow($meta, $pageId);

        $array_diff = array_diff($system, $page_keys);
        foreach ($array_diff as $tag => $tvName) {
            if ($tvValue = $this->object->getTVValue($tvName)) {
                $tpl = '@INLINE '.$tvValue;
                $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
                unset($system[$tag]);
            }
        }
        foreach ($system as $tag => $tagName) {
            if ($tagName) {
                if (strpos($tagName, '@INLINE') !== false) {
                    $tpl = $tagName;
                } elseif (mb_strpos($tagName, '[') !== false || mb_strpos($tagName, '{') !== false) {
                    $tpl = '@INLINE '.$tagName;
                } else {
                    $tpl = '@INLINE '.$this->object->get($tagName);
                }
                $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
            }
        }

        if (empty($page_arr['isfolder'])) {
            $q = $this->modx->newQuery('modContentType', ['name' => 'HTML']);
            $q->select('file_extensions');
            $this->config['this_page_suffix'] = $this->modx->getValue($q->prepare());
        }


        if (isset($meta['title'])) {
            $meta['pagetitle'] = $meta['title'];
        }


        return $meta;
    }

    /**
     * @param  array  $link
     * @param  array  $words
     * @param  array  $rule
     * @param  array  $diffParams
     *
     * @return array
     */
    protected function metaForSeoPage($link, $words, $rule, $diffParams)
    {
        if ($diffParams && array_key_exists($this->config['page_key'], $diffParams)) {
            $this->config['page_number'] = $diffParams[$this->config['page_key']];
        }

        $meta = [
            'find'       => 1,
            'diff'       => $diffParams,
            'params'     => array_combine(array_keys($words), array_column($words, 'input')),
            'seo_id'     => $link['id'],
            'url_id'     => $link['id'],
            'link'       => $link['link'],
            'url'        => !empty($link['new_url']) ? $link['new_url'] : $link['old_url'],
            'createdon'  => $link['createdon'],
            'editedon'   => $link['editedon'] === '0000-00-00 00:00:00' ? $link['createdon'] : $link['editedon'],
            'rule_id'    => $rule['id'],
            'success'    => true,
            'page_id'    => $link['page_id'],
            'total'      => (int)$link['total'],
            'old_total'  => (int)$link['total'],
            'has_slider' => array_reduce($words, function ($carry, $word) {
                if (!empty($word['field']['slider'])) {
                    $carry++;
                }
                return $carry;
            }, 0)
        ];
        $seo_system = ['id', 'field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'key'];
        $seo_array = [
            'title',
            'h1',
            'h2',
            'description',
            'introtext',
            'keywords',
            'text',
            'content',
            'link',
            'tpl',
            'introlength'
        ];


        if ($this->config['crumbsNested']
            && $nested = $this->findNestedCrumbs(array_column($words, 'id', 'field_id'), $link['page_id'],
                $link['id'])) {
            $meta['nested'] = [];
            foreach ($nested as $nestedLink) {
                $nestedLink['url'] = $nestedLink['new_url'] ?: $nestedLink['old_url'];
                $nestedLink['sflink'] = $nestedLink['link'];
                $nestedLink['sfurl'] = $nestedLink['url'].$this->config['url_suffix'];
                $meta['nested'][] = $nestedLink;
            }
            $meta['nested'] = $this->config['json_response']
                ? $meta['nested']
                : $this->modx->toJSON($meta['nested']);
        }


        $fields = array_column(array_column($words, 'field'), null, 'alias');

        $wordsToText = [
            'total' => (int)$link['total'],
            'count' => (int)$link['total'],
        ];
        $paramsToText = [];

        foreach ($words as $fieldAlias => $word) {
            foreach (array_diff_key($word, array_flip($seo_system)) as $key => $value) {
                if (count($words) === 1) {
                    $wordsToText[$key] = $value;
                }
                if (mb_strpos($key, 'value') !== false) {
                    $wordsToText[str_replace('value', $fieldAlias, $key)] = $value;
                }
            }
            $wordsToText[$fieldAlias.'_input'] = $word['input'];
            $wordsToText[$fieldAlias.'_alias'] = $word['alias'];
            $wordsToText[$fieldAlias.'_word'] = $word['id'];
            $wordsToText['m_'.$fieldAlias] = $wordsToText['m_'.$fieldAlias.'_i'];
            $paramsToText[$fieldAlias] = [
                'word'  => $word['id'],
                'input' => $word['input'],
                'value' => $word['value'],
                'alias' => $word['alias'],
                'field' => $word['field_id'],
                'class' => $word['field']['class'],
                'key'   => $word['field']['key']
            ];
        }


        if (!empty($rule['count_parents']) || $rule['count_parents'] === '0' || $rule['count_parents'] === 0) {
            $parents = $rule['count_parents'];
        } else {
            $parents = $link['page_id'];
        }


        if (!empty($this->config['pdopage_hash'])) {
            $meta['config'] = $this->getRuleCount($meta['params'], $fields, $parents,
                $this->modx->fromJSON($rule['count_where']), false, true);
            $meta['config']['parents'] = $parents;
        }

        if ($this->config['count_choose'] && $this->config['count_select']) {
            if ($minMax = $this->getRuleCount($meta['params'], $fields, $parents,
                $this->modx->fromJSON($rule['count_where']), 1)) {
                $wordsToText = array_merge($minMax, $wordsToText);
            }
        }

        if ($this->config['page_key']) {
            $wordsToText['page_number'] = $wordsToText[$this->config['page_key']] = $this->config['page_number'];
        }

        foreach (['id', 'page', 'page_id'] as $pkey) {
            if (!isset($wordsToText[$pkey])) {
                $wordsToText[$pkey] = $link['page_id'];
            }
        }

        $wordsToText['params'] = $paramsToText;

        $wordsToText = $this->prepareRow($wordsToText, $parents, $rule['id'], $rule);

        if ($link['custom']) {
            $seo_array = array_intersect_key($link, array_flip($seo_array));
        } else {
            $seo_array = array_intersect_key($rule, array_flip($seo_array));
        }

        if (!isset($wordsToText['resource'])) {
            $wordsToText['resource'] = $this->object->toArray();
        }
        if (!isset($wordsToText['original_page'])) {
            $wordsToText['original_page'] = $this->object->toArray();
        }

        foreach ($seo_array as $tag => $text) {
            if ($text) {
                if (strpos($text, '@INLINE') !== false) {
                    $tpl = $text;
                } else {
                    $tpl = '@INLINE '.$text;
                }
                $meta[$tag] = $this->pdo->getChunk($tpl, $wordsToText);
            }
        }

        if (!empty($link['tpl'])) {
            $meta['tpl'] = $link['tpl'];
        } elseif (!empty($rule['tpl'])) {
            $meta['tpl'] = $rule['tpl'];
        }

        if (!empty($link['introlength'])) {
            $meta['introlength'] = $link['introlength'];
        } elseif (!empty($rule['introlength'])) {
            $meta['introlength'] = $rule['introlength'];
        }

        foreach (['properties', 'introtexts'] as $prop) {
            $seo_values = [];
            if (!empty($link[$prop]) && !empty($this->modx->fromJSON($link[$prop])['values'])) {
                $seo_values = $this->modx->fromJSON($link[$prop])['values'];
            } elseif (!empty($rule[$prop]) && !empty($this->modx->fromJSON($rule[$prop])['values'])) {
                $seo_values = $this->modx->fromJSON($rule[$prop])['values'];
            }
            if (!empty($seo_values)) {
                $properties = [];
                $array_word = [];
                foreach ($wordsToText as $key => $val) {
                    $array_word['$'.$key] = "'".$val."'";
                }
                uksort($array_word, function ($a, $b) {
                    if (mb_strlen($a) === mb_strlen($b)) {
                        return 0;
                    }
                    return mb_strlen($a) < mb_strlen($b);
                });
                foreach ($seo_values as $value) {
                    $properties[] = str_replace(array_keys($array_word), array_values($array_word), $value);
                }
                $meta[$prop] = $properties;
            }
        }

        if (isset($meta['title'])) {
            $meta['pagetitle'] = $meta['title'];
        }

        return $meta;
    }

    /**
     * Поля, которые используются для категории
     * Следите, чтобы для одной категории не было двух полей с одинаковым псевдонимом
     *
     * @param  int  $pageId
     * @param  null|string  $key
     *
     * @return array
     */
    protected function fieldsForPage($pageId, $key = 'id')
    {
        $fields = [];

        $q = $this->modx->newQuery('sfField')
            ->select($this->modx->getSelectColumns('sfField', 'sfField', ''))
            ->innerJoin('sfFieldIds', 'FieldRule', 'sfField.id = FieldRule.field_id')
            ->innerJoin('sfRule', 'sfRule', 'FieldRule.multi_id = sfRule.id')
            ->where([
                'sfRule.active' => 1,
            ])
            ->groupby('sfField.id');

        if ($this->config['proMode']) {
            $q->where("1=1 and FIND_IN_SET({$pageId}, REPLACE(IFNULL(NULLIF(sfRule.pages, ''), sfRule.page), ' ', ''))");
        } else {
            $q->where(['sfRule.page' => $pageId]);
        }

        if ($q->prepare() && $q->stmt->execute()) {
            while ($field = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($key && isset($field[$key])) {
                    $fields[$field[$key]] = $field;
                } else {
                    $fields[] = $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Поиск правил для страницы по переданным полям
     * Также учитываются условия для полей, добавленных в правило
     *
     * @param  int  $pageId
     * @param  array  $fields
     * @param  array  $params
     *
     * @return array
     */
    protected function rulesByPageFieldsParams($pageId, $fields, $params)
    {
        $rules = [];

        $fieldsCondition = 'FoundFields.field_id IN ('.implode(',', array_column($fields, 'id')).')';

        foreach ($fields as $field) {
            $fieldsCondition .= str_replace('?', $this->modx->quote($params[$field['alias']]),
                " AND IF(FoundFields.where and FoundFields.field_id = {$field['id']},
                    case FoundFields.compare
                        when 1 then FIND_IN_SET(?, FoundFields.value) > 0
                        when 2 then FIND_IN_SET(?, FoundFields.value) = 0
                        when 3 then ? > FoundFields.value
                        when 4 then ? < FoundFields.value
                        when 5 then (? >= FoundFields.value and ? <= FoundFields.value)
                        when 5 then (? >= CONVERT(substring_index(FoundFields.value, ',', 1), SIGNED) 
                                    AND ? <= CONVERT(substring_index(FoundFields.value, ',', -1), SIGNED))
                        when 6 then ? LIKE CONCAT('%',FoundFields.value,'%')
                        when 7 then ? NOT LIKE CONCAT('%',FoundFields.value,'%')
                        else 1
                        end
                , 1)");
        }


        $q = $this->modx->newQuery('sfRule')
            ->select($this->modx->getSelectColumns('sfRule', 'sfRule', ''))
            ->select('count(FieldRule.id) as rule_fields, count(FoundFields.id) as found_fields')
            ->innerJoin('sfFieldIds', 'FieldRule', 'sfRule.id = FieldRule.multi_id')
            ->leftJoin('sfFieldIds', 'FoundFields', 'FieldRule.id = FoundFields.id AND '.$fieldsCondition)
            ->where(['sfRule.active' => 1])
            ->groupby('sfRule.id')
            ->groupby('sfRule.rank')
            ->groupby('sfRule.base')
            ->having('found_fields > 0 AND found_fields = rule_fields')
            ->having('(sfRule.base = 1 OR (sfRule.base = 0 AND found_fields = '.count($params).'))')
            ->sortby('found_fields', 'DESC')
            ->sortby('sfRule.rank', 'ASC')
            ->sortby('sfRule.base', 'DESC')
            ->sortby('sfRule.id', 'ASC');

        if ($this->config['proMode']) {
            $q->where("1=1 AND FIND_IN_SET({$pageId},REPLACE(IFNULL(NULLIF(sfRule.pages,''),sfRule.page),' ',''))");
        } else {
            $q->where(['sfRule.page' => $pageId]);
        }

        if ($q->prepare() && $q->stmt->execute()) {
            while ($rule = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $rules[$rule['id']] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Ищет и сопоставляет слова в словаре
     * Добавляет новые, если разрешено
     *
     * @param  array  $params
     * @param  array  $fields
     *
     * @return array
     */
    protected function wordsByParamsAndFields($params, $fields)
    {
        $words = [];

        $fieldsById = array_column(array_filter($fields, function ($field) {
            return empty($field['slider']);
        }), null, 'id');

        if ($sliderFields = array_filter($fields, function ($field) {
            return !empty($field['slider']);
        })) {
            foreach ($sliderFields as $field) {
                $values = array_map('trim', explode(',', $params[$field['alias']]));
                $min = (float)$values[0];
                $max = $min;
                if (isset($values[1])) {
                    $max = (float)$values[1];
                }
                unset($params[$field['alias']]);

                $q = $this->modx->newQuery('sfDictionary')
                    ->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', ''))
                    ->where(['field_id' => $field['id']])
                    ->where([
                        "substring_index(`sfDictionary`.input, ',', 1) <= {$min}",
                        "substring_index(`sfDictionary`.input, ',', -1) >= {$max}"
                    ])
                    ->sortby("sqrt(pow({$min} - CONVERT(substring_index(`sfDictionary`.input, ',', 1), SIGNED), 2)
                                + pow({$max} - CONVERT(substring_index(`sfDictionary`.input, ',', -1), SIGNED), 2))")
                    ->limit(1);
                if ($q->prepare() && $q->stmt->execute() && $word = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $words[$field['alias']] = $word;
                }
            }
        }

        if ($fieldsById) {
            $q = $this->modx->newQuery('sfDictionary')
                ->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', ''))
                ->where(['field_id:IN' => array_keys($fieldsById)])
                ->where(['input:IN' => array_values($params)]);

            if ($q->prepare() && $q->stmt->execute()) {
                while ($word = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $fieldAlias = $fieldsById[$word['field_id']]['alias'];
                    if (mb_strtolower($word['input']) === mb_strtolower($params[$fieldAlias])) {
                        $word['field'] = $fieldsById[$word['field_id']];
                        $words[$fieldAlias] = $word;
                    }
                }
            }

            foreach ($params as $alias => $input) {
                if (!isset($words[$alias]) && $this->config['mfilterWords']
                    && mb_strpos($input, $this->config['values_delimeter']) === false
                    && $word = $this->addNewWord($input, $fields[$alias])
                ) {
                    $words[$alias] = $word;
                }
            }
        }

        return $words;
    }

    /**
     * Добавление нового слова в словарь с фронта
     *
     * @param  string  $input
     * @param  array  $field
     *
     * @return array
     */
    protected function addNewWord($input, $field)
    {
        $word = null;

        if ($input && $value = $this->getValueByInputField($input, $field)) {
            $relation_id = $relation_value = '';
            if (is_array($value)) {
                $relation_value = $value['relation'];
                $value = $value['value'];
            }
            $processorProps = [
                'class'    => $field['class'],
                'key'      => $field['key'],
                'field_id' => $field['id'],
                'value'    => $value,
                'input'    => $input,
            ];

            if ($relation_value) {
                $relation_field = $field['relation_field'];
                $s = $this->modx->newQuery('sfDictionary');
                $s->where(['input' => $relation_value, 'field_id' => $relation_field, 'active' => 1]);
                $s->select('id');
                $relation_id = $this->modx->getValue($s->prepare());
            }

            if ($relation_id) {
                $processorProps['relation_word'] = $relation_id;
            }

            $otherProps = ['processors_path' => $this->config['processorsPath']];
            $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.print_r($response->response, 1));
                $this->modx->error->reset();
            } else {
                $word = $response->response['object'];
            }
        }

        return $word;
    }

    /**
     * @param  string  $input
     * @param  array  $field
     *
     * @return string|array
     */
    protected function getValueByInputField($input, $field)
    {
        $value = $input;

        if (mb_strtolower($field['class']) === 'msvendor') {
            $q = $this->modx->newQuery('msVendor');
            $q->limit(1);
            $q->where(['id' => $input]);
            $q->select('name');
            if ($q->prepare() && $q->stmt->execute()) {
                $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
            }
        } elseif ($field['xpdo']) {
            $xpdo_id = $field['xpdo_id'];
            $xpdo_name = $field['xpdo_name'];
            if ($xpdo_class = $field['xpdo_class']) {
                if ($package = $field['xpdo_package']) {
                    $this->modx->addPackage(strtolower($package),
                        $this->modx->getOption('core_path').'components/'.strtolower($package).'/model/');
                }
                $q = $this->modx->newQuery($xpdo_class);
                $q->where([$xpdo_id => $input]);
                $q->limit(1);
                if ($field['relation']) {
                    $relation_field = $field['relation_field'];
                    $relation_column = $field['relation_column'];
                    if ($relation_column) {
                        $q->select($xpdo_name.','.$relation_column);
                        if ($q->prepare() && $q->stmt->execute() && $row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $value = ['value' => $row[$xpdo_name], 'relation' => $row[$relation_column]];
                        }
                    } else {
                        $q->select($xpdo_name);
                        if ($q->prepare() && $q->stmt->execute()) {
                            $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
                        }
                    }
                } else {
                    $q->select($xpdo_name);
                    if ($q->prepare() && $q->stmt->execute()) {
                        $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @param  int  $pageId
     * @param  array  $ruleIds
     * @param  array  $wordIds
     *
     * @return array
     */
    protected function linksByRulesWordsIds($pageId, $ruleIds, $wordIds)
    {
        $links = [];

        $q = $this->modx->newQuery('sfUrls')
            ->select($this->modx->getSelectColumns('sfUrls', 'sfUrls', ''))
            ->select('count(FoundWords.id) as found_words')
            ->select('count(UrlWords.id) as url_words')
            ->innerJoin('sfUrlWord', 'UrlWords', 'sfUrls.id = UrlWords.url_id')
            ->leftJoin('sfUrlWord', 'FoundWords',
                'UrlWords.id = FoundWords.id AND FoundWords.word_id in ('.implode(',', $wordIds).')')
            ->where([
                'sfUrls.active'      => 1,
                'sfUrls.page_id'     => $pageId,
                'sfUrls.multi_id:IN' => $ruleIds
            ])
            ->groupby('sfUrls.id')
            ->groupby('sfUrls.multi_id')
            ->having('found_words > 0 and found_words = url_words')
            ->sortby('FIELD(sfUrls.multi_id, '.implode(',', $ruleIds).')')
            ->sortby('found_words', 'desc');

        if ($q->prepare() && $q->stmt->execute()) {
            while ($link = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $links[] = $link;
            }
        }

        return $links;
    }

    protected function getHashUrlForTM2($diff, $original)
    {
        $hash = [];
        foreach ($original as $tmVal) {
            if (!isset($tmVal['name'], $tmVal['value']) || $tmVal['name'] === 'page_id') {
                continue;
            }
            $value = $tmVal['value'];
            $fieldKey = $tmVal['name'];
            foreach ($diff as $key => $vals) {
                if (mb_strpos($fieldKey, $key) === 0) {
                    $hash[] = $fieldKey.'='.$value;
                }
            }
        }
        if (!empty($hash)) {
            $hash = '?'.implode('&', $hash);
        }
        return $hash;
    }

    public function getCrumbs($pageId = 0)
    {
        $page_array = [];
        if ($pageId && $page_array = $this->pdo->getArray('modResource', $pageId)) {
            if (empty($page_array['menutitle'])) {
                $page_array['menutitle'] = $page_array['pagetitle'];
            }
            if (mb_strtolower($page_array['class_key']) === 'modweblink') {
                $page_array['link'] = is_numeric(trim($page_array['content'], '[]~ '))
                    ? $this->pdo->makeUrl((int)trim($page_array['content'], '[]~ '), $page_array)
                    : $page_array['content'];
            } else {
                $page_array['link'] = $this->pdo->makeUrl($page_array['id'], $page_array);
            }
        }
        return $page_array;
    }

    public function changePdoPageSession($config = [], $hash = '')
    {
        $success = false;
        if (empty($hash)) {
            $hash = (string)$this->config['pdopage_hash'];
        }

        $to_delete = ['where', 'innerJoin', 'leftJoin', 'includeTVs'];
        foreach ($to_delete as $key) {
            unset($_SESSION['pdoPage'][$hash][$key]);
        }
        if (!empty($config)) {
            if (!empty($_SESSION['pdoPage'][$hash])) {
                unset($_SESSION['pdoPage'][$hash]['parents']);
                //            unset($config['innerJoin']);
                //            unset($config['leftJoin']);
                //            unset($config['includeTVs']);
                //                $this->modx->log(1, print_r($config, 1));

                unset($config['innerJoin']['msProductData']);
                unset($config['leftJoin']['msCategoryMember']);
                foreach ($config as $key => &$data) {
                    if (is_array($data)) {
                        foreach ($data as $param => $value) {
                            unset($data[$param]);
                            $param = str_replace('`', '', $param);

                            if (is_array($value)) {
                                foreach ($value as $k => &$v) {
                                    $v = str_replace(['`', 'msProductData.', 'modResource.'],
                                        ['', 'msProduct.', ''], $v);
                                }
                                $data[$param] = $value;
                            } else {
                                if (strpos($value, 'msCategoryMember.category_id') !== false) {
                                    $tmpExplode = explode('msCategoryMember.category_id', $value);
                                    $parents = array_pop($tmpExplode);
                                    $parents = str_replace(['IN', '=', '(', ')', ' '], '', $parents);
                                    $_SESSION['pdoPage'][$hash]['parents'] = $config['parents'] = $parents;
                                    continue;
                                }
                                $data[$param] = str_replace(['`', 'msProductData.', 'modResource.'],
                                    ['', 'msProduct.', ''], $value);
                            }
                        }
                        $_SESSION['pdoPage'][$hash][$key] = $this->modx->toJSON($data);
                    } else {
                        $_SESSION['pdoPage'][$hash][$key] = str_replace('`', '', $data);
                    }
                }
            }
        }
        //        $this->modx->log(1,print_r($_SESSION['pdoPage'][$hash],1));

        //        $this->loadHandler();
        //        $this->countHandler->prepareWhere($params);

        return $success;
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

    public function findNestedCrumbs($field_word = [], $page_id = 0, $url_id = 0)
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

    public function fastFindRuleId($pageId = 0, $baseParams = [], $diffParams = [], $fieldIds = [], $diffFieldIds = [])
    {
        $params_keys = array_keys(array_merge($baseParams, $diffParams));
        $allFieldsIds = array_merge(array_keys($fieldIds), array_keys($diffFieldIds));
        $ruleId = $rid_count = 0;
        $rule_aliases = [];
        $pageAliases = $this->fastPageAliases($pageId, array_keys($fieldIds));

        foreach ($pageAliases as $rule => $ralias) {
            $sort = $ralias['sort'];
            $base = $ralias['base'];
            unset($ralias['sort'], $ralias['base']);
            foreach ($ralias as $ra) {
                $rule_aliases[$rule]['sort'] = $sort;
                $rule_aliases[$rule]['base'] = $base;
                $rule_aliases[$rule]['fields'][$ra['id']] = [
                    'where'   => $ra['where'],
                    'compare' => $ra['compare'],
                    'value'   => $ra['value'],
                    'alias'   => $ra['alias']
                ];
            }
        }

        foreach ($rule_aliases as $rule => $rarray) {
            $fields = $rarray['fields'];
            if ($rarray['base']) {
                if (count(array_diff(array_keys($fields), $params_keys))) {
                    continue;
                }
            } else {
                if (count($params_keys) !== count(array_keys($fields))) {
                    continue;
                }
                if (count(array_diff(array_keys($fields), $params_keys))) {
                    continue;
                }
            }
            if (count($fields) > $rid_count) {
                $check = 0;
                foreach ($fields as $fieldId => $row) {
                    if (!in_array($fieldId, $fieldIds, true)) {
                        continue;
                    }
                    if ($row['where'] && $row['compare']) {
                        $value = $row['value'];
                        $values = explode($this->config['values_delimeter'], $value);
                        $get_param = array_merge($baseParams, $diffParams)[$row['alias']];
                        switch ($row['compare']) { //Обратный механизм поиска
                            case 1:
                                if (in_array($get_param, $values, true)) {
                                    $check++;
                                }
                                break;
                            case 2:
                                if (!in_array($get_param, $values, true)) {
                                    $check++;
                                }
                                break;
                            case 3:
                                if ($get_param > $value) {
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
                            case 6:
                                if (mb_strpos($get_param, $value) !== false) {
                                    $check++;
                                }
                                break;
                            case 7:
                                if (mb_strpos($get_param, $value) === false) {
                                    $check++;
                                }
                                break;
                        }
                    } else {
                        $check++;
                    }
                }
                if ($check === count($fields)) {
                    $rid_count = count($fields);
                    $ruleId = $rule;
                } else {
                    $rid_count = $ruleId = 0;
                }
            }
        }


        return $ruleId;
    }

    public function findRuleId($page_id = 0, $params = [], $first_params = [], $other_params = [])
    {
        if (!count($first_params)) {
            $copyparams = $params;
            $aliases = $this->fieldsAliases($page_id, 1);
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

        foreach ($page_aliases as $rule => $ralias) {
            $sort = $ralias['sort'];
            $base = $ralias['base'];
            unset($ralias['sort'], $ralias['base']);
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
                if (count($params_keys) !== count(array_keys($fields))) {
                    continue;
                }
                if (count(array_diff(array_keys($fields), $params_keys))) {
                    continue;
                }
            }
            if (count($fields) > $rid_count) {
                $check = 0;
                foreach ($fields as $alias => $row) {
                    if (!in_array($alias, $params_keys, true)) {
                        continue;
                    }
                    if ($row['where'] && $row['compare']) {
                        $value = $row['value'];
                        $values = explode($this->config['values_delimeter'], $value);
                        $get_param = $params[$alias];
                        switch ($row['compare']) { //Обратный механизм поиска
                            case 1:
                                if (in_array($get_param, $values, true)) {
                                    $check++;
                                }
                                break;
                            case 2:
                                if (!in_array($get_param, $values, true)) {
                                    $check++;
                                }
                                break;
                            case 3:
                                if ($get_param > $value) {
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
                            case 6:
                                if (strpos($get_param, $value) !== false) {
                                    $check++;
                                }
                                break;
                            case 7:
                                if (strpos($get_param, $value) === false) {
                                    $check++;
                                }
                                break;
                        }
                    } else {
                        $check++;
                    }
                }
                if ($check === count($fields)) {
                    $rid_count = count($fields);
                    $rule_id = $rule;
                } else {
                    $rid_count = $rule_id = 0;
                }
            }
        }


        return $rule_id;
    }


    /**
     * DEPRECATED METHOD
     *
     * @param  array  $params_keys
     * @param  int  $page_id
     *
     * @return int
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
                $q->innerJoin('sfFieldIds', 'sfFieldIds'.$key,
                    'sfFieldIds.multi_id = sfFieldIds'.$key.'.multi_id ');
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

    public function updateUrlTotal($url_id = 0, $total = 0)
    {
        if ($url_id && $url = $this->modx->getObject('sfUrls', $url_id)) {
            $url->set('total', $total);
            $url->set('editedon', strtotime(date('Y-m-d H:i:s')));
            $url->save();
        }
    }

    public function getRuleMeta(
        $params = [],
        $rule_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $original_params = []
    ) {
        $seo_system = ['id', 'field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'key'];
        $seo_array = [
            'title',
            'h1',
            'h2',
            'description',
            'introtext',
            'keywords',
            'text',
            'content',
            'link',
            'tpl',
            'introlength'
        ];
        $fields = $word_array = $aliases = $field_word = [];
        $meta = ['success' => false, 'diff' => array_merge($original_params, $params)];
        $countFields = $this->countRuleFields($rule_id);
        $diff_params = [];
        $check = 0;
        $link_id = 0;
        $has_slider = 0;

        $params_to_text = [];

        $sort_field_word = [];

        $fields_key = [];
        // если не нужно пересчитывать на странице с учётом гет параметра - то это закоментить, а ниже раскоментить
        $fields_keys = $this->getFieldsKey('alias');
        foreach ($fields_keys as $fk => $fks) {
            foreach ($fks as $alias => $field) {
                $fields_key[$alias] = $field;
            }
        }

        foreach ($params as $param => $input) {
            $q = $this->modx->newQuery('sfField');
            $q->where(['alias' => $param]);
            $q->select($this->modx->getSelectColumns('sfField', 'sfField', ''));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($field = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $field_id = $field['id'];
                    $alias = $field['alias'];
                    $fields[] = $field_id;
                    if ($field['slider']) {
                        $has_slider = 1;
                    }

                    if ($word = $this->getWordArray($input, $field_id, $field['slider'], $this->config['mfilterWords'],
                        $ajax)) {
                        $word_id = $word['id'];
                        foreach (array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                            if ($countFields === 1) {
                                $word_array[$tmp_key] = $tmp_array;
                            }
                            $word_array[str_replace('value', $alias, $tmp_key)] = $tmp_array;
                            $word_array[$alias.'_input'] = $word_array['input'];
                            $word_array[$alias.'_alias'] = $word_array['alias'];
                            $word_array[$alias.'_word'] = $word_id;
                            $word_array['m_'.$alias] = $word_array['m_'.$alias.'_i'];
                        }

                        $params_to_text[$param] = [
                            'word'  => $word_id,
                            'input' => $word['input'],
                            'value' => $word['value'],
                            'alias' => $word['alias'],
                            'field' => $word['field_id'],
                            'class' => $field['class'],
                            'key'   => $field['key']
                        ];
                        $aliases[$param] = $word['alias'];

                        $field_word[$field_id] = $word['id'];
                        $meta['success'] = true;
                        $meta['diff'] = [];
                    } else {
                        continue;
                    }

                    $q = $this->modx->newQuery('sfFieldIds');
                    $q->sortby('priority', 'ASC');
                    $q->where(['multi_id' => $rule_id, 'field_id' => $field_id]);
                    $q->select(['sfFieldIds.*']);
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $sort_field_word[$row['field_id']] = $row['priority'];
                            if ($row['where'] && $row['compare'] && $row['value']) {
                                $c = $this->modx->newQuery('sfDictionary');
                                $c->select(['sfDictionary.*']);
                                $c->where(['field_id' => $row['field_id'], 'active' => 1]);
                                // if($this->config['proMode'] && strpos($input,',') === false) {
                                // $c->where('1=1 AND FIND_IN_SET("' . str_replace(',','&',$input) . '",REPLACE(REPLACE(input,",","&"),"||",","))');
                                //  } else {
                                $c->where(['input' => $input]);
                                // }
                                $value = $row['value'];
                                $values = explode($this->config['values_delimeter'], $value);
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
                                    case 6:
                                        $c->where(['input:NOT LIKE' => '%'.$value.'%']);
                                        break;
                                    case 7:
                                        $c->where(['input:LIKE' => '%'.$value.'%']);
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
        }

        if (!$meta['success']) {
            return $meta;
        }


        if ($check) {
            //когда найдено слово в параметрах, которое подлежит к исключению
            $aliases = array_diff_key($aliases, $diff_params);
            $params = array_diff_key($params, $diff_params);
            $params_keys = array_keys($params);
            // if($rule_id = $this->findRule($params_keys,$page_id))
            if ($rule_id = $this->findRuleId($page_id, $params_keys)) {
                $meta = $this->getRuleMeta($params, $rule_id, $page_id, $ajax, $new, $original_params);
                $meta['diff'] = array_merge($meta['diff'], $diff_params);
                return $meta;
            }
        }


        $url_array = $this->multiUrl($aliases, $rule_id, $page_id, $ajax, $new, $field_word);

        if (isset($url_array['total'])) {
            $meta['old_total'] = $url_array['total'];
        }

        //синхронизация порядка с правилом
        asort($sort_field_word);
        foreach ($sort_field_word as $fid => $wid) {
            $sort_field_word[$fid] = $field_word[$fid];
        }
        if ($this->config['crumbsNested']) {
            if ($nested = $this->findNestedCrumbs($sort_field_word, $page_id, $url_array['id'])) {
                $meta['nested'] = [];
                foreach ($nested as $link) {
                    $link['url'] = $link['new_url'] ?: $link['old_url'];
                    $link['sflink'] = $link['link'];
                    $link['sfurl'] = $link['url'].$this->config['url_suffix'];
                    $meta['nested'][] = $link;
                }
                $meta['nested'] = $ajax
                    ? $meta['nested']
                    : $this->modx->toJSON($meta['nested']);
            }
        }


        if ($seo = $this->pdo->getArray('sfRule', ['id' => $rule_id, 'active' => 1])) {
            if (!empty($seo['count_parents']) || $seo['count_parents'] === '0' || $seo['count_parents'] === 0) {
                $parents = $seo['count_parents'];
            } else {
                $parents = $page_id;
            }

            if (!empty($this->config['pdopage_hash'])) {
                $meta['config'] = $this->getRuleCount($original_params, $fields_key, $parents, $seo['count_where'],
                    false, true);
                $meta['config']['parents'] = $parents;
            }

            if ($this->config['count_choose'] && $this->config['count_select']) {
                if ($min_max_array = $this->getRuleCount($original_params, $fields_key, $parents, $seo['count_where'],
                    1)) {
                    if (is_array($min_max_array)) {
                        $word_array = array_merge($min_max_array, $word_array);
                    }
                }
            } elseif ($this->config['count_childrens']) {
                $word_array['total'] = $this->getRuleCount($original_params, $fields_key, $parents,
                    $seo['count_where']);
            }

            $meta['total'] = $word_array['count'] = (int)$word_array['total'];

            if ($this->config['page_key']) {
                $word_array['page_number'] = $word_array[$this->config['page_key']] = $this->config['page_number'];
            }

            foreach (['id', 'page', 'page_id'] as $pkey) {
                if (!isset($word_array[$pkey])) {
                    $word_array[$pkey] = $page_id;
                }
            }

            $word_array['original_params'] = $params_to_text;
            if (!isset($word_array['params'])) {
                $word_array['params'] = $params_to_text;
            }

            $word_array = $this->prepareRow($word_array, $parents, $rule_id, $seo);

            if ($url_array['nourl']) {
                // $seo_array = $this->getPageMeta($page_id);
            } else {
                if ($url_array['custom']) {
                    $seo_array = array_intersect_key($url_array, array_flip($seo_array));
                } else {
                    $seo_array = array_intersect_key($seo, array_flip($seo_array));
                }
            }


            $meta['rule_id'] = $word_array['rule_id'] = $rule_id;
            $meta['url'] = $word_array['url'] = $url_array['url'];
            $meta['url_id'] = $meta['seo_id'] = $word_array['seo_id'] = $url_array['id'];
            $meta['link'] = $word_array['link'] = $url_array['link'];

            if (isset($url_array['createdon'])) {
                $meta['createdon'] = $url_array['createdon'];
                if ($meta['editedon'] === '0000-00-00 00:00:00') {
                    $meta['editedon'] = $url_array['createdon'];
                } else {
                    $meta['editedon'] = $url_array['editedon'];
                }
            }

            if ($this->config['proMode'] && $pageObject = $this->modx->getObject('modResource', $page_id)) {
                if (!isset($word_array['resource'])) {
                    $word_array['resource'] = $pageObject->toArray();
                }
                if (!isset($word_array['original_page'])) {
                    $word_array['original_page'] = $pageObject->toArray();
                }
            }


            foreach ($seo_array as $tag => $text) {
                if ($text) {
                    if (strpos($text, '@INLINE') !== false) {
                        $tpl = $text;
                    } else {
                        $tpl = '@INLINE '.$text;
                    }
                    $meta[$tag] = $this->pdo->getChunk($tpl, $word_array);
                }
            }

            if (!empty($url_array['tpl'])) {
                $meta['tpl'] = $url_array['tpl'];
            } elseif (!empty($seo['tpl'])) {
                $meta['tpl'] = $seo['tpl'];
            }

            if (!empty($url_array['introlength'])) {
                $meta['introlength'] = $url_array['introlength'];
            } elseif (!empty($seo['introlength'])) {
                $meta['introlength'] = $seo['introlength'];
            }

            foreach (['properties', 'introtexts'] as $prop) {
                $seo_values = [];
                if (!empty($url_array[$prop]) && !empty($url_array[$prop]['values'])) {
                    $seo_values = $url_array[$prop]['values'];
                } elseif (!empty($seo[$prop]) && !empty($seo[$prop]['values'])) {
                    $seo_values = $seo[$prop]['values'];
                }
                if (!empty($seo_values)) {
                    $properties = [];
                    $array_word = [];
                    foreach ($word_array as $key => $val) {
                        $array_word['$'.$key] = "'".$val."'";
                    }
                    uksort($array_word, function ($a, $b) {
                        if (strlen($a) == strlen($b)) {
                            return 0;
                        } else {
                            return strlen($a) < strlen($b);
                        }
                    });
                    foreach ($seo_values as $value) {
                        $properties[] = str_replace(array_keys($array_word), array_values($array_word), $value);
                    }
                    $meta[$prop] = $properties;
                }
            }
        }
        $diff = [];
        if (!empty($url_array['diff'])) {
            foreach ($url_array['diff'] as $param => $alias) {
                if ($diff_arr = $this->pdo->getArray('sfDictionary', ['alias' => $alias, 'active' => 1])) {
                    $diff[$param] = $diff_arr['input'];
                }
            }
            $meta['diff'] = array_merge($meta['diff'], $diff);
        }

        if (isset($meta['title'])) {
            $meta['pagetitle'] = $meta['title'];
        }

        $meta['page_id'] = $page_id;
        $meta['has_slider'] = $has_slider;
        $meta['params'] = $params;

        return $meta;
    }


    public function getRuleCount(
        $params = [],
        $fields_key = [],
        $parents = '',
        $count_where = [],
        $min_max = 0,
        $returnConfig = false
    ) {
        $this->loadHandler();
        return $this->countHandler->countByParams($params, $fields_key, $parents, $count_where, '', $min_max,
            $returnConfig);
    }

    /***
     * Deprecated method
     ***/
    public function _getRuleCount(
        $params = [],
        $fields_key = [],
        $parents,
        $count_where = [],
        $min_max = 0
    ) {
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
                $fields_key[$alias]['xpdo_package'] = $field['xpdo_package'];
            }
        }

        //        if(count(array_diff(array_keys($params), array_keys($fields_key)))) {
        //            $this->modx->log(modx::LOG_LEVEL_ERROR,"[SeoFilter] doesn't known this fields. Please add this fields to the first tab in component (Fields)".print_r(array_diff(array_keys($params), array_keys($fields_key)),1));
        //        }


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
                        $fields_where[$fw.':>='] = $slider[0];
                        if ($slider[1]) {
                            $fields_where[$fw.':<='] = $slider[1];
                        }
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
                        $this->pdo->setConfig(['loadModels' => 'tvsuperselect']);
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

        //        $this->modx->log(modx::LOG_LEVEL_ERROR,print_r($where,1));
        //        $this->modx->log(modx::LOG_LEVEL_ERROR,print_r($innerJoin,1));

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
                if (isset($run[0]['count'])) {
                    $count = $run[0]['count'];
                }
            }
            return $count;
        }
    }

    public function getWordArray($input = '', $field_id = 0, $slider = 0, $allow_new = 0, $ajax = 0)
    {
        $word = [];
        $q = $this->modx->newQuery('sfDictionary');
        if ($slider) {
            if (strpos($input, $this->config['values_delimeter']) === false) {
                $values = [$input, $input];
            } else {
                $values = array_map('trim', explode($this->config['values_delimeter'], $input));
            }
            $q->where(['field_id' => $field_id, 'active' => 1]);
            $q->limit(0);
            $q->select(['sfDictionary.*']);
            $qq = clone($q);
            $qq_count = 0;
            if ($values[0] === $values[1]) {
                $qq->andCondition("(`sfDictionary`.`input` = '{$input}' OR `sfDictionary`.`input` = '{$values[0]}')");
                $qq->limit(1);
                $qq_count = $this->modx->getCount('sfDictionary', $qq);
            }
            if ($qq_count) {
                if ($qq->prepare() && $qq->stmt->execute()) {
                    $word = $qq->stmt->fetch(PDO::FETCH_ASSOC);
                }
            } elseif ($this->modx->getCount('sfDictionary', $q)) {
                if ($q->prepare() && $q->stmt->execute()) {
                    $min_diff = 0;
                    $min_diff_word = [];
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $i_values = array_map('trim', explode($this->config['values_delimeter'], $row['input']));
                        if ($values[0] >= $i_values[0] && $values[1] <= $i_values[1]) {
                            $diff = ($values[0] - $i_values[0]) + ($i_values[1] - $values[1]);
                            if (empty($min_diff_word)) {
                                $min_diff_word = $row;
                                $min_diff = $diff;
                            } elseif ($diff < $min_diff) {
                                $min_diff_word = $row;
                                $min_diff = $diff;
                            }
                            //                            $word = $row;
                            //                            break;
                        }
                    }
                    $word = $min_diff_word;
                }
            }
        } else {
            $q->limit(1);
            $q->where(['field_id' => $field_id, 'active' => 1]);
            if ($this->config['proMode'] && strpos($input, ',') === false && $ajax) {
                $q->where('1=1 AND FIND_IN_SET("'.str_replace(',', '&',
                        $input).'",REPLACE(REPLACE(input,",","&"),"||",","))');
            } else {
                $q->where(['input' => $input]);
            }
            if ($this->modx->getCount('sfDictionary', $q)) {
                $q->select(['sfDictionary.*']);
                if ($q->prepare() && $q->stmt->execute()) {
                    $word = $q->stmt->fetch(PDO::FETCH_ASSOC);
                }
            } else {
                if ($allow_new && $field = $this->modx->getObject('sfField', $field_id)) {
                    /*** @var sfField $field */
                    if ($input && $value = $field->getValueByInput($input)) {
                        $relation_id = $relation_value = '';
                        if (is_array($value)) {
                            $relation_value = $value['relation'];
                            $value = $value['value'];
                        }
                        $processorProps = [
                            'class'    => $field->get('class'),
                            'key'      => $field->get('key'),
                            'field_id' => $field->get('id'),
                            'value'    => $value,
                            'input'    => $input,
                        ];

                        if ($relation_value) {
                            $relation_field = $field->get('relation_field');
                            $s = $this->modx->newQuery('sfDictionary');
                            $s->where(['input' => $relation_value, 'field_id' => $relation_field, 'active' => 1]);
                            $s->select('id');
                            $relation_id = $this->modx->getValue($s->prepare());
                        }

                        if ($relation_id) {
                            $processorProps['relation_word'] = $relation_id;
                        }

                        $otherProps = ['processors_path' => $this->config['processorsPath']];
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.print_r($response->response, 1));
                            $this->modx->error->reset();
                        } else {
                            $word = $response->response['object'];
                        }
                    }
                } else {
                    return false;
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
            'keywords'    => $this->config['keywords'],
            'h1'          => $this->config['h1'],
            'h2'          => $this->config['h2'],
            'text'        => $this->config['text'],
            'content'     => $this->config['content'],
            'link'        => $this->config['link'],
        ];
        $meta = [];
        $meta['page_id'] = $meta['id'] = $page_id;

        if ($page = $this->modx->getObject('modResource', $page_id)) {
            $page_arr = $page->toArray();
            $page_keys = array_keys($page_arr);

            $meta = array_merge($meta, $page_arr);
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
                if ($tagname) {
                    if (strpos($tagname, '@INLINE') !== false) {
                        $tpl = $tagname;
                    } elseif (strpos($tagname, '[') !== false || strpos($tagname, '{') !== false) {
                        $tpl = '@INLINE '.$tagname;
                    } else {
                        $tpl = '@INLINE '.$page->get($tagname);
                    }
                    $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
                }
            }

            if (empty($page_arr['isfolder'])) {
                $q = $this->modx->newQuery('modContentType', ['name' => 'HTML']);
                $q->select('file_extensions');
                $this->config['this_page_suffix'] = $this->modx->getValue($q->prepare());
            }
        }

        if (isset($meta['title'])) {
            $meta['pagetitle'] = $meta['title'];
        }

        return $meta;
    }

    public function prepareRow($row = [], $page_id = 0, $rule_id = 0, $rule = [])
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
                'input'     => !empty($row['input']) ? $row['input'] : 0,
                'seoFilter' => $this,
                'SeoFilter' => $this,
                'pdoTools'  => $this->pdo,
                'pdoFetch'  => $this->pdo,
                'rule_id'   => $rule_id,
                'page_id'   => $page_id,
                'rule'      => serialize($rule)
            ]));
            $tmp = (strpos($tmp, '[') === 0 || strpos($tmp, '{') === 0)
                ? json_decode($tmp, true)
                : unserialize($tmp, ['allowed_classes' => false]);
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

    public function getParamsByUrl($url, $pageId)
    {
        $params = [];

        $q = $this->modx->newQuery('sfUrls');
        $q->where(['old_url' => $url, 'OR:new_url:=' => $url]);
        $q->where(['page_id' => $pageId]);
        $q->innerJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
        $q->innerJoin('sfField', 'Field', 'Field.id = sfUrlWord.field_id');
        $q->innerJoin('sfDictionary', 'Word', 'Word.id = sfUrlWord.word_id AND Word.active = 1');
        $q->sortby('sfUrlWord.priority', 'ASC');
        $q->groupby('sfUrlWord.id');
        $q->select([
            'sfUrlWord.*',
            'sfUrls.multi_id as rule_id,sfUrls.page_id as page_id',
            'Field.class as field_class,Field.key as field_key,Field.alias as field_alias',
            'Word.input as word_input,Word.value as word_value,Word.alias as word_alias'

        ]);

        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $params[] = $row;
            }
        }


        return $params;
    }

    public function findUrlId($url = '')
    {
        $url_id = 0;
        if ($url) {
            $q = $this->modx->newQuery('sfUrls');
            $q->where(['old_url' => $url, 'OR:new_url:=' => $url]);
            $q->select('id');
            $url_id = $this->modx->getValue($q->prepare());
        }
        return $url_id;
    }

    public function findUrlArray($url = '', $page = 0)
    {
        $url_array = [];
        $q = $this->modx->newQuery('sfUrls');
        $q->where(['page_id' => $page]);
        $q->where(['old_url:LIKE' => $url, 'OR:new_url:LIKE' => $url]);
        $q->select($this->modx->getSelectColumns('sfUrls', 'sfUrls'));
        $q->limit(1);
        if ($q->prepare() && $q->stmt->execute()) {
            $url_array = $q->stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $url_array;
    }

    public function newUrl(
        $old_url = '',
        $multi_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $field_word = [],
        $link_tpl = ''
    ) {
        $seo_system = ['field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'createdon', 'key'];
        $url = [];
        if ($ajax) {
            $new = $ajax;
        }
        $link = '';
        if (!empty($link_tpl)) {
            $all_words = [];
            $words = [];
            foreach ($field_word as $fw) {
                $words[] = $fw['word_id'];
            }
            $q = $this->modx->newQuery('sfDictionary');
            $q->innerJoin('sfField', 'Field', 'Field.id = sfDictionary.field_id');
            $q->where(['id:IN' => $words, 'active' => 1]);
            $q->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', '', $seo_system, 1));
            $q->select($this->modx->getSelectColumns('sfField', 'Field', 'field_', ['alias']));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $all_words = array_merge($all_words,
                        $this->prepareWordsToLink($row, ['alias' => $row['field_alias']], count($all_words)));
                }
            }

            foreach (['id', 'page', 'page_id'] as $pkey) {
                if (!isset($all_words[$pkey])) {
                    $all_words[$pkey] = $page_id;
                }
            }

            $link = $this->pdo->getChunk('@INLINE '.$link_tpl, $all_words);
        }

        $processorProps = [
            'old_url'    => $old_url,
            'multi_id'   => $multi_id,
            'page_id'    => $page_id,
            'ajax'       => $ajax,
            'count'      => $new,
            'field_word' => $field_word,
            'link'       => $link
        ];

        $otherProps = ['processors_path' => $this->config['corePath'].'processors/'];
        $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]: '.print_r($response->getMessage(), 1));
            $this->modx->error->reset();
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
     *
     * @param  string  $value
     * @param  array  $field
     *
     * @return string
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

    public function multiUrl(
        $aliases = [],
        $multi_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $field_word = []
    ) {
        $url = [];
        if ($multi_id) {
            if ($rule = $this->pdo->getArray('sfRule', ['id' => $multi_id, 'active' => 1])) {
                $link_tpl = $rule['link_tpl'];
                // $tpl = '@INLINE ' . $rule['url'];
                // $url['url'] = $this->pdo->getChunk($tpl, $aliases);
                // $url_link = $this->pdo->getChunk($tpl, $aliases);
                $url_link = $rule['url'];
                foreach ($aliases as $key => $value) {
                    $url_link = str_replace('{$'.$key.'}', $value, $url_link);
                }
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
                    foreach ($field_word as $field_id => $word_id) {
                        $field_words[] = ['field_id' => $field_id, 'word_id' => $word_id];
                    }

                    $url = $this->newUrl($url_link, $multi_id, $page_id, $ajax, $new, $field_words, $link_tpl);
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

    public function checkStat()
    {
        $key = strtolower(__CLASS__);
        /** @var modDbRegister $registry */
        $registry = $this->modx->getService('registry', 'registry.modRegistry')
            ->getRegister('user', 'registry.modDbRegister');
        $registry->connect();
        $registry->subscribe('/modstore/'.md5($key));
        if ($res = $registry->read(['poll_limit' => 1, 'remove_read' => false])) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', ['service_url:LIKE' => '%modstore%']);
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', [
            'baseUrl'        => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout'        => 1,
            'connectTimeout' => 1,
        ]);

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $response = $rest->post('stat', [
                'package'            => $key,
                'version'            => $this->version,
                'keys'               => $c->prepare() && $c->stmt->execute()
                    ? $c->stmt->fetchAll(PDO::FETCH_ASSOC)
                    : [],
                'uuid'               => $this->modx->uuid,
                'database'           => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'].'-'.$this->modx->version['full_version'],
                'supports'           => $this->modx->version['code_name'].'-'.$this->modx->version['full_version'],
                'http_host'          => $this->modx->getOption('http_host'),
                'php_version'        => XPDO_PHP_VERSION,
                'language'           => $this->modx->getOption('manager_language'),
            ]);
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', [md5($key) => true], ['ttl' => 3600 * 24]);
    }

    public function createLinks($rule_id = 0, $urls = [], $where = [], $update = 0)
    {
        $otherProps = ['processors_path' => $this->config['processorsPath']];

        $old_links = [];
        $find_links = [];
        $new_links = [];
        $doubles = 0;
        $success_create = 0;
        $success_delete = 0;

        $old_links_ids = [];

        $q = $this->modx->newQuery('sfUrls');
        $q->where(['multi_id' => $rule_id]);
        if ($where) {
            foreach ($where as $field_id => $w) {
                $q->rightJoin('sfUrlWord', 'sfUrlWord', 'sfUrls.id = sfUrlWord.url_id');
                $q->where(['sfUrlWord.word_id' => $w['id']]);
                $q->groupby('sfUrls.id');
                $q->select([
                    'sfUrls.id,sfUrls.old_url as url,sfUrls.link as name,sfUrls.page_id as page'
                ]);
            }
        } else {
            $q->select('id,old_url as url,link as name,page_id as page');
        }

        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $old_links[$row['page']][$row['url']] = $row;
                $old_links_ids[$row['id']] = $row;
            }
        }


        $find_links_ids = [];
        foreach ($urls as $url) {
            if (isset($old_links[$url['page']][$url['url']])) {
                $find_links[$old_links[$url['page']][$url['url']]['id']] = $url;
                $find_links_ids[] = $old_links[$url['page']][$url['url']]['id'];
                continue;
            } else {
                $new_links[] = $url;
            }
        }


        $find_links_ids = array_unique($find_links_ids);


        $del_links = array_diff(array_keys($old_links_ids), $find_links_ids);
        if (!empty($del_links)) {
            $processorProps = [
                'ids'    => $this->modx->toJSON($del_links),
                'to_log' => 1
            ];
            $response = $this->modx->runProcessor('mgr/urls/remove', $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '.$response->getMessage());
                $this->modx->error->reset();
            } else {
                $success_delete = count($del_links);
            }
        }


        if (!empty($new_links)) {
            foreach ($new_links as $url) {
                $processorProps = [
                    'multi_id'   => $rule_id,
                    'old_url'    => $url['url'],
                    'page_id'    => $url['page'],
                    'link'       => $url['name'],
                    'field_word' => $url['relation'],
                    'from_rule'  => 1,
                ];
                $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                if ($response->isError()) {
                    if (in_array('double', $response->response['errors'], true)) {
                        $doubles++;
                        //                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                    }
                    //                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                    $this->modx->error->reset();
                } else {
                    //                    $this->modx->log(1,print_r($response['object'],1));
                    //                    $find_links_ids[] = $response['object']['id'];
                    $success_create++;
                }
            }
        }

        $update_links = 0;
        if (!empty($find_links)) {
            $q = $this->modx->newQuery('sfUrls');
            if ($update) {
                $q->where(['custom:!=' => 1, 'id:IN' => array_keys($find_links)]);
            } else {
                $q->where(['link' => '', 'id:IN' => array_keys($find_links)]);
            }
            $links = $this->modx->getIterator('sfUrls', $q);
            foreach ($links as $link) {
                $new_name = $find_links[$link->get('id')]['name'];
                if ($link->get('link') != $new_name) {
                    $update_links++;
                    $link->set('link', $new_name);
                    $link->set('editedon', strtotime(date('Y-m-d H:i:s')));
                    $link->save();
                }
            }
        }

        $all_links = count($find_links_ids) + $success_create;

        return [
            'was_links'     => count($old_links_ids), //было ссылок в правиле
            'old_links'     => count($find_links_ids), //ссылок старых осталось
            'add_links'     => $success_create, //новых ссылок добавлено
            'remove_links'  => $success_delete, //удалено ссылок из правило
            'doubles_links' => $doubles, //дубли, ссылок в других правилах
            'update_links'  => $update_links, //обновлено названий ссылок
            'all_links'     => $all_links //всего ссылок стало в правиле
        ];
    }

    public function generateUrlsByWord($word = [], $update = 0)
    {
        $response = [];
        if ($field_id = $word['field_id']) {
            $q = $this->modx->newQuery('sfFieldIds');
            $q->innerJoin('sfRule', 'Rule', 'Rule.id = sfFieldIds.multi_id');
            $q->where(['field_id' => $field_id]);
            //$q->select($this->modx->getSelectColumns('sfFieldIds','sfFieldIds','link_'));
            $q->select($this->modx->getSelectColumns('sfRule', 'Rule', ''));
            $rules = [];
            if ($q->prepare() && $q->stmt->execute()) {
                while ($rule = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rules[] = $rule;
                    $where = [$field_id => ['id' => $word['id']]];
                    if ($this->config['proMode']) {
                        $pages = $rule['pages'];
                        if (empty($pages)) {
                            $pages = $rule['page'];
                        }
                    } else {
                        $pages = $rule['page'];
                    }
                    $response[$rule['id']] = $this->generateUrls($rule['id'], $pages, $rule['link_tpl'], $rule['url'],
                        $where, $update);
                }
            }
        }
        return $response;
    }

    public function generateUrls(
        $rule_id = 0,
        $pages = '',
        $tpl = '',
        $url_mask = '',
        $where = [],
        $update = 0,
        $offset = 0
    ) {
        $pages = array_map('trim', explode(',', $pages));
        $urls = $response = [];

        $limit = (int)$this->modx->getOption('seofilter_url_limit', null, 500, true);
        $total = 0;

        if ($links = $this->gettingUrls($rule_id, $where)) {
            $pageData = [];
            $q = $this->modx->newQuery('modResource');
            $q->select($this->modx->getSelectColumns('modResource', 'modResource'));
            $q->where(['id:IN' => $pages]);
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $pageData[$row['id']] = $row;
                }
            }

            foreach ($pages as $page_id) {
                $total = count($links['words']) * count($pages);
                foreach ($links['words'] as $index => $link) {
                    if (empty($link['urls'])) {
                        continue;
                    }

                    $words = [];
                    foreach ($link['relation'] as $relation) {
                        $words = array_merge($words,
                            $links['fields'][$relation['field_id']]['words'][$relation['word_id']]);
                    }

                    if ($this->config['edit_url_mask']) {
                        $aliases = [];
                        foreach ($link['aliases'] as $alias => $value) {
                            $aliases['{$'.$alias.'}'] = $value;
                        }
                        $link_url = str_replace(array_keys($aliases), array_values($aliases), $url_mask);
                    } else {
                        $link_url = implode($this->config['level_separator'], $link['urls']);
                    }

                    foreach (['id', 'page', 'page_id'] as $pkey) {
                        if (!isset($words[$pkey])) {
                            $words[$pkey] = $page_id;
                        }
                    }

                    if (!isset($words['resource']) && isset($pageData[$page_id])) {
                        $words['resource'] = $pageData[$page_id];
                    }

                    $link_name = '';
                    if (!empty($tpl)) {
                        $link_name = $this->pdo->getChunk('@INLINE '.$tpl, $words);
                    }
                    unset($words);

                    $urls[] = [
                        'url'      => $link_url,
                        'name'     => $link_name,
                        'page'     => $page_id,
                        'relation' => $link['relation']
                    ];
                }
            }

            $response = $this->createLinks($rule_id, $urls, $where, $update);
        }
        $offset += $limit;
        if ($offset >= $total) {
            $percent = 1;
            $done = true;
        } else {
            $done = false;
            $percent = $offset / $total;
        }
        $response['data'] = [
            'done'   => $done,
            'limit'  => $limit,
            'offset' => $offset,
            'total'  => $total,
            'value'  => $percent,
            'text'   => "{$offset}/{$total}"
        ];

        return $response;
    }

    public function prepareWordsToLink($row = [], $field = [], $count_fields = 1)
    {
        $word = [];
        if (empty($field['alias']) && empty($row['field_alias'])) {
            return $word;
        }
        if (!empty($field['alias'])) {
            $alias = $field['alias'];
        } else {
            $alias = $row['field_alias'];
        }

        foreach ($row as $key => $val) {
            if ($key == 'id') {
                $word[$alias.'_id'] = $val;
                continue;
            }
            if ($count_fields == 1) {
                $word[$key] = $val;
            }
            $word[str_replace('value', $alias, $key)] = $val;
        }
        $word[$alias.'_input'] = $row['input'];
        $word[$alias.'_image'] = $row['image'];
        $word[$alias.'_alias'] = $row['alias'];
        $word['m_'.$alias] = $row['m_value_i'];

        return $word;
    }


    public function gettingUrls($rule_id = 0, $where = [])
    {
        if (!$rule_id) {
            return false;
        }
        $seo_system = ['field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'createdon', 'key'];
        $fields = [];
        $words = [];

        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(['multi_id' => $rule_id]);
        $q->sortby('priority', 'ASC');
        $q->innerJoin('sfField', 'Field', 'Field.id = sfFieldIds.field_id');
        $q->select($this->modx->getSelectColumns('sfFieldIds', 'sfFieldIds', ''));
        $q->select($this->modx->getSelectColumns('sfField', 'Field', 'field_'));
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $fields[$row['field_id']] = $row;
            }
        }

        if (empty($fields)) {
            return false;
        }

        foreach ($fields as $field_id => &$field) {
            $alias = $field['field_alias'];
            $q = $this->modx->newQuery('sfDictionary');
            $q->where(['field_id' => $field_id, 'active' => 1]);
            if (isset($where[$field_id])) {
                $q->where($where[$field_id]);
            }
            if ($field['where'] && $field['compare']) {
                $q->where($this->fieldWhere($field['compare'], $field['value']));
            }
            $q->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', '', $seo_system, 1));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $word = $this->prepareWordsToLink($row, ['alias' => $alias], count($fields));

                    if ($field['field_hideparam']) {
                        $word['url_part'] = $row['alias'];
                    } elseif ($field['field_valuefirst']) {
                        $word['url_part'] = $row['alias'].$this->config['separator'].$alias;
                    } else {
                        $word['url_part'] = $alias.$this->config['separator'].$row['alias'];
                    }

                    $forMulti = [
                        'urls'           => [
                            $word['url_part']
                        ],
                        'aliases'        => [
                            $alias => $row['alias'],
                        ],
                        //                        'words' => array(array('id'=>$row['id'])),
                        'field_relation' => $field['field_relation_field'],
                        'word_relation'  => $row['relation_word'],
                        'relation'       => [
                            [
                                'field_id'       => $field_id,
                                'word_id'        => $row['id'],
                                'field_relation' => $field['field_relation_field'],
                                'word_relation'  => $row['relation_word'],
                            ]
                        ],
                        'delete'         => 0
                    ];

                    $field['words'][$row['id']] = $word;
                    $words[$field_id][] = $forMulti;
                }
            }
        }

        $words1 = array_shift($words);
        foreach ($words as $words2) {
            $words1 = $this->wordsMultiplication($words1, $words2);
        }

        foreach ($words1 as $key => $words) {
            if ($words['delete']) {
                unset($words1[$key]);
                continue;
            }
        }

        return ['words' => array_values($words1), 'fields' => $fields];
    }


    public function wordsMultiplication(&$a1 = [], &$a2 = [])
    {
        $a3 = [];
        for ($i = 0, $iMax = count($a1); $i < $iMax; $i++) {
            for ($j = 0, $jMax = count($a2); $j < $jMax; $j++) {
                $delete = $a1[$i]['delete'] || $a2[$j]['delete'];

                $arr = [
                    'urls'     => array_merge($a1[$i]['urls'], $a2[$j]['urls']),
                    'aliases'  => array_merge($a1[$i]['aliases'], $a2[$j]['aliases']),
                    'relation' => array_merge($a1[$i]['relation'], $a2[$j]['relation']),
                    'delete'   => $delete
                ];
                $find = 1;
                if ($a2[$j]['field_relation'] && $a2[$j]['word_relation']) {
                    $find = 0;
                    foreach ($a1[$i]['relation'] as $relation) {
                        if ($relation['field_id'] === $a2[$j]['field_relation']
                            && $relation['word_id'] === $a2[$j]['word_relation']) {
                            $find = 1;
                            break;
                        }
                    }
                }
                if (!$find) {
                    $arr['delete'] = 1;
                }
                $a3[] = $arr;
                unset($arr);
            }
        }

        return $a3;
    }

    public function fieldWhere($compare = 0, $value = '', $param = 'input')
    {
        $where = [];
        $values = array_map('trim', explode(',', $value));
        switch ($compare) {
            case 1:
                // в массиве
                $where = ['input:IN' => $values];
                break;
            case 2:
                // не в массиве
                $where = ['input:NOT IN' => $values];
                break;
            case 3:
                // больше чем
                $where = ['input:>' => $value];
                break;
            case 4:
                // меньше чем
                $where = ['input:<' => $value];
                break;
            case 5:
                // в диапазоне
                if (count($values) >= 2) {
                    $where = ['input:>' => $values[0], 'AND:input:<' => $values[1]];
                }
                break;
            case 6:
                //LIKE %value%
                $where = ['input:LIKE' => '%'.$value.'%'];
                break;
            case 7:
                //NOT LIKE %value%
                $where = ['input:NOT LIKE' => '%'.$value.'%'];
                break;
        }
        return $where;
    }

    /**
     * Основной метод компонента, который по URL находит SEO-страницу
     *
     * @param  string  $requestUrl
     */
    public function processUrl($requestUrl)
    {
        if (!$request = $this->prepareUrl($requestUrl)) {
            return;
        }

        $this->superMethod($request);

        // if (!$page = $this->_findPageId($request)) {
        //     return;
        // }
        // if (!$page = $this->_findPageId($request)) {
        //     return;
        // }
    }

    /**
     * Небольшая подготовка адреса
     *
     * @param  string  $request
     *
     * @return string
     */
    protected function prepareUrl($request)
    {
        if (!empty($this->config['url_suffix'])) {
            $urlSuffix = $this->config['url_suffix'];
            if (mb_strpos($request, $urlSuffix, mb_strlen($request) - mb_strlen($urlSuffix)) !== false) {
                $request = mb_substr($request, 0, -mb_strlen($urlSuffix));
            }
        }
        return trim($request, '/');
    }

    /**
     * Поиск ID страницы, привязанной к SEO-правилам по URL части
     *
     * @param  string  $request
     *
     * @return int|null
     */
    protected function _findPageId($request)
    {
        $seoPageId = null;

        if (!$pageIds = $this->getPageIdsFromRules()) {
            return null;
        }
        if (!$pages = $this->getPagesFromRules()) {
            return null;
        }
        $this->modx->log(1, print_r($pages, 1));


        return $seoPageId;
    }


    /**
     * Новый метод.Получение основных данных по страницам, привязанным к правилам
     *
     * @return array|null
     */
    protected function getPagesFromRules()
    {
        $pages = [];

        if (!$pageIds = $this->getPageIdsFromRules()) {
            return null;
        }

        $contextKey = $this->modx->getOption('seofilter_catalog_context', null, $this->modx->context->key);
        if (($q = $this->modx->newQuery('modResource')
                ->where([
                    'id:IN'       => $pageIds,
                    'deleted'     => false,
                    'published'   => true,
                    'context_key' => $contextKey// TODO: вроде как-лишнее. Достаточно сортировки по контексту
                ])
                ->select($this->modx->getSelectColumns('modResource', 'modResource', '',
                    ['id', 'alias', 'uri', 'uri_override', 'context_key']))
                ->sortby("context_key = '{$contextKey}'", 'DESC')
                ->prepare())
            && $q->execute()) {
            while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                $url = $this->clearSuffixes($row['uri']);
                if ($row['uri_override']) {
                    $url = explode('/', $url);
                    $row['url'] = array_pop($url);
                } elseif ((int)$row['id'] === (int)$this->config['site_start']) {
                    $row['url'] = '';
                } else {
                    $row['url'] = $url;
                }

                $pages[$row['id']] = $row;
            }
        }

        return $pages;
    }

    /**
     * Новый метод. Получение ID всех страниц, привязанных к правилам
     *
     * @return array
     */
    protected function getPageIdsFromRules()
    {
        $pageIds = [];
        if (($q = $this->modx->newQuery('sfRule')
                ->where(['active' => true])
                ->select($this->modx->getSelectColumns('sfRule', 'sfRule', '', ['page', 'pages']))
                ->groupby('page,pages')
                ->prepare())
            && $q->execute()) {
            while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                if ($this->config['proMode'] && !empty($row['pages'])) {
                    foreach (array_map('trim', explode(',', $row['pages'])) as $pageId) {
                        if (!in_array((int)$pageId, $pageIds, true)) {
                            $pageIds[] = (int)$pageId;
                        }
                    }
                } elseif (!in_array((int)$row['page'], $pageIds, true)) {
                    $pageIds[] = (int)$row['page'];
                }
            }
        }
        return $pageIds;
    }

    public function superMethod($request)
    {
        $container_suffix = $this->config['container_suffix'];
        $url_suffix = $this->config['url_suffix'];
        $url_redirect = $this->config['redirect'];

        $base_get = array_map('trim', explode(',', $this->config['base_get']));
        $separator = $this->config['separator'];
        $params = []; //итоговый массив с параметром и значением
        $last_char = ''; //был ли в конце url-а слэш
        //если используете контексты, то переключить должны до события onPageNotFound
        //если же каталог находится строго в другом контексте, то можете добавить настройку и прописать туда свой контекст

        $between_urls = $this->config['between_urls'];
        $site_start = $this->config['site_start'];
        $page = $fast_search = 0; //переменные для проверки
        //если используете контексты, то переключить должны до события onPageNotFound
        $ctx = $this->modx->getOption('seofilter_catalog_context', null, $this->modx->context->key);
        //если же каталог находится строго в другом контексте, то можете добавить настройку и прописать туда свой контекст

        $check_doubles = false;
        $uris = $aliases = [];

        $q = $this->modx->newQuery('sfRule');
        $q->where(['active' => 1]);
        $q->select(['sfRule.*']);
        $all_pages = [];
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($this->config['proMode']) {
                    $pages = $row['pages'];
                    if (empty($pages)) {
                        $pages = $row['page'];
                    }
                } else {
                    $pages = $row['page'];
                    if (empty($pages) && !empty($row['pages'])) {
                        $pages = $row['pages'];
                    }
                }
                $pages = array_map('trim', explode(',', $pages));
                foreach ($pages as $linkedPageId) {
                    $all_pages[] = $linkedPageId;
                }
            }
        }
        $all_pages = array_unique($all_pages);
        if (empty($all_pages)) {
            return null;
        }
        $q = $this->modx->newQuery('modResource');
        $q->where([
            'id:IN'       => $all_pages,
            'deleted'     => 0,
            'published'   => 1,
            'context_key' => $ctx
        ]);
        $q->select([
            'modResource.id,modResource.alias,modResource.uri,modResource.uri_override'
        ]);
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $uri = $this->clearSuffixes($row['uri']);
                if ((int)$row['id'] === (int)$site_start) {
                    $uri = '';
                }

                $uris[$row['id']] = array_reverse(explode('/', $uri), 1);
                //переворот для удобства поиска

                $alias = $row['alias'];
                if ($row['uri_override']) {
                    //если url заморожен
                    $uri = explode('/', $uri);
                    $alias = array_pop($uri);
                }

                if (in_array($alias, $aliases, true)) {
                    $check_doubles = true;
                }
                $aliases[$row['id']] = $alias;
            }
        }

        //обязательная сортировка массива по количеству внутренних алиасов
        uasort($uris, function ($a, $b) {
            if (count($a) === count($b)) {
                return 0;
            }
            return (count($a) > count($b)) ? -1 : 1;
        });

        $tmp = explode($between_urls, $request);
        $r_tmp = array_reverse($tmp, 1); //перевёрнутый запрос


        if ($between_urls !== '/') {
            //if all links in the first level
            $page = 0;
            $remaining_part = '';
            foreach ($uris as $page_id => $uri_arr) {
                $uri_part = implode('/', array_reverse($uri_arr, 1));
                if (strpos($request, $uri_part) === 0) {
                    $page = $page_id;
                    $remaining_part = trim(str_replace($uri_part, '', $request), $between_urls);
                    break;
                }
            }

            if ($page && $remaining_part) {
                //we found one page
                $tmp = explode('/', $remaining_part);
            } elseif (array_key_exists($site_start, $aliases)) {
                $page = $site_start;
                if ($this->config['main_alias']) {
                    $upart = $aliases[$page].$between_urls;
                    if (strpos($request, $upart) === 0) {
                        $tmp = explode('/', substr($request, strlen($upart)));
                    } else {
                        return null;
                    }
                } else {
                    $tmp = explode('/', $request);
                }
            }
        } elseif ($check_doubles) {
            //если есть дубли синонимов
            foreach ($uris as $page_id => $uri_arr) {
                $need_count = count($uri_arr); //сколько совпадений подряд нужно
                $uri_count = 0; //количество совпадений
                $pos_count = false; //позиция, на которой произошло сопадение
                $check_break = false; //проверка, чтобы в разнобой не пошло
                foreach ($r_tmp as $t_key => $t_alias) {
                    foreach ($uri_arr as $u_key => $uri) {
                        if ($uri === $t_alias) {
                            if ($pos_count !== false) {
                                if ($pos_count - $uri_count !== $t_key) {
                                    $check_break = true;
                                    break;
                                }
                            } else {
                                $pos_count = $t_key;
                            }
                            $uri_count++;
                            break; //выходим из перебора uri для текущего alias-а в адресе
                        }
                    }
                    if ($check_break) {
                        break;
                    }
                }
                if ($need_count === $uri_count) {
                    //ссылка найдена
                    $page = $page_id;
                    $tmp = array_slice($tmp, ++$pos_count);
                    break;
                }
            }
        } else {
            //простой механизм поиска
            $tmp_id = 0;

            foreach ($r_tmp as $t_key => $t_alias) {
                foreach ($aliases as $pageId => $pageAlias) {
                    if (strpos($pageAlias, '/') !== false && strpos($pageAlias, $t_alias) === 0) {
                        $pageAliases = explode('/', $pageAlias);
                        $findPage = true;
                        foreach ($pageAliases as $pk => $pAlias) {
                            if ($r_tmp[$t_key + $pk] !== $pAlias) {
                                $findPage = false;
                                break;
                            }
                        }
                        if ($findPage) {
                            $page = $pageId;
                            $tmp_id = $t_key + $pk;
                            break 2;
                        }
                    } elseif ($t_alias === $pageAlias) {
                        $page = $pageId;
                        $tmp_id = $t_key;
                        break 2;
                    }
                }
            }
            if ($page) {
                for ($i = 0; $i <= $tmp_id; $i++) {
                    array_shift($tmp);
                }
            }
        }

        //если страница не найдена, то проверим, вдруг это главная страница
        if (!$page && array_key_exists($site_start, $aliases)) {
            $page = $site_start;
        }


        if ($page) {
            if ((int)$page === (int)$site_start) {
                $url = '';
            } else {
                $url = $this->modx->makeUrl($page, $ctx, '', -1);
            }
            if ($this->modx->getOption('site_url') && $this->modx->getOption('site_url') !== '/'
                && strpos($url, $this->modx->getOption('site_url')) !== false) {
                $url = str_replace($this->modx->getOption('site_url'), '', $url);
            }
            if (($c_suffix = $this->config['container_suffix'])
                && strlen($url) && strpos($url, $c_suffix, strlen($url) - strlen($c_suffix)) !== false) {
                $url = substr($url, 0, -strlen($c_suffix));
            }
            foreach ($this->config['possibleSuffixes'] as $possibleSuffix) {
                if (substr($url, -strlen($possibleSuffix)) === $possibleSuffix) {
                    $url = substr($url, 0, -strlen($possibleSuffix));
                }
            }

            if ($between_urls === '/') {
                if (implode('/', array_reverse(array_diff($r_tmp, $tmp))) !== trim($url, '/')) {
                    return;
                }
            } elseif ($url) {
                if (trim($url, '/') !== str_replace($between_urls.implode('/', $tmp), '', $request)) {
                    return;
                }
            }

            if (!empty($this->config['page_tpl'])) {
                //^page-\d+$
                $page_part = $this->pdo->parseChunk('@INLINE '.$this->config['page_tpl'], [
                    'pageVarKey'              => $this->config['page_key'],
                    'pagevarkey'              => $this->config['page_key'],
                    $this->config['page_key'] => '\d+$'
                ]);
                $page_part = '/^'.trim($page_part, '/').'/';

                foreach ($tmp as $k => $s) {
                    if (preg_match($page_part, $s)) {
                        $page_num = preg_replace("/\D+/", '', $s);
                        unset($tmp[$k]);
                        if ($page_num > 1) {
                            $this->config['page_number'] = (int)$page_num;
                            $_GET[$this->config['page_key']] = $_REQUEST[$this->config['page_key']] = $page_num;
                        }
                    }
                }
            }

            if ($tmp && $url_array = $this->findUrlArray(implode($this->config['level_separator'], $tmp),
                    $page)) {
                if ($url_array['active']) {
                    $old_url = $url_array['old_url'];
                    $new_url = $url_array['new_url'];
                    $rule_id = $url_array['multi_id'];
                    $toFind = implode($this->config['level_separator'], $tmp).$url_suffix;


                    if ($new_url && ($new_url !== implode($this->config['level_separator'], $tmp))) {
                        if ($container_suffix && strpos($url, $container_suffix,
                                strlen($url) - strlen($container_suffix)) !== false) {
                            $url = substr($url, 0, -strlen($container_suffix));
                        }
                        if (((int)$site_start === (int)$page) && $this->config['main_alias']) {
                            $q = $this->modx->newQuery('modResource', ['id' => $page]);
                            $q->select('alias');
                            $malias = $this->modx->getValue($q->prepare());
                            $new_url = $malias.$between_urls.$new_url;
                        }
                        $this->modx->sendRedirect($url.'/'.$new_url.$url_suffix, [
                            'type'         => 'REDIRECT_HEADER',
                            'responseCode' => 'HTTP/1.1 301 Moved Permanently'
                        ]);
                    } elseif ($url_redirect && ($url_suffix !== $last_char)
                        && ((strpos($_SERVER['REQUEST_URI'],
                                    $toFind) === false) || (strpos($_SERVER['QUERY_STRING'],
                                    $toFind) === false)) //when server have bugs
                    ) {
                        if ($container_suffix && strpos($url, $container_suffix,
                                strlen($url) - strlen($container_suffix)) !== false) {
                            $url = substr($url, 0, -strlen($container_suffix));
                        }
                        $this->modx->sendRedirect($url.'/'.implode($this->config['level_separator'],
                                $tmp).$url_suffix, [
                            'type'         => 'REDIRECT_HEADER',
                            'responseCode' => 'HTTP/1.1 301 Moved Permanently'
                        ]);
                    }


                    $tmp = explode($this->config['level_separator'], $old_url);
                    $menuTitle = '';
                    if ($url_array['menu_on']) {
                        $menuTitle = $url_array['menutitle'];
                    }

                    $q = $this->modx->newQuery('sfUrlWord');
                    $q->sortby('priority', 'ASC');
                    $q->innerJoin('sfField', 'sfField', 'sfUrlWord.field_id = sfField.id');
                    $q->innerJoin('sfDictionary', 'sfDictionary', 'sfUrlWord.word_id = sfDictionary.id');
                    $q->where(['sfUrlWord.url_id' => $url_array['id']]);
                    $q->select([
                        'sfUrlWord.id',
                        'sfField.id as field_id, sfField.alias as field_alias,sfField.key as field_key,sfField.tagmanager as field_tm2',
                        'sfDictionary.value as word_value, sfDictionary.input as word_input, sfDictionary.alias as word_alias'
                    ]);
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($this->config['proMode'] && mb_strpos($row['word_input'], '||') !== false) {
                                $_GET[$row['field_alias']] = $_REQUEST[$row['field_alias']] = str_replace('||', ',',
                                    $row['word_input']);
                                $params[$row['field_alias']] = $row['word_input'];
                            } elseif ($row['field_tm2']) {
                                $tm2Multitags = array_map('trim', explode(',', $this->config['tm2_multitags']));
                                $tm2Numeric = array_map('trim', explode(',', $this->config['tm2_numeric']));
                                $fieldKey = 'f_'.$row['field_key'];

                                if (in_array($row['field_key'], $tm2Multitags, true)) {
                                    if (isset($_GET[$fieldKey]['like'])) {
                                        $input = $_GET[$fieldKey];
                                        $input['like'][] = trim($row['word_input'], $this->config['tm2_tags_guard']);
                                    } else {
                                        $input = [
                                            'like' => [
                                                trim($row['word_input'], $this->config['tm2_tags_guard'])
                                            ]
                                        ];
                                    }
                                } elseif (in_array($row['field_key'], $tm2Numeric, true)) {
                                    $numbers = array_map('trim', explode(',', $row['word_input']));
                                    $input = ['from' => $numbers[0]];
                                    if (isset($numbers[1])) {
                                        $input['to'] = $numbers[1];
                                    }
                                } elseif (isset($_GET[$fieldKey])) {
                                    $input = $_GET[$fieldKey];
                                    $input[] = $row['word_input'];
                                } else {
                                    $input = [$row['word_input']];
                                }

                                $_GET[$fieldKey] = $_REQUEST[$fieldKey] = $input;
                                $params[$row['field_alias']] = $row['word_input'];
                            } else {
                                $_GET[$row['field_alias']] = $_REQUEST[$row['field_alias']] = $params[$row['field_alias']] = $row['word_input'];
                            }

                            if (!$menuTitle) {
                                $menuTitle = $row['word_value'];
                            }
                        }
                    }

                    $q = $this->modx->newQuery('sfFieldIds');
                    $q->where(['multi_id' => $rule_id]);
                    $urlFields = $this->modx->getCount('sfFieldIds', $q);

                    //Доп проверка на изменения в базе
                    if ((count($params) !== $urlFields)
                        && ($links = $this->pdo->getCollection('sfFieldIds', ['multi_id' => $rule_id],
                            ['sortby' => 'priority']))
                        && count($tmp) === count($links)) {  //дополнительная проверка на количество параметров в адресе и пересечении
                        foreach ($links as $lkey => $link) {
                            if ($field = $this->pdo->getArray('sfField', $link['field_id'])) {
                                $alias = $field['alias'];
                                if ($field['hideparam']) {
                                    if ($word = $this->pdo->getArray('sfDictionary', ['alias' => $tmp[$lkey]])) {
                                        if ($this->config['proMode'] && strpos($word['input'],
                                                '||') !== 0) {
                                            $_GET[$alias] = $_REQUEST[$alias] = str_replace('||', ',',
                                                $word['input']);
                                            $params[$alias] = $word['input'];
                                        } else {
                                            $_GET[$alias] = $_REQUEST[$alias] = $params[$alias] = $word['input'];
                                        }
                                        if (!$menuTitle) {
                                            $menuTitle = $word['value'];
                                        }
                                    }
                                } else {
                                    $tmp_arr = explode($separator, $tmp[$lkey]);
                                    $word_alias = '';
                                    if ($field['valuefirst']) {
                                        $del = array_pop($tmp_arr);
                                        if ($del === $alias) {
                                            $word_alias = implode($separator, $tmp_arr);
                                        }
                                    } else {
                                        $del = array_shift($tmp_arr);
                                        if ($del === $alias) {
                                            $word_alias = implode($separator, $tmp_arr);
                                        }
                                    }
                                    if ($word_alias && $word = $this->pdo->getArray('sfDictionary',
                                            ['alias' => $word_alias, 'field_id' => $field['id']])) {
                                        if ($this->config['proMode'] && strpos($word['input'],
                                                '||') !== 0) {
                                            $_GET[$alias] = $_REQUEST[$alias] = str_replace('||', ',',
                                                $word['input']);
                                            $params[$alias] = $word['input'];
                                        } else {
                                            $_GET[$alias] = $_REQUEST[$alias] = $params[$alias] = $word['input'];
                                        }
                                        if (!$menuTitle) {
                                            $menuTitle = $word['value'];
                                        }
                                    }
                                }
                            }
                        }
                    }


                    if (count($params)) {
                        $original_params = array_diff_key(
                            array_merge($_GET, $params),
                            array_flip(array_merge([$this->modx->context->getOption('request_param_alias', 'q')],
                                $base_get))
                        );

                        $fast_search = true;
                        $meta = $this->getRuleMeta($params, $rule_id, $page, 0, 0, $original_params);

                        //обновление счётчика, если отличается количество
                        if (empty($meta['diff']) && $this->config['count_childrens']
                            && (int)$meta['url_id'] && ((int)$meta['total'] !== (int)$meta['old_total'])) {
                            $this->updateUrlTotal($meta['url_id'], $meta['total']);
                        }

                        if ($this->config['hideEmpty'] && $this->config['count_childrens'] && empty($meta['total'])) {
                            $this->modx->setPlaceholder('sf.seo_id', $url_array['id']);
                            return;
                        }

                        if ($this->config['lastModified']) {
                            if (empty($meta['editedon']) && $meta['editedon'] !== '0000-00-00 00:00:00') {
                                $modified = $meta['editedon'];
                            } else {
                                $modified = $meta['createdon'];
                            }
                            $modified = date('r', strtotime($modified));
                            $qtime = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : '';
                            if ($qtime && strtotime($qtime) >= strtotime($modified)) {
                                header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                                exit();
                            }
                            header("Last-Modified: $modified");
                        }

                        $this->initialize($ctx, [
                            'page'   => $page,
                            'params' => $params,
                            'hash'   => http_build_query(array_intersect_key($_GET, $params))
                        ]);

                        if ($this->modx->getOption('msvendorcollections_on_frontend', null, 0)
                            && is_dir($this->modx->getOption('core_path').'components/msvendorcollections/model/')
                            && ($msVC = $this->modx->getService('msvendorcollections', 'msVendorCollections',
                                $this->modx->getOption('msvendorcollections_core_path', null,
                                    $this->modx->getOption('core_path').'components/msvendorcollections/').'model/msvendorcollections/',
                                []))
                            && !$msVC->initialized[$ctx]) {
                            $msVC->initialize($ctx, ['page' => $page]);
                        }


                        $meta['menutitle'] = $menuTitle;
                        if (isset($meta['properties'])) {
                            $meta['properties'] = $this->modx->toJSON($meta['properties']);
                        }
                        if (isset($meta['introtexts'])) {
                            $meta['introtexts'] = $this->modx->toJSON($meta['introtexts']);
                        }
                        if (isset($meta['url'])) {
                            $meta['url'] .= $this->config['url_suffix'];
                        }

                        if ($ctx !== 'web') {
                            $this->modx->switchContext($ctx);
                        }

                        $plugin_response = $this->invokeEvent('sfOnReturnMeta',
                            ['action' => 'plugin', 'page' => $page, 'meta' => $meta, 'SeoFilter' => $this]);
                        if (isset($plugin_response['success']) && $plugin_response['success']) {
                            $meta = $plugin_response['data']['meta'];
                        }

                        $meta['params'] = $this->modx->toJSON($params);
                        $this->modx->setPlaceholders($meta, 'sf.');

                        $this->modx->resourceMethod = 'id';
                        $this->modx->resourceIdentifier = $page;
                        $this->modx->invokeEvent('OnWebPageInit');
                        $this->modx->sendForward($page);
                    } elseif ($url = $this->modx->getObject('sfUrls',
                        ['page_id' => $page, 'old_url' => $old_url, 'multi_id' => $rule_id])) {
                        $url->set('active', 0);
                        $url->save();
                    }
                } else {
                    $this->modx->setPlaceholder('sf.seo_id', $url_array['id']);
                }
            }
        }
    }

    /**
     * @param  array  $config
     *
     * @return array
     */
    protected function prepareConfig($config = [])
    {
        $corePath = $this->modx->getOption('seofilter_core_path', $config,
            $this->modx->getOption('core_path').'components/seofilter/'
        );

        $assetsUrl = $this->modx->getOption('seofilter_assets_url', $config,
            $this->modx->getOption('assets_url').'components/seofilter/'
        );

        return array_merge([
            'assetsUrl'     => $assetsUrl,
            'cssUrl'        => $assetsUrl.'css/',
            'jsUrl'         => $assetsUrl.'js/',
            'imagesUrl'     => $assetsUrl.'images/',
            'connectorUrl'  => $assetsUrl.'connector.php',
            'actionUrl'     => $assetsUrl.'action.php',
            'json_response' => true,

            'corePath'       => $corePath,
            'customPath'     => $this->getOption('custom_path', $corePath.'custom/'),
            'modelPath'      => $corePath.'model/',
            'chunksPath'     => $corePath.'elements/chunks/',
            'templatesPath'  => $corePath.'elements/templates/',
            'chunkSuffix'    => '.chunk.tpl',
            'snippetsPath'   => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',

            'hash'             => '',
            'params'           => [],
            'ajax'             => $this->getOption('ajax', 1),
            'separator'        => $this->getOption('separator', '-'),
            'level_separator'  => $this->getOption('level_separator', '/'),
            'between_urls'     => $this->getOption('between_urls', '/'),
            'redirect'         => $this->getOption('url_redirect', 0),
            'site_start'       => (int)$this->modx->context->getOption('site_start', 1),
            'site_url'         => $this->modx->context->getOption('site_url',
                $this->modx->getOption('site_url', null, '')),
            'charset'          => $this->modx->context->getOption('modx_charset', 'UTF-8'),
            'base_get'         => $this->getOption('base_get', ''),
            'values_delimeter' => $this->getOption('values_delimeter', ','),
            'container_suffix' => $this->getOption('container_suffix',
                $this->modx->getOption('container_suffix', null, '/')),
            'url_suffix'       => $this->getOption('url_suffix', ''),
            'decline'          => $this->getOption('decline', 0),
            'morpher_token'    => $this->getOption('morpher_token', 0),

            'count_childrens' => $this->getOption('count', 0),
            'ajax_recount'    => $this->getOption('ajax_recount', 0),
            'count_choose'    => $this->getOption('choose', ''),
            'count_select'    => $this->getOption('select', ''),
            'count_class'     => $this->getOption('count_handler_class', 'sfCountHandler'),
            'prepareSnippet'  => $this->getOption('snippet', ''),

            'title'       => $this->getOption('title', ''),
            'description' => $this->getOption('description', ''),
            'introtext'   => $this->getOption('introtext', ''),
            'keywords'    => $this->getOption('keywords', ''),
            'link'        => $this->getOption('link', ''),
            'h1'          => $this->getOption('h1', ''),
            'h2'          => $this->getOption('h2', ''),
            'text'        => $this->getOption('text', ''),
            'content'     => $this->getOption('content', ''),

            'page_key'      => $this->getOption('page_key', 'page'),
            'page_tpl'      => $this->getOption('page_tpl', ''),
            'page_number'   => $this->modx->getOption($this->getOption('page_key', 'page'), $config, 1),
            'admin_version' => $this->getOption('admin_version', 1),
            'main_alias'    => $this->getOption('main_alias', 0),

            'replace_host'     => $this->getOption('replace_host', 0),
            'replacebefore'    => $this->getOption('replacebefore', 0),
            'replaceseparator' => $this->getOption('replaceseparator', ' / '),

            'jtitle'       => $this->getOption('jtitle', ''),
            'jdescription' => $this->getOption('jdescription', ''),
            'jintrotext'   => $this->getOption('jintrotext', ''),
            'jkeywords'    => $this->getOption('jkeywords', ''),
            'jlink'        => $this->getOption('jlink', ''),
            'jh1'          => $this->getOption('jh1', ''),
            'jh2'          => $this->getOption('jh2', ''),
            'jtext'        => $this->getOption('jtext', ''),
            'jcontent'     => $this->getOption('jcontent', ''),

            'tpls_path'        => $this->getOption('tpls_path', ''),
            'url_help'         => $this->getOption('url_help', ''),
            'hideEmpty'        => $this->getOption('hide_empty', 0),
            'possibleSuffixes' => array_map('trim',
                explode(',', $this->getOption('possible_suffixes', '/,.html,.php'))),
            'lastModified'     => $this->getOption('last_modified', 0),
            'crumbsReplace'    => $this->getOption('crumbs_replace', 1),
            'crumbsNested'     => $this->getOption('crumbs_nested', 0),
            'crumbsCurrent'    => $this->getOption('crumbs_tpl_current', 'tpl.SeoFilter.crumbs.current'),
            'mfilterWords'     => $this->getOption('mfilter_words', 0),
            'superHiddenProps' => $this->getOption('super_hidden_props', 0),
            'hiddenTab'        => $this->getOption('hidden_tab', 0),
            'proMode'          => $this->getOption('pro_mode', 0),
            'scheme'           => $this->getOption('url_scheme', $this->modx->getOption('link_tag_scheme')),
            'defaultWhere'     => $this->getOption('default_where', '{"published":1,"deleted":0}'),
            'content_richtext' => $this->getOption('content_richtext', ''),
            'content_ace'      => $this->getOption('content_ace', 'content,Rule.content'),
            'collect_words'    => $this->getOption('collect_words', 1),
            'classes'          => $this->getOption('classes', 'msProduct'),
            'templates'        => $this->getOption('templates', ''),
            'edit_url_mask'    => $this->getOption('edit_url_mask', 0),
            'tm2_multitags'    => $this->modx->getOption('tag_mgr2.multitags', null, ''),
            'tm2_tags_guard'   => $this->modx->getOption('tag_mgr2.guard_key', null, ''),
            'tm2_numeric'      => $this->modx->getOption('tag_mgr2.numeric', null, '')
        ], $config);
    }

    /**
     * @param $option
     * @param  null  $default
     * @param  string  $ns
     *
     * @return mixed
     */
    protected function getOption($option, $default = null, $ns = 'seofilter_')
    {
        return $this->modx->getOption($ns.$option, null, $default, true);
    }


}