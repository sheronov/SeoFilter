<?php
$output = '';
if(isset($options)) {
    $field_id = (int)$options;
}
if(isset($input) && isset($field_id)) {
    $class_key = 'sfDictionary';
    $modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
    $q = $modx->newQuery('sfDictionary',array('field_id' => $field_id,'input'=>$input));
    $q->limit(1);
    $q->select('value,alias');
    if ($q->prepare() && $q->stmt->execute()) {
        $output = $q->stmt->fetch(PDO::FETCH_ASSOC);

    }
    if(isset($tpl)) {
        $pdo = $modx->getService('pdoTools');
        if (!($pdo instanceof pdoTools))
            $output = '';
        if(isset($output['value']) && isset($output['alias'])) {
            $output = $pdo->getChunk($tpl,array('value'=>$output['value'],'alias'=>$output['alias']));
        } else {
            $output = '';
        }
    }

}
return $output;