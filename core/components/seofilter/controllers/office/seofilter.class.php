<?php

class officeModExtraController extends officeDefaultController
{

    /**
     * @param array $config
     */
    public function setDefault($config = array())
    {
        if (defined('MODX_ACTION_MODE') && MODX_ACTION_MODE && !empty($_SESSION['Office']['SeoFilter'])) {
            $this->config = $_SESSION['Office']['SeoFilter'];
            $this->config['json_response'] = true;
        } else {
            $this->config = array_merge(array(
                'tplOuter' => 'tpl.SeoFilter.office',
            ), $config);

            $_SESSION['Office']['SeoFilter'] = $this->config;
        }

        $this->office->config['processorsPath'] = MODX_CORE_PATH . 'components/seofilter/processors/office/';
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('seofilter:default');
    }


    /**
     * @param string $ctx
     *
     * @return bool
     */
    public function initialize($ctx = 'web')
    {
        $this->modx->error->errors = array();
        $this->modx->error->message = '';

        return $this->loadPackage();
    }


    /**
     * @return string
     */
    public function defaultAction()
    {
        /*
        // Check for authorized user
        if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
            return $this->modx->user->isAuthenticated('mgr')
                ? $this->modx->lexicon('office_err_mgr_auth')
                : '';
        }
        */

        $config = $this->office->makePlaceholders($this->office->config);
        $css = trim($this->modx->getOption('office_seofilter_frontend_css', null,
            MODX_ASSETS_URL . 'components/office/css/main/default.css', true));
        if (!empty($css)) {
            $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
        }

        $js = trim($this->modx->getOption('office_seofilter_frontend_js', null,
            MODX_ASSETS_URL . 'components/seofilter/js/office/default.js'));
        if (!empty($js)) {
            $this->office->addClientExtJS();
            $this->office->addClientLexicon(array(
                'seofilter:default',
            ), 'seofilter/lexicon');

            $this->office->addClientJs(array(
                MODX_ASSETS_URL . 'components/seofilter/js/mgr/seofilter.js',
                MODX_ASSETS_URL . 'components/seofilter/js/mgr/misc/utils.js',
                MODX_ASSETS_URL . 'components/seofilter/js/office/home.panel.js',
                MODX_ASSETS_URL . 'components/seofilter/js/office/items.grid.js',
                MODX_ASSETS_URL . 'components/seofilter/js/office/items.windows.js',
                str_replace($config['pl'], $config['vl'], $js),
            ), 'seofilter/all');
        }

        return $this->modx->getChunk($this->config['tplOuter']);
    }


    /**
     * @return bool
     */
    public function loadPackage()
    {
        $corePath = $this->modx->getOption('seofilter.core_path', null,
            $this->modx->getOption('core_path') . 'components/seofilter/');
        $modelPath = $corePath . 'model/';

        return $this->modx->addPackage('seofilter', $modelPath);
    }


    /**
     * @param array $data
     *
     * @return array|string
     */
    public function Processor(array $data)
    {
        if (empty($data['method'])) {
            return $this->error('You need to specify processor method');
        }
        $method = $data['method'];
        unset($data['method']);

        /** @var modProcessorResponse|array $response */
        $response = $this->office->runProcessor($method, $data)->getResponse();

        if (is_array($response)) {
            if (!isset($response['data'])) {
                $response['data'] = array();
            }
            if ($response['errors'] === null) {
                $response['errors'] = array();
            }
            if ($response['message'] === null) {
                $response['message'] = '';
            }

            return json_encode($response);
        } else {
            return $response;
        }
    }

}

return 'officeModExtraController';