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
    'morpher_username' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'morpher_password' => array(
        'xtype' => 'text-password',
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
    'count' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_count',
    ),
    'choose' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_count',
    ),
    'snippet' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_main',
    ),
    'select' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'seofilter_count',
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
//    'pagetpl' => array(
//        'xtype' => 'textfield',
//        'value' => '@INLINE / [[%seofilter_page]] [[+page]]',
//        'area' => 'seofilter_seo',
//    ),
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
