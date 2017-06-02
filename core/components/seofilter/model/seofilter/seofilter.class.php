<?php

class SeoFilter
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();


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
        $separator = $this->modx->getOption('seofilter_separator', null, '-', true); //добавить в системные настройки (разделитель параметра и значения)
        $valuefirst = $this->modx->getOption('seofilter_valuefirst', null, 0, true); //добавить в системные настройки (если true, то значение спереди)
        $redirect  = $this->modx->getOption('seofilter_redirect', null, 1, true); //добавит

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,
            'actionUrl' => $actionUrl,

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



}