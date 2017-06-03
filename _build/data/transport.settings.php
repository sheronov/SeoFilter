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
