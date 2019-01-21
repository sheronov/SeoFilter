<?php
include_once 'setting.inc.php';

$_lang['seofilter'] = 'SeoFilter';
$_lang['seofilter_menu_desc'] = 'Управление ЧПУ и SEO для фильтров';
$_lang['seofilter_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';



$_lang['seofilter_field'] = 'Поле';
$_lang['seofilter_fields'] = 'Поля фильтра';
$_lang['seofilter_fields_intro'] = 'На этой странице нужно добавить поля, которые вы хотите использовать для формирования SEO правил. Псевдоним поля должен соответсвовать псевдониму в фильтре(aliases). После добавления поля автоматически собираются значения в словарь на третьей вкладке';

$_lang['seofilter_rule'] = 'Правило';
$_lang['seofilter_rules'] = 'Правила SEO';
$_lang['seofilter_rules_intro'] = 'Здесь вы можете настроить правила для формирования адресов для одного и более полей. Также для каждого правила можно задать шаблоны SEO. При добавлении поля в правило можно задавать конкретные значения для ограничения правила, чтобы не пересекать все значения поля с другими значениями';

$_lang['seofilter_seometa'] = 'SEO шаблоны';
$_lang['seofilter_seometas'] = 'SEO шаблоны';
$_lang['seofilter_seometas_intro'] = 'Для большего удобства в управлении все прописанные SEO и Meta поля собраны в одну таблицу';

$_lang['seofilter_dictionary'] = 'Словарь';
$_lang['seofilter_dictionary_intro'] = 'После добавления поля здесь появляются запросы и их склонения (если включена соответствующая настройка в настройках). Слова можно откорректировать в ручную, при изменении псевдонима происходит автоматическая перегенерация страниц в таблице URL. Если необходимо, то можно в ручную добавить слова к полям';

$_lang['seofilter_urls'] = 'SEO-страницы';
$_lang['seofilter_urls_intro'] = 'Для каждой страницы можно задать уникальный url-адрес, SEO и meta-данные или же отключить. По каждой странице есть небольшая статистика переходов. SEO-страницы появляются здесь автоматически после добавления правил.';

$_lang['seofilter_field_id'] = 'Id';
$_lang['seofilter_field_name'] = 'Название';
$_lang['seofilter_field_description'] = 'Описание';
$_lang['seofilter_field_active'] = 'Активно (собирать значения)';
$_lang['seofilter_field_page'] = 'Страница';
$_lang['seofilter_field_pages'] = 'Страницы';
$_lang['seofilter_field_pages_more'] = 'ID страниц (несколько через запятую)';
$_lang['seofilter_field_class'] = 'Класс';
$_lang['seofilter_field_class_more'] = 'Выберите класс';
$_lang['seofilter_field_key'] = 'Ключ';
$_lang['seofilter_field_alias'] = 'Псевдоним (alias)';
$_lang['seofilter_field_dont'] = 'Не обрабатывать';
$_lang['seofilter_field_translit'] = 'Транслитерация';
$_lang['seofilter_field_baseparam'] = 'Базовый параметр';
$_lang['seofilter_field_baseparam_more'] = 'Базовый параметр (оставляет свои мета-тексты)';
$_lang['seofilter_field_urltpl'] = 'Шаблон url';
$_lang['seofilter_field_priority'] = 'Приоритет';
$_lang['seofilter_field_method'] = 'Как обрабатывать значения';
$_lang['seofilter_field_exact'] = 'Точное вхождение (строгий поиск)';
$_lang['seofilter_field_valuefirst'] = 'Значение перед псевдонимом';
$_lang['seofilter_field_hideparam'] = 'Скрывать псевдоним в url';
$_lang['seofilter_field_slider'] = 'Поле типа "Слайдер" (number фильтр)';

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

$_lang['seofilter_field_err_name'] = 'Вы должны указать имя поля.';
$_lang['seofilter_field_err_ae'] = 'Поле с таким именем уже существует.';
$_lang['seofilter_field_err_nf'] = 'Поле не найдено.';
$_lang['seofilter_field_err_ns'] = 'Поле не указано.';
$_lang['seofilter_field_err_remove'] = 'Ошибка при удалении поля.';
$_lang['seofilter_field_err_save'] = 'Ошибка при сохранении поля.';

$_lang['seofilter_grid_search'] = 'Поиск';
$_lang['seofilter_grid_actions'] = 'Действия';

$_lang['seofilter_combo_select'] = 'Выберите из списка';
$_lang['seofilter_page'] = 'Страница';

$_lang['seofilter_rule_id'] = 'Id';
$_lang['seofilter_rule_active'] = 'Активно';
$_lang['seofilter_rule_page'] = 'Страница';
$_lang['seofilter_rule_name'] = 'Название';
$_lang['seofilter_rule_url'] = 'URL маска';
$_lang['seofilter_rule_url_more'] = 'Маска URL адреса (генерируется автоматически при добавлении полей)';
$_lang['seofilter_rule_count'] = 'Кол-во';
$_lang['seofilter_rule_fields'] = 'Поля';
$_lang['seofilter_rule_tablefields'] = 'Выберите поля';
$_lang['seofilter_rule_title'] = 'Тег title';
$_lang['seofilter_rule_base'] = 'Базовое правило';
$_lang['seofilter_rule_rank'] = 'Приоритет';
$_lang['seofilter_rule_base_more'] = 'Базовое правило (остаются мета-теги при других параметрах)';
$_lang['seofilter_rule_editedon'] = 'Отредактировано';
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
$_lang['seofilter_rule_link_tpl'] = 'Шаблон для названия страниц в меню и в хлебных крошках (синтаксис как в SEO)';
$_lang['seofilter_rule_duplicate'] = 'Сделать копию';
$_lang['seofilter_rule_copy'] = 'Копирование правила';
$_lang['seofilter_copy'] = 'Копия';
$_lang['seofilter_rule_copy_fields'] = 'Копировать добавленные поля';
$_lang['seofilter_rule_relinks'] = 'Перегенерировать названия ссылок';

$_lang['seofilter_rule_count_parents'] = 'Parents (для подсчёта)';
$_lang['seofilter_rule_count_where'] = 'Доп. условие для подсчёта ресурсов (в JSON формате)';

$_lang['seofilter_rule_err_name'] = 'Вы должны указать имя правила.';
$_lang['seofilter_rule_err_ae'] = 'Правило с таким именем для выбранной страницы уже существует.';
$_lang['seofilter_rule_err_nf'] = 'Правило не найдено.';
$_lang['seofilter_rule_err_ns'] = 'правило не указано.';
$_lang['seofilter_rule_err_remove'] = 'Ошибка при удалении правила.';
$_lang['seofilter_rule_err_save'] = 'Ошибка при сохранении правила.';

$_lang['seofilter_seo'] = 'SEO шаблоны';
$_lang['seofilter_seo_after_save'] = '<p>Ввести информацию можно будет после сохранения</p>';


$_lang['seofilter_seometa_id'] = 'Id';
$_lang['seofilter_seometa_name'] = 'Название';
$_lang['seofilter_seometa_title'] = 'Заголовок страницы';
$_lang['seofilter_seometa_h1'] = 'Заголовок H1';
$_lang['seofilter_seometa_h2'] = 'Заголовок H2';
$_lang['seofilter_seometa_active'] = 'Активно';
$_lang['seofilter_seometa_description'] = 'Описание';
$_lang['seofilter_seometa_introtext'] = 'Вводный текст';
$_lang['seofilter_seometa_keywords'] = 'Ключевые слова';
$_lang['seofilter_seometa_text'] = 'Дополнительное текстовое поле';
$_lang['seofilter_seometa_content'] = 'Содержимое (Контент)';
$_lang['seofilter_seometa_create'] = 'Добавить значения';
$_lang['seofilter_seometa_update'] = 'Изменить SEO поля';
$_lang['seofilter_seometa_enable'] = 'Включить SEO';
$_lang['seofilter_seometa_disable'] = 'Отключить SEO';
$_lang['seofilter_seometa_remove'] = 'Удалить SEO поля';
$_lang['seofilter_seometa_remove_confirm'] = 'Вы уверены? Это очистит SEO значения в Поле или в Пересечении полей.';


$_lang['seofilter_dictionary_create'] = 'Добавить запись';
$_lang['seofilter_dictionary_update'] = 'Отредактировать запись';
$_lang['seofilter_dictionary_disable'] = 'Отключить запись';
$_lang['seofilter_dictionary_remove'] = 'Удалить запись';
$_lang['seofilter_dictionaries_remove'] = 'Удалить записи';
$_lang['seofilter_dictionary_remove_confirm'] = 'Удаление записи повлияет на подстановку значений';
$_lang['seofilter_dictionaries_remove_confirm'] = 'Удаление записей повлияет на подстановку значений';
$_lang['seofilter_dictionary_id'] = 'Id';
$_lang['seofilter_dictionary_fieldtitle'] = 'Название поля';
$_lang['seofilter_dictionary_field_id'] = 'Поле';
$_lang['seofilter_dictionary_active'] = 'Создавать ссылки';
$_lang['seofilter_dictionary_input'] = 'Запрос';
$_lang['seofilter_dictionary_value'] = 'Значение';
$_lang['seofilter_dictionary_alias'] = 'Псевдоним (alias)';
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
$_lang['seofilter_dictionary_m_value_i'] = 'ИП множественное число';
$_lang['seofilter_dictionary_m_value_r'] = 'РП множественное число';
$_lang['seofilter_dictionary_m_value_d'] = 'ДП множественное число';
$_lang['seofilter_dictionary_m_value_v'] = 'ВП множественное число';
$_lang['seofilter_dictionary_m_value_t'] = 'ТП множественное число';
$_lang['seofilter_dictionary_m_value_p'] = 'ПП множественное число';
$_lang['seofilter_dictionary_m_value_o'] = 'О ком, о чём во множ. числе';
$_lang['seofilter_dictionary_value_to'] = 'Куда';
$_lang['seofilter_dictionary_value_in'] = 'Где';
$_lang['seofilter_dictionary_value_from'] = 'Откуда';
$_lang['seofilter_dictionary_editedon'] = 'Отредактировано';

$_lang['seofilter_dictionary_decline'] = 'Склонения по падежам';
$_lang['seofilter_dictionary_decline_desc'] = 'Склонения по падежам для русского, украинского и казахского языка';
$_lang['seofilter_dictionary_decline_desc_save'] = 'Склонения по падежам будут доступны после сохранения';
$_lang['seofilter_dictionary_err_input'] = 'Ошибка, не введёно вводное слово для словаря';
$_lang['seofilter_dictionary_err_ae'] = 'Ошибка, уже есть такое слово для этого поля';

$_lang['seofilter_fieldids'] = 'Поля';
$_lang['seofilter_fieldids_id'] = 'Id';
$_lang['seofilter_fieldids_field_id'] = 'Поле';
$_lang['seofilter_fieldids_alias'] = 'Псевдоним( alias)';
$_lang['seofilter_fieldids_multi_id'] = 'Правило';
$_lang['seofilter_fieldids_priority'] = 'Порядок (можно изменять в таблицы перетаскиванием полей)';
$_lang['seofilter_fieldids_where'] = 'Условие (для создания конкретных страниц)';
$_lang['seofilter_fieldids_compare'] = 'Операция сравнения';
$_lang['seofilter_fieldids_value'] = 'Значение (в словаре - запросы, несколько через запятую)';
$_lang['seofilter_fieldids_condition'] = 'Where';

$_lang['seofilter_fieldids_update'] = 'Изменить условия';
$_lang['seofilter_fieldids_remove'] = 'Исключить поле';
$_lang['seofilter_fieldids_remove_confirm'] = 'Вы уверены, что хотите исключить это поле?';

$_lang['seofilter_url_view'] = 'Перейти к странице';
$_lang['seofilter_url_id'] = 'Id';
$_lang['seofilter_url_name'] = 'Название';
$_lang['seofilter_url_link'] = 'Название страницы';
$_lang['seofilter_url_multi_id'] = 'Правило';
$_lang['seofilter_url_page_id'] = 'Страница';
$_lang['seofilter_url_old_url'] = 'Базовый URL-адрес';
$_lang['seofilter_url_new_url'] = 'Индивидуальный URL-адрес';
$_lang['seofilter_url_editedon'] = 'Отредактировано';
$_lang['seofilter_url_createdon'] = 'Создано';
$_lang['seofilter_url_count'] = 'Переходов';
$_lang['seofilter_url_ajax'] = 'Из них переходов через фильтр';
$_lang['seofilter_url_active'] = 'Активно';
$_lang['seofilter_url_custom'] = 'Мета-теги';
$_lang['seofilter_url_active_more'] = 'Активно (если нет, то будет ошибка 404)';
$_lang['seofilter_url_err_url'] = 'Не задан URL адрес для ссылки!';
$_lang['seofilter_url_err_page'] = 'SEO ссылка должна быть привязана к странице!';
$_lang['seofilter_url_err_ae'] = 'Ссылка с таким адресом уже существует!';
$_lang['seofilter_url_menu'] = 'Меню';
$_lang['seofilter_url_menu_on'] = 'Показывать в меню';
$_lang['seofilter_url_menutitle'] = 'Пункт меню (по умолчанию Название)';
$_lang['seofilter_url_link_attributes'] = 'Атрибуты ссылки';
$_lang['seofilter_url_menuindex'] = 'Позиция в меню';
$_lang['seofilter_url_image'] = 'Текстовое поле, изображение например';


$_lang['seofilter_menu_enable'] = 'Показывать в меню';
$_lang['seofilter_menu_disable'] = 'Убрать из меню';
$_lang['seofilter_url_create'] = 'Добавить URL';
$_lang['seofilter_url_update'] = 'Изменить URL';
$_lang['seofilter_url_enable'] = 'Включить URL';
$_lang['seofilter_url_disable'] = 'Отключить URL (будет 404 ошибка)';
$_lang['seofilter_url_remove'] = 'Удалить URL';
$_lang['seofilter_urls_remove'] = 'Удалить URL';
$_lang['seofilter_urls_remove_confirm'] = 'Вы уверены, что хотите удалить эти адреса? Страницы будут не доступны';
$_lang['seofilter_url_remove_confirm'] = 'Вы уверены, что хотите удалить этот адрес? Страница будет не доступна';
$_lang['seofilter_multiseo_intro'] = 'Синтаксис для прописывания динамических значений в шаблоны: {$alias}, {$alias_r}, {$m_alias} и т.д. где "alias" - это псевдоним поля, добавленный в первой вкладке. Падежи добавляются с припиской "_d", "_v" и т.д. Множественное число с приставкой "{$m_".';

$_lang['seofilter_url_urlword'] = 'Состав SEO-страницы';
$_lang['seofilter_urlword_word_edit'] = 'Изменение слова коснётся всех адресов, связанных с ним!';
$_lang['seofilter_urlword_word_id'] = 'Id';
$_lang['seofilter_urlword_field_id'] = 'ID поля';
$_lang['seofilter_urlword_field_name'] = 'Поле';
$_lang['seofilter_urlword_field_alias'] = 'Псевдоним в мета-тегах';
$_lang['seofilter_urlword_word_name'] = 'Слово';
$_lang['seofilter_urlword_word_alias'] = 'Псевдоним в url';
$_lang['seofilter_urlword_priority'] = 'Приоритет';
$_lang['seofilter_url_word_update'] = 'Изменить слово';


$_lang['seofilter_field_xpdo'] = 'Значение в другой таблице';
$_lang['seofilter_field_xpdo_package'] = 'Компонент, например customextra';
$_lang['seofilter_field_xpdo_class'] = 'Класс, например customExtraItem';
$_lang['seofilter_field_xpdo_id'] = 'Поле для сопоставления (id)';
$_lang['seofilter_field_xpdo_name'] = 'Поле, где хранится значение (name)';
$_lang['seofilter_field_xpdo_where'] = 'Дополнительное условие/ограничение для сбора значений в JSON формате';

$_lang['sf_err_ajax_nf'] = 'Ошибка. Не найден action.';

$_lang['seofilter_fieldids_after_save'] = 'После сохранения можно будет выбрать поля, задать условия и изменять приоритет удерживая поле мышью';

$_lang['seofilter_filter_key'] = 'Выберите ключ';
$_lang['seofilter_filter_class'] = 'Выберите класс';
$_lang['seofilter_filter_field'] = 'Выберите поле';
$_lang['seofilter_filter_class_or'] = 'или выберите класс';
$_lang['seofilter_filter_rule'] = 'Выберите правило';
$_lang['seofilter_filter_resource'] = 'Выберите страницу';
$_lang['seofilter_filter_resource_or'] = 'или выберите страницу';
$_lang['seofilter_clear_counters'] = 'Сбросить счётчики';
$_lang['seofilter_clear_counters_confirm'] = 'Вы уверены что хотите сбросить все счётчики?';

$_lang['seofilter_url_seoedit'] = 'Изменить SEO';
$_lang['seofilter_url_seoedit_intro'] = 'Здесь вы можете индивидуально изменять meta-теги, url-адрес страницы и очистить счётчики.';

$_lang['seofilter_seo_custom'] = 'Использовать индивидуальные мета-теги';

$_lang['seofilter_compare_in'] = 'Содержится в массиве (IN [x])';
$_lang['seofilter_compare_notin'] = 'Не содержится в массиве (NOT IN [x])';
$_lang['seofilter_compare_larger'] = 'Больше чем (> x)';
$_lang['seofilter_compare_less'] = "Меньше чем (< x)";
$_lang['seofilter_compare_range'] = "В диапазоне (два значения через запятую)";

$_lang['sf_err_value_duplicate'] = 'Дублрирование свойств!';
$_lang['seofilter_rule_properties'] = 'Множественные поля';
$_lang['seofilter_rule_properties_intro'] = 'Свойства, массив которых будет в JSON формате в плейсхолдере [[!+sf.properties]].<br> Здесь можете использовать все теже параметры, что и во вкладке SEO';
$_lang['seofilter_rule_properties_introtexts'] = 'Аналогичный массив, но тег [[!+sf.introtexts]].';
$_lang['seofilter_rule_tpl'] = 'Шаблон чанка для результатов на странице';
$_lang['seofilter_add_value'] = 'Добавить значение';
$_lang['seofilter_help_window'] = 'Помощь';
$_lang['seofilter_help_window_open'] = 'Открыть помощь';
$_lang['seofilter_dictionary_decl'] = 'Принудительно просклонять значение';
$_lang['seofilter_dictionary_decls'] = 'Принудительно просклонять значения';
$_lang['seofilter_dictionary_createdon'] = 'Добавлено';

$_lang['seofilter_field_relation'] = 'Зависимое поле';
$_lang['seofilter_field_relation_field'] = 'Зависит от';
$_lang['seofilter_field_relation_column'] = 'По столбцу';
$_lang['seofilter_word_relation'] = 'Зависит от';
$_lang['seofilter_word_image'] = 'Изображение для меню';
$_lang['seofilter_rule_introlength'] = 'Ограничение длины анонса. 0 - без ограничений. "-1" - не показывать';
$_lang['seofilter_rule_recount'] = 'Пересчитать результаты по каждой ссылке';
$_lang['seofilter_url_recount'] = 'Пересчитать результаты';
$_lang['seofilter_url_recount_all'] = 'Пересчитать все результаты';
$_lang['seofilter_url_recount_all_confirm'] = 'Вы уверены, что хотите пересчитать результаты?<br> Операция может занять какое-то время';
$_lang['seofilter_menu_more'] = 'Ещё';
$_lang['seofilter_rule_recount_new'] = 'Подсчитать результаты по каждой ссылке';
$_lang['seofilter_rule_recount_title'] = 'Количество успешно пересчитано';
$_lang['seofilter_url_recount_wait'] = 'Пожалуйста подождите';
$_lang['seofilter_url_recount_process'] = 'Идёт пересчёт...';
$_lang['seofilter_rule_recount_message'] = '<br><b>Подсчёты результатов по правилу (id={$rule_id}):</b><br>Всего ссылок связанных с правилом: {$all_links}. <br> Cсылок, имеющих результаты: <b>{$links}</b>. <br> Количество результатов (не уникальных): {$total}. ';
$_lang['seofilter_dictionary_remove_confirmation'] = '<b class="red">Внимание!</b> Удаление слова приводит к удалению ссылок, связанных с ним! <b>Вы уверены?</b> Сейчас ещё можно отредактировать, а вернуть нет.';
$_lang['seofilter_url_total'] = 'Результатов';
$_lang['seofilter_url_total_more'] = 'Количество результатов';
$_lang['seofilter_url_total_desc'] = 'Промежуточные результаты подсчёта для ускорения меню';
$_lang['seofilter_combo_relation_select'] = 'Будет доступно при зависимом поле';
$_lang['seofilter_info'] = 'Информация';
$_lang['seofilter_rule_information'] = '{if $was_links?}<nobr>Связанных ссылок с правилом: <b>{$was_links}</b></nobr><br>{/if}
                                        {if $remove_links?}Удалено <b>{$remove_links}</b> старых ссылок<br>{/if}
                                        {if $old_links && ($old_links != $was_links)}Старых ссылок осталось: <b>{$old_links}</b><br>{/if}
                                        {if $add_links?}Новых ссылок добавлено: <b>{$add_links}</b><br>{/if}
                                        {if $doubles_links?}Ссылок не добавлено: <b>{$doubles_links}</b> (дубли)<br>{/if}
                                        {if $update_links?}Названий ссылок обновлено: <b>{$update_links}</b><br>{/if}
                                        {if $all_links && ($remove_links || $add_links || $doubles_links)}Итого ссылок у правила: <b>{$all_links}</b> шт.<br>{/if}';
$_lang['seofilter_word_recount'] = 'Пересчитать результаты по слову';
$_lang['seofilter_words_recount'] = 'Пересчитать результаты по словам';
$_lang['seofilter_word_recount_message'] = '<b>Подсчёт результатов по слову (id={$word_id}):</b><br>Всего ссылок связанных со словом: {$all_links}. <br> Cсылок, имеющих результаты: <b>{$links}</b>. <br> Количество результатов (не уникальных): {$total}.<br><br>';
$_lang['seofilter_word_add_info'] = 'Для правила (id={$rule_id}):<br><b>добавлено {$add_links|decl:"ссылка|ссылки|ссылок":true}</b><br><br>';
$_lang['seofilter_word_update_info'] = 'Для правила (id={$rule_id}):<br>{if $remove_links}<b>удалено {$remove_links|decl:"ссылка|ссылки|ссылок":true}</b><br>{/if}<b>добавлено {$add_links|decl:"новая ссылка|новых ссылки|новых ссылок":true}</b><br><br>';
$_lang['seofilter_dictionary_alias_double'] = 'Для одного поля псевдонимы слов не могут совпадать';
$_lang['seofilter_Tagger'] = 'Tagger (теги компонента Tagger)';
$_lang['seofilter_word_enable'] = 'Активировать слово (создадутся ссылки)';
$_lang['seofilter_words_enable'] = 'Активировать слова (создадутся ссылки)';
$_lang['seofilter_word_disable'] = 'Отключить слово (удалятся ссылки)';
$_lang['seofilter_words_disable'] = 'Отключить слова (удалятся ссылки)';
$_lang['seofilter_word_disable_confirmation'] = 'Отключение слов приводит к безвозвратному удалению ссылок. Повторное включение создаст новые ссылки. Вы действительно хотите продолжить?';
$_lang['seofilter_word_enable_confirmation'] = 'Активация слов приводит к созданию ссылок для всех правил, связанных с этим словом. Вы действительно хотите продолжить?';
$_lang['seofilter_word_disable_info'] = 'Для правила (id={$rule_id}):<br><b>удалено {$total|decl:"ссылка|ссылки|ссылок":true}</b><br><br>';
$_lang['seofilter_field_slider_title'] = 'Слайдер';
$_lang['seofilter_field_xpdo_title'] = 'Внешняя таблица';
$_lang['seofilter_field_relation_title'] = 'Зависимое поле';

$_lang['seofilter_field_resource_parent'] = 'Родитель <small>(parent + мультикатегории ms2)</small>';
$_lang['seofilter_field_ms_vendor'] = 'Производитель <small>(vendor из miniShop2)</small>';
$_lang['seofilter_field_ms_vendorcollection'] = 'Зависимые коллекции<small>(msVendorCollection)</small>';
$_lang['seofilter_field_ms_category'] = 'Категория товара <small>(+ мультикатегории ms2)</small>';
$_lang['seofilter_field_ms_option'] = 'Пример опции <small>(miniShop2)</small>';
$_lang['seofilter_field_ms_data'] = 'Обычное поле <small>(miniShop2)</small>';
$_lang['seofilter_field_ms_price'] = 'Цена в виде слайдера <small>(miniShop2)</small>';
$_lang['seofilter_field_manually'] = 'Добавить обычное поле';
$_lang['seofilter_field_parent'] = 'Родитель (parent)';
$_lang['seofilter_field_vendor'] = 'Производитель (vendor)';
$_lang['seofilter_field_category'] = 'Категория товара';
$_lang['seofilter_field_alias_help'] = 'Псевдоним фильтра (alias)';
$_lang['seofilter_field_class_help'] = 'Класс для поиска значений по объектам. Можно ввести свой, но нужно будет расширить класс подсчётов';
$_lang['seofilter_field_key_help'] = 'Название столбца в таблице/ТВ-поля/опции';
$_lang['seofilter_field_name_help'] = 'Название фильтра';
$_lang['seofilter_field_hideparam_help'] = 'При формировании адреса псевдоним не будет участвовать';
$_lang['seofilter_field_valuefirst_help'] = 'Для формирования адресов наоборот "/{$value}-{$alias}"';
$_lang['seofilter_field_slider_help'] = 'Для слайдеров по цене и подобным. Значения не будут собираться.';
$_lang['seofilter_field_exact_help'] = 'Снимите галочку, если в одном поле хранятся несколько значений через разделитель (ТВ с множественным выбором и т.д.)';
$_lang['seofilter_field_active_help'] = 'Влияет только на сбор значений. Из этого поля можно будет формировать правила.';
$_lang['seofilter_field_xpdo_help'] = 'Когда в значении хранится ID строки из другой таблицы (parent к примеру)';
$_lang['seofilter_field_relation_help'] = 'Поле может зависеть от другого, если оно само из другой таблицы и имеет отношение по ID к "родителю".';
$_lang['seofilter_field_condition'] = 'Условие ограничения';

$_lang['seofilter_settings'] = 'Настройки';
$_lang['seofilter_settings_success'] = 'Настройки успешно сохранены';
$_lang['seofilter_settings_url'] = 'URL-настройки';
$_lang['seofilter_settings_main'] = 'Основные настройки';
$_lang['seofilter_settings_pro'] = 'PRO-настройки';
$_lang['seofilter_settings_default'] = 'Поля для замен при сбросе фильтра';
$_lang['seofilter_settings_ajax'] = 'jQuery селекторы для ajax-замен';
$_lang['seofilter_settings_count'] = 'Подсчёты и выборки для текстов';
$_lang['seofilter_settings_save'] = 'Сохранить настройки';
$_lang['seofilter_settings_intro'] = 'Для большего удобства здесь продублированы настройки из системных. Внимательно относитесь к изменениям.';
$_lang['seofilter_url_data'] = 'Свойства страницы';
$_lang['seofilter_url_meta'] = 'Индивидуальное SEO';
$_lang['seofilter_url_menu_data'] = 'Параметры для использования в sfMenu';
$_lang['seofilter_url_menu_on_help'] = 'Просто ограничитель для удобства вывода определенных страниц через showHidden=0 в sfMenu';
$_lang['seofilter_url_recount_help'] = 'Единоразово пересчитает результаты для этой ссылки при сохранении';
$_lang['seofilter_url_link_help'] = 'По умолчанию используется в меню и в хлебных крошках. Плейсхолдер [[!+sf.link]]';
$_lang['seofilter_where_help'] = 'Поддерживаются условия по полям ресурса, товара, ТВ-полям, и опциям:  Data.vendor, Option.size, TV.model и т.д. Пример: {"parent":7,"Data.price:>":50,"TV.model":"BMW","Option.size":"4x4"}';
$_lang['seofilter_rule_parent_help'] = 'Выберите страницу, от которой будут строится все SEO-страницы по данному правилу';
$_lang['seofilter_rule_parents_help'] = 'Введите ID страниц, от которых будут строится все SEO-страницы по данному правилу. По каждой странице будет создано одинаковое количество SEO-страниц.';
$_lang['seofilter_rule_count_parents_help'] = 'Нужно указывать, если ресурсы для фильтрации хранятся в другой категории, отличной от привязанной к правилу.';
$_lang['seofilter_rule_base_help'] = 'По умолчанию включен. Пример, юзер в фильтре на сайте выбрал одну категорию и три цвета. Если у правило с категорией - базовое, то будет найдена SEO страница и три цвета будут перечислены как get-параметры';
$_lang['seofilter_rule_rank_help'] = 'Чем меньше приоритет - тем раньше пытается примениться правило. Тесно связано с базовыми правилами. Если пользователь в каталоге выбрал одну категорию и один цвет, а общего правила для двух полей нет - то применится правило с наименьшим приоритетом. (если оба правила - базовые)';
$_lang['seofilter_rule_active_help'] = 'Если отключить правило, то все SEO-страницы, связанные с правилами будут отдавать 404 и продадут из меню через сниппет sfMenu. Это не удаляет страницы.';
$_lang['seofilter_rule_name_help'] = 'Просто название правила для удобства.';
$_lang['seofilter_rule_link_tpl_help'] = 'Важно! Шаблон для названия SEO-страниц. Сперва можете добавить поля в правило и потом будут видны доступные псевдонимы. Пример: "{$color_r} {$category}". Обработка через Fenom, доступны падежи';
$_lang['seofilter_rule_relinks_help'] = 'Применить новый шаблон названия к уже существующим SEO-страницам. Это заменит все названия, которые могли быть скорректированы вручную';
$_lang['seofilter_rule_recount_help'] = 'Пересчитает результаты по каждой ссылке, связанной с этим правилом, при сохранении.';
$_lang['seofilter_rule_url_help'] = 'По такому шаблону будут сформированы адреса SEO-страниц. Заполняется автоматически. Разделители между разными полями и между значением и псевдонимом можно изменить в настройках компонента.';
$_lang['seofilter_rule_create_links'] = 'Обрабатываются SEO-страницы';
$_lang['seofilter_rule_create_links_wait'] = 'Обработано url-адресов:';
$_lang['seofilter_remove_empty_links'] = 'Удалить пустые страницы';
$_lang['seofilter_url_remove_process'] = 'Идёт обработка всех страниц';
$_lang['seofilter_url_remove_process_wait'] = 'Первое число - страницы с результатами';
$_lang['seofilter_remove_empty_links_confirm'] = 'Вы действительно хотите удалить страницы с 0 результатами? Убедитесь, что подсчёты настроены правильно. SeoFilter пересчитает и удалит пустые ссылки навсегда.';

$_lang['seofilter_compare_like'] = 'Значение совпадает с (LIKE %x%)';
$_lang['seofilter_compare_notlike'] = 'Значение не совпадает с (NOT LIKE %x%)';
$_lang['seofilter_rule_fields_where'] = 'Условия полей';