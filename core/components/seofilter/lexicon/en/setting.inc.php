<?php

$_lang['area_seofilter_main'] = 'Main';
$_lang['area_seofilter_seo'] = 'Default replacement fields';
$_lang['area_seofilter_jquery'] = 'JQuery selectors for replacements via AJAX';
$_lang['area_seofilter_count'] = 'Calculations and selection';


$_lang['setting_seofilter_ajax'] = 'Change tags, titles with AJAX';
$_lang['setting_seofilter_ajax_desc'] = 'Default yes, replaces title, description and other fields when working with filter, look the instruction';
$_lang['setting_seofilter_decline'] = 'Declining the value by case';
$_lang['setting_seofilter_decline_desc'] = 'Default no, declination uses the service morpher.ru (Limit of free requests - 1000 per day)';
$_lang['setting_seofilter_redirect'] = 'Redirect to priority URL';
$_lang['setting_seofilter_redirect_desc'] = 'Defaul yes. Redirect to a priority-based address when crossing fields so that there are no duplicates';
$_lang['setting_seofilter_replace'] = 'Replace tags with values from the dictionary';
$_lang['setting_seofilter_replace_desc'] = 'Replaces a construct {$value} in all SEO fields with values from the dictionary. To case {$value_r},{$value_p} ...';
$_lang['setting_seofilter_separator'] = 'Base separator parameters and values';
$_lang['setting_seofilter_separator_desc'] = 'Default "-", It breaks the value from the parameter';
$_lang['setting_seofilter_valuefirst'] = 'First the value, then parameter ';
$_lang['setting_seofilter_valuefirst_desc'] = 'Default no. If in the address first the value, and then through the separator parameter.. Example ".../red-color"';

$_lang['setting_seofilter_morpher_username'] = 'Login for service morpher.ru';
$_lang['setting_seofilter_morpher_username_desc'] = 'If you specify it, there will be more inclination options including where is, where to, from where';
$_lang['setting_seofilter_morpher_password'] = 'Password for service morpher.ru';
$_lang['setting_seofilter_morpher_password_desc'] = 'Password for login on morpher.ru';
$_lang['setting_seofilter_base_get'] = 'Parameters that do not affect meta tags';
$_lang['setting_seofilter_base_get_desc'] = 'GET-parameters that do not affect the META tags. Default "price,page,limit,tpl,sort"';

$_lang['setting_seofilter_count'] = 'Counting descendants';
$_lang['setting_seofilter_count_desc'] = 'Default no. If yes, the placeholder {$count} in SEO templates will be available';
$_lang['setting_seofilter_choose'] = 'Selection by resource field';
$_lang['setting_seofilter_choose_desc'] = 'For example: "msProductData.price". Several values by commos. Multiplied {choose} with fields from select and creating: min_price_{select}, max_price_{select}. Synonyms by "=", for example "msProductData.old_price=op"';
$_lang['setting_seofilter_select'] = 'Which resource fields to choose';
$_lang['setting_seofilter_select_desc'] = 'For example: "id,msProductData.price". Placeholder creating: min_{choose}_id, min_{choose}_price, max_{choose}_price etc. Synonyms allowed "msProductData.old_price as old"';

$_lang['setting_seofilter_templates'] = 'Resource templates for tracking';
$_lang['setting_seofilter_templates_desc'] = 'For the plugin to add values to the SEO dictionary after saving the resource';
$_lang['setting_seofilter_classes'] = 'Resources Class_key for tracking';
$_lang['setting_seofilter_classes_desc'] = 'Default "msProduct". To run a plug-in to add values to a SEO dictionary after a resource is saved';
$_lang['setting_seofilter_snippet'] = 'Snippet to prepare/process words for META tags';
$_lang['setting_seofilter_snippet_desc'] = 'PrepareSnippet accepts an array of words serialize($row), rule id ($rule_id), and pages ($page_id), and $pdoTools. Must return $row!';

