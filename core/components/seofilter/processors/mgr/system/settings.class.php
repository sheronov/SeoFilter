<?php

class sfSettingsSaveProcessor extends modProcessor
{
    public $languageTopics = ['seofilter'];
    //public $permission = 'save';

    public $options_count = 0;
    public $clearCache    = false;
    public $booleans      = [
        'main_alias',
        'url_redirect',
        'count',
        'hide_empty',
        'ajax',
        'decline',
        'crumbs_nested',
        'crumbs_replace',
        'replacebefore',
        'pro_mode',
        'last_modified',
        'replace_host',
        'mfilter_words',
        'hidden_tab',
        'admin_version',
        'collect_words',
        'ajax_recount'
    ];

    public function process()
    {
        $settings = $this->getProperties();
        unset($settings['action']);


        $aliases = [

        ];

        foreach ($settings as $key => $value) {
            if (empty($key)) {
                continue;
            }
            if (isset(array_flip($this->booleans)[$key])) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            if (isset($aliases[$key])) {
                $key = $aliases;
            }
            if ($this->setOption('seofilter_'.$key, $value)) {
                $this->options_count++;
            }
        }

        if ($this->clearCache) {
            $this->modx->cacheManager->refresh(['system_settings' => []]);
        }

        //        $this->modx->log(1,print_r($settings,1));
        return $this->success($this->options_count);
    }

    public function setOption($key, $value, $clearCache = true)
    {
        if (!$setting = $this->modx->getObject('modSystemSetting', $key)) {
            $setting = $this->modx->newObject('modSystemSetting');
            if (in_array($key, $this->booleans, true)) {
                $setting->set('xtype', 'combo-boolean');
            }
            $setting->set('key', $key);
            $setting->set('namespace', 'seofilter');
        }
        $setting->set('value', $value);
        if ($setting->save()) {
            $this->modx->config[$key] = $value;
            $this->clearCache = true;
            return true;
        }
        return false;
    }


}

return 'sfSettingsSaveProcessor';