<?php

class SeoFilter
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    public $pdoTools;


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
        $site_start = $this->modx->context->getOption('site_start', 1);  //если назначена главной страница с фильтром
        $charset = $this->modx->context->getOption('modx_charset', 'UTF-8'); //кодировка

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
                $meta = $this->getmeta($params,$pageId);
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


    public function getmeta($params, $pageId = 0) {
        $seo_system = array('id','field_id','multi_id','name','rank','active','class');
        $meta = array();
        $pdo = $this->modx->getService('pdoFetch');
        if(count($params) && ($pdo instanceof pdoFetch)) {
            foreach ($params as $param => $value) {
                if ($field = $pdo->getArray('sfField', array('alias' => $param))) {
                    $word_array = array();
                    if ($word = $pdo->getArray('sfDictionary', array('input'=>$value,'field_id'=>$field['id']))) {
                        $word_array = array_diff_key($word, array_flip($seo_system));
                        $meta['url'] = $this->fieldUrl($word['alias'],$field);
                    }

                    if ($seo = $pdo->getArray('sfSeoMeta', array('field_id'=>$field['id']))) {
                        $seo_array = array_diff_key($seo, array_flip($seo_system));
                        foreach ($seo_array as $tag => $text) {
                            if ($text) {
                                $tpl = '@INLINE ' . $text;
                                $meta[$tag] = $pdo->getChunk($tpl, $word_array);
                            }
                        }

                    }
                }
            }
        }
        return $meta;
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


}