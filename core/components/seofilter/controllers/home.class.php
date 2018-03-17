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
        $this->SeoFilter->checkStat();
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
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/combobox.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fields.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fields.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fieldids.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/fieldids.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/rules.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/rules.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urlwords.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urlwords.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urls.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/urls.windows.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/dictionary.grid.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/dictionary.windows.js');
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
        return $this->SeoFilter->config['templatesPath'] . 'home.tpl';
    }
}