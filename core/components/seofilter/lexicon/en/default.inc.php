<?php
include_once 'setting.inc.php';

$_lang['seofilter'] = 'SeoFilter';
$_lang['seofilter_menu_desc'] = 'The managing of SEF URL and SEO for filters';
$_lang['seofilter_intro_msg'] = 'You can select several items at once with Shift or Ctrl';



$_lang['seofilter_field'] = 'Field';
$_lang['seofilter_fields'] = 'Filter fields';
$_lang['seofilter_fields_intro'] = 'On this page you need to add some fields that you want to use for creating the SEO rules. The field synonym must match the filter synonym. When you will add fields, the values will automatically collect in the dictionary on the Third tab';

$_lang['seofilter_rule'] = 'SEO rule';
$_lang['seofilter_rules'] = 'SEO rules';
$_lang['seofilter_rules_intro'] = 'You can set up the rules for creating addresses for one or more fields. You can also set SEO templates for each rule. When you add a field to a rule, you can specify specific values to limit the rule so that all values in the field do not intersect with other values';

$_lang['seofilter_seometa'] = 'SEO meta';
$_lang['seofilter_seometas'] = 'SEO templates';
$_lang['seofilter_seometas_intro'] = 'All prescribed SEO and Meta fields are collected in a single table for your convenience';

$_lang['seofilter_dictionary'] = 'Dictionary';
$_lang['seofilter_dictionary_intro'] = 'When you add a field, prompts and their insights are displayed here(if appropriate settings are enabled). Words can be adjusted manually. When a synonym is changed, the pages are automatically regenerated into the URL table. If necessary, you can add words to the fields manually';

$_lang['seofilter_urls'] = 'URL table';
$_lang['seofilter_urls_intro'] = 'After the rules are added, the addresses and migration statistics automatically appear here. You can also individually set a unique address, SEO and meta field for each page, or disable it at all.';

$_lang['seofilter_field_id'] = 'Id';
$_lang['seofilter_field_name'] = 'Name';
$_lang['seofilter_field_description'] = 'Description';
$_lang['seofilter_field_active'] = 'Active (The words are collected)';
$_lang['seofilter_field_page'] = 'Page';
$_lang['seofilter_field_pages'] = 'Pages';
$_lang['seofilter_field_pages_more'] = 'Pages ID (Several ID separated by commas)';
$_lang['seofilter_field_class'] = 'Class';
$_lang['seofilter_field_class_more'] = 'Select class';
$_lang['seofilter_field_key'] = 'Key';
$_lang['seofilter_field_alias'] = 'Alias';
$_lang['seofilter_field_dont'] = 'Do not process';
$_lang['seofilter_field_translit'] = 'Transliteration';
$_lang['seofilter_field_baseparam'] = 'Base parameter';
$_lang['seofilter_field_baseparam_more'] = 'Base parameter (leaves its meta-texts)';
$_lang['seofilter_field_urltpl'] = 'Url templates';
$_lang['seofilter_field_priority'] = 'Priority';
$_lang['seofilter_field_method'] = 'How to process values';
$_lang['seofilter_field_exact'] = 'Exact occurrence (Hard Search)';
$_lang['seofilter_field_valuefirst'] = 'Value before parameter';
$_lang['seofilter_field_hideparam'] = 'Hide the parameter in url';
$_lang['seofilter_field_slider'] = 'Field type "Slider" (number filters)';

$_lang['seofilter_msProductData'] = 'msProductData (product fields)';
$_lang['seofilter_modResource'] = 'modResource (resourse fields)';
$_lang['seofilter_msVendor'] = 'msVendor (Vendor fields)';
$_lang['seofilter_msProductOption'] = 'msProductOption (minishop2 options)';
$_lang['seofilter_modTemplateVar'] = 'modTemplateVar (TV fields)';

