<?php
/** @var array $scriptProperties */
$output = '';
if(isset($options) && empty($field_id)) {
    $field_id = (int)$options;
}
$input = $modx->getOption('input',$scriptProperties,'');
if(!empty($input) || $input === '0' || $input === 0) {
    $tpl = $modx->getOption('tpl', $scriptProperties, '');
    $modx->addPackage('seofilter', $modx->getOption('core_path').'components/seofilter/model/');
    $q = $modx->newQuery('sfDictionary');
    $where = array('input'=>$input);
    if(isset($field_id)) {
        $where['field_id'] = (int)$field_id;
    }
    $q->where($where);
    $q->limit(1);
    $q->select(array('sfDictionary.*'));
    if ($q->prepare() && $q->stmt->execute()) {
        $output = $q->stmt->fetch(PDO::FETCH_ASSOC);
    }
    if(empty($output) && !empty($field_id)) {
        if($field = $modx->getObject('sfField',(int)$field_id)) {
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
                    $modx->error->reset();
                } else {
                    $output = $response->response['object'];
                }
            }
        }
    }
    if(!empty($tpl)) {
        $pdoTools = $modx->getService('pdoTools');
        if (!($pdoTools instanceof pdoTools))
            return '';
        if(!empty($output)) {
            $output = $pdoTools->getChunk($tpl,$output);
        } else {
            $output = '';
        }
    }
}
return $output;