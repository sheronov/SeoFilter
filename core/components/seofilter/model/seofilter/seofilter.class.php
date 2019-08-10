<?php

class SeoFilter
{
    public $version = '1.6.4';
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();
    /** @var pdoFetch $pdo */
    public $pdo;
    /** @var sfCountHandler $countHandler */
    public $countHandler = null;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('seofilter_core_path', $config,
            $this->modx->getOption('core_path') . 'components/seofilter/'
        );

        $assetsUrl = $this->modx->getOption('seofilter_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/seofilter/'
        );
        $customPath = $this->modx->getOption('seofilter_custom_path', $config, $corePath . 'custom/');
        $actionUrl = $assetsUrl . 'action.php';
        $connectorUrl = $assetsUrl . 'connector.php';
        $ajax = $this->modx->getOption('seofilter_ajax', null, 1, true);
        $separator = $this->modx->getOption('seofilter_separator', null, '-', true);
        $level_separator = $this->modx->getOption('seofilter_level_separator', null, '/', true);
        $between_urls = $this->modx->getOption('seofilter_between_urls', null, '/', true);
        $base_get = $this->modx->getOption('seofilter_base_get', null, '', true);
        $values_delimeter = $this->modx->getOption('seofilter_values_delimeter', null, ',', true);
        $site_start = $this->modx->context->getOption('site_start', 1);
        $site_url = $this->modx->context->getOption('site_url', '');
        $charset = $this->modx->context->getOption('modx_charset', 'UTF-8');

        $container_suffix = $this->modx->getOption('container_suffix', null, '/');
        $seo_container_suffix = $this->modx->getOption('seofilter_container_suffix', null, $container_suffix, true);
        $url_suffix = $this->modx->getOption('seofilter_url_suffix', null, '', true);
        $redirect = $this->modx->getOption('seofilter_url_redirect', null, 0, true);
        $replace_host = $this->modx->getOption('seofilter_replace_host', null, 0, true);

        $title = $this->modx->getOption('seofilter_title', null, '', true);
        $description = $this->modx->getOption('seofilter_description', null, '', true);
        $introtext = $this->modx->getOption('seofilter_introtext', null, '', true);
        $keywords = $this->modx->getOption('seofilter_keywords', null, '', true);
        $link = $this->modx->getOption('seofilter_link', null, '', true);
        $h1 = $this->modx->getOption('seofilter_h1', null, '', true);
        $h2 = $this->modx->getOption('seofilter_h2', null, '', true);
        $text = $this->modx->getOption('seofilter_text', null, '', true);
        $content = $this->modx->getOption('seofilter_content', null, '', true);
        $page_key = $this->modx->getOption('seofilter_page_key', null, 'page', true);
        $page_tpl = $this->modx->getOption('seofilter_page_tpl', null, '', true);
        $lastModified = $this->modx->getOption('seofilter_last_modified', null, 0, true);
        $mfilterWords = $this->modx->getOption('seofilter_mfilter_words', null, 0, true);

        $count_childrens = $this->modx->getOption('seofilter_count', null, 0, true);
        $count_choose = $this->modx->getOption('seofilter_choose', null, '', true);
        $count_select = $this->modx->getOption('seofilter_select', null, '', true);
        $prepareSnippet = $this->modx->getOption('seofilter_snippet', null, '', true);
        $hideEmpty = $this->modx->getOption('seofilter_hide_empty', null, 0, true);

        $replacebefore = $this->modx->getOption('seofilter_replacebefore', null, 0, true);
        $replaceseparator = $this->modx->getOption('seofilter_replaceseparator', null, ' / ', true);
        $jtitle = $this->modx->getOption('seofilter_jtitle', null, '', true);
        $jlink = $this->modx->getOption('seofilter_jlink', null, '', true);
        $jdescription = $this->modx->getOption('seofilter_jdescription', null, '', true);
        $jintrotext = $this->modx->getOption('seofilter_jintrotext', null, '', true);
        $jkeywords = $this->modx->getOption('seofilter_jkeywords', null, '', true);
        $jh1 = $this->modx->getOption('seofilter_jh1', null, '', true);
        $jh2 = $this->modx->getOption('seofilter_jh2', null, '', true);
        $jtext = $this->modx->getOption('seofilter_jtext', null, '', true);
        $jcontent = $this->modx->getOption('seofilter_jcontent', null, '', true);

        $content_richtext = $this->modx->getOption('seofilter_content_richtext', null, '');
        $content_ace = $this->modx->getOption('seofilter_content_ace', null, 'content,Rule.content');
        $collect_words = $this->modx->getOption('seofilter_collect_words', null, 1);
        $hiddenTab = $this->modx->getOption('seofilter_hidden_tab', null, 0, true);
        $superHiddenProps = $this->modx->getOption('seofilter_super_hidden_props', null, 0, true);
        $tplsPath = $this->modx->getOption('seofilter_tpls_path', null, '', true);
        $urlHelp = $this->modx->getOption('seofilter_url_help', null, '', true);
        $crumbsReplace = $this->modx->getOption('seofilter_crumbs_replace', null, 1, true);
        $crumbsNested = $this->modx->getOption('seofilter_crumbs_nested', null, 0, true);
        $crumbsCurrent = $this->modx->getOption('seofilter_crumbs_tpl_current', null, 'tpl.SeoFilter.crumbs.current',
            true);

        $proMode = $this->modx->getOption('seofilter_pro_mode', null, 0, true);
        $scheme = $this->modx->getOption('seofilter_url_scheme', null, $this->modx->getOption('link_tag_scheme'), true);
        $defaultWhere = $this->modx->getOption('seofilter_default_where', null, '{"published":1,"deleted":0}', true);

        $possibleSuffixes = array_map('trim',
            explode(',', $this->modx->getOption('seofitler_possible_suffixes', null, '/,.html,.php', true)));
        $admin_version = $this->modx->getOption('seofilter_admin_version', null, 1);
        $main_alias = $this->modx->getOption('seofilter_main_alias', null, 0);
        $count_class = $this->modx->getOption('seofilter_count_handler_class', null, 'sfCountHandler', true);
        $classes = $this->modx->getOption('seofilter_classes', null, 'msProduct', true);
        $templates = $this->modx->getOption('seofilter_templates', null, '', true);
        $decline = $this->modx->getOption('seofilter_decline', null, 0, true);
        $morpher_token = $this->modx->getOption('seofilter_morpher_token', null, 0, true);

        $edit_url_mask = $this->modx->getOption('seofilter_edit_url_mask', null, 0, true);

        $this->pdo = $this->modx->getService('pdoFetch');
        $this->pdo->setConfig(array('loadModels' => 'seofilter'));

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,
            'actionUrl' => $actionUrl,
            'json_response' => true,

            'corePath' => $corePath,
            'customPath' => $customPath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'params' => array(),
            'ajax' => $ajax,
            'separator' => $separator,
            'level_separator' => $level_separator,
            'between_urls' => $between_urls,
            'redirect' => $redirect,
            'site_start' => $site_start,
            'site_url' => $site_url,
            'charset' => $charset,
            'base_get' => $base_get,
            'values_delimeter' => $values_delimeter,
            'container_suffix' => $seo_container_suffix,
            'url_suffix' => $url_suffix,
            'decline' => $decline,
            'morpher_token' => $morpher_token,

            'count_childrens' => $count_childrens,
            'count_choose' => $count_choose,
            'count_select' => $count_select,
            'count_class' => $count_class,
            'prepareSnippet' => $prepareSnippet,

            'title' => $title,
            'description' => $description,
            'introtext' => $introtext,
            'keywords' => $keywords,
            'link' => $link,
            'h1' => $h1,
            'h2' => $h2,
            'text' => $text,
            'content' => $content,
            'page_key' => $page_key,
            'page_tpl' => $page_tpl,
            'page_number' => 1,
            'admin_version' => $admin_version,
            'main_alias' => $main_alias,

            'replace_host' => $replace_host,
            'replacebefore' => $replacebefore,
            'replaceseparator' => $replaceseparator,
            'jtitle' => $jtitle,
            'jdescription' => $jdescription,
            'jintrotext' => $jintrotext,
            'jkeywords' => $jkeywords,
            'jlink' => $jlink,
            'jh1' => $jh1,
            'jh2' => $jh2,
            'jtext' => $jtext,
            'jcontent' => $jcontent,
            'tpls_path' => $tplsPath,
            'url_help' => $urlHelp,
            'hideEmpty' => $hideEmpty,
            'possibleSuffixes' => $possibleSuffixes,
            'lastModified' => $lastModified,
            'crumbsReplace' => $crumbsReplace,
            'crumbsNested' => $crumbsNested,
            'crumbsCurrent' => $crumbsCurrent,
            'mfilterWords' => $mfilterWords,
            'superHiddenProps' => $superHiddenProps,
            'hiddenTab' => $hiddenTab,
            'proMode' => $proMode,
            'scheme' => $scheme,
            'defaultWhere' => $defaultWhere,
            'content_richtext' => $content_richtext,
            'content_ace' => $content_ace,
            'collect_words' => $collect_words,
            'classes' => $classes,
            'templates' => $templates,
            'edit_url_mask' => $edit_url_mask
        ), $config);

        $this->modx->addPackage('seofilter', $this->config['modelPath']);
        $this->modx->lexicon->load('seofilter:default');
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties Properties for initialization.
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {

        if (isset($this->initialized[$ctx]) && $this->initialized[$ctx]) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);
