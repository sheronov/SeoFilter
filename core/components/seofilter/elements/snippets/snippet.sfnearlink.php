<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path') . 'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools)) {
    return '';
}

$id = $modx->getOption('id', $scriptProperties, $modx->resource->id);
$parent = $modx->getOption('parent', $scriptProperties, $modx->resource->parent);
$context_key = $modx->getOption('context_key', $scriptProperties, $modx->resource->context_key);
$parents = $modx->getOption('parents', $scriptProperties, '');
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, '');
$ignoreZeroRank = $modx->getOption('ignoreZeroRank', $scriptProperties, 0);

$tpl = $modx->getOption('tpl', $scriptProperties,
    '@INLINE <li><a href="{$url}"{$classes}>{$name}</a></li><li class="active"><span>↓</span></li>');
$level = $modx->getOption('level', $scriptProperties, 1);
$snippet = $modx->getOption('snippet', $scriptProperties, 'sfLink');
$proMode = $modx->getOption('seofilter_pro_mode', null, 0, true);
if (empty($context_key)) {
    $context_key = 'web';
}
if (empty($parents)) {
    $parents = $modx->getParentIds($parent, 10, ['context' => $context_key]);
    array_unshift($parents, $parent);
    array_pop($parents);
} else {
    $parents = array_map('trim', explode(',', $parents));
}
//поиск правила
$q = $modx->newQuery('sfRule');
if ($ignoreZeroRank) {
    $q->sortby('IF(sfRule.rank = 0, 1, 0)', 'ASC');
}
$q->sortby('sfRule.rank','ASC');
$q->sortby('sfRule.base', 'ASC');
$q->sortby('sfRule.id', 'ASC');
$q->innerJoin('sfFieldIds', 'sfFieldX', 'sfRule.id = sfFieldX.multi_id');
$q->innerJoin('sfFieldIds', 'sfFieldIds', 'sfRule.id = sfFieldIds.multi_id');
$q->innerJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id');
$q->select($modx->getSelectColumns('sfRule', 'sfRule', 'rule_',
    ['id', 'name', 'description', 'page', 'pages', 'base', 'count_parents', 'rank', 'active']));
$q->select($modx->getSelectColumns('sfField', 'sfField'));
$q->select('COUNT(sfFieldX.id) as level');
$q->where(['active' => 1]);
$q->limit(0);
$q->groupby('sfRule.id,sfField.id');
$q->having('COUNT(sfFieldX.id) = ' . $level);

$addWhere = [];
foreach ($parents as $i => $p) {
    if ($proMode) {
        $addWhere[] = "(FIND_IN_SET({$p},REPLACE(IFNULL(NULLIF(pages,''),page),' ','')) OR FIND_IN_SET({$p},REPLACE(NULLIF(count_parents,''),' ','')))";
        //    $q->where("1=1 AND (FIND_IN_SET({$parent},REPLACE(IFNULL(NULLIF(pages,''),page),' ','')) OR FIND_IN_SET({$parent},REPLACE(NULLIF(count_parents,''),' ','')))");
    } else {
        $addWhere[] = "(page = {$p} OR FIND_IN_SET({$p},REPLACE(NULLIF(count_parents,''),' ','')))";
        //    $q->where("1=1 AND (page = {$parent} OR FIND_IN_SET({$parent},REPLACE(NULLIF(count_parents,''),' ','')))");
    }
}
$q->where('1=1 AND (' . implode(' OR ', $addWhere) . ')');

if ($modx->getCount('sfRule', $q)) {
    //если правило найдено, то получить все поля ресурса
    if ((int)$id === $modx->resource->id) {
        $resource = $modx->resource->toArray();
    } else {
        if ($object = $modx->getObject('modResource', $id)) {
            $resource = $object->toArray();
        } else {
            return '';
        }
    }

    $rules = [];
    $fields = [];
    if ($q->prepare() && $q->stmt->execute()) {
        while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
            if ((int)$level === 1) {
                if (isset($rules[$row['rule_id']])) {
                    unset($rules[$row['rule_id']]);
                    continue;
                }
                $rules[$row['rule_id']] = $row['rule_id'];
            } else {
                //TODO: сделать проверку через HAVING
            }
            $fields[$row['id']] = $row;
        }
    }

    $params = [
        'rules' => implode(',', array_keys($rules)),
        'pages' => implode(',', $parents),
        'tpl'   => $tpl
    ];

    if (empty($fields) || empty($rules)) {
        return '';
    }

    foreach ($fields as $field) {
        //TODO: добить switch по классу
        if (isset($resource[$field['key']]) && !empty($resource[$field['key']])) {
            switch (mb_strtolower($field['class'])) {
                case 'modtemplatevar':
                    if (is_array($resource[$field['key']]) && isset($resource[$field['key']][1]) && !empty($resource[$field['key']][1])) {
                        $values = explode('||', $resource[$field['key']][1]);
                        $params[$field['alias']] = array_shift($values);
                    } else {
                        $params[$field['alias']] = $resource[$field['key']];
                    }
                    break;
                default:
                    if (is_array($resource[$field['key']])) {
                        $params[$field['alias']] = array_shift($resource[$field['key']]);
                    } else {
                        $params[$field['alias']] = $resource[$field['key']];
                    }
            }
        }
    }

    return $modx->runSnippet($snippet, array_merge($scriptProperties, $params)) . $outputSeparator;
}
return '';