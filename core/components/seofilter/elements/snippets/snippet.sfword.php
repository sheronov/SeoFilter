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
    if(!$output) {
        if($field = $modx->getObject('sfField',$field_id)) {
            if($value = $field->getValueByInput($input,$field->get('class'),$field->get('key'))) {
                $path = $modx->getOption('seofilter_core_path', null, $modx->getOption('core_path') . 'components/seofilter/');
                $processorProps = array(
                    'class' => $field->get('class'),
                    'key' => $field->get('key'),
                    'field_id' => $field->get('id'),
                    'input' => $input,
                    'value' => $value
                );
                $otherProps = array('processors_path' => $path . 'processors/');
                $response = $modx->runProcessor('mgr/dictionary/create', $processorProps, $otherProps);
                if ($response->isError()) {
                    $modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter]' . $response->getMessage());
                } else {
                    $output = array('value'=>$value,'alias'=>$response->response['object']['alias']);
                }
            }
        }
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