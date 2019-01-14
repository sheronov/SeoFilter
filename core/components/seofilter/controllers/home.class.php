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
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/settings.panel.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->SeoFilter->config['jsUrl'] . 'mgr/sections/home.js');

        if($this->SeoFilter->config['content_richtext']) {
            $this->loadRichTextEditor();
        }

        $this->addHtml('<script type="text/javascript">
        SeoFilter.config = ' . json_encode($this->SeoFilter->config) . ';
        SeoFilter.config.connector_url = "' . $this->SeoFilter->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ xtype: "seofilter-page-home"});
        });
        </script>
        ');



    }

    public function loadRichTextEditor()
    {
        $content_xtype = 'textarea';
        $useEditor = $this->modx->getOption('use_editor');
        $whichEditor = $this->modx->getOption('which_editor');

        if ($useEditor && !empty($whichEditor))
        {
            $redactor_name = strtolower(str_replace(' ','',$whichEditor));
            $config = array();
            foreach($this->modx->config as $param=>$value) {
                if(strpos($param,$redactor_name) === 0) {
                    $new_param = trim(trim(substr($param,strlen($redactor_name)),'.'),'_');
                    $config[$new_param] = $value;
                    $config[$param] = $value;
                }
            }

            switch ($whichEditor) {
                case 'CKEditor':
                    $content_xtype = 'modx-htmleditor';
                    break;
                default:
                    $content_xtype = 'textarea';
            }


//            $this->modx->log(1,print_r($config,1));
            $textEditors = $this->modx->invokeEvent('OnRichTextEditorRegister');
            // invoke the OnRichTextEditorInit event
            $onRichTextEditorInit = $this->modx->invokeEvent('OnRichTextEditorInit',array_merge($config,array(
                'editor' => $whichEditor, // Not necessary for Redactor
                'mode' =>  modSystemEvent::MODE_UPD
            )));
            if (is_array($onRichTextEditorInit))
            {
                $onRichTextEditorInit = implode('', $onRichTextEditorInit);
            }
            if($onRichTextEditorInit) {
                $this->addHtml($onRichTextEditorInit);
            }
            $this->setPlaceholder('onRichTextEditorInit', $onRichTextEditorInit);
        }

        $this->SeoFilter->config['content_xtype'] = $content_xtype;
    }

    public function loadRTE() {
        $rte = $this->modx->getOption('which_editor');
        if ($this->modx->context->getOption('use_editor', false, $this->modx->_userConfig) && !empty($rte)) {
            $textEditors = $this->modx->invokeEvent('OnRichTextEditorRegister');
            $onRichTextEditorInit = $this->modx->invokeEvent('OnRichTextEditorInit',array(
                'editor' => $rte,
                'mode' =>  modSystemEvent::MODE_UPD,
            ));
        }
    }


    public function addCss($script)
    {
        if($this->SeoFilter->config['admin_version']) {
            $script = $script . '?v=' . $this->SeoFilter->version;
        }
        parent::addCss($script);
    }

    public function addJavascript($script)
    {
        if($this->SeoFilter->config['admin_version']) {
            $script = $script . '?v=' . $this->SeoFilter->version;
        }
        parent::addJavascript($script);
    }

    public function addLastJavascript($script)
    {
        if($this->SeoFilter->config['admin_version']) {
            $script = $script . '?v=' . $this->SeoFilter->version;
        }
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