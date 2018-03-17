<?php

$properties = array();

$tmp = array(
    'rules'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'parents'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'depth' => array(
        'type' => 'numberfield',
        'value' => 1,
    ),
    'urls'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'sortby'=> array(
        'type'=> 'textfield',
        'value' => 'count',
    ),
    'sortdir'=> array(
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC'),
        ),
        'value' => 'DESC',
    ),
    'sortcount'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'countChildren'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'fast'=> array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'mincount' => array(
        'type'=> 'numberfield',
        'value' => 0,
    ),
    'level'=> array(
        'type'=> 'numberfield',
        'value' => 0,
    ),
    'relative'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'onlyrelative'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'double'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'nesting'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'limit' => array(
        'type'=> 'numberfield',
        'value' => 0,
    ),
    'offset' => array(
        'type'=> 'numberfield',
        'value' => 0,
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
    'context'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'cache' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'userank'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'groupbyrule'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'groupsort' => array(
        'type'=> 'textfield',
        'value' => 'level',
    ),
    'groupdir' => array(
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC'),
        ),
        'value' => 'ASC',
    ),
    'showHidden' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'count_where' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'cacheTime' => array(
        'type'=> 'numberfield',
        'value' => 3600,
    ),
    'fastMode' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'firstClass' => array(
        'type'=> 'textfield',
        'value' => 'first',
    ),
    'hereClass' => array(
        'type'=> 'textfield',
        'value' => 'active',
    ),
    'innerClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'lastClass' => array(
        'type'=> 'textfield',
        'value' => 'last',
    ),
    'levelClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'outerClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'parentClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'rowClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'selfClass' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'hideSubMenus'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'where'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tpl' => array(
        'type'=> 'textfield',
        'value' => '@INLINE <li{$classes}><a href="{$url}">{$name}{if $total?} ({$total}){/if}</a>{$wrapper}</li>',
    ),
    'tplOuter' => array(
        'type'=> 'textfield',
        'value' => '@INLINE <ul{$classes}>{$wrapper}</ul>',
    ),
    'tplGroup' => array(
        'type'=> 'textfield',
        'value' => '@INLINE <div><h4>{$name}</h4>{$wrapper}</div>',
    ),
    'tplHere'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplInner' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplInnerHere' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplInnerRow'=> array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplParentRow' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplParentRowActive' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'tplParentRowHere' => array(
        'type'=> 'textfield',
        'value' => '',
    ),
    'toPlaceholder' => array(
        'type'=> 'textfield',
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