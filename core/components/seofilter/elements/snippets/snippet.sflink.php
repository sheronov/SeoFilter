<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools))
    return '';

$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$tpl = $modx->getOption('tpl', $scriptProperties, '@INLINE <a href="[[+url]]">[[+name]]</a>');
$rules = $modx->getOption('rules',$scriptProperties,'');
$output =  '';
if(!empty($rules)) {
    $all = array();
    $fields = array();
    $words = array();
    $link = array();

    $rules = explode(',',$rules);
    $q = $modx->newQuery('sfFieldIds');
    $q->where(array('sfFieldIds.multi_id:IN'=>$rules));
    $q->leftJoin('sfField','sfField','sfFieldIds.field_id = sfField.id');
    $q->select(array('sfField.*','sfFieldIds.multi_id'));
    if($q->prepare() && $q->stmt->execute()) {
        while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
            $all[$row['multi_id']][$row['id']] = $row;
            $fields[$row['id']] = $row;
        }
    }


    foreach($fields as $id => $field) {
        if(isset($scriptProperties[$field['alias']])) {
            $q = $modx->newQuery('sfDictionary');
            $where = array('sfDictionary.field_id'=>$id);
            if($field['exact']) {
                $where['input'] = $scriptProperties[$field['alias']];
            } else {
                $where['input:LIKE'] = $scriptProperties[$field['alias']];
            }
            $q->where($where);
            $q->select(array('sfDictionary.*'));
            if($q->prepare() && $q->stmt->execute()) {
                while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $words[$field['id']] = $row['id'];
                }
            }
        }
    }

    foreach($rules as $rule_id) {
        $q = $modx->newQuery('sfUrls');
        $q->where(array('multi_id'=>$rule_id,'active'=>1));
        $q->select(array('sfUrls.*'));
        $q->limit(1);
        $i = 0;
        foreach($all[$rule_id] as $field_id => $field) {
            $word_id = $words[$field_id];
            $q->innerJoin('sfUrlWord','sfUrlWord'.$i,'sfUrlWord'.$i.'.url_id = sfUrls.id AND sfUrlWord'.$i.'.word_id = '.$word_id.' AND sfUrlWord'.$i.'.field_id = '.$field_id);
            $i++;
        }
        if($modx->getCount('sfUrls',$q)) {
            if($q->prepare() && $q->stmt->execute()) {
                $link = $q->stmt->fetch(PDO::FETCH_ASSOC);
                break;
            }
        } else {
            continue;
        }
    }

    if(!empty($link)) {
        $url = $link['new_url']?:$link['old_url'];
        $link['url'] = $modx->makeUrl($link['page_id'],$scriptProperties['context'],'',$scriptProperties['scheme']).$url;
        $link['name'] = $link['menutitle']?:$link['link'];
        $output = $pdo->getChunk($tpl,$link,$fastMode);
    }
}
if($toPlaceholder) {
    $modx->setPlaceholder($toPlaceholder,$output);
} else {
    return $output;
}
