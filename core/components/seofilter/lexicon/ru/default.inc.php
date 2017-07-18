<?php
include_once 'setting.inc.php';

$_lang['seofilter'] = 'SeoFilter';
$_lang['seofilter_menu_desc'] = 'Управление ЧПУ и SEO для mFilter2';
$_lang['seofilter_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';

$_lang['seofilter_page'] = 'стр.';
$_lang['seofilter_from'] = 'из';

$_lang['seofilter_field'] = 'Поле';
$_lang['seofilter_fields'] = 'Поля фильтра';
$_lang['seofilter_fields_intro'] = 'На этой странице вы создаёте и редактируете базовые поля для формирования ЧПУ в фильтре После сохранения в режиме редактирования можно будет ввести информацию по полям для SEO. ';

$_lang['seofilter_rule'] = 'Правило SEO';
$_lang['seofilter_rules'] = 'Правила SEO';
$_lang['seofilter_rules_intro'] = 'Здесь вы можете настроить правила для формирования адресов для двух и более полей. Также для каждого правила можно задать шаблоны SEO. В будущем можно будет задавать конкретные значения для двух полей одного ключа, чтобы получить одну страницу. Пример: Страницы по цветам светло-серый и тёмно-серый можно будет объединить просто в серый';

$_lang['seofilter_seometa'] = 'SEO';
$_lang['seofilter_seometas'] = 'SEO шаблоны';
$_lang['seofilter_seometas_intro'] = 'Для большего удобства в управлении все прописанные SEO и Meta поля собраны в одну таблицу';

$_lang['seofilter_dictionary'] = 'Словарь';
$_lang['seofilter_dictionary_intro'] = 'После добавления поля здесь появляются запросы и их склонения, если включено, которые можно откорректировать в ручную';

$_lang['seofilter_urls'] = 'Таблица URL';
$_lang['seofilter_urls_intro'] = 'После добавления правил здесь автоматически появляются адреса и статистика по переходам. Возможно в будущем каждой страницы можно будет индивидуально задать SEO и meta поля';

$_lang['seofilter_field_id'] = 'Id';
$_lang['seofilter_field_name'] = 'Название';
$_lang['seofilter_field_description'] = 'Описание';
$_lang['seofilter_field_active'] = 'Активно';
$_lang['seofilter_field_page'] = 'Страница';
$_lang['seofilter_field_pages'] = 'Страницы';
$_lang['seofilter_field_pages_more'] = 'ID страниц (несколько через запятую)';
$_lang['seofilter_field_class'] = 'Класс';
$_lang['seofilter_field_class_more'] = 'Выберите класс (или впишите свой)';
$_lang['seofilter_field_key'] = 'Ключ';
$_lang['seofilter_field_alias'] = 'Синоним';
$_lang['seofilter_field_dont'] = 'Не обрабатывать';
$_lang['seofilter_field_translit'] = 'Транслитерация';
$_lang['seofilter_field_baseparam'] = 'Базовый параметр';
$_lang['seofilter_field_baseparam_more'] = 'Базовый параметр (оставляет свои мета-тексты)';
$_lang['seofilter_field_urltpl'] = 'Шаблон url';
$_lang['seofilter_field_priority'] = 'Приоритет';
$_lang['seofilter_field_method'] = 'Как обрабатывать значения';
$_lang['seofilter_field_exact'] = 'Точное вхожение (строгий поиск)';
$_lang['seofilter_field_valuefirst'] = 'Значение перед параметром';
$_lang['seofilter_field_hideparam'] = 'Скрывать параметр в url';

$_lang['seofilter_msProductData'] = 'msProductData (поля товара)';
$_lang['seofilter_modResource'] = 'modResource (поля ресурса)';
$_lang['seofilter_msVendor'] = 'msVendor (поля производителя)';
$_lang['seofilter_msProductOption'] = 'msProductOption (опции minishop2)';
$_lang['seofilter_modTemplateVar'] = 'modTemplateVar (TV - поля)';

$_lang['seofilter_field_create'] = 'Добавить поле';
$_lang['seofilter_field_update'] = 'Изменить поле';
$_lang['seofilter_field_enable'] = 'Включить поле';
$_lang['seofilter_fields_enable'] = 'Включить поля';
$_lang['seofilter_field_disable'] = 'Отключить поле';
$_lang['seofilter_fields_disable'] = 'Отключить поля';
$_lang['seofilter_field_remove'] = 'Удалить поле';
$_lang['seofilter_fields_remove'] = 'Удалить поля';
$_lang['seofilter_field_remove_confirm'] = 'Вы уверены, что хотите удалить это поле?';
$_lang['seofilter_fields_remove_confirm'] = 'Вы уверены, что хотите удалить эти поля?';
$_lang['seofilter_field_active'] = 'Включено';

$_lang['seofilter_field_err_name'] = 'Вы должны указать имя поля.';
$_lang['seofilter_field_err_ae'] = 'Поле с таким именем уже существует.';
$_lang['seofilter_field_err_nf'] = 'Поле не найдено.';
$_lang['seofilter_field_err_ns'] = 'Поле не указано.';
$_lang['seofilter_field_err_remove'] = 'Ошибка при удалении поля.';
$_lang['seofilter_field_err_save'] = 'Ошибка при сохранении поля.';

$_lang['seofilter_grid_search'] = 'Поиск';
$_lang['seofilter_grid_actions'] = 'Действия';

$_lang['seofilter_combo_select'] = 'Выберите из списка';

$_lang['seofilter_rule_id'] = 'Id';
$_lang['seofilter_rule_active'] = 'Активно';
$_lang['seofilter_rule_page'] = 'Страница';
$_lang['seofilter_rule_name'] = 'Название';
$_lang['seofilter_rule_url'] = 'URL маска';
$_lang['seofilter_rule_url_more'] = 'Маска URL адреса (можно изменять во вкладке Таблица URL)';
$_lang['seofilter_rule_count'] = 'Кол-во';
$_lang['seofilter_rule_fields'] = 'Поля';
$_lang['seofilter_rule_tablefields'] = 'Выберите поля';
$_lang['seofilter_rule_title'] = 'Тег title';
$_lang['seofilter_rule_base'] = 'Базовое правило';
$_lang['seofilter_rule_base_more'] = 'Базовое правило (оставляет свои мета-теги при других параметрах)';

$_lang['seofilter_rule_create'] = 'Добавить правило';
$_lang['seofilter_rule_update'] = 'Изменить правило';
$_lang['seofilter_rule_enable'] = 'Включить правило';
$_lang['seofilter_rules_enable'] = 'Включить правила';
$_lang['seofilter_rule_disable'] = 'Отключить правило';
$_lang['seofilter_rules_disable'] = 'Отключить правила';
$_lang['seofilter_rule_remove'] = 'Удалить правило';
$_lang['seofilter_rules_remove'] = 'Удалить правила';
$_lang['seofilter_rule_remove_confirm'] = 'Вы уверены, что хотите удалить это правило?';
$_lang['seofilter_rules_remove_confirm'] = 'Вы уверены, что хотите удалить эти правила?';
$_lang['seofilter_rule_active'] = 'Включено';

$_lang['seofilter_rule_count_parents'] = 'Parents (для подсчёта)';
$_lang['seofilter_rule_count_where'] = 'Доп. условие для подсчёта ресурсов (в JSON формате)';

$_lang['seofilter_rule_err_name'] = 'Вы должны указать имя правила.';
$_lang['seofilter_rule_err_ae'] = 'Правило с таким именем уже существует.';
$_lang['seofilter_rule_err_nf'] = 'Правило не найдено.';
$_lang['seofilter_rule_err_ns'] = 'правило не указано.';
$_lang['seofilter_rule_err_remove'] = 'Ошибка при удалении правила.';
$_lang['seofilter_rule_err_save'] = 'Ошибка при сохранении правила.';

$_lang['seofilter_seo'] = 'SEO';
$_lang['seofilter_seo_after_save'] = '<p>Ввести информацию можно будет после сохранения</p>';


$_lang['seofilter_seometa_id'] = 'Id';
$_lang['seofilter_seometa_name'] = 'Название';
$_lang['seofilter_seometa_title'] = 'Заголовок страницы';
$_lang['seofilter_seometa_h1'] = 'Заголовок H1';
$_lang['seofilter_seometa_h2'] = 'Заголовок H2';
$_lang['seofilter_seometa_active'] = 'Активно';
$_lang['seofilter_seometa_description'] = 'Описание';
$_lang['seofilter_seometa_introtext'] = 'Вводный текст';
$_lang['seofilter_seometa_text'] = 'Текстовое поле';
$_lang['seofilter_seometa_content'] = 'Контент';
$_lang['seofilter_seometa_create'] = 'Добавить значения';
$_lang['seofilter_seometa_update'] = 'Изменить SEO поля';
$_lang['seofilter_seometa_enable'] = 'Включить SEO';
$_lang['seofilter_seometa_disable'] = 'Отключить SEO';
$_lang['seofilter_seometa_remove'] = 'Удалить SEO поля';
$_lang['seofilter_seometa_remove_confirm'] = 'Вы уверены? Это очистит SEO значения в Поле или в Пересечении полей.';


$_lang['seofilter_dictionary_create'] = 'Добавить запись';
$_lang['seofilter_dictionary_update'] = 'Отредактировать запись';
$_lang['seofilter_dictionary_disable'] = 'Отключить запись';
$_lang['seofilter_dictionary_remove'] = 'Удалить';
$_lang['seofilter_dictionary_remove_confirm'] = 'Удаление записей повлияет на подстановку значений';
$_lang['seofilter_dictionary_id'] = 'Id';
$_lang['seofilter_dictionary_fieldtitle'] = 'Название поля';
$_lang['seofilter_dictionary_field_id'] = 'Поле';
$_lang['seofilter_dictionary_active'] = 'Активно';
$_lang['seofilter_dictionary_input'] = 'Запрос';
$_lang['seofilter_dictionary_value'] = 'Значение';
$_lang['seofilter_dictionary_alias'] = 'Синоним';
$_lang['seofilter_dictionary_class'] = 'Класс';
$_lang['seofilter_dictionary_key'] = 'Ключ';
$_lang['seofilter_dictionary_menu_on'] = 'Показывать в меню';
$_lang['seofilter_dictionary_menutitle'] = 'Пункт меню';
$_lang['seofilter_dictionary_menuindex'] = 'Позиция в меню';
$_lang['seofilter_dictionary_link_attributes'] = 'Атрибуты ссылки';
$_lang['seofilter_dictionary_value_i'] = 'В именительном падеже';
$_lang['seofilter_dictionary_value_r'] = 'В родительном падеже';
$_lang['seofilter_dictionary_value_d'] = 'В дательном падеже';
$_lang['seofilter_dictionary_value_v'] = 'В винительном падеже';
$_lang['seofilter_dictionary_value_t'] = 'В творительном падеже';
$_lang['seofilter_dictionary_value_p'] = 'В предложном падеже';
$_lang['seofilter_dictionary_value_o'] = 'О чём / о ком';
$_lang['seofilter_dictionary_values_i'] = 'ИП множественное число';
$_lang['seofilter_dictionary_values_r'] = 'РП множественное число';
$_lang['seofilter_dictionary_values_d'] = 'ДП множественное число';
$_lang['seofilter_dictionary_values_v'] = 'ВП множественное число';
$_lang['seofilter_dictionary_values_t'] = 'ТП множественное число';
$_lang['seofilter_dictionary_values_p'] = 'ПП множественное число';
$_lang['seofilter_dictionary_values_o'] = 'О ком, о чём во множ. числе';
$_lang['seofilter_dictionary_value_to'] = 'Куда';
$_lang['seofilter_dictionary_value_in'] = 'Где';
$_lang['seofilter_dictionary_value_from'] = 'Откуда';

$_lang['seofilter_dictionary_decline'] = 'Склонения по падежам';
$_lang['seofilter_dictionary_decline_desc'] = 'Склонения по падежам для русского, украинского и казахского языка';
$_lang['seofilter_dictionary_err_input'] = 'Ошибка, не введёно вводное слово для словаря';
$_lang['seofilter_dictionary_err_ae'] = 'Ошибка, уже есть такое слово для этого поля';

$_lang['seofilter_fieldids'] = 'Поля';
$_lang['seofilter_fieldids_id'] = 'Id';
$_lang['seofilter_fieldids_field_id'] = 'Поле';
$_lang['seofilter_fieldids_multi_id'] = 'Правило';
$_lang['seofilter_fieldids_priority'] = 'Порядок (можно изменять в таблицы перетаскиванием полей)';
$_lang['seofilter_fieldids_where'] = 'Условие (для создания конкретных страниц)';
$_lang['seofilter_fieldids_compare'] = 'Сравнение';
$_lang['seofilter_fieldids_value'] = 'Значение';
$_lang['seofilter_fieldids_condition'] = 'Where';

$_lang['seofilter_fieldids_update'] = 'Изменить условия';
$_lang['seofilter_fieldids_remove'] = 'Исключить поле';
$_lang['seofilter_fieldids_remove_confirm'] = 'Вы уверены, что хотите исключить это поле?';

$_lang['seofilter_url_view'] = 'Перейти к странице';
$_lang['seofilter_url_id'] = 'Id';
$_lang['seofilter_url_multi_id'] = 'Правило';
$_lang['seofilter_url_page_id'] = 'Страница';
$_lang['seofilter_url_old_url'] = 'Базовый Url';
$_lang['seofilter_url_new_url'] = 'Индивидуальный Url';
$_lang['seofilter_url_editedon'] = 'Отредактировано';
$_lang['seofilter_url_createdon'] = 'Создано';
$_lang['seofilter_url_count'] = 'Переходов';
$_lang['seofilter_url_ajax'] = 'Из них переходов через фильтр';
$_lang['seofilter_url_active'] = 'Активно';
$_lang['seofilter_url_active_more'] = 'Активно (если нет, то будет отдавать 404)';
$_lang['seofilter_url_err_url'] = 'Не задан URL адрес для ссылки!';
$_lang['seofilter_url_err_ae'] = 'Ссылка с таким адресом уже существует!';


$_lang['seofilter_url_create'] = 'Добавить URL';
$_lang['seofilter_url_update'] = 'Изменить URL';
$_lang['seofilter_url_disable'] = 'Отключить URL (будет 404)';
$_lang['seofilter_url_remove'] = 'Удалить URL';
$_lang['seofilter_urls_remove'] = 'Удалить URL';
$_lang['seofilter_urls_remove_confirm'] = 'Вы уверены, что хотите удалить эти адреса? Страницы будут не доступны';
$_lang['seofilter_url_remove_confirm'] = 'Вы уверены, что хотите удалить этот адрес? Страница будет не доступна';
$_lang['seofilter_multiseo_intro'] = 'Синтаксис для двух и более полей: {$alias1}, {$alias2}, где "aliasX" - это синоним поля, прописанный в полях фильтра. На первой вкладке в поле URL адреса как-раз они прописаны. Падежи добавляются так: {$alias1_r} и т.д. За обработку отвечает Fenom.';

$_lang['seofilter_field_xpdo'] = 'Значение в другой таблице';
$_lang['seofilter_field_xpdo_package'] = 'Компонент, например customextra';
$_lang['seofilter_field_xpdo_class'] = 'Класс, например customExtraItem';
$_lang['seofilter_field_xpdo_id'] = 'Поле для сопоставления (id)';
$_lang['seofilter_field_xpdo_name'] = 'Поле, где хранится значение (name)';
$_lang['seofilter_field_xpdo_where'] = 'Дополнительное условие (where)';

$_lang['sf_err_ajax_nf'] = 'Ошибка. Не найден action.';

$_lang['seofilter_fieldids_after_save'] = 'После сохранения можно будет выбрать поля, задать условия и изменять приоритет удерживая поле мышью';

$_lang['seofilter_filter_key'] = 'Выберите ключ';
$_lang['seofilter_filter_class'] = 'Выберите класс';
$_lang['seofilter_filter_field'] = 'Выберите поле';
$_lang['seofilter_filter_class_or'] = 'или выберите класс';
$_lang['seofilter_filter_rule'] = 'Выберите правило';
$_lang['seofilter_filter_resource_or'] = 'или выберите страницу';
$_lang['seofilter_clear_counters'] = 'Сбросить счётчики';
$_lang['seofilter_clear_counters_confirm'] = 'Вы уверены что хотите сбросить все счётчики?';