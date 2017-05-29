<?php
/**
 * The home manager controller for SeoFilter.
 *
 */
class SeoFilterHomeManagerController extends modExtraManagerController
{
    /** @var SeoFilter $SeoFilter */
    public $SeoFilter;


    /**
     *
     */
    public function initialize()
    {
        $path = $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/';
        $this->SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $path);
        parent::initialize();
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
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fields.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fields.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        SeoFilter.config = ' . json_encode($this->SeoFilter->config) . ';
        SeoFilter.config.connector_url = "' . $this->SeoFilter->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ xtype: "seofilter-page-home"});
        });
        </script>
        ');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->SeoFilter->config['templatesPath'] . 'home.tpl';
    }
}