//        $this->config['ctx'] = $ctx;

        if ($this->config['ajax']) {
            $config = $this->makePlaceholders($this->config);
            $js = trim($this->modx->getOption('seofilter_frontend_js', null, $this->config['jsUrl'] . 'web/default.js',
                false));
            if (!empty($js) && preg_match('/\.js/i', $js)) {

                $js_file = str_replace($config['pl'], $config['vl'], $js);
                //$js_file = $this->pdo->parseChunk('@INLINE ' . $js, $this->config);

                if (preg_match('/\.js$/i', $js_file) && $this->config['admin_version']) {
                    $js_file .= '?v=' . mb_substr(md5($this->version), 0, 10);
                }

                //TODO: костыль для точного подключения Js-файла
                if (isset($this->modx->loadedjscripts[$js_file])) {
                    unset($this->modx->loadedjscripts[$js_file]);
                }
                $this->modx->regClientScript($js_file);
            }

            if ($this->config['page']) {
//                    $aliases = $this->fieldsAliases($this->config['page'], 1);
//                    $this->config['aliases'] = $aliases;
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
                $q->where(array('sfField.slider' => 1));
                if ($this->config['proMode']) {
                    $q->where('1=1 AND FIND_IN_SET(' . $this->config['page'] . ',REPLACE(IFNULL(NULLIF(sfRule.pages,""),sfRule.page)," ",""))');
                } else {
                    $q->where(array('sfRule.page' => $this->config['page']));
                }
                $this->config['slider'] = $this->modx->getCount('sfFieldIds', $q);
            }

            $data = json_encode(array(
                'jsUrl' => $this->config['jsUrl'] . 'web/',
                'actionUrl' => $this->config['actionUrl'],
                'ctx' => $ctx,
                'page' => $this->config['page'],
                'params' => $this->config['params'],
//                    'aliases' => $this->config['aliases'],
                'slider' => $this->config['slider'],
                'crumbs' => $this->config['crumbsReplace'],
                'separator' => $this->config['separator'],
                'redirect' => $this->config['redirect'],
                'url' => $this->config['url'],
                'between' => $this->config['between_urls'],
                'replacebefore' => $this->config['replacebefore'],
                'replaceseparator' => $this->config['replaceseparator'],
                'jtitle' => $this->config['jtitle'],
                'jlink' => $this->config['jlink'],
                'jdescription' => $this->config['jdescription'],
                'jintrotext' => $this->config['jintrotext'],
                'jkeywords' => $this->config['jkeywords'],
                'jh1' => $this->config['jh1'],
                'jh2' => $this->config['jh2'],
                'jtext' => $this->config['jtext'],
                'jcontent' => $this->config['jcontent'],
            ), true);

            $this->modx->regClientStartupScript(
                '<script type="text/javascript">seoFilterConfig = ' . $data . ';</script>', true
            );


        }
        $this->initialized[$ctx] = true;
        return true;
    }

    public function getFieldsKey($key = 'key')
    {
        $fields = array();
        $q = $this->modx->newQuery('sfField');
//        $q->where(array('active'=>1));
        $q->select(array('sfField.*'));
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['class'] == 'modTemplateVar') {
                    if (strtolower($row['xpdo_package']) == 'tvsuperselect') {
                        $fields['tvss'][$row[$key]] = $row;
                    } else {
                        $fields['tvs'][$row[$key]] = $row;
                    }
                } elseif (strtolower($row['class']) == 'tagger') {
                    $fields['tagger'][$row[$key]] = $row;
                } elseif ($row['class'] == 'msVendor') {
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
        $values = array();
        if (strpos($input, '||') !== false) {
            $values = array_map('trim', explode('||', $input));
        } elseif (strpos($input, ',') !== false) {
            $values = array_map('trim', explode(',', $input));
        } else {
            $values = array($input);
        }

        return $values;
    }

    public function returnChanges($after = array(), $before = array(), $type = '', $double = 1)
    {
        $changes = array();
        if (is_array($after)) {
            foreach ($after as $param => $val) {
                if (is_array($val)) {
                    if (isset($before[$param])) {
                        if ($type = 'tvs') {
                            $tv_changes = array('after' => array(), 'before' => array());
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

        $q = $this->modx->newQuery('modResource', array('id' => $resource_id));
        $to_config = array('where' => 'where', 'join' => 'innerJoin', 'leftjoin' => 'leftJoin');
        $this->loadHandler();
        $conditions = $this->countHandler->prepareWhere($this->modx->fromJSON($where));
        if (!empty($conditions['where'])) {
            foreach ($conditions['where'] as $where_key => $where_arr) {
                if (strpos($where_key, '.') === false) {
                    $conditions['where']['modResource.' . $where_key] = $where_arr;
                    unset($conditions['where'][$where_key]);
                }
            }
        }
        foreach ($to_config as $prop => $propConfig) {
            if (!empty($conditions[$prop])) {
                if (in_array($propConfig, array('leftJoin', 'innerJoin'))) {
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
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
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

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }

    public function getResourceData($resource_id = 0, $fields = array())
    {
        $data = array();
        foreach (array('tvs' => 'modTemplateVarResource', 'tvss' => 'tvssOption') as $var => $class) {
            if (!empty($fields[$var])) {
                if ($var == 'tvss') {
                    $this->modx->addPackage('tvsuperselect',
                        $this->modx->getOption('core_path') . 'components/tvsuperselect/model/');
                }
                foreach ($fields[$var] as $f_key => $field) {
                    if ($field['xpdo_where']) {
                        if (!$this->checkResourceCondition($resource_id, $field['xpdo_where'])) {
                            unset($fields[$var][$f_key]);
                        }
                    }
                }
                if (!empty($fields[$var])) {
                    $q = $this->modx->newQuery($class);
                    if ($var == 'tvss') {
                        $q->innerJoin('modTemplateVar', 'TV', 'TV.id = tvssOption.tv_id');
                        $q->where(array(
                            'resource_id' => $resource_id,
                            'TV.name:IN' => array_keys($fields[$var])
                        ));
                    } else {
                        $q->innerJoin('modTemplateVar', 'TV', 'TV.id = modTemplateVarResource.tmplvarid');
                        $q->where(array(
                            'contentid' => $resource_id,
                            'TV.name:IN' => array_keys($fields[$var])
                        ));
                    }
                    $q->select(array(
                        'DISTINCT ' . $class . '.value',
                        'TV.id,TV.name'
                    ));
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                            $data[$var][$row['name']][] = $row['value'];
                        }
                    }
                }
            }
        }

        if (in_array('tagger', array_keys($fields))) {
            foreach ($fields['tagger'] as $f_key => $field) {
                if ($field['xpdo_where']) {
                    if (!$this->checkResourceCondition($resource_id, $field['xpdo_where'])) {
                        unset($fields['tagger'][$f_key]);
                    }
                }
            }
            if (!empty($fields['tagger'])) {
                $taggerPath = $this->modx->getOption('tagger.core_path', null,
                    $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tagger/');
                /** @var Tagger $tagger */
                $tagger = $this->modx->getService('tagger', 'Tagger', $taggerPath . 'model/tagger/',
                    array('core_path' => $taggerPath));
                if (($tagger instanceof Tagger)) {
                    $q = $this->modx->newQuery('TaggerTagResource', array('resource' => $resource_id));
                    $q->innerJoin('TaggerTag', 'Tag', 'Tag.id = TaggerTagResource.tag');
                    $q->innerJoin('TaggerGroup', 'Group', 'Group.id = Tag.group');
                    $q->where(array(
                        'Group.id:IN' => array_keys($fields['tagger']),
                        'OR:Group.alias:IN' => array_keys($fields['tagger'])
                    ));
                    $q->select($this->modx->getSelectColumns('TaggerTag', 'Tag', ''));
                    $q->select($this->modx->getSelectColumns('TaggerGroup', 'Group', 'group_'));
                    if ($this->modx->getCount('TaggerTagResource')) {
                        if ($q->prepare() && $q->stmt->execute()) {
                            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                if (in_array($row['group_id'], array_keys($fields['tagger']))) {
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


        if ($resource = $this->modx->getObject('modResource', $resource_id)) {
            $resource = $resource->toArray();
            foreach ($resource as $param => $val) {
                if (!empty($fields['data']) && in_array($param, array_keys($fields['data']))) {
                    if ($fields['data'][$param]['xpdo_where']) {
                        if ($this->checkResourceCondition($resource_id, $fields['data'][$param]['xpdo_where'])) {
                            $data['data'][$param] = $val;
                        } else {
//                            $this->modx->log(1,'The values '.print_r($val,1).' don\'t satisfy field (id='.$fields['data'][$param]['id']. ') where condition = "'.$fields['data'][$param]['xpdo_where'].'"');
                        }

                    } else {
                        $data['data'][$param] = $val;
                    }
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
            if ($count_class != 'sfCountHandler') {
                $this->loadCustomClasses('count');
            }
            if (!class_exists($count_class)) {
                $count_class = 'sfCountHandler';
            }
            $this->countHandler = new $count_class($this->modx, $this->config);
            if (!($this->countHandler instanceof sfCountHandler)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[SeoFilter] Could not initialize count handler class: "' . $count_class . '"');
                return false;
            }
        }
        return true;
    }

    /**
     * Method loads custom classes from specified directory
     *
     * @var string $dir Directory for load classes
     * @return void
     */
    public function loadCustomClasses($dir)
    {
        $customPath = $this->config['customPath'];
        $placeholders = array(
            'base_path' => MODX_BASE_PATH,
            'core_path' => MODX_CORE_PATH,
            'assets_path' => MODX_ASSETS_PATH,
        );
        $pl1 = $this->pdo->makePlaceholders($placeholders, '', '[[+', ']]', false);
        $pl2 = $this->pdo->makePlaceholders($placeholders, '', '[[++', ']]', false);
        $pl3 = $this->pdo->makePlaceholders($placeholders, '', '{', '}', false);
        $customPath = str_replace($pl1['pl'], $pl1['vl'], $customPath);
        $customPath = str_replace($pl2['pl'], $pl2['vl'], $customPath);
        $customPath = str_replace($pl3['pl'], $pl3['vl'], $customPath);
        if (strpos($customPath, MODX_BASE_PATH) === false && strpos($customPath, MODX_CORE_PATH) === false) {
            $customPath = MODX_BASE_PATH . ltrim($customPath, '/');
        }
        $customPath = rtrim($customPath, '/') . '/' . ltrim($dir, '/');

        if (file_exists($customPath) && $files = scandir($customPath)) {
            foreach ($files as $file) {
                if (preg_match('#\.class\.php$#i', $file)) {
                    /** @noinspection PhpIncludeInspection */
                    include_once $customPath . '/' . $file;
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[SeoFilter] Custom path is not exists: \"{$customPath}\"");
        }
    }


    public function pageAliases($page_id = 0, $first_params = array())
    {
        $field_id = $rule_id = 0;
        $rule_ids = $aliases = array();
        foreach ($first_params as $alias) {
            $q = $this->modx->newQuery('sfField');
            $q->where(array('alias' => $alias));
            $q->limit(1);
            $q->select('id');
            if ($q->prepare() && $q->stmt->execute()) {
                $field_id = $q->stmt->fetch(PDO::FETCH_COLUMN);
            }

            $q = $this->modx->newQuery('sfFieldIds');
            $q->innerJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id AND sfRule.active = 1');
            $q->where(array('sfFieldIds.field_id' => $field_id));
            if ($this->config['proMode']) {
                $q->where('1=1 AND FIND_IN_SET(' . $page_id . ',REPLACE(IFNULL(NULLIF(sfRule.pages,""),sfRule.page)," ",""))');
            } else {
                $q->where(array('sfRule.page' => $page_id));
            }
            $q->select(array('sfFieldIds.multi_id', 'sfRule.rank', 'sfRule.base'));
            $q->sortby('sfFieldIds.priority', 'ASC');
            $q->limit(0);
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rule_ids[$row['multi_id']] = array('rank' => $row['rank'], 'base' => $row['base']);
                }
            }

            foreach ($rule_ids as $rule_id => $rarray) {
                $q = $this->modx->newQuery('sfFieldIds');
                $q->innerJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
                $q->where(array('sfFieldIds.multi_id' => $rule_id));
                $q->select(array('sfFieldIds.*', 'sfField.alias as alias'));
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
        $aliases = $rules_id = array();
        $q = $this->modx->newQuery('sfRule');
        $q->limit(0);
        if ($page) {
            $q->where(array('active' => 1));
            if ($this->config['proMode']) {
                $q->where('1=1 AND FIND_IN_SET(' . $page . ',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
//                $q->where(array('(page = '.$page.') OR (1 = 1 AND FIND_IN_SET('.$page.',pages))'));
            } else {
                $q->where(array('page' => $page));
            }
        }
        $q->select('id,base');
        if ($q->prepare() && $q->stmt->execute()) {
            $fields = $delfields = array();
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $rules_id[$row['id']] = $row['base'];
            }

            foreach ($rules_id as $rule_id => $rule_base) {
                $q = $this->modx->newQuery('sfFieldIds');
                $q->where(array('multi_id' => $rule_id));
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
                $q->where(array('id:IN' => array_unique($fields)));
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
     * @var array $array With keys and values
     * @var string $prefix
     *
     * @return array $array Two nested arrays With placeholders and values
     */
    public function makePlaceholders(array $array = array(), $prefix = '')
    {
        $result = array(
            'pl' => array(),
            'vl' => array(),
        );
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result, $this->makePlaceholders($v, $prefix . $k . '.'));
            } else {
                $result['pl'][$prefix . $k] = '[[+' . $prefix . $k . ']]';
                $result['vl'][$prefix . $k] = $v;
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
        $urls = array();

        $specialChars = array(
            '%' => '%25',
            '+' => '%2B',
            '&' => '%26'
        );
        foreach ($params as $param => $value) {
            //замены для корректности URL, как и в JS для хэша
            $value = str_replace(array_keys($specialChars), array_values($specialChars), $value);
            $urls[] = $param . '=' . $value;
        }

        return '?' . implode('&', $urls);
    }

    public function clearSuffixes($url = '')
    {
        foreach ($this->config['possibleSuffixes'] as $possibleSuffix) {
            if (substr($url, -strlen($possibleSuffix)) == $possibleSuffix) {
                $url = substr($url, 0, -strlen($possibleSuffix));
            }
        }
        return $url;
    }


    public function process($action, $data = array())
    {
        if (isset($data['data']['hash'])) {
            $this->config['pdopage_hash'] = $data['data']['hash'];
            unset($data['data']['hash']);
        }
        switch ($action) {
            case 'metabyurl':
                $meta = array();
                $delArray = array('http://www.', 'https://www.', 'http://', 'https://');
                $pageId = $data['pageId'];
                $findUrl = '';
                if (!empty($data['data'])) {
                    $fullUrl = explode('?', str_replace($delArray, '', $data['data']['full_url']));
                    $fullUrl = $this->clearSuffixes(array_shift($fullUrl));
                    $pageUrl = $this->clearSuffixes(str_replace($delArray, '', $data['data']['page_url']));
                    if ($this->config['main_alias'] && $pageId == $this->config['site_start']) {
                        $q = $this->modx->newQuery('modResource');
                        $q->where(array('id' => $pageId));
                        $q->select('alias');
                        $alias = $this->modx->getValue($q->prepare());
                        if ($pageUrl != $fullUrl) {
                            $pageUrl .= '/' . $alias;
                        }
                    }
                    $findUrl = $this->clearSuffixes(trim(str_replace($pageUrl, '', $fullUrl), '/'));
                    $findUrl = trim($findUrl, $this->config['between_urls']);
                }
                if ($findUrl) {
                    if ($url_words = $this->getParamsByUrl($findUrl)) {
                        $rule_id = 0;
                        $params = array();
                        foreach ($url_words as $row) {
                            $params[$row['field_alias']] = $row['word_input'];
                            $rule_id = $row['rule_id'];
                        }

                        $q = $this->modx->newQuery('sfFieldIds');
                        $q->where(array('multi_id' => $rule_id));
                        $url_fields = $this->modx->getCount('sfFieldIds', $q);

                        if ((count($params) == $url_fields)) { //Доп проверка на изменения в базе
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
                        $meta['link_url'] = $meta['url'] . $this->config['url_suffix'];
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
                    array('action' => $action, 'page' => $pageId, 'meta' => $meta, 'SeoFilter' => $this));
                if ($plugin_response['success']) {
                    $meta = $plugin_response['data']['meta'];
                }

                $response = array(
                    'success' => true,
                    'data' => $meta,
                );
                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            case 'meta_results':
                $json_response = $this->config['json_response'];
                $this->config['json_response'] = false;
                $response = $this->process('getmeta', $data);
                $this->config['json_response'] = $json_response;
                $config = array();
                if (isset($response['data']['config'])) {
                    $config = $response['data']['config'];
                    unset($response['data']['config']);
                }

                $response['data']['pdopage_change'] = $this->changePdoPageSession($config);

                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            case 'getmeta':
                $diff = $original_params = array();
                $params = $copyparams = $data['data'];
                $pageId = (int)$data['pageId'];
//                $aliases = $data['aliases'];
                $aliases = $this->fieldsAliases($pageId, 1);

                $base_get = array_map('trim', explode(',', $this->config['base_get']));
                $page_key = 1;

                if ($this->config['page_key'] && isset($params[$this->config['page_key']])) {
                    $page_key = $params[$this->config['page_key']];
                    $this->config['page_number'] = $page_key;
                }

                if (!empty($params) && is_array($params)) {
                    $original_params = array_diff_key($params, array_flip($base_get));
                }

                if (is_array($params) && count($params) && is_array($aliases)) {
                    $diff = array_flip(array_diff(array_keys($params), $aliases));
                }

                if (!empty($diff)) {
                    foreach ($diff as $dif => $dff) {
                        unset($copyparams[$dif]);
                    }
                    $diff = array_diff_key($params, $copyparams);
                    $params = array_intersect_key($params, $copyparams);
                }

                //нахождение первичного параметра

                $find = 0;

                $rule_count = 0;
                $meta = array();


                if (is_array($params) && count($params)) { //тут проверяет, были ли переданы первичные алиасы в правилах. если их нет, то и правил нет)
                    $base_params = $params;
                    $diff_params = array_diff_key($diff, array_flip($base_get));
                    $diff = array_diff_key($diff, $diff_params);

                    foreach ($base_params as $param => $value) {
                        if (strpos($value, $this->config['values_delimeter']) !== false) {
//                        if(count(array_map('trim', explode($this->config['values_delimeter'],$value))) > 1) {
                            $q = $this->modx->newQuery('sfDictionary');
                            $q->innerJoin('sfField', 'sfField', 'sfField.id = sfDictionary.field_id');
                            $q->where(array('sfDictionary.active' => 1, 'sfField.alias' => $param));
                            if ($this->config['proMode'] && strpos($value, ',') === false) {
                                $q->where('1=1 AND FIND_IN_SET("' . str_replace(',', '&',
                                        $value) . '",REPLACE(REPLACE(sfDictionary.input,",","&"),"||",","))');
                            } else {
                                $q->where(array('sfDictionary.input' => $value));
                            }
                            if (!$this->modx->getCount('sfDictionary', $q)) {
                                $find_range = 0;
                                $c = $this->modx->newQuery('sfField');
                                $c->where(array('sfField.alias' => $param, 'sfField.slider' => 1));
                                if ($this->modx->getCount('sfField', $c)) {
                                    $values = array_map('trim', explode($this->config['values_delimeter'], $value));
                                    $c->leftJoin('sfDictionary', 'sfDictionary',
                                        'sfDictionary.field_id = sfField.id AND sfDictionary.active = 1');
                                    $c->select('sfField.id,sfDictionary.input');
                                    if ($c->prepare() && $c->stmt->execute()) {
                                        while ($inp = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $i_values = array_map('trim',
                                                explode($this->config['values_delimeter'], $inp['input']));
                                            if ($values[0] >= $i_values[0] && $values[1] <= $i_values[1]) {
                                                $find_range = 1;
                                                //unset($base_params[$param]);
                                                //$base_params[$param] = $inp['input'];
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
                        if (count(array_map('trim', explode($this->config['values_delimeter'], $value))) > 1) {
                            $q = $this->modx->newQuery('sfDictionary');
                            $q->innerJoin('sfField', 'sfField', 'sfField.id = sfDictionary.field_id');
                            $q->where(array('sfDictionary.active' => 1, 'sfField.alias' => $param));
                            if ($this->config['proMode'] && strpos($value, ',') === false) {
                                $q->where('1=1 AND FIND_IN_SET("' . str_replace(',', '&',
                                        $value) . '",REPLACE(REPLACE(sfDictionary.input,",","&"),"||",","))');
                            } else {
                                $q->where(array('sfDictionary.input' => $value));
                            }
                            if (!$this->modx->getCount('sfDictionary', $q)) {
                                $find_range = 0;
                                $c = $this->modx->newQuery('sfField');
                                $c->where(array('sfField.alias' => $param, 'sfField.slider' => 1));
                                if ($this->modx->getCount('sfField', $c)) {
                                    $values = array_map('trim', explode($this->config['values_delimeter'], $value));
                                    $c->leftJoin('sfDictionary', 'sfDictionary',
                                        'sfDictionary.field_id = sfField.id AND sfDictionary.active = 1');
                                    $c->select('sfField.id,sfDictionary.input');
                                    if ($c->prepare() && $c->stmt->execute()) {
                                        foreach ($c->stmt->fetchAll(PDO::FETCH_ASSOC) as $inp) {
                                            $i_values = array_map('trim',
                                                explode($this->config['values_delimeter'], $inp['input']));
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

                        $rule_array = $this->pdo->getArray('sfRule', array('id' => $rule_id, 'active' => 1));
                        $rule_base = $rule_array['base'];


                        if ((count($diff_fields) && $rule_base) || !count($diff_fields)) {
                            $meta = $this->getRuleMeta(array_intersect_key(array_merge($base_params, $diff_params),
                                array_flip($rule_fields)), $rule_id, $pageId, 1, 0, $original_params);
                            if (!empty($meta['diff'])) {
                                $diff = array_merge($diff, $meta['diff']);
                            }
                            if ($meta['success']) {
                                $meta['find'] = $find = 1;
                                //обновление счётчика, если отличается количество
                                if (empty($diff) && empty($diff_fields) && ($meta['total'] != $meta['old_total']) && !$meta['has_slider']) {
                                    $this->updateUrlTotal($meta['url_id'], $meta['total']);
                                }
                            } else {
                                $find = 0;
                            }
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
                    $meta['full_url'] = $this->modx->makeUrl($pageId, '', '', '-1');
                } else {
                    $meta['link_url'] = $meta['url'] . $this->config['url_suffix'];
                }


                if ($this->config['crumbsReplace']) {
                    $crumb_array = $this->getCrumbs($pageId);
                    if ($find) {
                        $crumb_array['sflink'] = $meta['link'];
                        $crumb_array['sfurl'] = $meta['link_url'];
                    }
                    if (!empty($meta['nested'])) {
                        $crumb_array['sfnested'] = $meta['nested'];
                    }

                    $crumbs = $this->pdo->getChunk($this->config['crumbsCurrent'], $crumb_array);
                    $meta['crumbs'] = $crumbs;
                }

                if ($meta['url']) {
                    if ($pageId == $this->config['site_start']) {
                        if ($this->config['main_alias']) {
                            $q = $this->modx->newQuery('modResource');
                            $q->where(array('id' => $pageId));
                            $q->select('alias');
                            $alias = $this->modx->getValue($q->prepare());
                            $meta['url'] = '/' . $alias . $this->config['between_urls'] . $meta['url'] . $this->config['url_suffix'];
                        } else {
                            $meta['url'] = '/' . $meta['url'] . $this->config['url_suffix'];
                        }
                    } else {
                        $meta['url'] = $this->config['between_urls'] . $meta['url'] . $this->config['url_suffix'];
                    }
                    $meta['full_url'] = $this->clearSuffixes($this->modx->makeUrl($pageId, '', '',
                            '-1')) . $meta['url'];
                } else {
                    if ($pageId == $this->config['site_start']) {
                        $meta['url'] = '';
                    } else {
                        $meta['url'] = isset($this->config['this_page_suffix'])
                            ? $this->config['this_page_suffix']
                            : $this->config['container_suffix'];
                    }
                }

                if (count($diff)) {
                    if ($this->config['page_tpl'] && array_key_exists($this->config['page_key'], $diff)) {
                        if ($diff[$this->config['page_key']] == 1) {
                            unset($diff[$this->config['page_key']]);
                        } else {
                            $page_part = $this->pdo->parseChunk('@INLINE ' . $this->config['page_tpl'], array(
                                'pageVarKey' => $this->config['page_key'],
                                'pagevarkey' => $this->config['page_key'],
                                $this->config['page_key'] => $diff[$this->config['page_key']]
                            ));

                            if (mb_substr($meta['url'], -1, 1) === mb_substr($page_part, 0, 1)) {
                                $page_part = mb_substr($page_part, 1);
                            }

                            $meta['url'] .= $page_part;
                            $meta['full_url'] .= $page_part;

                            unset($diff[$this->config['page_key']]);
                        }
                    }

                    if (!empty($diff)) {
                        $hash_part = $this->getHashUrl($diff);
                        if (strpos($meta['url'], '?')) {
                            $meta['url'] .= str_replace('?', '&', $hash_part);
                            $meta['full_url'] .= str_replace('?', '&', $hash_part);
                        } else {
                            $meta['url'] .= $hash_part;
                            $meta['full_url'] .= $hash_part;
                        }
                    }
                }

                $plugin_response = $this->invokeEvent('sfOnReturnMeta',
                    array('action' => $action, 'page' => $pageId, 'meta' => $meta, 'SeoFilter' => $this));
                if ($plugin_response['success']) {
                    $meta = $plugin_response['data']['meta'];
                }

                $response = array(
                    'success' => true,
                    'data' => $meta,
                );
                return $this->config['json_response']
                    ? $this->modx->toJSON($response)
                    : $response;
                break;
            default:
                return $this->error('sf_err_ajax_nf', array(), array('action' => $action));
        }
    }

    public function getCrumbs($pageId = 0)
    {
        $page_array = array();
        if ($pageId) {
            if ($page_array = $this->pdo->getArray('modResource', $pageId)) {
                if (empty($page_array['menutitle'])) {
                    $page_array['menutitle'] = $page_array['pagetitle'];
                }
                if ($page_array['class_key'] == 'modWebLink') {
                    $page_array['link'] = is_numeric(trim($page_array['content'], '[]~ '))
                        ? $this->pdo->makeUrl(intval(trim($page_array['content'], '[]~ ')), $page_array)
                        : $page_array['content'];
                } else {
                    $page_array['link'] = $this->pdo->makeUrl($page_array['id'], $page_array);
                }
            }
        }
        return $page_array;
    }

    public function changePdoPageSession($config = array(), $hash = '')
    {
        $success = false;
        if (empty($hash)) {
            $hash = (string)$this->config['pdopage_hash'];
        }

        $to_delete = array('where', 'innerJoin', 'leftJoin', 'includeTVs');
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
                                    $v = str_replace(array('`', 'msProductData.', 'modResource.'),
                                        array('', 'msProduct.', ''), $v);
                                }
                                $data[$param] = $value;
                            } else {
                                if (strpos($value, 'msCategoryMember.category_id') !== false) {
                                    $parents = array_pop(explode('msCategoryMember.category_id', $value));
                                    $parents = str_replace(array('IN', '=', '(', ')', ' '), '', $parents);
                                    $_SESSION['pdoPage'][$hash]['parents'] = $config['parents'] = $parents;
                                    continue;
                                }
                                $data[$param] = str_replace(array('`', 'msProductData.', 'modResource.'),
                                    array('', 'msProduct.', ''), $value);
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

    public function findSeoLink($field_word = array(), $page_id = 0, $url_id = 0)
    {
        $link = array();
        $q = $this->modx->newQuery('sfUrls');
        $q->where(array('page_id' => $page_id));
        if ($url_id) {
            $q->where(array('id:!=' => $url_id));
        }
        $q->groupby('sfUrls.id');
        $q->select($this->modx->getSelectColumns('sfUrls', 'sfUrls', ''));
        $index = 0;
        foreach ($field_word as $i => $fw) {
            $q->rightJoin('sfUrlWord', "sfUrlWord{$index}", "sfUrls.id = sfUrlWord{$index}.url_id");
            $q->where(array(
                "sfUrlWord{$index}.word_id" => $fw['word_id'],
                "sfUrlWord{$index}.field_id" => $fw['field_id']
            ));
            if (!$index) {
                $q->innerJoin('sfUrlWord', 'sfUrlCount', "sfUrlWord{$index}.url_id = sfUrlCount.url_id");
                $q->select("COUNT(sfUrlWord{$index}.id) as levels");
            }
            $index++;
//            $q->sortby('sfUrlWord.priority','ASC');
        }
        if (count($field_word)) {
            $q->having('levels = ' . count($field_word));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['field_word'] = $field_word;
                $link = $row;
            }
        }

        return $link;
    }

    public function recursiveLinkFind($field_word = array(), $page_id = 0, $skip = 0, $url_id = 0)
    {
        $result = array(
            'find' => false,
            'link' => array()
        );
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

    public function findNestedCrumbs($field_word = array(), $page_id = 0, $url_id = 0)
    {
        $links = array();
        $count = count($field_word);
        $for_find = array();
        foreach ($field_word as $field_id => $word_id) {
            $for_find[] = array(
                'field_id' => $field_id,
                'word_id' => $word_id
            );
        }
        $for_find = array_reverse($for_find, 0);

//        $this->modx->log(1,print_r($for_find,1));
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
//        $links[] = $this->findSeoLink($for_find,$page_id);

        return $links;
    }

    public function error($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );
        return $this->config['json_response']
            ? $this->modx->toJSON($response)
            : $response;
    }

    public function ruleFields($rule_id = 0)
    {
        $fields = array();

        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('sfFieldIds.multi_id' => $rule_id));
        $q->leftJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id');
        $q->select('sfField.alias');
        if ($q->prepare() && $q->stmt->execute()) {
            $fields = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $fields;
    }

    public function findRuleId($page_id = 0, $params = array(), $first_params = array(), $other_params = array())
    {
        if (!count($first_params)) {
            $copyparams = $params;
//            $aliases = $this->fieldsAliases($this->config['page'],1);
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
        $diff = $find = $rule_aliases = array();
        $params_keys = array_keys(array_merge($first_params, $other_params));

        $page_aliases = $this->pageAliases($page_id, array_keys($first_params));
        foreach ($page_aliases as $rule => $ralias) {
            $sort = $ralias['sort'];
            $base = $ralias['base'];
            unset($ralias['sort']);
            unset($ralias['base']);
            foreach ($ralias as $ra) {
                $rule_aliases[$rule]['sort'] = $sort;
                $rule_aliases[$rule]['base'] = $base;
                $rule_aliases[$rule]['fields'][$ra['alias']] = array(
                    'where' => $ra['where'],
                    'compare' => $ra['compare'],
                    'value' => $ra['value']
                );
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
                        $values = explode($this->config['values_delimeter'], $value);
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
                if ($check == count($fields)) {
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
     * @param array $params_keys
     * @param int $page_id
     * @return int
     */
    public function findRule($params_keys = array(), $page_id = 0)
    {
        //$this->modx->log(modx::LOG_LEVEL_ERROR, 'SEOFILTER: '.print_r($params_keys,1));
        $rule_id = 0;
        $params = $params_keys;
        $find_params = array();
        $q = $this->modx->newQuery('sfFieldIds');
        $q->limit(0);
        $shift = array_shift($params);
        $q->innerJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id AND sfField.alias = "' . $shift . '"');
        if (count($params)) {
            foreach ($params as $key => $alias) {
                $q->innerJoin('sfFieldIds', 'sfFieldIds' . $key,
                    'sfFieldIds.multi_id = sfFieldIds' . $key . '.multi_id ');
                $q->innerJoin('sfField', 'sfField' . $key,
                    'sfFieldIds' . $key . '.field_id = sfField' . $key . '.id AND sfField' . $key . '.alias = "' . $alias . '"');
            }
        }
        $q->innerJoin('sfRule', 'sfRule',
            'sfFieldIds.multi_id = sfRule.id AND sfRule.active = 1 AND sfRule.page = ' . $page_id);
        $q->sortby('sfRule.rank', 'ASC');
        $q->select(array('sfFieldIds.*', 'sfField.alias', 'sfRule.base'));
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
        $q->where(array('multi_id' => $rule_id));
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
        $params = array(),
        $rule_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $original_params = array()
    ) {
        $seo_system = array('id', 'field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'key');
        $seo_array = array(
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
        );
        $fields = $word_array = $aliases = $field_word = array();
        $meta = array('success' => true, 'diff' => array());
        $countFields = $this->countRuleFields($rule_id);
        $diff_params = array();
        $check = 0;
        $link_id = 0;
        $has_slider = 0;

        $params_to_text = array();

        $sort_field_word = array();

        $fields_key = array();
        // если не нужно пересчитывать на странице с учётом гет параметра - то это закоментить, а ниже раскоментить
        $fields_keys = $this->getFieldsKey('alias');
        foreach ($fields_keys as $fk => $fks) {
            $fields_key = array_merge($fields_key, $fks);
        }

        foreach ($params as $param => $input) {
            if ($field = $this->pdo->getArray('sfField', array('alias' => $param))) {
                $field_id = $field['id'];
                $alias = $field['alias'];
                $fields[] = $field_id;
                if ($field['slider']) {
                    $has_slider = 1;
                }

                if ($word = $this->getWordArray($input, $field_id, $field['slider'], $this->config['mfilterWords'],
                    $ajax)) {
                    foreach (array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                        if ($countFields == 1) {
                            $word_array[$tmp_key] = $tmp_array;
                        }
                        $word_array[str_replace('value', $alias, $tmp_key)] = $tmp_array;
                        $word_array[$alias . '_input'] = $word_array['input'];
                        $word_array[$alias . '_alias'] = $word_array['alias'];
                        $word_array['m_' . $alias] = $word_array['m_' . $alias . '_i'];
                    }

                    $params_to_text[$param] = array(
                        'input' => $word['input'],
                        'value' => $word['value'],
                        'alias' => $word['alias'],
                        'field' => $word['field_id'],
                        'class' => $field['class'],
                        'key' => $field['key']
                    );
                    $aliases[$param] = $word['alias'];

//                    $fields_key[$alias]['class'] = $field['class'];
//                    $fields_key[$alias]['key'] = $field['key'];
//                    $fields_key[$alias]['exact'] = $field['exact'];
//                    $fields_key[$alias]['slider'] = $field['slider'];
//                    $fields_key[$alias]['xpdo_package'] = $field['xpdo_package'];

                    $field_word[$field_id] = $word['id'];
                } else {
                    //здесь когда переданы левые значения через ajax
                    $meta['success'] = false;
                    $meta['diff'] = array_merge($original_params, $params);
                    return $meta;
                }

                $q = $this->modx->newQuery('sfFieldIds');
                $q->sortby('priority', 'ASC');
                $q->where(array('multi_id' => $rule_id, 'field_id' => $field_id));
                $q->select(array('sfFieldIds.*'));
                if ($q->prepare() && $q->stmt->execute()) {
                    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        $sort_field_word[$row['field_id']] = $row['priority'];
                        if ($row['where'] && $row['compare'] && $row['value']) {
                            $c = $this->modx->newQuery('sfDictionary');
                            $c->select(array('sfDictionary.*'));
                            $c->where(array('field_id' => $row['field_id'], 'active' => 1));
//                            if($this->config['proMode'] && strpos($input,',') === false) {
//                                $c->where('1=1 AND FIND_IN_SET("' . str_replace(',','&',$input) . '",REPLACE(REPLACE(input,",","&"),"||",","))');
//                            } else {
                            $c->where(array('input' => $input));
//                            }
                            $value = $row['value'];
                            $values = explode($this->config['values_delimeter'], $value);
                            switch ($row['compare']) { //Обратный механизм поиска
                                case 1:
                                    $c->where(array('input:NOT IN' => $values));
                                    break;
                                case 2:
                                    $c->where(array('input:IN' => $values));
                                    break;
                                case 3:
                                    $c->where(array('input:<' => $value));
                                    break;
                                case 4:
                                    $c->where(array('input:>' => $value));
                                    break;
                                case 5:
                                    $c->where(array('input:<' => $values[0], 'AND:input:>' => $values[1]));
                                    break;
                                case 6:
                                    $c->where(array('input:NOT LIKE' => '%' . $value . '%'));
                                    break;
                                case 7:
                                    $c->where(array('input:LIKE' => '%' . $value . '%'));
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
                $meta['nested'] = array();
                foreach ($nested as $link) {
                    $link['url'] = $link['new_url'] ?: $link['old_url'];
                    $link['sflink'] = $link['link'];
                    $link['sfurl'] = $link['url'] . $this->config['url_suffix'];
                    $meta['nested'][] = $link;
                }
                $meta['nested'] = $ajax
                    ? $meta['nested']
                    : $this->modx->toJSON($meta['nested']);
            }
        }


        if ($seo = $this->pdo->getArray('sfRule', array('id' => $rule_id, 'active' => 1))) {
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

            foreach (array('id', 'page', 'page_id') as $pkey) {
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
                if ($meta['editedon'] == '0000-00-00 00:00:00') {
                    $meta['editedon'] = $url_array['createdon'];
                } else {
                    $meta['editedon'] = $url_array['editedon'];
                }
            }


            foreach ($seo_array as $tag => $text) {
                if ($text) {
                    if (strpos($text, '@INLINE') !== false) {
                        $tpl = $text;
                    } else {
                        $tpl = '@INLINE ' . $text;
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

            foreach (array('properties', 'introtexts') as $prop) {
                $seo_values = array();
                if (!empty($url_array[$prop]) && !empty($url_array[$prop]['values'])) {
                    $seo_values = $url_array[$prop]['values'];
                } elseif (!empty($seo[$prop]) && !empty($seo[$prop]['values'])) {
                    $seo_values = $seo[$prop]['values'];
                }
                if (!empty($seo_values)) {
                    $properties = array();
                    $array_word = array();
                    foreach ($word_array as $key => $val) {
                        $array_word['$' . $key] = "'" . $val . "'";
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
        $diff = array();
        if (!empty($url_array['diff'])) {
            foreach ($url_array['diff'] as $param => $alias) {
                if ($diff_arr = $this->pdo->getArray('sfDictionary', array('alias' => $alias, 'active' => 1))) {
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
        $params = array(),
        $fields_key = array(),
        $parents = '',
        $count_where = array(),
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
        $params = array(),
        $fields_key = array(),
        $parents,
        $count_where = array(),
        $min_max = 0
    ) {
        $count = 0;
        $innerJoin = array();
        $addTVs = array();
        $fields_where = array();
        $params_keys = array_diff(array_keys($params), array_keys($fields_key));


        foreach ($params_keys as $param) {
            if ($field = $this->pdo->getArray('sfField', array('alias' => $param))) {
                $alias = $field['alias'];
                $fields_key[$alias]['class'] = $field['class'];
                $fields_key[$alias]['key'] = $field['key'];
                $fields_key[$alias]['exact'] = $field['exact'];
                $fields_key[$alias]['slider'] = $field['slider'];
                $fields_key[$alias]['xpdo_package'] = $field['xpdo_package'];
            }
        }

//        if(count(array_diff(array_keys($params), array_keys($fields_key)))) {
//            $this->modx->log(modx::LOG_LEVEL_ERROR,"[SeoFilter] don't known this fields. Please add this fields to the first tab in component (Fields)".print_r(array_diff(array_keys($params), array_keys($fields_key)),1));
//        }


        foreach ($fields_key as $field_alias => $field) {
            switch ($field['class']) {
                case 'msProductData':
                case 'modResource':
                case 'msProductOption':
                    $fw = $field['class'] . '.' . $field['key'];
                    if ($field['class'] == 'msProductData') {
                        $innerJoin['msProductData'] = array(
                            'class' => 'msProductData',
                            'on' => 'msProductData.id = modResource.id'
                        );
                    }
                    if ($field['class'] == 'msProductOption') {
                        $innerJoin['msOption' . $field['key']] = array(
                            'class' => 'msProductOption',
                            'on' => 'msOption' . $field['key'] . '.product_id = modResource.id'
                        );
                        $fields_where['msOption' . $field['key'] . '.key'] = $field['key'];
                        $fw = 'msOption' . $field['key'] . '.value';
                    }
                    if ($field['slider']) {
                        $slider = explode($this->config['values_delimeter'], $params[$field_alias]);
                        $fields_where[$fw . ':>='] = $slider[0];
                        if ($slider[1]) {
                            $fields_where[$fw . ':<='] = $slider[1];
                        }
                    } else {
                        $values = explode($this->config['values_delimeter'], $params[$field_alias]);
                        if (!isset($count_where[$fw])) {
                            $fields_where[$fw . ':IN'] = $values;
                        }
                    }
                    break;
                case 'modTemplateVar':
                    $addTVs[] = $field['key'];
                    if (strtolower($field['xpdo_package']) == 'tvsuperselect') {
                        $this->pdo->setConfig(array('loadModels' => 'tvsuperselect'));
                        $innerJoin['tvssOption' . $field['key']] = array(
                            'class' => 'tvssOption',
                            'on' => 'tvssOption' . $field['key'] . '.resource_id = modResource.id'
                        );
                        $fields_where['tvssOption' . $field['key'] . '.value:LIKE'] = '%' . $params[$field_alias] . '%';
                    } elseif ($field['exact']) {
                        $fields_where['TV' . $field['key'] . '.value'] = $params[$field_alias];
                    } else {
                        $fields_where['TV' . $field['key'] . '.value:LIKE'] = '%' . $params[$field_alias] . '%';
                    }
                    break;
                case 'msVendor':
                    $innerJoin['msProductData'] = array(
                        'class' => 'msProductData',
                        'on' => 'msProductData.id = modResource.id'
                    );
                    $innerJoin['msVendor'] = array('class' => 'msVendor', 'on' => 'msVendor.id = msProductData.vendor');
                    $fields_where[$field['class'] . '.id'] = $params[$field_alias];
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
                            $innerJoin['msProductData'] = array(
                                'class' => 'msProductData',
                                'on' => 'msProductData.id = modResource.id'
                            );
                            break;
                        case 'msProductOption':
                            $innerJoin['msProductOption'] = array(
                                'class' => 'msProductOption',
                                'on' => 'msProductOption.product_id = modResource.id'
                            );
                            break;
                        case 'msVendor':
                            $innerJoin['msProductData'] = array(
                                'class' => 'msProductData',
                                'on' => 'msProductData.id = modResource.id'
                            );
                            $innerJoin['msVendor'] = array(
                                'class' => 'msVendor',
                                'on' => 'msVendor.id = msProductData.vendor'
                            );
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
            $select = $min_max_array = $count_choose = $count_select = array();
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
                            $innerJoin['msProductData'] = array(
                                'class' => 'msProductData',
                                'on' => 'msProductData.id = modResource.id'
                            );
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
                        $innerJoin['msProductData'] = array(
                            'class' => 'msProductData',
                            'on' => 'msProductData.id = modResource.id'
                        );
                    }
                    if ($class == 'msProductOption') {
                        $innerJoin['msProductOption'] = array(
                            'class' => 'msProductOption',
                            'on' => 'msProductOption.product_id = modResource.id'
                        );
                    }
                    $sortby = $class . '.' . $choose_alias;
                } else {
                    if (strpos($choose, '=')) {
                        $chooses = explode('=', $choose);
                        $choose = array_shift($chooses);
                        $choose_alias = implode('=', $chooses);
                    } else {
                        $choose_alias = $choose;
                    }
                    $sortby = 'modResource.' . $choose_alias;
                }

                foreach (array('max' => 'DESC', 'min' => 'ASC') as $m => $sort) {
                    if ($m == 'min') {
                        $where = array_merge($where, array($sortby . ':>' => 0));
                    }
                    $this->pdo->setConfig(array(
                        'showLog' => 0,
                        'class' => 'modResource',
                        'parents' => $parents,
                        'includeTVs' => $addTVs,
                        'innerJoin' => $innerJoin,
                        'where' => $where,
                        'limit' => 1,
                        'sortby' => $sortby,
                        'sortdir' => $sort,
                        'return' => 'data',
                        'select' => $select
                    ));

                    if ($run = $this->pdo->run()) {
                        foreach ($run[0] as $key => $value) {
                            $min_max_array[$m . '_' . $choose_alias . '_' . $key] = $value;
                        }
                    }

                }
            }

            return $min_max_array;
        } else {
            $this->pdo->setConfig(array(
                'showLog' => 0,
                'class' => 'modResource',
                'parents' => $parents,
                'includeTVs' => $addTVs,
                'innerJoin' => $innerJoin,
                'where' => $where,
                'return' => 'data',
                'select' => array(
                    'modResource' => 'COUNT(modResource.id) as count'
                )
            ));


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
        $word = array();
        $q = $this->modx->newQuery('sfDictionary');
        if ($slider) {
            if (strpos($input, $this->config['values_delimeter']) === false) {
                $values = array($input, $input);
            } else {
                $values = array_map('trim', explode($this->config['values_delimeter'], $input));
            }
            $q->where(array('field_id' => $field_id, 'active' => 1));
            $q->limit(0);
            $q->select(array('sfDictionary.*'));
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
                    $min_diff_word = array();
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
            $q->where(array('field_id' => $field_id, 'active' => 1));
            if ($this->config['proMode'] && strpos($input, ',') === false && $ajax) {
                $q->where('1=1 AND FIND_IN_SET("' . str_replace(',', '&',
                        $input) . '",REPLACE(REPLACE(input,",","&"),"||",","))');
            } else {
                $q->where(array('input' => $input));
            }
            if ($this->modx->getCount('sfDictionary', $q)) {
                $q->select(array('sfDictionary.*'));
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
                        $processorProps = array(
                            'class' => $field->get('class'),
                            'key' => $field->get('key'),
                            'field_id' => $field->get('id'),
                            'value' => $value,
                            'input' => $input,
                        );

                        if ($relation_value) {
                            $relation_field = $field->get('relation_field');
                            $s = $this->modx->newQuery('sfDictionary');
                            $s->where(array('input' => $relation_value, 'field_id' => $relation_field, 'active' => 1));
                            $s->select('id');
                            $relation_id = $this->modx->getValue($s->prepare());
                        }

                        if ($relation_id) {
                            $processorProps['relation_word'] = $relation_id;
                        }

                        $otherProps = array('processors_path' => $this->config['processorsPath']);
                        $response = $this->modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                        if ($response->isError()) {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] ' . print_r($response->response, 1));
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
        $system = array(
            'title' => $this->config['title'],
            'description' => $this->config['description'],
            'introtext' => $this->config['introtext'],
            'keywords' => $this->config['keywords'],
            'h1' => $this->config['h1'],
            'h2' => $this->config['h2'],
            'text' => $this->config['text'],
            'content' => $this->config['content'],
            'link' => $this->config['link'],
        );
        $meta = array();
        $meta['page_id'] = $meta['id'] = $page_id;

        if ($page = $this->modx->getObject('modResource', $page_id)) {
            $page_arr = $page->toArray();
            $page_keys = array_keys($page_arr);

            $meta = array_merge($meta, $page_arr);
            $variables = $this->prepareRow($meta, $page_id);

            $array_diff = array_diff($system, $page_keys);
            foreach ($array_diff as $tag => $tvname) {
                if ($tvvalue = $page->getTVValue($tvname)) {
                    $tpl = '@INLINE ' . $tvvalue;
                    $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
                    unset($system[$tag]);
                }
            }
            foreach ($system as $tag => $tagname) {
                if ($tagname) {
                    if (strpos($tagname, '@INLINE') !== false) {
                        $tpl = $tagname;
                    } elseif (strpos($tagname, '[') !== false || strpos($tagname, '{') !== false) {
                        $tpl = '@INLINE ' . $tagname;
                    } else {
                        $tpl = '@INLINE ' . $page->get($tagname);
                    }
                    $meta[$tag] = $this->pdo->getChunk($tpl, $variables);
                }
            }

            if (empty($page_arr['isfolder'])) {
                $q = $this->modx->newQuery('modContentType', array('name' => 'HTML'));
                $q->select('file_extensions');
                $this->config['this_page_suffix'] = $this->modx->getValue($q->prepare());
            }

        }

        if (isset($meta['title'])) {
            $meta['pagetitle'] = $meta['title'];
        }


        return $meta;
    }

    public function prepareRow($row = array(), $page_id = 0, $rule_id = 0, $rule = array())
    {
        if (!empty($this->config['prepareSnippet'])) {
            $name = trim($this->config['prepareSnippet']);
            array_walk_recursive($row, function (&$value) {
                $value = str_replace(
                    array('[', ']', '{', '}'),
                    array('*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
                    $value
                );
            });
            $tmp = $this->modx->runSnippet($name, array_merge($this->config, array(
                'row' => serialize($row),
                'input' => $row['input'] ? $row['input'] : 0,
                'seoFilter' => $this,
                'SeoFilter' => $this,
                'pdoTools' => $this->pdo,
                'pdoFetch' => $this->pdo,
                'rule_id' => $rule_id,
                'page_id' => $page_id,
                'rule' => serialize($rule)
            )));
            $tmp = (strpos($tmp,'[') === 0 || strpos($tmp,'{') === 0)
                ? json_decode($tmp, true)
                : unserialize($tmp,array('allowed_classes' => false));
            if (!is_array($tmp)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    '[SeoFilter]: Preparation snippet must return an array, instead of "' . gettype($tmp) . '"');
            } else {
                $row = array_merge($row, $tmp);
            }
            array_walk_recursive($row, function (&$value) {
                $value = str_replace(
                    array('*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
                    array('[', ']', '{', '}'),
                    $value
                );
            });
        }
        return $row;
    }

    public function getParamsByUrl($url = '')
    {
        $params = array();

        $q = $this->modx->newQuery('sfUrls');
        $q->where(array('old_url' => $url, 'OR:new_url:=' => $url));
        $q->leftJoin('sfUrlWord', 'sfUrlWord', 'sfUrlWord.url_id = sfUrls.id');
        $q->innerJoin('sfField', 'Field', 'Field.id = sfUrlWord.field_id');
        $q->innerJoin('sfDictionary', 'Word', 'Word.id = sfUrlWord.word_id AND Word.active = 1');
        $q->sortby('sfUrlWord.priority', 'ASC');
        $q->groupby('sfUrlWord.id');
        $q->select(array(
            'sfUrlWord.*',
            'sfUrls.multi_id as rule_id,sfUrls.page_id as page_id',
            'Field.class as field_class,Field.key as field_key,Field.alias as field_alias',
            'Word.input as word_input,Word.value as word_value,Word.alias as word_alias'

        ));
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
            $q->where(array('old_url' => $url, 'OR:new_url:=' => $url));
            $q->select('id');
            $url_id = $this->modx->getValue($q->prepare());
        }
        return $url_id;
    }

    public function findUrlArray($url = '', $page = 0)
    {
        $url_array = array();
        $q = $this->modx->newQuery('sfUrls');
        $q->where(array('page_id' => $page));
        $q->where(array('old_url:LIKE' => $url, 'OR:new_url:LIKE' => $url));
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
        $field_word = array(),
        $link_tpl = ''
    ) {
        $seo_system = array('field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'createdon', 'key');
        $url = array();
        if ($ajax) {
            $new = $ajax;
        }
        $link = '';
        if (!empty($link_tpl)) {
            $all_words = array();
            $words = array();
            foreach ($field_word as $fw) {
                $words[] = $fw['word_id'];
            }
            $q = $this->modx->newQuery('sfDictionary');
            $q->innerJoin('sfField', 'Field', 'Field.id = sfDictionary.field_id');
            $q->where(array('id:IN' => $words, 'active' => 1));
            $q->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', '', $seo_system, 1));
            $q->select($this->modx->getSelectColumns('sfField', 'Field', 'field_', array('alias')));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $all_words = array_merge($all_words,
                        $this->prepareWordsToLink($row, array('alias' => $row['field_alias']), count($all_words)));
                }
            }

            foreach (array('id', 'page', 'page_id') as $pkey) {
                if (!isset($all_words[$pkey])) {
                    $all_words[$pkey] = $page_id;
                }
            }

            $link = $this->pdo->getChunk('@INLINE ' . $link_tpl, $all_words);
        }

        $processorProps = array(
            'old_url' => $old_url,
            'multi_id' => $multi_id,
            'page_id' => $page_id,
            'ajax' => $ajax,
            'count' => $new,
            'field_word' => $field_word,
            'link' => $link
        );

        $otherProps = array('processors_path' => $this->config['corePath'] . 'processors/');
        $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]: ' . print_r($response->getMessage(), 1));
            $this->modx->error->reset();
        } else {
            $url = $response->response['object'];
        }

        return $url;
    }

    public function addUrlCount($url_id = 0, $filter = 0)
    {
        if ($url = $this->modx->getObject('sfUrls', array('active' => 1, 'id' => $url_id))) {
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
     * @param string $value
     * @param array $field
     * @return string
     */
    public function fieldUrl($value = '', $field = array())
    {
        if (!$alias = $field['alias']) {
            $alias = $field['key'];
        }
        if ($field['hideparam']) {
            $url = $value;
        } else {
            if ($field['valuefirst']) {
                $url = $value . $this->config['separator'] . $alias;
            } else {
                $url = $alias . $this->config['separator'] . $value;
            }
        }
        return $url;
    }

    public function multiUrl(
        $aliases = array(),
        $multi_id = 0,
        $page_id = 0,
        $ajax = 0,
        $new = 0,
        $field_word = array()
    ) {
        $url = array();
        if ($multi_id) {
            if ($rule = $this->pdo->getArray('sfRule', array('id' => $multi_id, 'active' => 1))) {
                $link_tpl = $rule['link_tpl'];
                //               $tpl = '@INLINE ' . $rule['url'];
                // $url['url'] = $this->pdo->getChunk($tpl, $aliases);
                //               $url_link = $this->pdo->getChunk($tpl, $aliases);
                $url_link = $rule['url'];
                foreach ($aliases as $key => $value) {
                    $url_link = str_replace('{$' . $key . '}', $value, $url_link);
                }
                if ($url_array = $this->pdo->getArray('sfUrls', array('page_id' => $page_id, 'old_url' => $url_link))) {
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
                    $field_words = array();
                    foreach ($field_word as $field_id => $word_id) {
                        $field_words[] = array('field_id' => $field_id, 'word_id' => $word_id);
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
                $url['url'] .= $param . '=' . $alias;
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
        $registry->subscribe('/modstore/' . md5($key));
        if ($res = $registry->read(array('poll_limit' => 1, 'remove_read' => false))) {
            return;
        }
        $c = $this->modx->newQuery('transport.modTransportProvider', array('service_url:LIKE' => '%modstore%'));
        $c->select('username,api_key');
        /** @var modRest $rest */
        $rest = $this->modx->getService('modRest', 'rest.modRest', '', array(
            'baseUrl' => 'https://modstore.pro/extras',
            'suppressSuffix' => true,
            'timeout' => 1,
            'connectTimeout' => 1,
        ));

        if ($rest) {
            $level = $this->modx->getLogLevel();
            $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
            $response = $rest->post('stat', array(
                'package' => $key,
                'version' => $this->version,
                'keys' => $c->prepare() && $c->stmt->execute()
                    ? $c->stmt->fetchAll(PDO::FETCH_ASSOC)
                    : array(),
                'uuid' => $this->modx->uuid,
                'database' => $this->modx->config['dbtype'],
                'revolution_version' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'supports' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
                'http_host' => $this->modx->getOption('http_host'),
                'php_version' => XPDO_PHP_VERSION,
                'language' => $this->modx->getOption('manager_language'),
            ));
            $this->modx->setLogLevel($level);
        }
        $registry->subscribe('/modstore/');
        $registry->send('/modstore/', array(md5($key) => true), array('ttl' => 3600 * 24));
    }

    public function createLinks($rule_id = 0, $urls = array(), $where = array(), $update = 0)
    {
        $otherProps = array('processors_path' => $this->config['processorsPath']);

        $old_links = array();
        $find_links = array();
        $new_links = array();
        $doubles = 0;
        $success_create = 0;
        $success_delete = 0;

        $old_links_ids = array();

        $q = $this->modx->newQuery('sfUrls');
        $q->where(array('multi_id' => $rule_id));
        if ($where) {
            foreach ($where as $field_id => $w) {
                $q->rightJoin('sfUrlWord', 'sfUrlWord', 'sfUrls.id = sfUrlWord.url_id');
                $q->where(array('sfUrlWord.word_id' => $w['id']));
                $q->groupby('sfUrls.id');
                $q->select(array(
                    'sfUrls.id,sfUrls.old_url as url,sfUrls.link as name,sfUrls.page_id as page'
                ));
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


        $find_links_ids = array();
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


        //TODO: закомментировать при пошаговом добавлении ссылок и добавить метод проверки после
        $del_links = array_diff(array_keys($old_links_ids), $find_links_ids);
        if (!empty($del_links)) {
            $processorProps = array(
                'ids' => $this->modx->toJSON($del_links),
                'to_log' => 1
            );
            $response = $this->modx->runProcessor('mgr/urls/remove', $processorProps, $otherProps);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] ' . $response->getMessage());
                $this->modx->error->reset();
            } else {
                $success_delete = count($del_links);
            }
        }


        if (!empty($new_links)) {
            foreach ($new_links as $url) {
                $processorProps = array(
                    'multi_id' => $rule_id,
                    'old_url' => $url['url'],
                    'page_id' => $url['page'],
                    'link' => $url['name'],
                    'field_word' => $url['relation'],
                    'from_rule' => 1,
                );
                $response = $this->modx->runProcessor('mgr/urls/create', $processorProps, $otherProps);
                if ($response->isError()) {
                    if (in_array('double', $response->response['errors'])) {
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
                $q->where(array('custom:!=' => 1, 'id:IN' => array_keys($find_links)));
            } else {
                $q->where(array('link' => '', 'id:IN' => array_keys($find_links)));
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

        return array(
            'was_links' => count($old_links_ids), //было ссылок в правиле
            'old_links' => count($find_links_ids), //ссылок старых осталось
            'add_links' => $success_create, //новых ссылок добавлено
            'remove_links' => $success_delete, //удалено ссылок из правило
            'doubles_links' => $doubles, //дубли, ссылок в других правилах
            'update_links' => $update_links, //обновлено названий ссылок
            'all_links' => $all_links //всего ссылок стало в правиле
        );
    }

    public function generateUrlsByWord($word = array(), $update = 0)
    {
        $response = array();
        if ($field_id = $word['field_id']) {
            $q = $this->modx->newQuery('sfFieldIds');
            $q->innerJoin('sfRule', 'Rule', 'Rule.id = sfFieldIds.multi_id');
            $q->where(array('field_id' => $field_id));
            //$q->select($this->modx->getSelectColumns('sfFieldIds','sfFieldIds','link_'));
            $q->select($this->modx->getSelectColumns('sfRule', 'Rule', ''));
            $rules = array();
            if ($q->prepare() && $q->stmt->execute()) {
                while ($rule = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rules[] = $rule;
                    $where = array($field_id => array('id' => $word['id']));
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
        $where = array(),
        $update = 0,
        $offset = 0
    ) {
        $pages = array_map('trim', explode(',', $pages));
        $urls = $response = array();

        $limit = $this->modx->getOption('seofilter_url_limit', null, 500, true);
        $total = 0;

        if ($links = $this->gettingUrls($rule_id, $where)) {
            foreach ($pages as $page_id) {
                $total = count($links['words']) * count($pages);
                foreach ($links['words'] as $index => $link) {
                    if (empty($link['urls'])) {
                        continue;
                    }
//                    TODO: пошаговое добавление ссылок
//                    if($index >= $limit + $offset || $index < $offset) {
//                        continue;
//                    }

                    $words = array();
                    foreach ($link['relation'] as $relation) {
                        $words = array_merge($words,
                            $links['fields'][$relation['field_id']]['words'][$relation['word_id']]);
                    }
                    //TODO в $link['aliases'] хранятся синонимы для обработки url адреса через Fenom
                    if ($this->config['edit_url_mask']) {
                        $aliases = array();
                        foreach ($link['aliases'] as $alias => $value) {
                            $aliases['{$' . $alias . '}'] = $value;
                        }
                        $link_url = str_replace(array_keys($aliases), array_values($aliases), $url_mask);
                    } else {
                        $link_url = implode($this->config['level_separator'], $link['urls']);
                    }

                    foreach (array('id', 'page', 'page_id') as $pkey) {
                        if (!isset($words[$pkey])) {
                            $words[$pkey] = $page_id;
                        }
                    }

                    $link_name = '';
                    if (!empty($tpl)) {
                        $link_name = $this->pdo->getChunk('@INLINE ' . $tpl, $words);
                    }
                    unset($words);

                    $urls[] = array(
                        'url' => $link_url,
                        'name' => $link_name,
                        'page' => $page_id,
                        'relation' => $link['relation']
                    );
                }
            }

//            $this->modx->log(1,print_r($urls,1));
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
        $response['data'] = array(
            'done' => $done,
            'limit' => $limit,
            'offset' => $offset,
            'total' => $total,
            'value' => $percent,
            'text' => "{$offset}/{$total}"
        );

        return $response;
    }

    public function prepareWordsToLink($row = array(), $field = array(), $count_fields = 1)
    {
        $word = array();
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
                $word[$alias . '_id'] = $val;
                continue;
            }
            if ($count_fields == 1) {
                $word[$key] = $val;
            }
            $word[str_replace('value', $alias, $key)] = $val;
        }
        $word[$alias . '_input'] = $row['input'];
        $word[$alias . '_image'] = $row['image'];
        $word[$alias . '_alias'] = $row['alias'];
        $word['m_' . $alias] = $row['m_value_i'];

        return $word;
    }


    public function gettingUrls($rule_id = 0, $where = array())
    {
        if (!$rule_id) {
            return false;
        }
        $seo_system = array('field_id', 'multi_id', 'name', 'rank', 'active', 'class', 'editedon', 'createdon', 'key');
        $fields = array();
        $words = array();

        $q = $this->modx->newQuery('sfFieldIds');
        $q->where(array('multi_id' => $rule_id));
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
            $q->where(array('field_id' => $field_id, 'active' => 1));
            if (isset($where[$field_id])) {
                $q->where($where[$field_id]);
            }
            if ($field['where'] && $field['compare']) {
                $q->where($this->fieldWhere($field['compare'], $field['value']));
            }
//            $q->select($this->modx->getSelectColumns('sfDictionary','sfDictionary','',array('id','relation_word','input','value','alias')));
            $q->select($this->modx->getSelectColumns('sfDictionary', 'sfDictionary', '', $seo_system, 1));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $word = $this->prepareWordsToLink($row, array('alias' => $alias), count($fields));
//                    $word = $row;

                    if ($field['field_hideparam']) {
                        $word['url_part'] = $row['alias'];
                    } elseif ($field['field_valuefirst']) {
                        $word['url_part'] = $row['alias'] . $this->config['separator'] . $alias;
                    } else {
                        $word['url_part'] = $alias . $this->config['separator'] . $row['alias'];
                    }

                    $forMulti = array(
                        'urls' => array(
                            $word['url_part']
                        ),
                        'aliases' => array(
                            $alias => $row['alias'],
                        ),
//                        'words' => array(array('id'=>$row['id'])),
                        'field_relation' => $field['field_relation_field'],
                        'word_relation' => $row['relation_word'],
                        'relation' => array(
                            array(
                                'field_id' => $field_id,
                                'word_id' => $row['id'],
                                'field_relation' => $field['field_relation_field'],
                                'word_relation' => $row['relation_word'],
                            )
                        ),
                        'delete' => 0
                    );

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

        return array('words' => array_values($words1), 'fields' => $fields);
    }

    public function mergeWords(& $a1 = array(), & $a2 = array())
    {
        $a3 = array();
        for ($i = 0; $i < count($a1); $i++) {
            for ($j = 0; $j < count($a2); $j++) {
                $a3[] = $a1[$i] + $a2[$j];
            }
        }
        return $a3;
    }


    public function wordsMultiplication(& $a1 = array(), & $a2 = array())
    {
        $a3 = array();
//        foreach($a1 as $i => &$word1)  {
        for ($i = 0; $i < count($a1); $i++) {
            if ($i) {
//                unset($a1[$i-1]);
            }
//            foreach($a2 as $j => &$word2) {
            for ($j = 0; $j < count($a2); $j++) {
                $delete = $a1[$i]['delete'] || $a2[$j]['delete'];

                $arr = array(
                    'urls' => array_merge($a1[$i]['urls'], $a2[$j]['urls']),
//                    'urls' => $a1[$i]['urls'],
                    'aliases' => array_merge($a1[$i]['aliases'], $a2[$j]['aliases']),
//                    'aliases' => $a1[$i]['aliases'],
//                    'words'=>array_merge($a1[$i]['words'],$a2[$j]['words']),
//                    'words'=> $a1[$i]['words'],
                    'relation' => array_merge($a1[$i]['relation'], $a2[$j]['relation']),
//                    'relation'=>$a1[$i]['relation'],
                    'delete' => $delete
                );
                $find = 1;
                if ($a2[$j]['field_relation'] && $a2[$j]['word_relation']) {
                    $find = 0;
                    foreach ($a1[$i]['relation'] as $relation) {
                        if ($relation['field_id'] == $a2[$j]['field_relation']
                            && $relation['word_id'] == $a2[$j]['word_relation']) {
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
        $where = array();
        $values = array_map('trim', explode(',', $value));
        switch ($compare) {
            case 1:
                // в массиве
                $where = array('input:IN' => $values);
                break;
            case 2:
                // не в массиве
                $where = array('input:NOT IN' => $values);
                break;
            case 3:
                // больше чем
                $where = array('input:>' => $value);
                break;
            case 4:
                // меньше чем
                $where = array('input:<' => $value);
                break;
            case 5:
                // в диапазоне
                if (count($values) >= 2) {
                    $where = array('input:>' => $values[0], 'AND:input:<' => $values[1]);
                }
                break;
            case 6:
                //LIKE %value%
                $where = array('input:LIKE' => '%' . $value . '%');
                break;
            case 7:
                //NOT LIKE %value%
                $where = array('input:NOT LIKE' => '%' . $value . '%');
                break;
        }
        return $where;
    }
}