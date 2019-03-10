<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();


$tmp = array(
    'separator' => array(
        'xtype' => 'textfield',
        'value' => '-',
        'area' => 'seofilter_main',
    ),
    'base_get' => array(
        'xtype' => 'textfield',
        'value' => 'price,page,limit,tpl,sort',
        'area' => 'seofilter_main',
    ),
    'ajax' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main',
    ),
//    'replace' => array(
//        'xtype' => 'combo-boolean',
//        'value' => true,
//        'area' => 'seofilter_main',
//    ),

    'decline' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main',
    ),
    'morpher_token' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'classes' => array(
        'xtype' => 'textfield',
        'value' => 'msProduct',
        'area' => 'seofilter_main',
    ),
    'templates' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'page_key' => array(
        'xtype' => 'textfield',
        'value' => 'page',
        'area' => 'seofilter_main',
    ),
    'page_tpl' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'url_suffix' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'url_redirect' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main',
    ),
    'last_modified' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main',
    ),
    'mfilter_words' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main',
    ),
    'values_separator' => array(
        'xtype' => 'textfield',
        'value' => ', || и ',
        'area' => 'seofilter_main',
    ),
    'values_delimeter' => array(
        'xtype' => 'textfield',
        'value' => ',',
        'area' => 'seofilter_main',
    ),
    'snippet' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'replace_host' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main'
    ),
    'pro_mode' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main'
    ),
    'url_scheme' => array(
        'xtype' => 'textfield',
        'value' => 'full',
        'area' => 'seofilter_main'
    ),
    'level_separator' => array(
        'xtype' => 'textfield',
        'value' => '/',
        'area' => 'seofilter_main'
    ),
    'between_urls' => array(
        'xtype' => 'textfield',
        'value' => '/',
        'area' => 'seofilter_main'
    ),
    'main_alias' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main'
    ),
    'admin_version' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main'
    ),
    'collect_words' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main'
    ),
    'content_richtext' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main'
    ),
    'content_ace' => array(
        'xtype' => 'textfield',
        'value' => 'content,Rule.content',
        'area' => 'seofilter_main'
    ),
    'frontend_js' => array(
        'xtype' => 'textfield',
        'value' => '[[+jsUrl]]web/default.js',
        'area' => 'seofilter_main'
    ),
    'hide_empty' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_count',
    ),
    'count' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_count',
    ),
    'choose' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_count',
    ),
    'count_handler_class' => array(
        'xtype' => 'textfield',
        'value' => 'sfCountHandler',
        'area' => 'seofilter_count'
    ),
    'select' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_count',
    ),
    'default_where' => array(
        'xtype' => 'textfield',
        'value' => '{"published":1,"deleted":0}',
        'area' => 'seofilter_count'
    ),
    'title' => array(
        'xtype' => 'textfield',
        'value' => 'pagetitle',
        'area' => 'seofilter_seo',
    ),
    'description' => array(
        'xtype' => 'textfield',
        'value' => 'description',
        'area' => 'seofilter_seo',
    ),
    'introtext' => array(
        'xtype' => 'textfield',
        'value' => 'introtext',
        'area' => 'seofilter_seo',
    ),
    'keywords' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_seo',
    ),
    'link' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_seo',
    ),
    'h1' => array(
        'xtype' => 'textfield',
        'value' => 'pagetitle',
        'area' => 'seofilter_seo',
    ),
    'h2' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_seo',
    ),
    'text' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_seo',
    ),
    'content' => array(
        'xtype' => 'textfield',
        'value' => 'content',
        'area' => 'seofilter_seo',
    ),
    'crumbs_tpl_current' => array(
        'xtype' => 'textfield',
        'value' => 'tpl.SeoFilter.crumbs.current',
        'area' => 'seofilter_seo'
    ),
    'replacebefore' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_jquery',
    ),
    'replaceseparator' => array(
        'xtype' => 'textfield',
        'value' => ' / ',
        'area' => 'seofilter_jquery',
    ),
    'jtitle' => array(
        'xtype' => 'textfield',
        'value' => 'title',
        'area' => 'seofilter_jquery',
    ),
    'jdescription' => array(
        'xtype' => 'textfield',
        'value' => 'meta[name="description"]',
        'area' => 'seofilter_jquery',
    ),
    'jintrotext' => array(
        'xtype' => 'textfield',
        'value' => '.sf_introtext',
        'area' => 'seofilter_jquery',
    ),
    'jkeywords' => array(
        'xtype' => 'textfield',
        'value' => '.sf_keywords',
        'area' => 'seofilter_jquery',
    ),
    'jlink' => array(
        'xtype' => 'textfield',
        'value' => '.sf_link',
        'area' => 'seofilter_jquery',
    ),
    'jh1' => array(
        'xtype' => 'textfield',
        'value' => '.sf_h1',
        'area' => 'seofilter_jquery',
    ),
    'jh2' => array(
        'xtype' => 'textfield',
        'value' => '.sf_h2',
        'area' => 'seofilter_jquery',
    ),
    'jtext' => array(
        'xtype' => 'textfield',
        'value' => '.sf_text',
        'area' => 'seofilter_jquery',
    ),
    'jcontent' => array(
        'xtype' => 'textfield',
        'value' => '.sf_content',
        'area' => 'seofilter_jquery',
    ),
    'crumbs_replace' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_jquery'
    ),
    'crumbs_nested' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_jquery'
    )

);


foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'seofilter_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
