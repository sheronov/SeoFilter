<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path') . 'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools)) {
    return '';
}

$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$tpl = $modx->getOption('tpl', $scriptProperties, '@INLINE <a href="[[+url]]"[[+classes]]>[[+name]]</a>');
$rules = $modx->getOption('rules', $scriptProperties, '');
$pages = $modx->getOption('pages', $scriptProperties, '');
$where = $modx->getOption('where', $scriptProperties, '');
$fastMode = $modx->getOption('fastMode', $fastMode, false);
$as_name = $modx->getOption('as_name', $scriptProperties, '');
$link_classes = $modx->getOption('link_classes', $scriptProperties, '');
$proMode = $modx->getOption('seofilter_pro_mode', null, 0, true);
$output = '';

if (!empty($rules) || !empty($pages)) {
    $all = [];
    $fields = [];
    $words = [];
    $link = [];


    if (!empty($rules)) {
        $rules = array_map('trim', explode(',', $rules));
    } else {
        $rules = [];
    }

    if (!empty($pages)) {
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = $modx->fromJSON($where);
            }
        }
        $pages = array_map('trim', explode(',', $pages));
        foreach ($pages as $page) {
            $q = $modx->newQuery('sfRule');
            if ($proMode) {
                $q->where('1=1 AND FIND_IN_SET(' . $page . ',REPLACE(IFNULL(NULLIF(pages,""),page)," ",""))');
            } else {
                $q->where(['page' => $page]);
            }
            if (is_array($where)) {
                $q->where($where);
            }
            $q->select('id');
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_COLUMN)) {
                    $rules[] = $row;
                }
            }
        }
    }
    $rules = array_unique($rules);


    $q = $modx->newQuery('sfFieldIds');
    $q->where(['sfFieldIds.multi_id:IN' => $rules]);
    $q->leftJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id');
    $q->select(['sfField.*', 'sfFieldIds.multi_id']);
    if ($q->prepare() && $q->stmt->execute()) {
        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
            $all[$row['multi_id']][$row['id']] = $row;
            $fields[$row['id']] = $row;
        }
    }


    foreach ($fields as $id => $field) {
        if (isset($scriptProperties[$field['alias']])) {
            $q = $modx->newQuery('sfDictionary');
            $where = ['sfDictionary.field_id' => $id];
            if ($field['exact']) {
                $where['input'] = $scriptProperties[$field['alias']];
            } else {
                $where['input:LIKE'] = '%' . $scriptProperties[$field['alias']] . '%';
            }
            $q->where($where);
            $q->select(['sfDictionary.*']);
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $words[$field['id']] = $row['id'];
                }
            }
        }
    }

    foreach ($rules as $rule_id) {
        $q = $modx->newQuery('sfUrls');
        if (is_array($pages) && count($pages)) {
            $q->where(['page_id:IN' => $pages]);
        }
        $q->where(['multi_id' => $rule_id, 'active' => 1]);
        $q->select(['sfUrls.*']);
        $q->limit(1);
        $i = 0;
        if (isset($all[$rule_id]) && is_array($all[$rule_id])) {
            foreach ($all[$rule_id] as $field_id => $field) {
                $word_id = $words[$field_id];
                $q->innerJoin('sfUrlWord', 'sfUrlWord' . $i,
                    'sfUrlWord' . $i . '.url_id = sfUrls.id AND sfUrlWord' . $i . '.word_id = ' . $word_id . ' AND sfUrlWord' . $i . '.field_id = ' . $field_id);
                $i++;
            }
        }
        if ($modx->getCount('sfUrls', $q)) {
            if ($q->prepare() && $q->stmt->execute()) {
                $link = $q->stmt->fetch(PDO::FETCH_ASSOC);
                break;
            }
        } else {
            continue;
        }
    }

    if (!empty($link)) {
        $c_suffix = $modx->getOption('container_suffix', null, '/');
        $u_suffix = $modx->getOption('seofilter_url_suffix', null, '', true);
        $between_urls = $modx->getOption('seofilter_between_urls', null, '/', true);
        $possibleSuffixes = array_map('trim',
            explode(',', $modx->getOption('seofitler_possible_suffixes', null, '/,.html,.php', true)));
        $possibleSuffixes = array_unique(array_merge($possibleSuffixes, [$c_suffix]));
        $main_alias = $modx->getOption('seofilter_main_alias', null, 0);
        $site_start = $modx->context->getOption('site_start', 1);

        $url = $link['new_url'] ?: $link['old_url'];
        $page_url = $modx->makeUrl($link['page_id'], $scriptProperties['context'], '', $scriptProperties['scheme']);

        foreach ($possibleSuffixes as $possibleSuffix) {
            if (substr($page_url, -strlen($possibleSuffix)) == $possibleSuffix) {
                $page_url = substr($page_url, 0, -strlen($possibleSuffix));
            }
        }

        if ($site_start == $link['page_id']) {
            if ($main_alias) {
                $qq = $modx->newQuery('modResource', ['id' => $link['page_id']]);
                $qq->select('alias');
                $malias = $modx->getValue($qq->prepare());
                $link['url'] = $page_url . '/' . $malias . $between_urls . $url . $u_suffix;
            } else {
                $link['url'] = $page_url . '/' . $url . $u_suffix;
            }
        } else {
            $link['url'] = $page_url . $between_urls . $url . $u_suffix;
        }

        if ($as_name) {
            $link['name'] = $as_name;
        } else {
            $link['name'] = $link['menutitle'] ?: $link['link'];
        }
        if ($link_classes) {
            $link['classes'] = ' class="' . $link_classes . '"';
        }
        $output = $pdo->getChunk($tpl, $link, $fastMode);
    }
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