$_lang['seofilter_field_create'] = 'Add field';
$_lang['seofilter_field_update'] = 'Change field';
$_lang['seofilter_field_enable'] = 'Activate field';
$_lang['seofilter_fields_enable'] = 'Activate fields';
$_lang['seofilter_field_disable'] = 'Deactivate field';
$_lang['seofilter_fields_disable'] = 'Deactivate fields';
$_lang['seofilter_field_remove'] = 'Delete field';
$_lang['seofilter_fields_remove'] = 'Delete fields';
$_lang['seofilter_field_remove_confirm'] = 'Are you sure that you want to delete this field?';
$_lang['seofilter_fields_remove_confirm'] = 'Are you sure that you want to delete these fields?';
$_lang['seofilter_field_active'] = 'Activated';

$_lang['seofilter_field_err_name'] = 'You need to specify a field name.';
$_lang['seofilter_field_err_ae'] = 'The field with the same name already exists.';
$_lang['seofilter_field_err_nf'] = 'The Field not found.';
$_lang['seofilter_field_err_ns'] = 'The field is not specified.';
$_lang['seofilter_field_err_remove'] = 'Error deleting field.';
$_lang['seofilter_field_err_save'] = 'Error saving field.';

$_lang['seofilter_grid_search'] = 'Search';
$_lang['seofilter_grid_actions'] = 'Action';

$_lang['seofilter_combo_select'] = 'Select from the list';
$_lang['seofilter_page'] = 'Page';

$_lang['seofilter_rule_id'] = 'Id';
$_lang['seofilter_rule_active'] = 'Activated';
$_lang['seofilter_rule_page'] = 'Page';
$_lang['seofilter_rule_name'] = 'Name';
$_lang['seofilter_rule_url'] = 'URL mask';
$_lang['seofilter_rule_url_more'] = 'URL address mask (can be changed in the Table URL tab)';
$_lang['seofilter_rule_count'] = 'Count';
$_lang['seofilter_rule_fields'] = 'Fields';
$_lang['seofilter_rule_tablefields'] = 'Select fields';
$_lang['seofilter_rule_title'] = 'Title tag';
$_lang['seofilter_rule_base'] = 'Base rule';
$_lang['seofilter_rule_rank'] = 'Priority';
$_lang['seofilter_rule_base_more'] = 'Base rule (The meta tags remain in other parameters)';
$_lang['seofilter_rule_editedon'] = 'Edited';
$_lang['seofilter_rule_create'] = 'Add rule';
$_lang['seofilter_rule_update'] = 'Change rule';
$_lang['seofilter_rule_enable'] = 'Activate rule';
$_lang['seofilter_rules_enable'] = 'Activate rules';
$_lang['seofilter_rule_disable'] = 'Deactivate rule';
$_lang['seofilter_rules_disable'] = 'Deactivate rules';
$_lang['seofilter_rule_remove'] = 'Delete rule';
$_lang['seofilter_rules_remove'] = 'Delete rules';
$_lang['seofilter_rule_remove_confirm'] = 'Are you sure that you want to delete this rule?';
$_lang['seofilter_rules_remove_confirm'] = 'Are you sure that you want to delete these rules?';
$_lang['seofilter_rule_link_tpl'] = 'Template for reference name (syntax like in SEO)';
$_lang['seofilter_rule_duplicate'] = 'Make a copy';
$_lang['seofilter_rule_copy'] = 'Copy rule';
$_lang['seofilter_copy'] = 'Copy';
$_lang['seofilter_rule_copy_fields'] = 'Copy added fields';
$_lang['seofilter_rule_relinks'] = 'Regenerate links name';

$_lang['seofilter_rule_count_parents'] = 'Parents (for counting)';
$_lang['seofilter_rule_count_where'] = 'Additional condition to count resources (in JSON format)';

