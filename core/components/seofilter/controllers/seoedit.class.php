<?php
/**
 * The Seoedit manager controller for SeoFilter.
 *
 */
class SeoFilterSeoeditManagerController extends modExtraManagerController
{
    /** @var SeoFilter $SeoFilter */
    public $SeoFilter;

    public $urlArray = array();

    /**
     *
     */
    public function initialize()
    {
        $path = $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/';
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $path);
        $this->SeoFilter->checkStat();
        parent::initialize();
    }

    public function process(array $scriptProperties = array())
    {
        if($scriptProperties['id']) {
            if($url = $this->modx->getObject('sfUrls',$scriptProperties['id'])) {
                $this->urlArray = $url->toArray();

                if($rule = $this->modx->getObject('sfRule',$url->get('multi_id'))) {
                    if (!$url->get('custom')) {
                        $this->urlArray['title'] = $rule->get('title');
                        $this->urlArray['h1'] = $rule->get('h1');
                        $this->urlArray['h2'] = $rule->get('h2');
                        $this->urlArray['description'] = $rule->get('description');
                        $this->urlArray['introtext'] = $rule->get('introtext');
                        $this->urlArray['keywords'] = $rule->get('keywords');
                        $this->urlArray['text'] = $rule->get('text');
                        $this->urlArray['content'] = $rule->get('content');
                        $this->urlArray['properties'] = $rule->get('properties');
                        $this->urlArray['introtexts'] = $rule->get('introtexts');
                        $this->urlArray['tpl'] = $rule->get('tpl');
                        $this->urlArray['introlength'] = $rule->get('introlength');
                    } else {
                        $rule_prop = $rule->get('properties');
                        if(empty($this->urlArray['properties']) && !empty($rule_prop)) {
                            $this->urlArray['properties'] = $rule_prop;
                        }
                        $rule_intros = $rule->get('introtexts');
                        if(empty($this->urlArray['introtexts']) && !empty($rule_intros)) {
                            $this->urlArray['introtexts'] = $rule_intros;
                        }
                        $rule_tpl = $rule->get('tpl');
                        if(empty($this->urlArray['tpl']) && !empty($rule_tpl)) {
                            $this->urlArray['tpl'] = $rule_tpl;
                        }
                        $rule_length = $rule->get('introlength');
                        if(empty($this->urlArray['introlength']) && !empty($rule_length)) {
                            $this->urlArray['introlength'] = $rule_length;
                        }
                    }
                }


//                $this->urlArray['properties'] = $this->modx->toJSON($this->urlArray['properties']);
//                $this->urlArray['introtexts'] = $this->modx->toJSON($this->urlArray['properties']);
            }
        }
//        $this->modx->log(modX::LOG_LEVEL_ERROR,print_r($this->urlArray,1));
        parent::process($scriptProperties);
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('seofilter:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('seofilter');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {

        $this->addCss($this->SeoFilter->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->SeoFilter->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/seofilter.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/combobox.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urlwords.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urlwords.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/dictionary.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/dictionary.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/seoedit.panel.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/sections/seoedit.js');
        $this->addHtml('<script type="text/javascript">
        SeoFilter.config = ' . json_encode($this->SeoFilter->config) . ';
        SeoFilter.config.connector_url = "' . $this->SeoFilter->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ 
            xtype: "seofilter-page-seoedit",
            record:  '.$this->modx->toJSON($this->urlArray).',
            url_id: "'.$this->scriptProperties['id'].'",
            mode: "update"
            });
        });
        </script>
        ');

    }

    public function addCss($script)
    {
        $script = $script . '?v=' . $this->SeoFilter->version;
        parent::addCss($script);
    }

    public function addJavascript($script)
    {
        $script = $script . '?v=' . $this->SeoFilter->version;
        parent::addJavascript($script);
    }

    public function addLastJavascript($script)
    {
        $script = $script . '?v=' . $this->SeoFilter->version;
        parent::addLastJavascript($script);
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->SeoFilter->config['templatesPath'] . 'seoedit.tpl';
    }
}