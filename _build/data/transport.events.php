<?php

$events = array();

$tmp = array(
    'sfOnBeforeWordAdd',
    'sfOnWordAdd',
    'sfOnBeforeUrlAdd',
    'sfOnUrlAdd',
    'sfOnReturnMeta',
    'sfOnBeforeUrlUpdate',
    'sfOnUrlUpdate',
    'sfOnUrlBeforeRemove',
    'sfOnUrlRemove',
);

foreach ($tmp as $k => $v) {
    /** @var modEvent $event */
    $event = $modx->newObject('modEvent');
    $event->fromArray(array(
        'name' => $v,
        'service' => 6,
        'groupname' => PKG_NAME,
    ), '', true, true);
    $events[] = $event;
}

return $events;