<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => '@INLINE <a href="[[+url]]">[[+name]]</a>',
    ),
    'toPlaceholder' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'context' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'scheme'=> array(
        'type' => 'list',
        'options' => array(
            array(
                'name' => 'System default',
                'value' => '',
            ),
            array(
                'name' => '-1 (relative to site_url)',
                'value' => -1,
            ),
            array(
                'name' => 'full (absolute, prepended with site_url)',
                'value' => 'full',
            ),
            array(
                'name' => 'abs (absolute, prepended with base_url)',
                'value' => 'abs',
            ),
            array(
                'name' => 'http (absolute, forced to http scheme)',
                'value' => 'http',
            ),
            array(
                'name' => 'https (absolute, forced to https scheme)',
                'value' => 'https',
            ),
        ),
        'value' => '',
    ),
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name' => $k,
            'desc' => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;