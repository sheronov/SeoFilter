<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var SeoFilter $SeoFilter */
$SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
        $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', $scriptProperties);
$pdo = $SeoFilter->pdo;
if (!($SeoFilter instanceof SeoFilter) && !($pdo instanceof pdoFetch)) return '';

$tpl = $modx->getOption('tpl', $scriptProperties, '@INLINE <a href="/{$link}">{$value}</a>');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$rule_id = $modx->getOption('rule_id', $scriptProperties, 0);
$rules_id = $modx->getOption('rules_id', $scriptProperties, '');
$return_link = $modx->getOption('return_link', $scriptProperties, 0);

$system_settings = array('tpl','toPlaceholder','rule_id','rules_id','return_link'); //TODO: убрать лишние параметры сниппета
$sf_tags = array_diff_key($scriptProperties,array_flip($system_settings));

$output = '';
if($rules_id) {
    $rules = array_map('trim',explode('||',$rules_id));
    foreach($rules as $rule) {
        $rule_array = array_map('trim',explode(':',$rule));
        $rid = $rule_array[0];
        $fields = array_map('trim',explode(',',$rule_array[1]));
        $fcount = count($fields);
        $count = 0;
        $arr_get = array();
        foreach($fields as $field) {
            if(isset($_GET[$field]) && !strpos($modx->stripTags($_GET[$field]),',')) {
                $count++;
                $arr_get[$field] = $modx->stripTags($_GET[$field]);
            }
        }
        if($count == $fcount) {
            $rule_id = $rid;
            $sf_tags = array_merge($arr_get,$sf_tags);
        }
    }
}
//print_r($sf_tags);
//$modx->log(modx::LOG_LEVEL_ERROR, print_r(array_diff_key($scriptProperties,array_flip($system_settings)),1));
if($rule_id) {
    $q = $modx->newQuery('sfRule',$rule_id);
    $q->limit(1);
    $q->select(array('sfRule.*'));
    if($q->prepare() && $q->stmt->execute()) {
        $row = $q->stmt->fetch(PDO::FETCH_ASSOC);
        $page_id = $row['page'];

        $word_array = $word_aliases = array();
        $aliases = $SeoFilter->fieldsAliases($page_id);
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

        if($return_link)  {
            $output = $url;
        } else {
            $output = $pdo->getChunk($tpl, array_merge(array('link' => $url), $word_array));
        }

        return $output;
    }

}


return '';