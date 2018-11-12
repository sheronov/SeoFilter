{if !$sfnested}{set $sfnested = $_modx->getPlaceholder('sf.nested')|fromJSON}{/if}
{if !$sflink}{set $sflink = $_modx->getPlaceholder('sf.link')}{/if}
{if !$sfurl}{set $sfurl = $_modx->getPlaceholder('sf.url')}{/if}
{if !$idx}{set $idx = 1}{/if}
{set $page_link = $link}
{foreach ['.html','.php'] as $suffix}
    {set $msufx = '*'~$suffix}
    {if $page_link | match : $msufx}
        {set $r_mask = '/'~$suffix~'$/'}
        {set $page_link = ($page_link | ereplace: $r_mask:'/')}
        {break}
    {/if}
{/foreach}
<li class="sf_crumb{if !$sflink} active{/if}" data-idx="{$idx++}" data-separator="{$outputSeparator|htmlentities}">{if $sflink}<a href="{$link}">{$menutitle}</a>{else}{$menutitle}{/if}
</li>{if $sfnested?}{foreach $sfnested as $inner_link}{$outputSeparator}<li class="sf_crumb_nested" data-idx="{$idx++}"><a href="{$page_link}{$inner_link['sfurl']}">{$inner_link['sflink']}</a>
</li>{/foreach}{/if}{if $sflink}{$outputSeparator}<li class="active sf_crumbs" data-idx="{$idx++}"><span class="sf_link">{$sflink}</span>
    {*закомментированный ниже вариант позволит возвращать ссылку *}
    {*<a href="{$page_link}{$sfurl}" class="sf_link">{$sflink}</a>*}
</li>{/if}
