<?php
/** @var array $scriptProperties */
/** @var sfMenu $sfMenu */
$path = $modx->getOption('sfmenu_class_path', $scriptProperties, $modx->getOption('core_path') . 'components/seofilter/model/',true);
$fqn = $modx->getOption('sfMenu.class', null, 'seofilter.sfmenu', true);
if ($sfClass = $modx->loadClass($fqn,$path, false, true)) {
    if (!empty($sortBy)) {
        $scriptProperties['sortby'] = $sortBy;
    }
    if (!empty($sortDir)) {
        $scriptProperties['sortdir'] = $sortDir;
    }

    if (!empty($countchildren)) {
        $scriptProperties['countChildren'] = $countchildren;
    }

    if((int)$scriptProperties['relative'] && (int)$modx->getPlaceholder('sf.seo_id')) {
        $scriptProperties['double'] = 1;
        $scriptProperties['nesting'] = 1;
        $scriptProperties['groupbyrule'] = 0;
        $scriptProperties['hideSubMenus'] = 1;
    }
    if((int)$scriptProperties['countChildren']) {
        $scriptProperties['fast'] = 0;
    } elseif(!isset($scriptProperties['fast'])) {
        $scriptProperties['fast'] = 1;
    }
    $sfMenu = new $sfClass($modx, $scriptProperties);
} else {
    return false;
}
$sfMenu->pdoTools->addTime('sfMenu loaded');

$output = '';
$links = array();
$cache = (int)$modx->getOption('cache',$scriptProperties,1);

if (empty($scriptProperties['cache_key'])) {
    $scriptProperties['cache_key'] = 'sfmenu/' . sha1(serialize($scriptProperties));
}
if ($cache) {
    $links = $sfMenu->pdoTools->getCache($scriptProperties);
}
if(empty($links)) {
    $links = $sfMenu->getTree($scriptProperties['rules'],$scriptProperties['parents']);
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