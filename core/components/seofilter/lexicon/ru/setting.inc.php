<?php

$_lang['area_seofilter_main'] = 'Основные';
$_lang['area_seofilter_seo'] = 'Поля для подстановки по-умолчанию';
$_lang['area_seofilter_jquery'] = 'jQuery селекторы для подстановки через AJAX';


$_lang['setting_seofilter_some_setting'] = 'Какая-то настройка';
$_lang['setting_seofilter_some_setting_desc'] = 'Это описание для какой-то настройки';

$_lang['setting_seofilter_ajax'] = 'Изменять теги, заголовки через AJAX';
$_lang['setting_seofilter_ajax_desc'] = 'По-умолчанию да, заменяет теги title, description и другие поля при работе с фильтром, см. инструкцию';
$_lang['setting_seofilter_decline'] = 'Склонять значение по падежам';
$_lang['setting_seofilter_decline_desc'] = 'По-умолчанию нет, для склонения используется сервис morpher.ru (Лимит бесплатных запросов - 1000 в сутки)';
$_lang['setting_seofilter_redirect'] = 'Перенаправлять на приоритетный URL';
$_lang['setting_seofilter_redirect_desc'] = 'По-умолчанию да. Перенаправляет на адрес, на основе приоритетов при пересечении полей, чтобы не было дублей';
$_lang['setting_seofilter_replace'] = 'Заменять теги значениями из словаря';
$_lang['setting_seofilter_replace_desc'] = 'Заменяет во всех SEO полях конструкцию [value] на значение из словаря. По падежам [value_r],[value_p] ...';
$_lang['setting_seofilter_separator'] = 'Базовый разделитель параметра и значения';
$_lang['setting_seofilter_separator_desc'] = 'По-умолчанию "-", по нему идёт разбтвка значения от параметра';
$_lang['setting_seofilter_valuefirst'] = 'Сначала значение, потом параметр ';
$_lang['setting_seofilter_valuefirst_desc'] = 'По-умолчанию нет. Если в адресе сначала значение, а потом через разделитель параметр. Пример ".../red-color"';

$_lang['setting_seofilter_morpher_username'] = 'Логин к сервису morpher.ru';
$_lang['setting_seofilter_morpher_username_desc'] = 'Если указать, будет больше вариантов склонения, включая где, куда, откуда';
$_lang['setting_seofilter_morpher_password'] = 'Пароль к сервису morpher.ru';
$_lang['setting_seofilter_morpher_password_desc'] = 'Пароль к логину на morpher.ru';
$_lang['setting_seofilter_base_get'] = 'Параметры, не влияющие на мета-теги';
$_lang['setting_seofilter_base_get_desc'] = 'GET-параметры, не влияющие на мета-теги. По-умолчанию "price,page,limit,tpl,sort"';

$_lang['setting_seofilter_count'] = 'Подсчитывать потомков';
$_lang['setting_seofilter_count_desc'] = 'По-умолчанию нет. Если да, то будет доступен плейсхолдер {$count} в SEO шаблонах';
$_lang['setting_seofilter_choose'] = 'Отдельные выборки по ключам';
$_lang['setting_seofilter_choose_desc'] = 'Можно указать несколько значений, через запятую, например: "price". Перемножит на настройку выбранных полей ресурса. Для использования в SEO шаблонах';
$_lang['setting_seofilter_select'] = 'Какие поля ресурса выбирать';
$_lang['setting_seofilter_select_desc'] = 'Например: "msProduct.id,msProductData.price", Доступный поля msProduct и msProductData. Образуют плейсхолдеры: min_price_id, min_price_price, max_price_id,max_price_price и т.д.';

$_lang['setting_seofilter_templates'] = 'Шаблоны ресурсов для отслеживания';
$_lang['setting_seofilter_templates_desc'] = 'Для работы плагина по добавлению значений в SEO словарь после сохранения ресурса';
$_lang['setting_seofilter_classes'] = 'Class_key ресурсов для отслеживания';
$_lang['setting_seofilter_classes_desc'] = 'По умолчанию "msProduct". Для работы плагина по добавлению значений в SEO словарь после сохранения ресурса';
$_lang['setting_seofilter_snippet'] = 'Сниппет для подготовки/обработки слов для мета-тегов';
$_lang['setting_seofilter_snippet_desc'] = 'prepareSnippet принимает массив слов($row), id правила($rule_id), и страницы($page_id), а также $pdoTools. Должен вернуть serialize($row) !';
