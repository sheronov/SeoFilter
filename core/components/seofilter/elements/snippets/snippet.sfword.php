<?php
$data = array();
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
        $data = $q->stmt->fetch(PDO::FETCH_ASSOC);

    }
    if(isset($tpl)) {
        $pdo = $modx->getService('pdoTools');
        if (!($pdo instanceof pdoTools))
            return '';
        if(isset($data['value']) && isset($data['alias'])) {
            return $pdo->getChunk($tpl,array('value'=>$data['value'],'alias'=>$data['alias']));
        } else {
            return '';
        }
    }

}
return $data;