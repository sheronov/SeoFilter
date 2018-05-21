<?php

$properties = array();

$tmp = array(
    'input' => array(
        'type' => 'textfield',
        'value'=>'',
    ),
    'field_id' => array(
        'type' => 'textfield',
        'value'=>'',
    ),
    'tpl' => array(
        'type' => 'textfield',
        'value' => '',
    )
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