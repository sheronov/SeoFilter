<?php
/** @var array $scriptProperties */
$path = $modx->getOption('seofilter_core_path', $scriptProperties,
        $modx->getOption('core_path') . 'components/seofilter/').'model/';
if ($sfClass = $modx->loadClass('seofilter.sfmenu',$path, false, true)) {
    if((int)$scriptProperties['relative'] && (int)$modx->getPlaceholder('sf.seo_id')) {
        $scriptProperties['double'] = 1;
        $scriptProperties['nesting'] = 1;
        $scriptProperties['groupbyrule'] = 0;
        $scriptProperties['hideSubMenus'] = 1;
    }
    $sfMenu = new sfMenu($modx, $scriptProperties);
} else {
    return false;
}
$sfMenu->pdoTools->addTime('pdoTools loaded');

$output = '';
$links = array();
$cache = (int)$modx->getOption('cache',$scriptProperties,1);


if (!empty($sortBy)) {
    $scriptProperties['sortby'] = $sortBy;
}
if (!empty($sortDir)) {
    $scriptProperties['sortdir'] = $sortDir;
}

if (!empty($countchildren)) {
    $scriptProperties['countChildren'] = $countchildren;
}


if (empty($scriptProperties['cache_key'])) {
    $scriptProperties['cache_key'] = 'sfmenu/' . sha1(serialize($scriptProperties));
}
if ($cache) {
    $links = $sfMenu->pdoTools->getCache($scriptProperties);
}
if(empty($links)) {
    $links = $sfMenu->getTree($scriptProperties['rules'],$scriptProperties['parents']);
//    $output.= '<pre>'.print_r($links,1).'</pre>';
    if ($cache) {
        $sfMenu->pdoTools->setCache($links, $scriptProperties);
    }
}

if (!empty($links)) {
    if(((!empty($scriptProperties['onlyrelative'])) && (int)$modx->getPlaceholder('sf.seo_id')) || empty($scriptProperties['onlyrelative'])) {
        $output .= $sfMenu->makeMenu($links);

    }
}

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="pdoMenuLog">' . print_r($sfMenu->pdoTools->getTime(), 1) . '</pre>';
}

if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}