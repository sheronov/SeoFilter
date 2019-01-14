<?php
/** @var array $scriptProperties */
/** @var sfMenu $sfMenu */
$path = $modx->getOption('sfmenu_class_path', $scriptProperties, $modx->getOption('core_path') . 'components/seofilter/model/',true);
$fqn = $modx->getOption('sfMenu.class', null, 'seofilter.sfmenu', true);
if ($sfClass = $modx->loadClass($fqn,$path, false, true)) {
    $scriptProperties['nesting'] = 0;
    $scriptProperties['groupbyrule'] = 0;

    if(!in_array($scriptProperties['scheme'],array('full','http','https'))) {
        $scriptProperties['scheme'] = 'full';
    }
    if (!empty($sortBy)) {
        $scriptProperties['sortby'] = $sortBy;
    }
    if (!empty($sortDir)) {
        $scriptProperties['sortdir'] = $sortDir;
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
$sfMenu->pdoTools->addTime('sfSitemap loaded');

// Default variables
if (empty($tpl)) {
    $tpl = "@INLINE \n<url>\n\t<loc>[[+url]]</loc>\n\t<lastmod>[[+date]]</lastmod>\n\t<changefreq>[[+update]]</changefreq>\n\t<priority>[[+priority]]</priority>\n</url>";
}
if (empty($tplWrapper)) {
    $tplWrapper = "@INLINE <?xml version=\"1.0\" encoding=\"[[++modx_charset]]\"?>\n<urlset xmlns=\"[[+schema]]\">\n[[+output]]\n</urlset>";
}
if (empty($sitemapSchema)) {
    $sitemapSchema = 'http://www.sitemaps.org/schemas/sitemap/0.9';
}
if (empty($outputSeparator)) {
    $outputSeparator = "\n";
}
if (empty($cacheKey)) {
    $scriptProperties['cacheKey'] = 'sfsitemap/' . substr(md5(json_encode($scriptProperties)), 0, 6);
}


if (!empty($cache)) {
    $data = $sfMenu->pdoTools->getCache($scriptProperties);
}
if (empty($data)) {
    $now = time();
    $data = $urls = array();
    $rows = $sfMenu->getTree($scriptProperties['rules'],$scriptProperties['parents']);
    foreach ($rows as $row) {
        if(!empty($row['editedon']) && $row['editedon'] != '0000-00-00 00:00:00') {
            $time = strtotime($row['editedon']);
        } else {
            $time = strtotime($row['createdon']);
        }

        $row['date'] = date('c', $time);

        $datediff = floor(($now - $time) / 86400);
        if ($datediff <= 1) {
            $row['priority'] = '1.0';
            $row['update'] = 'daily';
        } elseif (($datediff > 1) && ($datediff <= 7)) {
            $row['priority'] = '0.75';
            $row['update'] = 'weekly';
        } elseif (($datediff > 7) && ($datediff <= 30)) {
            $row['priority'] = '0.50';
            $row['update'] = 'weekly';
        } else {
            $row['priority'] = '0.25';
            $row['update'] = 'monthly';
        }
        if (!empty($priorityTV) && !empty($row[$priorityTV])) {
            $row['priority'] = $row[$priorityTV];
        }

        // Add item to output
        $data[$row['url']] = $sfMenu->pdoTools->parseChunk($tpl, $row);
    }
    $sfMenu->pdoTools->addTime('Rows processed');
    if (!empty($cache)) {
        $sfMenu->pdoTools->setCache($data, $scriptProperties);
    }
}

$output = implode($outputSeparator, $data);
$output = $sfMenu->pdoTools->getChunk($tplWrapper, array(
    'schema' => $sitemapSchema,
    'output' => $output,
    'items' => $output,
));
$sfMenu->pdoTools->addTime('Rows wrapped');

if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="pdoSitemapLog">' . print_r($sfMenu->pdoTools->getTime(), 1) . '</pre>';
}

if (!empty($forceXML)) {
    header("Content-Type:text/xml");
    @session_write_close();
    exit($output);
} else {
    return $output;
}