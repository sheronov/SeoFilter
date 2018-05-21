<?php
/** @var array $scriptProperties */
$modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
$pdo = $modx->getService('pdoTools');
if (!($pdo instanceof pdoTools))
    return '';

$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$tpl = $modx->getOption('tpl', $scriptProperties, '@INLINE <a href="[[+url]]">[[+name]]</a>');
$rules = $modx->getOption('rules',$scriptProperties,'');
$pages = $modx->getOption('pages',$scriptProperties,'');
$where = $modx->getOption('where',$scriptProperties,'');
$as_name = $modx->getOption('as_name',$scriptProperties,'');
$output =  '';
if(!empty($rules) || !empty($pages)) {
    $all = array();
    $fields = array();
    $words = array();
    $link = array();


    if(!empty($rules)) {
        $rules = array_map('trim',explode(',',$rules));
    } else {
        $rules = array();
    }

    if(!empty($pages)) {
        if(!empty($where)) {
            if(!is_array($where)) {
                $where = $modx->fromJSON($where);
            }
        }
        $pages = array_map('trim',explode(',',$pages));
        foreach($pages as $page) {
            $q = $modx->newQuery('sfRule');
            $q_where = array('page'=>$page);
            if(is_array($where)) {
                $q_where = array_merge($q_where,$where);
            }
            $q->where($q_where);
            $q->select('id');
            $rules[] = $modx->getValue($q->prepare());
        }
    }

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
                $where['input:LIKE'] = '%'.$scriptProperties[$field['alias']].'%';
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
        if(isset($all[$rule_id]) && is_array($all[$rule_id])) {
            foreach ($all[$rule_id] as $field_id => $field) {
                $word_id = $words[$field_id];
                $q->innerJoin('sfUrlWord', 'sfUrlWord' . $i, 'sfUrlWord' . $i . '.url_id = sfUrls.id AND sfUrlWord' . $i . '.word_id = ' . $word_id . ' AND sfUrlWord' . $i . '.field_id = ' . $field_id);
                $i++;
            }
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
        $c_suffix = $modx->getOption('container_suffix',null,'/');
        $u_suffix = $modx->getOption('seofilter_url_suffix',null,'',true);
        $between_links = $modx->getOption('seofilter_between_links',null,'/',true);
        $possibleSuffixes = array_map('trim',explode(',',$this->modx->getOption('seofitler_possible_suffixes',null,'/,.html,.php',true)));
        $possibleSuffixes = array_unique(array_merge($possibleSuffixes,array($c_suffix)));

        $url = $link['new_url']?:$link['old_url'];
        $page_url = $modx->makeUrl($link['page_id'],$scriptProperties['context'],'',$scriptProperties['scheme']);

        foreach($possibleSuffixes as $possibleSuffix) {
            if (substr($url, -strlen($possibleSuffix)) == $possibleSuffix) {
                $url = substr($url, 0, -strlen($possibleSuffix));
            }
        }
        $link['url'] = $page_url.$between_links.$url.$u_suffix;
        if($as_name) {
            $link['name'] = $as_name;
        } else {
            $link['name'] = $link['menutitle']?:$link['link'];
        }
        $output = $pdo->getChunk($tpl,$link,$fastMode);
    }
}
if(!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder,$output);
} else {
    return $output;
}
