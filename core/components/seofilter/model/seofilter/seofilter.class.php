<?php

class SeoFilter
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    public $pdo;


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
        $actionUrl = $assetsUrl . 'action.php';
        $connectorUrl = $assetsUrl . 'connector.php';
        $separator = $this->modx->getOption('seofilter_separator', null, '-', true);
        $valuefirst = $this->modx->getOption('seofilter_valuefirst', null, 0, true);
        $redirect  = $this->modx->getOption('seofilter_redirect', null, 1, true);
        $site_start = $this->modx->context->getOption('site_start', 1);
        $charset = $this->modx->context->getOption('modx_charset', 'UTF-8');
        $title = $this->modx->getOption('seofilter_title', null, 'pagetitle', true);
        $description = $this->modx->getOption('seofilter_description', null, 'description', true);
        $introtext = $this->modx->getOption('seofilter_introtext', null, 'introtext', true);
        $h1 = $this->modx->getOption('seofilter_h1', null, 'longtitle', true);
        $h2= $this->modx->getOption('seofilter_h2', null, '', true);
        $text = $this->modx->getOption('seofilter_text', null, '', true);
        $content= $this->modx->getOption('seofilter_content', null, 'content', true);


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
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'params' => array(),
            'separator' => $separator,
            'valuefirst' => $valuefirst,
            'redirect' => $redirect,
            'site_start' => $site_start,
            'charset' => $charset,

            'title' => $title,
            'description' => $description,
            'introtext' => $introtext,
            'h1' => $h1,
            'h2' => $h2,
            'text' => $text,
            'content' => $content,
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
    public function initialize($ctx = 'web', $scriptProperties = array()) {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;

        $config = $this->makePlaceholders($this->config);
        if ($css = trim($this->modx->getOption('seofilter_frontend_css',null,$this->config['cssUrl'].'web/default.css',true))) {
           // $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
        }
        if ($js = trim($this->modx->getOption('seofilter_frontend_js',null,$this->config['jsUrl'].'web/default.js',true))) {
            $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));

            if($this->config['page']) {
                $q = $this->modx->newQuery('sfField');
                $q->limit(0);
                $q->where(array('page' => $this->config['page']));
                $q->select(array('DISTINCT sfField.alias'));
                if($q->prepare() && $q->stmt->execute()) {
                    $this->config['aliases'] = $q->stmt->fetchALL(PDO::FETCH_COLUMN);
                }
                $this->config['url'] = $this->modx->makeUrl($this->config['page'],$ctx,'','full');
            }

            $data = json_encode(array(
                'jsUrl' => $this->config['jsUrl'] . 'web/',
                'actionUrl' => $this->config['actionUrl'],
                'ctx' => $ctx,
                'page' => $this->config['page'],
                'params' => $this->config['params'],
                'aliases' => $this->config['aliases'],
                'separator' => $this->config['separator'],
                'valuefirst' => $this->config['valuefirst'],
                'redirect' => $this->config['redirect'],
                'url' => $this->config['url'],
            ), true);
            $this->modx->regClientStartupScript(
                '<script type="text/javascript">seoFilterConfig = ' . $data . ';</script>', true
            );
        }
        $this->initialized[$ctx] = true;
        return true;
    }

    /**
     * Method for transform array to placeholders
     *
     * @var array $array With keys and values
     * @var string $prefix
     *
     * @return array $array Two nested arrays With placeholders and values
     */
    public function makePlaceholders(array $array = array(), $prefix = '') {
        $result = array(
            'pl' => array(),
            'vl' => array(),
        );
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = array_merge_recursive($result, $this->makePlaceholders($v, $prefix . $k . '.'));
            }
            else {
                $result['pl'][$prefix . $k] = '[[+' . $prefix . $k . ']]';
                $result['vl'][$prefix . $k] = $v;
            }
        }

        return $result;
    }

    public function process($action, $data = array())
    {
        $params = $data['data'];
        $pageId = $data['pageId'];
        switch ($action) {
            case 'getmeta':
                if(count($params) > 1) {
                    $meta = $this->getMultiMeta($params,$pageId);
                } elseif(count($params) == 1) {
                    $meta = $this->getFieldMeta($params,$pageId);
                } else {
                    $meta = $this->getPageMeta($pageId);
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

    public function getPageMeta($page_id) {
        $system = array(
            'title'=>$this->config['title'],
            'description'=>$this->config['description'],
            'introtext'=>$this->config['introtext'],
            'h1'=>$this->config['h1'],
            'h2'=>$this->config['h2'],
            'text'=>$this->config['text'],
            'content'=>$this->config['content']
        );
        $meta = array();

        if($page = $this->modx->getObject('modResource',$page_id)) {
            $page_keys = array_keys($page->toArray());

            $array_diff = array_diff($system, $page_keys);
            foreach ($array_diff as $tag => $tvname) {
                if ($tvvalue = $page->getTVValue($tvname)) {
                    $tpl = '@INLINE ' . $tvvalue;
                    $meta[$tag] = $this->pdo->getChunk($tpl);
                    unset($system[$tag]);
                }
            }
            foreach ($system as $tag => $tagname) {
                $tpl = '@INLINE ' . $page->get($tagname);
                $meta[$tag] = $this->pdo->getChunk($tpl);
            }
        }
        return $meta;
    }

    public function getFieldMeta($params, $page_id = 0) {
        $seo_system = array('id','field_id','multi_id','name','rank','active','class');
        $meta = array();
        if(count($params)) {
            foreach ($params as $param => $value) {
                if ($field = $this->pdo->getArray('sfField', array('alias' => $param))) {
                    $word_array = array();
                    if ($word = $this->pdo->getArray('sfDictionary', array('input'=>$value,'field_id'=>$field['id']))) {
                        $word_array = array_diff_key($word, array_flip($seo_system));
                        $meta['url'] = $this->fieldUrl($word['alias'],$field);
                    }

                    if ($seo = $this->pdo->getArray('sfSeoMeta', array('field_id'=>$field['id']))) {
                        $seo_array = array_diff_key($seo, array_flip($seo_system));
                        foreach ($seo_array as $tag => $text) {
                            if ($text) {
                                $tpl = '@INLINE ' . $text;
                                $meta[$tag] = $this->pdo->getChunk($tpl, $word_array);
                            }
                        }

                    }
                }
            }
        }
        return $meta;
    }

    public function getMultiMeta($params, $page_id = 0,$multi_id = 0) {
        $seo_system = array('id','field_id','multi_id','name','rank','active','class');
        $meta = array();
        $fields = array();
        $word_array = array();
        $aliases = array();
        $find_multi = 0;

        foreach ($params as $param => $value) {
            if ($field = $this->pdo->getArray('sfField', array('alias' => $param))) {
                $fields[] = $field['id'];
                if ($word = $this->pdo->getArray('sfDictionary', array('input'=>$value,'field_id'=>$field['id']))) {
                    foreach(array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
                        $word_array[str_replace('value',$field['alias'],$tmp_key)] = $tmp_array;
                    }
                    $aliases[$param] = $word['alias'];
                }
            }
        }

        if($find_multi = $this->findMultiIdByFields($fields,$page_id)) {
            if ($seo = $this->pdo->getArray('sfSeoMeta', array('multi_id'=>$find_multi))) {
                $seo_array = array_diff_key($seo, array_flip($seo_system));
                foreach ($seo_array as $tag => $text) {
                    if ($text) {
                        $tpl = '@INLINE ' . $text;
                        $meta[$tag] = $this->pdo->getChunk($tpl, $word_array);
                    }
                }

            }
        }
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($meta,1));
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($find_multi,1));
        if(!$find_multi && !$meta) {
            $meta = $this->getPageMeta($page_id);
            $meta['url'] = $this->multiUrl($params,0);
        } else {
            $meta['url'] = $this->multiUrl($aliases,$find_multi);
        }
        $this->modx->log(modx::LOG_LEVEL_ERROR, print_r($aliases,1));


        return $meta;
    }

    public function findMultiIdByFields($fields = array(),$page_id = 0) {
        $multi_id = 0;
        $max_priority = count($fields);
        if($max_priority) {
            $q = $this->modx->newQuery('sfFieldIds');
            $q->select(array('DISTINCT sfFieldIds.multi_id'));
            $q->where(array(
                'sfFieldIds.field_id'=>$fields[0]
            ));
            $shift = array_shift($fields);
            foreach($fields as $key => $field) {
                $q->innerJoin(
                    'sfFieldIds', 'sfFieldIds'.$key, 'sfFieldIds.multi_id = sfFieldIds'.$key.'.multi_id ');
                $q->where(array(
                    'sfFieldIds'.$key.'.field_id'=>$field,
                ));
            }
            if(($count = $this->modx->getCount('sfFieldIds',$q)) >= 1) {
                if($q->prepare() && $q->stmt->execute()) {
                    $rows = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach($rows as $key=>$row) {
                        if($this->modx->getCount('sfFieldIds',array('multi_id'=>$row)) != $max_priority) {
                            unset($rows[$key]);
                        }
                    }
                    if(count($rows) == 1) {
                        $multi_id = $rows[0];
                    } elseif($marray = $this->pdo->getArray('sfMultiField',array('id:IN'=>$rows,'page'=>$page_id))) {
                        $multi_id = $marray['id'];
                    }
                }
            }
        }
        return $multi_id;
    }

    public function findMultiId($url = '') {
        if($url_array = $this->pdo->getArray('sfUrls',array('old_url'=>$url,'OR:new_url:='=>$url))) {
            return $url_array['multi_id'];
        } else {
            return 0;
        }
    }

    public function findUrlArray($url = '') {
        if($url_array = $this->pdo->getArray('sfUrls',array('old_url'=>$url,'OR:new_url:='=>$url))) {
            return $url_array;
        } else {
            return array();
        }
    }



    public function fieldUrl($value = '', $field = array()) {
        if(!$alias = $field['alias']) {
            $alias = $field['key'];
        }
        if($field['hideparam']) {
            $url = $value;
        } else if ($field['valuefirst']) {
            $url = $value.$this->config['separator'].$alias;
        } else {
            $url = $alias.$this->config['separator'].$value;
        }
        return $url;
    }

    public function multiUrl($aliases = array(),$multi_id = 0) {
        $url = '';
        if($multi_id) {
            if($marray = $this->pdo->getArray('sfMultiField',$multi_id)) {
                $tpl = '@INLINE ' . $marray['url'];
                $url = $this->pdo->getChunk($tpl, $aliases);
                if($url_array = $this->pdo->getArray('sfUrls',array('old_url'=>$url))) {
                    if($url_array['new_url']) {
                        $url = $url_array['new_url'];
                    }
                }
            }
        } else {
            $total = 1;
            $count = count($aliases);
            foreach($aliases as $param => $alias) {
                if($total == 1) {
                    $url .= '?';
                }
                $url .= $param.'='.$alias;
                if($total != $count) {
                    $url .= '&';
                }
                $total++;
            }
        }
        return $url;
    }


}