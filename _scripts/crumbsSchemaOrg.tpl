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
<li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"
    class="sf_crumb breadcrumb-item{if !$sflink} active{/if}" data-separator="{$outputSeparator|htmlentities}">
    <a title="{$menutitle}" itemprop="item" href="{$link}">
        <span itemprop="name">{$menutitle}</span>
        <meta itemprop="position" content="{$idx++}">
    </a>
</li>
{if $sfnested?}
    {foreach $sfnested as $inner_link}
        {$outputSeparator}
        <li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"
            class="breadcrumb-item sf_crumb_nested">
            <a title="{$inner_link['sflink']}" itemprop="item" href="{$page_link}{$inner_link['sfurl']}">
                <span itemprop="name">{$inner_link['sflink']}</span>
                <meta itemprop="position" content="{$idx++}">
            </a>
        </li>
    {/foreach}
{/if}
{if $sflink}
    {$outputSeparator}
    <li itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem"
        class="sf_crumbs breadcrumb-item active">
        <a title="{$sflink}" itemprop="item" href="{$page_link}{$sfurl}" class="sf_link">
            <span itemprop="name">{$sflink}</span>
            <meta itemprop="position" content="{$idx++}">
        </a>
    </li>
{/if}

