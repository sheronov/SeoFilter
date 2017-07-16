<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var SeoFilter $SeoFilter */
$SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
        $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
$pdo = $SeoFilter->pdo;
if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) return '';

$tpl = $modx->getOption('tpl', $scriptProperties, '@INLINE <a href="/{$link}">{$value}</a>');
$sortby = $modx->getOption('sortby', $scriptProperties, 'name');
$sortdir = $modx->getOption('sortdir', $scriptProperties, 'ASC');
$limit = $modx->getOption('limit', $scriptProperties, 1);
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, "\n");
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$rule_id = $modx->getOption('rule_id', $scriptProperties, 0);

$system_settings = array('tpl','sortby','sortdir','limit','toPlaceholder','outputSeparator','rule_id'); //TODO: убрать лишние параметры сниппета
$sf_tags = array_diff_key($scriptProperties,array_flip($system_settings));

$output = '';

$modx->log(modx::LOG_LEVEL_ERROR, print_r(array_diff_key($scriptProperties,array_flip($system_settings)),1));
if($rule_id) {
    $word_array = $word_aliases = array();
    $aliases = $SeoFilter->fieldsAliases(0,0,$rule_id);
    $seo_system = array('id','active','class','key');
    $fields = array_flip($aliases);

    foreach($sf_tags as $param => $input) {
        $word = $SeoFilter->getWordArray($input,$fields[$param]);
        foreach(array_diff_key($word, array_flip($seo_system)) as $tmp_key => $tmp_array) {
            $word_array[str_replace('value',$param,$tmp_key)] = $tmp_array;
        }
        $word_array['value'] = $word['value'];
        $word_array[$param.'_input'] = $word['input'];
        $word_array[$param.'_alias'] = $word['alias'];
        unset($word_array['alias']);
        $word_aliases[$param] = $word['alias'];
    }

    $q = $modx->newQuery('sfRule',$rule_id);
    $q->limit(1);
    $q->select(array('sfRule.*'));
    if($q->prepare() && $q->stmt->execute()) {
        $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
        $page_id = $row['page'];
        $url_mask = '@INLINE '.$row['url'];
        $url_add = $pdo->getChunk($url_mask,$word_aliases);
        $url_array = $SeoFilter->findUrlArray($url_add,$page_id);
        if(count($url_array)) {
            if($url_array['new_url']) {
                $url_add = $url_array['new_url'];
            }
        } else {
            $url_array = $SeoFilter->newUrl($url_add,$rule_id,$page_id,0);
        }
        $url = $modx->makeUrl($page_id) . mb_strtolower($url_add);


        $output = $pdo->getChunk($tpl,array_merge(array('link'=>$url),$word_array));
        return $output;
    }

}


return '';