$_lang['setting_seofilter_jcontent'] = 'jQuery-selector for the field: Content';
$_lang['setting_seofilter_jcontent_desc'] = 'Default ".sf_content"';
$_lang['setting_seofilter_jdescription'] = 'jQuery-selector for the field: Description';
$_lang['setting_seofilter_jdescription_desc'] = 'Default "meta[name="description"]"';
$_lang['setting_seofilter_jlink'] = 'jQuery-selector for the field: Link name';
$_lang['setting_seofilter_jlink_desc'] = 'Default ".sf_link"';
$_lang['setting_seofilter_jh1'] = 'jQuery-selector for the field: Title H1';
$_lang['setting_seofilter_jh1_desc'] = 'Default ".sf_h1"';
$_lang['setting_seofilter_jh2'] = 'jQuery-selector for the field: Title H2';
$_lang['setting_seofilter_jh2_desc'] = 'Default ".sf_h1"';
$_lang['setting_seofilter_jintrotext'] = 'jQuery-selector for the field: Introtext';
$_lang['setting_seofilter_jintrotext_desc'] = 'Default ".sf_introtext"';
$_lang['setting_seofilter_jkeywords'] = 'jQuery-selector for the field: Keywords';
$_lang['setting_seofilter_jkeywords_desc'] = 'Default ".sf_keywords"';
$_lang['setting_seofilter_jtext'] = 'jQuery-selector for the field: Text field';
$_lang['setting_seofilter_jtext_desc'] = 'Default ".sf_text"';
$_lang['setting_seofilter_jtitle'] = 'jQuery-selector for the field: Pagetitle';
$_lang['setting_seofilter_jtitle_desc'] = 'Default "title"';
$_lang['setting_seofilter_replacebefore'] = 'Add SEO header through separator';
$_lang['setting_seofilter_replacebefore_desc'] = 'If so, it will add and remove the SEO header to the title of the page, preserving the nesting and built-in ajax-pagination of mFilter2.';
$_lang['setting_seofilter_replaceseparator'] = 'Title Separator';
$_lang['setting_seofilter_replaceseparator_desc'] = 'Default "/". If you use pdoTitle and SetMeta=1 option in mFilter2, Ajax-pagination will work if the settings above are enabled.';

$_lang['setting_seofilter_content'] = 'The field where the content is stored';
$_lang['setting_seofilter_content_desc'] = 'Default "content". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_description'] = 'The field where the description is stored';
$_lang['setting_seofilter_description_desc'] = 'Default "description". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_link'] = 'The field where the link name is stored';
$_lang['setting_seofilter_link_desc'] = 'Default "". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_h1'] = 'The field where the Title H1 is stored';
$_lang['setting_seofilter_h1_desc'] = 'Default "longtitle". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_h2'] = 'The field where the Title H2 is stored';
$_lang['setting_seofilter_h2_desc'] = 'Default "". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_introtext'] = 'The field where the introtext is stored';
$_lang['setting_seofilter_introtext_desc'] = 'Default "introtext". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_keywords'] = 'The field where the keywords is stored';
$_lang['setting_seofilter_keywords_desc'] = 'Default "". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_pagetpl'] = 'Field with template for pagination in header';
$_lang['setting_seofilter_pagetpl_desc'] = 'Default "@INLINE / [[%seofilter_page]] [[+page]]". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_text'] = 'Field where the text field is stored';
$_lang['setting_seofilter_text_desc'] = 'Default "". You can specify any of the resource fields or the TV fields as they are called.';
$_lang['setting_seofilter_title'] = 'The field where the pagetitle is stored';
$_lang['setting_seofilter_title_desc'] = 'Default "pagetitle". You can specify any of the resource fields or the TV fields as they are called.';

