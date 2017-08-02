<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools))
    return '';
$output = $link = $url_mask = '';
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$rule_id = $page = 0;
if(isset($rules)) {
    $aliases = array();
    $output = $link = $url_mask = '';
    if(!isset($context)) {
        $context = 'web';
    }
    foreach(explode(',',$rules) as $rule_id) {
        $q = $modx->newQuery('sfFieldIds');
        $q->innerJoin('sfField','sfField','sfFieldIds.field_id = sfField.id');
        $q->innerJoin('sfRule','sfRule','sfFieldIds.multi_id = sfRule.id');
        $q->where(array('sfFieldIds.multi_id'=>$rule_id));
        $fields_count = $modx->getCount('sfFieldIds',$q);
        $q->innerJoin('sfDictionary','sfDictionary','sfField.id = sfDictionary.field_id');
        $q->where(array('sfDictionary.input:IN'=>$scriptProperties));
        $q->select(array('sfFieldIds.*','sfField.alias','sfRule.url','sfRule.page','sfDictionary.alias as word_alias'));
        $q->limit(0);
        $words_count = $modx->getCount('sfFieldIds',$q);
        if($fields_count != $words_count) {
            continue;
        }
        if($q->prepare() && $q->stmt->execute()) {
            $check = 1;
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if($get_param = $scriptProperties[$row['alias']]) {
                    $rule_id = $row['multi_id'];
                    $url_mask = $row['url'];
                    $page = $row['page'];
                    $aliases[$row['alias']] = $row['word_alias'];
                    if($row['where'] && $row['compare']) {
                        $check = 0;
                        $value = $row['value'];
                        $values = explode(',', $value);
                        switch ($row['compare']) { //Обратный механизм поиска
                            case 1:
                                if(in_array($get_param,$values))
                                    $check = 1;
                                break;
                            case 2:
                                if(!in_array($get_param,$values))
                                    $check = 1;
                                break;
                            case 3:
                                if($get_param < $value)
                                    $check = 1;
                                break;
                            case 4:
                                if($get_param < $value)
                                    $check = 1;
                                break;
                            case 5:
                                if($get_param > $values[0] && $get_param < $values[1])
                                    $check = 1;
                                break;
                        }
                    }
                } else {
                    $check = 0;
                }

            }
            if(!$check) {
                $rule_id = $page = $url_mask = 0;
                continue;
            }
            if($check && $rule_id) {
                break;
            }
        }
    }
    if($rule_id && $url_mask) {
        $link = $modx->makeUrl($page,$context).$pdo->getChunk('@INLINE '.$url_mask,$aliases);
    }
    if(isset($tpl)) {
        $output = $pdo->getChunk($tpl,array('link'=>$link));
    } else {
        $output = $link;
    }
}
if($toPlaceholder) {
    $modx->setPlaceholder($toPlaceholder,$output);
} else {
    return $output;
}