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
    'valuefirst' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'seofilter_main',
    ),
    'redirect' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main',
    ),
    'ajax' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main',
    ),
    'replace' => array(
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'seofilter_main',
    ),
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
    'h1' => array(
        'xtype' => 'textfield',
        'value' => 'longtitle',
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
