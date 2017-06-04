<?php
include_once 'setting.inc.php';

$_lang['seofilter'] = 'SeoFilter';
$_lang['seofilter_menu_desc'] = 'Управление ЧПУ и SEO для mFilter2';
$_lang['seofilter_intro_msg'] = 'Вы можете выделять сразу несколько предметов при помощи Shift или Ctrl.';

$_lang['seofilter_field'] = 'Поле';
$_lang['seofilter_fields'] = 'Поля фильтра';
$_lang['seofilter_fields_intro'] = 'На этой странице вы создаёте и редактируете базовые поля для формирования ЧПУ в фильтре После сохранения в режиме редактирования можно будет ввести информацию по полям для SEO. ';

$_lang['seofilter_multifield'] = 'Пересечение';
$_lang['seofilter_multifields'] = 'Пересечение полей';
$_lang['seofilter_multifields_intro'] = 'Здесь вы можете настроить правила для формирования адресов для двух и более полей. Можно задавать конкретные значения для двух полей одного ключа, чтобы получить одну страницу.';

$_lang['seofilter_seometa'] = 'SEO';
$_lang['seofilter_seometas'] = 'SEO';
$_lang['seofilter_seometas_intro'] = 'Для большего удобства в управлении все прописанные SEO и Meta поля собраны в одну таблицу';

$_lang['seofilter_dictionary'] = 'Словарь';
$_lang['seofilter_dictionary_intro'] = 'После добавления поля здесь появляются запросы и их склонения, если включено, которые можно откорректировать в ручную';


$_lang['seofilter_field_id'] = 'Id';
$_lang['seofilter_field_name'] = 'Название';
$_lang['seofilter_field_description'] = 'Описание';
$_lang['seofilter_field_active'] = 'Активно';
$_lang['seofilter_field_page'] = 'Страница';
$_lang['seofilter_field_class'] = 'Класс';
$_lang['seofilter_field_key'] = 'Ключ';
$_lang['seofilter_field_alias'] = 'Синоним';
$_lang['seofilter_field_translit'] = 'Транслитерация';
$_lang['seofilter_field_urltpl'] = 'Шаблон url';
$_lang['seofilter_field_priority'] = 'Приоритет';

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

$_lang['seofilter_multifield_id'] = 'Id';
$_lang['seofilter_multifield_active'] = 'Активно';
$_lang['seofilter_multifield_page'] = 'Страница';
$_lang['seofilter_multifield_name'] = 'Название';
$_lang['seofilter_multifield_url'] = 'URL адрес';
$_lang['seofilter_multifield_url_more'] = 'URL адрес (если нужен конкретный URL и прописаны условия)';
$_lang['seofilter_multifield_count'] = 'Кол-во';
$_lang['seofilter_multifield_fields'] = 'Поля';
$_lang['seofilter_multifield_tablefields'] = 'Выберите поля';

$_lang['seofilter_multifield_create'] = 'Добавить пересечение';
$_lang['seofilter_multifield_update'] = 'Изменить пересечение';
$_lang['seofilter_multifield_enable'] = 'Включить пересечение';
$_lang['seofilter_multifields_enable'] = 'Включить пересечения';
$_lang['seofilter_multifield_disable'] = 'Отключить пересечение';
$_lang['seofilter_multifields_disable'] = 'Отключить пересечения';
$_lang['seofilter_multifield_remove'] = 'Удалить пересечение';
$_lang['seofilter_multifields_remove'] = 'Удалить пересечения';
$_lang['seofilter_multifield_remove_confirm'] = 'Вы уверены, что хотите удалить это пересечение?';
$_lang['seofilter_multifields_remove_confirm'] = 'Вы уверены, что хотите удалить эти пересечения?';
$_lang['seofilter_multifield_active'] = 'Включено';

$_lang['seofilter_multifield_err_name'] = 'Вы должны указать имя пересечения.';
$_lang['seofilter_multifield_err_ae'] = 'Пересечение с таким именем уже существует.';
$_lang['seofilter_multifield_err_nf'] = 'Пересечение не найдено.';
$_lang['seofilter_multifield_err_ns'] = 'Пересечение не указано.';
$_lang['seofilter_multifield_err_remove'] = 'Ошибка при удалении пересечения.';
$_lang['seofilter_multifield_err_save'] = 'Ошибка при сохранении пересечения.';

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


$_lang['seofilter_dictionary_create'] = 'Добавить запись';
$_lang['seofilter_dictionary_id'] = 'Id';
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

$_lang['seofilter_disctionary_decline'] = 'Склонения по падежам';
$_lang['seofilter_disctionary_decline_desc'] = 'Склонения по падежам только для русского языка';

$_lang['seofilter_fieldids_id'] = 'Id';
$_lang['seofilter_fieldids_field_id'] = 'Поле';
$_lang['seofilter_fieldids_multi_id'] = 'Пересечение';
$_lang['seofilter_fieldids_priority'] = 'Порядок';
$_lang['seofilter_fieldids_where'] = 'Условие (для создания конкретных страниц)';
$_lang['seofilter_fieldids_compare'] = 'Сравнение';
$_lang['seofilter_fieldids_value'] = 'Значение';
$_lang['seofilter_fieldids_condition'] = 'Where';

$_lang['seofilter_fieldids_update'] = 'Изменить условия';
$_lang['seofilter_fieldids_remove'] = 'Исключить поле';
$_lang['seofilter_fieldids_remove_confirm'] = 'Вы уверены, что хотите исключить это поле?';