$_lang['seofilter_rule_err_name'] = 'You need to specify a rule name.';
$_lang['seofilter_rule_err_ae'] = 'A rule with the same name for the selected page already exists.';
$_lang['seofilter_rule_err_nf'] = 'Rule not found.';
$_lang['seofilter_rule_err_ns'] = 'Rule not specified.';
$_lang['seofilter_rule_err_remove'] = 'Error deleting rule.';
$_lang['seofilter_rule_err_save'] = 'Error saving rule.';

$_lang['seofilter_seo'] = 'SEO';
$_lang['seofilter_seo_after_save'] = '<p>You can enter information after you save</p>';


$_lang['seofilter_seometa_id'] = 'Id';
$_lang['seofilter_seometa_name'] = 'Name';
$_lang['seofilter_seometa_title'] = 'Pagetitle';
$_lang['seofilter_seometa_h1'] = 'Title H1';
$_lang['seofilter_seometa_h2'] = 'Title H2';
$_lang['seofilter_seometa_active'] = 'Activated';
$_lang['seofilter_seometa_description'] = 'Description';
$_lang['seofilter_seometa_introtext'] = 'Introtext';
$_lang['seofilter_seometa_keywords'] = 'Лeywords';
$_lang['seofilter_seometa_text'] = 'Text field';
$_lang['seofilter_seometa_content'] = 'Content';
$_lang['seofilter_seometa_create'] = 'Add values';
$_lang['seofilter_seometa_update'] = 'Change SEO fields';
$_lang['seofilter_seometa_enable'] = 'Activate SEO';
$_lang['seofilter_seometa_disable'] = 'Deactivate SEO';
$_lang['seofilter_seometa_remove'] = 'Delete SEO fields';
$_lang['seofilter_seometa_remove_confirm'] = 'Are you sure? It clears the SEO values in the field or at the intersection of the fields.';


$_lang['seofilter_dictionary_create'] = 'Add word';
$_lang['seofilter_dictionary_update'] = 'Edit word';
$_lang['seofilter_dictionary_disable'] = 'Deactivate word';
$_lang['seofilter_dictionary_remove'] = 'Delete word';
$_lang['seofilter_dictionaries_remove'] = 'Delete words';
$_lang['seofilter_dictionary_remove_confirm'] = 'Deleting a word will affect the value substitution';
$_lang['seofilter_dictionaries_remove_confirm'] = 'Deleting words will affect the value substitution';
$_lang['seofilter_dictionary_id'] = 'Id';
$_lang['seofilter_dictionary_fieldtitle'] = 'Field name';
$_lang['seofilter_dictionary_field_id'] = 'Field';
$_lang['seofilter_dictionary_active'] = 'Active';
$_lang['seofilter_dictionary_input'] = 'Request';
$_lang['seofilter_dictionary_value'] = 'Value';
$_lang['seofilter_dictionary_alias'] = 'Synonym';
$_lang['seofilter_dictionary_class'] = 'Class';
$_lang['seofilter_dictionary_key'] = 'Key';
$_lang['seofilter_dictionary_menu_on'] = 'Show on Menu';
$_lang['seofilter_dictionary_menutitle'] = 'Menutitle';
$_lang['seofilter_dictionary_menuindex'] = 'Menuindex';
$_lang['seofilter_dictionary_link_attributes'] = 'Link attributes';
$_lang['seofilter_dictionary_value_i'] = 'Nominative case';
$_lang['seofilter_dictionary_value_r'] = 'Genetive case';
$_lang['seofilter_dictionary_value_d'] = 'Dative case';
$_lang['seofilter_dictionary_value_v'] = 'Accusative case';
$_lang['seofilter_dictionary_value_t'] = 'Instrumental case';
$_lang['seofilter_dictionary_value_p'] = 'Prepositional case';
$_lang['seofilter_dictionary_value_o'] = 'About what / About who';
$_lang['seofilter_dictionary_m_value_i'] = 'Nominative case Plural';
$_lang['seofilter_dictionary_m_value_r'] = 'Genetive case Plural';
$_lang['seofilter_dictionary_m_value_d'] = 'Dative case Plural';
$_lang['seofilter_dictionary_m_value_v'] = 'Accusative case Plural';
$_lang['seofilter_dictionary_m_value_t'] = 'Instrumental case Plural';
$_lang['seofilter_dictionary_m_value_p'] = 'Prepositional case Plural';
$_lang['seofilter_dictionary_m_value_o'] = 'About who, about what in plural';
$_lang['seofilter_dictionary_value_to'] = 'Where to';
$_lang['seofilter_dictionary_value_in'] = 'Where is';
$_lang['seofilter_dictionary_value_from'] = 'From where';
$_lang['seofilter_dictionary_editedon'] = 'Edited';