$_lang['setting_seofilter_url_suffix'] = 'The suffix for generated pages through SeoFilter.';
$_lang['setting_seofilter_url_suffix_desc'] = 'The default is empty. Such an ending will be substituted through AJAX and in the links to the menu, etc. For example, "/" or ".html"';
$_lang['setting_seofilter_url_redirect'] = 'Redirect to a page with the correct suffix';
$_lang['setting_seofilter_url_redirect_desc'] = 'The default is "No". If so, it will redirect to the page with the correct url suffix.';
$_lang['setting_seofilter_page_key'] = 'The name of the pagination variable, usually "page", for the page number in SEO-texts';
$_lang['setting_seofilter_page_key_desc'] = 'If you specify, then in the SEO-templates will be available placeholder {$ page_number}, as well as a placeholder by name - if the name is pagе, it will be {$ page}.';

$_lang['setting_seofilter_url_help'] = 'URL address for opening help in iframe';
$_lang['setting_seofilter_tpls_path'] = 'Path for file elements';
$_lang['setting_seofilter_hide_empty'] = 'Redirect to page 404 on blank pages';
$_lang['setting_seofilter_hide_empty_desc'] = 'Blank pages are those with no results. The counting function must be enabled.';

$_lang['setting_seofilter_morpher_token'] = 'Token to the service moprher.ru';
$_lang['setting_seofilter_morpher_token_desc'] = 'If you specify, then do not rush into the guest limit of requests';
$_lang['setting_seofilter_container_suffix'] = 'Suffix on resetting to original pages';
$_lang['setting_seofilter_container_suffix_desc'] = 'By default, as from the system configuration of container_suffix. Use if you use the url freeze for parent directories';
$_lang['setting_seofilter_last_modified'] = 'Send the Last-Modified header for "virtual" pages';
$_lang['setting_seofilter_last_modified_desc'] = 'There is no default. When enabled, the date of the link change will be sent if available, otherwise the creation date';
$_lang['setting_seofilter_mfilter_words'] = 'Allow new words to be added via Ajax mFilter2';
$_lang['setting_seofilter_mfilter_words_desc'] = 'There is no default. Enable only if new values are not written through processors or are dynamically generated in mFilter2';
$_lang['setting_seofilter_crumbs_tpl_current'] = 'Chunk for the replacement of bread crumbs through Ajax';
$_lang['setting_seofilter_crumbs_tpl_current_desc'] = 'The default is "tpl.SeoFilter.crumbs.current". It is not recommended to change if you do not know why.';
$_lang['setting_seofilter_crumbs_replace'] = 'Allow replacing bread crumbs through Ajax';
$_lang['setting_seofilter_crumbs_replace_desc'] = 'By default, yes. You can turn it off if you do not use bread crumbs';
$_lang['setting_seofilter_count_handler_class'] = 'Handler class for counting';
$_lang['setting_seofilter_count_handler_class_desc'] = 'The default is "sfCountHandler". Name of the class that implements the logic of counting';
$_lang['setting_seofilter_values_separator'] = 'Separate multiple values in texts';
$_lang['setting_seofilter_values_separator_desc'] = 'The default is ", || и ". Recorded and commas. Through || write values for the connection of the last element, you can leave only a comma.';
$_lang['setting_seofilter_values_delimeter'] = 'Value delimiter in the address bar';
$_lang['setting_seofilter_values_delimeter_desc'] = 'Default ",". Override if in mFilter2 you use another delimiter';
$_lang['setting_seofilter_page_tpl'] = 'The template for generating friendly URLs to pages of pagination';
$_lang['setting_seofilter_page_tpl_desc'] = 'For sharing with pdoPage pageLinkScheme setting. You can specify this  "/page-[[+page]]"';

$_lang['setting_seofilter_hidden_tab'] = 'Show hidden property tab';
$_lang['setting_seofilter_hidden_tab_desc'] = 'There is no default. Contains two property arrays and a numeric field';
$_lang['setting_seofilter_super_hidden_props'] = 'Show files from a folder';
$_lang['setting_seofilter_super_hidden_props_desc'] = 'There is no default. Displays a list of files from the folder from the seofilter_tpls_path';

$_lang['setting_seofilter_replace_host'] = 'Replace domain from site_url to $ _SERVER ["http_host"]';
$_lang['setting_seofilter_replace_host_desc'] = 'There is no default. Use if the site is available across multiple domains or contexts with different addresses are used, but without switching them';