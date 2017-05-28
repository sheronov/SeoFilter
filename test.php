<?php
// Подключаем
define('MODX_API_MODE', true);
require '../index.php';

// Включаем обработку ошибок
$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

echo '<pre>';
print_r($modx->config);
echo 'test';