$_lang['seofilter_dictionary_decline'] = 'Case inclinations';
$_lang['seofilter_dictionary_decline_desc'] = 'Case inclinations for Russian, Ukrainian and Kazakh';
$_lang['seofilter_dictionary_decline_desc_save'] = 'Case inclinations will be available after you save';
$_lang['seofilter_dictionary_err_input'] = 'Error, input word for dictionary is not entered';
$_lang['seofilter_dictionary_err_ae'] = 'Error, there is already a word for this field';

$_lang['seofilter_fieldids'] = 'Fields';
$_lang['seofilter_fieldids_id'] = 'Id';
$_lang['seofilter_fieldids_field_id'] = 'Field';
$_lang['seofilter_fieldids_alias'] = 'Alias';
$_lang['seofilter_fieldids_multi_id'] = 'Rule';
$_lang['seofilter_fieldids_priority'] = 'Priority (You can change the tables by dragging fields)';
$_lang['seofilter_fieldids_where'] = 'Condition (To create specific pages)';
$_lang['seofilter_fieldids_compare'] = 'Compare operation';
$_lang['seofilter_fieldids_value'] = 'Value (in the dictionary - several requests separeted by commos)';
$_lang['seofilter_fieldids_condition'] = 'Where';

$_lang['seofilter_fieldids_update'] = 'Change conditions';
$_lang['seofilter_fieldids_remove'] = 'Exclude field';
$_lang['seofilter_fieldids_remove_confirm'] = 'Are you sure that you want to exclude this field?';

$_lang['seofilter_url_view'] = 'Go to page';
$_lang['seofilter_url_id'] = 'Id';
$_lang['seofilter_url_name'] = 'Name';
$_lang['seofilter_url_link'] = 'Reference name';
$_lang['seofilter_url_multi_id'] = 'Rule';
$_lang['seofilter_url_page_id'] = 'Page';
$_lang['seofilter_url_old_url'] = 'Base Url';
$_lang['seofilter_url_new_url'] = 'Individual Url';
$_lang['seofilter_url_editedon'] = 'Edited';
$_lang['seofilter_url_createdon'] = 'Cteated';
$_lang['seofilter_url_count'] = 'Crossings';
$_lang['seofilter_url_ajax'] = 'The filter crossings';
$_lang['seofilter_url_active'] = 'Active';
$_lang['seofilter_url_custom'] = 'Meta-tags';
$_lang['seofilter_url_active_more'] = 'Active (If not, it will give an error of 404)';
$_lang['seofilter_url_err_url'] = 'URL for link is not specified!';
$_lang['seofilter_url_err_ae'] = 'A link with this address already exists!';
$_lang['seofilter_url_menu'] = 'Menu';
$_lang['seofilter_url_menu_on'] = 'Show on menu';
$_lang['seofilter_url_menutitle'] = 'Menu item (default Name)';
$_lang['seofilter_url_link_attributes'] = 'Reference attributes';
$_lang['seofilter_url_menuindex'] = 'Position on menu';
$_lang['seofilter_url_image'] = 'Text field, an image for example';


