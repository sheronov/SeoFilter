<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools))
    return '';

$output = $link = $url_mask = '';
$find = array();
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$tpl = $modx->getOption('tpl', $scriptProperties, '');
$rules = $modx->getOption('rules',$scriptProperties,'');
unset($scriptProperties['tpl']);
unset($scriptProperties['toPlaceholder']);
unset($scriptProperties['rules']);
$rule_id = $page = 0;
if(isset($rules)) {
    $aliases = array();
    $output = $link = $url_mask = '';
    if(!isset($context)) {
        $context = 'web';
    }
    foreach($scriptProperties as $param => $value) {
        if($scriptProperties[$param]) {
            $aliases[$param] = $value;
        }
    }

    if(!$rule_id) {
        foreach(explode(',',$rules) as $rid) {
            $q = $modx->newQuery('sfFieldIds');
            $q->where(array('sfFieldIds.multi_id' => $rid));
            $fields_count = $modx->getCount('sfFieldIds', $q);

            if(count($aliases) < $fields_count) {
                continue;
            }

            $check = 0;
            foreach($aliases as $param => $input) {
                $q = $modx->newQuery('sfFieldIds');
                $q->where(array('sfFieldIds.multi_id' => $rid));
                $q->rightJoin('sfField', 'sfField', 'sfFieldIds.field_id = sfField.id');
                $q->rightJoin('sfRule', 'sfRule', 'sfFieldIds.multi_id = sfRule.id');
                $q->rightJoin('sfDictionary', 'sfDictionary', 'sfField.id = sfDictionary.field_id');
                $q->where(array('sfField.alias' => $param, 'sfDictionary.input' => $input,'sfRule.active'=>1));
                $q->select(array('sfFieldIds.*', 'sfField.alias', 'sfRule.url', 'sfRule.page', 'sfDictionary.input as word_input', 'sfDictionary.alias as word_alias'));
                $q->limit(1);
                if ($q->prepare() && $q->stmt->execute()) {
                    if ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['where'] && $row['compare']) {
                            $value = $row['value'];
                            $values = explode(',', $value);
                            switch ($row['compare']) { //Обратный механизм поиска
                                case 1:
                                    if (in_array($input, $values))
                                        $check++;
                                    break;
                                case 2:
                                    if (!in_array($input, $values))
                                        $check++;
                                    break;
                                case 3:
                                    if ($input < $value)
                                        $check++;
                                    break;
                                case 4:
                                    if ($input < $value)
                                        $check++;
                                    break;
                                case 5:
                                    if ($input > $values[0] && $input < $values[1])
                                        $check++;
                                    break;
                            }
                        } else {
                            $check++;
                        }
                        $find[$row['alias']] = $row['word_alias'];
                        if ($check == $fields_count) {
                            $rule_id = $row['multi_id'];
                            $url_mask = $row['url'];
                            $page = $row['page'];
                            break;
                        }
                    }
                }
            }
            if($rule_id) {
                break;
            } else {
                $find = array();
            }
        }
    }

    if($rule_id && $url_mask) {
        $link = $pdo->getChunk('@INLINE '.$url_mask,$find);
    }

    if($tpl) {
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
