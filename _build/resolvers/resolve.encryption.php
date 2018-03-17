<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $transport->xpdo->loadClass('transport.xPDOObjectVehicle', XPDO_CORE_PATH, true, true);
    $transport->xpdo->loadClass('encryptedVehicle', MODX_CORE_PATH . 'components/' . strtolower($transport->name) . '/model/', true, true);
}