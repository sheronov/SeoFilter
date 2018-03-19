<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => "@INLINE <url>\n\t<loc>[[+url]]</loc>\n\t<lastmod>[[+date]]</lastmod>\n\t<changefreq>[[+update]]</changefreq>\n\t<priority>[[+priority]]</priority>\n</url>",
    ),
    'tplWrapper' => array(
        'type' => 'textfield',
        'value' => "@INLINE <?xml version=\"1.0\" encoding=\"[[++modx_charset]]\"?>\n<urlset xmlns=\"[[+schema]]\">\n[[+output]]\n</urlset>",
    ),
    'context' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'depth' => array(
        'type' => 'numberfield',
        'value' => 10,
    ),
    'showHidden' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'fast'=> array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'sitemapSchema' => array(
        'type' => 'textfield',
        'value' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
    ),
    'urls' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'parents' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'rules' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'sortby' => array(
        'type' => 'textfield',
        'value' => 'link',
    ),
    'sortcount'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'countChildren'=> array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'mincount' => array(
        'type'=> 'numberfield',
        'value' => 0,
    ),
    'sortdir'=> array(
        'type' => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC'),
        ),
        'value' => 'ASC',
    ),
    'where' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'outputSeparator' => array(
        'type' => 'textfield',
        'value' => "\n",
    ),
    'forceXML' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'cache' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'cacheKey' => array(
        'type' => 'textfield',
        'value' => '',
    ),
    'cacheTime' => array(
        'type' => 'numberfield',
        'value' => 3600,
    ),
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(array(
        'name' => $k,
        'desc' => PKG_NAME_LOWER . '_prop_' . $k,
        'lexicon' => PKG_NAME_LOWER . ':properties',
    ), $v);
}

return $properties;