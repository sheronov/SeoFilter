<?php

$_lang['seofilter_prop_outputSeparator'] = 'Output Results Separator.';
$_lang['seofilter_prop_sortBy'] = 'Sorting field.';
$_lang['seofilter_prop_sortDir'] = 'Sorting direction.';

$_lang['seofilter_prop_urls'] =' List of links from the URL table for output in results. If the reference id starts with a hyphen, the reference is excluded from the sample ';
$_lang['seofilter_prop_rules'] = 'A list of search rules (separated by commas).';
$_lang['seofilter_prop_parents'] ='Direct parent list, separated by a comma, to search for results. Without taking into account their descendants. By default, the selection is not restricted. If the parents id begins with a hyphen, it is excluded from the selection. ';
$_lang['seofilter_prop_depth'] = 'Depth of search by parent resources';
$_lang['seofilter_prop_sortby'] = 'Any reference field to sort. For random sorting, specify "RAND ()" ';
$_lang['seofilter_prop_sortdir'] = 'Sorting direction: descending or ascending.';
$_lang['seofilter_prop_sortcount'] = 'Sort by the number of resources on the pages? (works after the first sort) ';
$_lang['seofilter_prop_countChildren'] = 'Print the exact number of resources for each link in the [[+total]].';
$_lang['seofilter_prop_count_where'] = 'The condition in the JSON format for calculating resources. Unites with the condition specified in the rule ';
$_lang['seofilter_prop_mincount'] = 'The minimum number of resources for links to be left and the rest to be excluded. Only when counting children.';
$_lang['seofilter_prop_scheme'] = 'Link forming scheme: parameter for modX :: makeUrl ()';
$_lang['seofilter_prop_userank'] = 'Consider the priority of the rule when nesting links or grouping. Allows you to more accurately control the sorting. ';
$_lang['seofilter_prop_context'] = 'You can force a context (several are separated by commas) for which links will be generated. The default is the all links will be displayed. ';
$_lang['seofilter_prop_cache'] = 'Caching the results of the snippet. Do not affect classes of current or active links. ';
$_lang['seofilter_prop_level'] = 'The maximum level of the nesting of the menu, where 1 are rules consisting of one field, and 0 without restrictions';
$_lang['seofilter_prop_nesting'] = 'Use nested links. Virtually attach links from two fields to the link where there is the first field, etc. ';
$_lang['seofilter_prop_double'] = 'Allow duplicate links when nesting. A link of two fields will be inserted into two links from one field, etc. ';
$_lang['seofilter_prop_relative'] = 'Rearrange the menu for the selected item. Apply when there are rules from one and two or more fields. ';
$_lang['seofilter_prop_onlyrelative'] = 'Displays the menu only on the virtual page. Works in conjunction with the relative setting. ';
$_lang['seofilter_prop_groupbyrule'] = 'Group by rules. Tasks the tplGroup. ';
$_lang['seofilter_prop_groupsort'] = 'Sort rules. "level" for sorting by the number of constituent fields';
$_lang['seofilter_prop_groupdir'] = 'Sort direction for the group: in descending order or ascending.';
$_lang['seofilter_prop_showHidden'] = 'Show links hidden in the menu.';
$_lang['seofilter_prop_cacheTime'] = 'Cache actuality time in seconds.';
$_lang['seofilter_prop_fastMode'] = 'Quick mode for processing chunks. All unhandled tags (conditions, snippets, etc.) will be cut. ';
$_lang['seofilter_prop_limit'] = 'Limiting the output of results on a page.';
$_lang['seofilter_prop_offset'] = 'Skipping results from the beginning.';

$_lang['seofilter_prop_firstClass'] = 'Class for the first menu item.';
$_lang['seofilter_prop_hereClass'] = 'Class for the active menu item.';
$_lang['seofilter_prop_innerClass'] = 'Class of internal menu links.';
$_lang['seofilter_prop_lastClass'] = 'Class of the last menu item.';
$_lang['seofilter_prop_levelClass'] =' The class of the menu level. For example, if you specify "level", then there will be "level1", "level2", etc. ';
$_lang['seofilter_prop_outerClass'] = 'Menu wrapper class.';
$_lang['seofilter_prop_parentClass'] = 'Class of the menu category.';
$_lang['seofilter_prop_rowClass'] = 'Class of one menu bar.';
$_lang['seofilter_prop_selfClass'] = 'The class of the current document in the menu.';

$_lang['seofilter_prop_hideSubMenus'] = 'Do not display inactive menu branches, that is, nested links if they are not active.';

$_lang['seofilter_prop_where'] = 'An array of additional sample parameters encoded in JSON.';

$_lang['seofilter_prop_tpl'] = 'The name of the chunk for the output of the result (you can also INLINE).';
$_lang['seofilter_prop_tplGroup'] = 'Chunk of group registration when grouping is enabled by rules.';
$_lang['seofilter_prop_tplHere'] = 'Chunk of the current page in the menu';
$_lang['seofilter_prop_tplInner'] = 'Chunk-wrapper for nested menu items. If empty - will use "tplOuter". ';
$_lang['seofilter_prop_tplInnerHere'] = 'The wrapper wrapper for the current page for the nested menu item.';
$_lang['seofilter_prop_tplInnerRow'] = 'Chunk wrapper of the active item for the submenu.';
$_lang['seofilter_prop_tplOuter'] = 'Chunk wrapper for the entire menu block.';
$_lang['seofilter_prop_tplParentRow'] = 'The link design chunk that has nested menus.';
$_lang['seofilter_prop_tplParentRowActive'] = 'Chunk for creating an active link, in which there are submenus.';
$_lang['seofilter_prop_tplParentRowHere'] = 'The design chunk of the current link, in which there are submenus.';
$_lang['seofilter_prop_toPlaceholder'] = 'If not empty, the snippet will store all the data in the locationholder with that name, instead of displaying no screen.';
$_lang['seofilter_prop_tplWrapper'] = 'Name of a chunk serving as a wrapper template for the output. This does not work with toSeparatePlaceholders.';

$_lang['seofilter_prop_forceXML'] = 'Force the output page as xml.';
$_lang['seofilter_prop_sitemapSchema'] = 'Schema of sitemap.';
$_lang['seofilter_prop_cacheTime'] = 'Time until the cache expires, in seconds.';
$_lang['seofilter_prop_cacheKey'] = 'Cache key. Stored in "core/cache/default/yourkey"';
$_lang['seofilter_prop_fast'] = 'Fast snippet mode. Uses values from the database instead of counting on the fly.';
$_lang['seofilter_prop_input'] = 'The query by which to find the value at the dictionary SeoFilter';
$_lang['seofilter_prop_field_id'] = 'Field ID, to refine the search value';
$_lang['seofilter_prop_pages'] = 'List of id pages for search - the search is in the order as the values are passed (you may do not need a list of rules).';
$_lang['seofilter_prop_as_name'] = 'The value that will substitute for the original name of the link.';
$_lang['seofilter_prop_link_classes'] = 'The classes that you want to add to the link.';