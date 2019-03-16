<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if (!class_exists('xPDOObjectVehicle')) {
    $transport->xpdo->loadClass('transport.xPDOObjectVehicle', MODX_CORE_PATH . 'xpdo/', true, true);
}
$transport->xpdo->loadClass('encryptedVehicle',
    MODX_CORE_PATH . 'components/' . strtolower($transport->name) . '/model/', true, true);