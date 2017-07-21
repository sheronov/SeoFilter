<?php

$properties = array();

$tmp = array(
    'tpl_link' => array(
        'type' => 'textfield',
        'value' => '@INLINE <a href="/{$link}">{$value}</a>',
    ),
    'toPlaceholder' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'rule_id' => array(
        'type' => 'textfield',
        'value' => 0,
    ),
    'rules_id' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'return_link' => array(
        'type' => 'combo-boolean',
        'value' => false,
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