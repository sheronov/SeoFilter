<?php
/** @var SeoFilter $SeoFilter */
/** @var modX $modx */
define('MODX_API_MODE', true);

if (!file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
} else {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
}

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$SeoFilter = $modx->getService('seofilter', 'SeoFilter', $modx->getOption('seofilter_core_path', null,
        $modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
if (!($SeoFilter instanceof SeoFilter)) {
    echo 'Load SeoFilter failed';
    exit;
}
$processorProps = array(
  'all'=>1
);
$otherProps = array('processors_path' => $SeoFilter->config['processorsPath']);
$response = $modx->runProcessor('mgr/urls/recount', $processorProps, $otherProps);
if ($response->isError()) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[SeoFilter] '. print_r($response->response, 1));
    $modx->error->reset();
    echo "Recounting failed \n";
} else {
    echo "Recounting success \n";
}