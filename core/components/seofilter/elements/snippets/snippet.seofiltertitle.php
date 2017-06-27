<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var SeoFilter $SeoFilter */
if (!$SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
        $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties)
) {
    return 'Could not load SeoFilter class!';
}

$tpl = $modx->getOption('tpl', $scriptProperties, 'Item');
$sortby = $modx->getOption('sortby', $scriptProperties, 'name');
$sortdir = $modx->getOption('sortbir', $scriptProperties, 'ASC');
$limit = $modx->getOption('limit', $scriptProperties, 5);
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, "\n");
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);

// Build query
$c = $modx->newQuery('SeoFilterItem');
$c->sortby($sortby, $sortdir);
$c->limit($limit);
$items = $modx->getIterator('SeoFilterItem', $c);

// Iterate through items
$list = array();
/** @var SeoFilterItem $item */
foreach ($items as $item) {
    $list[] = $modx->getChunk($tpl, $item->toArray());
}

// Output
$output = implode($outputSeparator, $list);
if (!empty($toPlaceholder)) {
    // If using a placeholder, output nothing and set output to specified placeholder
    $modx->setPlaceholder($toPlaceholder, $output);

    return '';
}
// By default just return output
return $output;
