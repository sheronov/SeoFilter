{$_modx->runSnippet('sfNearLink',[
    'outputSeparator' => $outputSeparator,
    'tpl'=>'@INLINE <li><a href="{$url}">{$name}</a></li>'
])}
<li class="active">{$menutitle}</li>