$_lang['seofilter_menu_enable'] = 'Show in menu';
$_lang['seofilter_menu_disable'] = 'Remove from menu';
$_lang['seofilter_url_create'] = 'Add URL';
$_lang['seofilter_url_update'] = 'Change URL';
$_lang['seofilter_url_enable'] = 'Activate URL';
$_lang['seofilter_url_disable'] = 'Deactivate URL (it will be 404 error)';
$_lang['seofilter_url_remove'] = 'Delete URL';
$_lang['seofilter_urls_remove'] = 'Delete URLs';
$_lang['seofilter_urls_remove_confirm'] = 'Are you sure that you want to delete these addresses? Pages will not be available';
$_lang['seofilter_url_remove_confirm'] = 'Are you sure that you want to delete this address? Page will not be available';
$_lang['seofilter_multiseo_intro'] = 'Syntax for writing dynamic values to patterns: {$alias}, {$alias_r}, {$m_alias} etc, where "alias" is the alias of the field, added on the first tab. Cases are added with the "_d", "_v" etc. Plural with a prefix "{$m_".';

$_lang['seofilter_url_urlword'] = 'Table of available words';
$_lang['seofilter_urlword_word_edit'] = 'Change the word access all addresses associated with it!';
$_lang['seofilter_urlword_word_id'] = 'Id';
$_lang['seofilter_urlword_field_id'] = 'Field ID';
$_lang['seofilter_urlword_field_name'] = 'Field';
$_lang['seofilter_urlword_field_alias'] = 'Alias in meta-tags';
$_lang['seofilter_urlword_word_name'] = 'Word';
$_lang['seofilter_urlword_word_alias'] = 'Alias in url';
$_lang['seofilter_urlword_priority'] = 'Priority';
$_lang['seofilter_url_word_update'] = 'Change word';


$_lang['seofilter_field_xpdo'] = 'Value in another table';
$_lang['seofilter_field_xpdo_package'] = 'Component, for example customextra';
$_lang['seofilter_field_xpdo_class'] = 'Class, for example customExtraItem';
$_lang['seofilter_field_xpdo_id'] = 'Field to match (id)';
$_lang['seofilter_field_xpdo_name'] = 'The field where the value is stored (name)';
$_lang['seofilter_field_xpdo_where'] = 'Additional condition (where)';

$_lang['sf_err_ajax_nf'] = 'Error. Action not found.';

$_lang['seofilter_fieldids_after_save'] = 'After you save, you can select fields, set conditions, and change the priority of the mouse';

$_lang['seofilter_filter_key'] = 'Select key';
$_lang['seofilter_filter_class'] = 'Select class';
$_lang['seofilter_filter_field'] = 'Select field';
$_lang['seofilter_filter_class_or'] = 'or select class';
$_lang['seofilter_filter_rule'] = 'Select rule';
$_lang['seofilter_filter_resource'] = 'Select page';
$_lang['seofilter_filter_resource_or'] = 'or select page';
$_lang['seofilter_clear_counters'] = 'Reset counters';
$_lang['seofilter_clear_counters_confirm'] = 'Are you sure that you want to reset all counters?';

$_lang['seofilter_url_seoedit'] = 'Change SEO';
$_lang['seofilter_url_seoedit_intro'] = 'Here you can individually change the meta tags, the URL of the page and clear the counters.';

$_lang['seofilter_seo_custom'] = 'Use individual meta-tags';

$_lang['seofilter_compare_in'] = 'Contains';
$_lang['seofilter_compare_notin'] = 'Does not contain';
$_lang['seofilter_compare_larger'] = 'Larger';
$_lang['seofilter_compare_less'] = 'Less';
$_lang['seofilter_compare_range'] = 'In the range (by commos)';

$_lang['sf_err_value_duplicate'] = 'Duplicate propert!';
$_lang['seofilter_rule_properties'] = 'Properties';
$_lang['seofilter_rule_properties_intro'] = 'Properties whose array will be in the JSON format in the placeholder [[!+sf.properties]]. <br> Here you can use all the same parameters as in the SEO tab';
