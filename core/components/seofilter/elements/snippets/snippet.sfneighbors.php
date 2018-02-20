<?php
/** @var array $scriptProperties */
$id = $modx->getOption('id',$scriptProperties,$modx->getPlaceholder('sf.seo_id'));
$rule_id = $modx->getOption('rule_id',$scriptProperties,$modx->getPlaceholder('sf.rule_id'));
$tplPrev = $modx->getOption('tplPrev', $scriptProperties, '@INLINE <span class="link-prev"><a href="[[+url]]">&larr; [[+name]]</a></span>');
$tplNext = $modx->getOption('tplNext', $scriptProperties, '@INLINE <span class="link-next"><a href="[[+url]]">[[+name]] &rarr;</a></span>');
$tplWrapper = $modx->getOption('tplWrapper', $scriptProperties, '@INLINE <div class="neighbors">[[+prev]][[+next]]</div>');
$scheme = $modx->getOption('scheme',$scriptProperties,'');
$context = $modx->getOption('context',$scriptProperties,$modx->context->key);
$sortby = $modx->getOption('sortby',$scriptProperties,'id');
$countChildren = $modx->getOption('countChildren',$scriptProperties,0);
$mincount = $modx->getOption('mincount',$scriptProperties,1);
$loop = $modx->getOption('loop',$scriptProperties,1);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,'');
$replace = $modx->getOption('replace',$scriptProperties,'');

if(empty($id)) {
    return '';
}

$modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools))
    return '';

$page_id = $modx->resource->id;
$row = $neighbors =  array();
$q = $modx->newQuery('sfUrls');
$q->where(array('id' => (int)$id, 'active' => 1));
if ($modx->getCount('sfUrls', $q)) {
    $q->select(array('sfUrls.*'));
    if($q->prepare() && $q->stmt->execute()) {
        $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
    }
}
if(!empty($row)) {
    if(empty($rule_id)) {
        $rule_id = $row['multi_id'];
    }
    $seo_id = $row['id'];
    $page_id = $row['page_id'];
    if(isset($row[$sortby])) {
        $sorting = $row[$sortby];
    } else {
        $sorting = $row['id'];
    }

    $ids = array($seo_id);
    $first_or_last = false;
    foreach(array('prev','next') as $k) {
        $q = $modx->newQuery('sfUrls');
        $q->select(array('sfUrls.*'));
        $q->limit(1);
        $where = $cwhere = array(
            'multi_id'=>(int)$rule_id,
            'active'=>1,
            'id:NOT IN' => $ids
        );

        $c = clone($q);
        if ($k == 'prev') {
            $where[$sortby.':<'] = $sorting;
            $q->sortby($sortby,'desc');
        } else {
            $where[$sortby.':>'] = $sorting;
            $q->sortby($sortby,'asc');
        }
        $q->where($where);

        $tpl = ${'tpl'.ucfirst($k)};
        if($modx->getCount('sfUrls',$q)) {
            //всё ок
        } elseif($loop) {
            $first_or_last = true;
            $q = clone($c);
            $q->where($cwhere);
            if($k == 'prev') {
                $q->sortby($sortby,'desc');
            } else {
                $q->sortby($sortby,'asc');
            }
        } else {
            continue;
        }
        if($q->prepare() && $q->stmt->execute()) {
            if($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $url = $row['new_url']?:$row['old_url'];
                $page_url = $modx->makeUrl($row['page_id'],$context,'',$scheme);
                $c_suffix = $modx->getOption('container_suffix',null,'/');
                $u_suffix = $modx->getOption('seofilter_url_suffix',null,'',true);
                if($c_suffix) {
                    if(strpos($page_url,$c_suffix,strlen($page_url)-strlen($c_suffix))) {
                        $page_url = substr($page_url,0,-strlen($c_suffix));
                    }
                }
                if (substr($page_url, -1) != '/') {
                    $page_url .= '/';
                }
                $row['url'] = $page_url.$url.$u_suffix;
                $row['name'] = $row['menutitle']?:$row['link'];
                if(!empty($replace)) {
                    $row['name'] = str_replace(array($replace,'  '),'',$row['name']);
                }
                $row['position'] = $k;
                $neighbors[$k] = $pdo->getChunk($tpl,$row);
                $ids[] = $row['id'];
                if($first_or_last) {
                    if(isset($row[$sortby])) {
                        $sorting = $row[$sortby];
                    } else {
                        $sorting = $row['id'];
                    }
                }
            }

        }

    }

    if(!empty($neighbors)) {
        $output =  $pdo->getChunk($tplWrapper,$neighbors);
        if(!empty($toPlaceholder)) {
            $modx->setPlaceholder($toPlaceholder,$output);
        } else {
            return $output;
        }
    } else {
        return '';
    }
} else {
    return '';
}



