<?php

$_lang['area_seofilter_main'] = 'Основные';
$_lang['area_seofilter_seo'] = 'Поля для подстановки по умолчанию';
$_lang['area_seofilter_jquery'] = 'jQuery селекторы для подстановки через AJAX';
$_lang['area_seofilter_count'] = 'Подсчёты и выборки';


$_lang['setting_seofilter_ajax'] = 'Изменять теги, заголовки через AJAX';
$_lang['setting_seofilter_ajax_desc'] = 'По умолчанию да, заменяет теги title, description и другие поля при работе с фильтром, см. инструкцию';
$_lang['setting_seofilter_decline'] = 'Склонять значение по падежам';
$_lang['setting_seofilter_decline_desc'] = 'По умолчанию нет, для склонения используется сервис morpher.ru (Лимит бесплатных запросов - 1000 в сутки)';
$_lang['setting_seofilter_redirect'] = 'Перенаправлять на приоритетный URL';
$_lang['setting_seofilter_redirect_desc'] = 'По умолчанию да. Перенаправляет на адрес, на основе приоритетов при пересечении полей, чтобы не было дублей';
$_lang['setting_seofilter_replace'] = 'Заменять теги значениями из словаря';
$_lang['setting_seofilter_replace_desc'] = 'Заменяет во всех SEO полях конструкцию {$value} на значения из словаря. По падежам {$value_r},{$value_p} ...';
$_lang['setting_seofilter_separator'] = 'Базовый разделитель параметра и значения';
$_lang['setting_seofilter_separator_desc'] = 'По умолчанию "-", по нему идёт разбивка значения от параметра';
$_lang['setting_seofilter_valuefirst'] = 'Сначала значение, потом параметр ';
$_lang['setting_seofilter_valuefirst_desc'] = 'По умолчанию нет. Если в адресе сначала значение, а потом через разделитель параметр. Пример ".../red-color"';

$_lang['setting_seofilter_morpher_username'] = 'Логин к сервису morpher.ru';
$_lang['setting_seofilter_morpher_username_desc'] = 'Если указать, будет больше вариантов склонения, включая где, куда, откуда';
$_lang['setting_seofilter_morpher_password'] = 'Пароль к сервису morpher.ru';
$_lang['setting_seofilter_morpher_password_desc'] = 'Пароль к логину на morpher.ru';
$_lang['setting_seofilter_base_get'] = 'Параметры, не влияющие на мета-теги';
$_lang['setting_seofilter_base_get_desc'] = 'GET-параметры, не влияющие на мета-теги. По умолчанию "price,page,limit,tpl,sort"';

$_lang['setting_seofilter_count'] = 'Подсчитывать потомков';
$_lang['setting_seofilter_count_desc'] = 'По умолчанию нет. Если да, то будет доступен плейсхолдер {$count} в SEO шаблонах';
$_lang['setting_seofilter_choose'] = 'Выборки по полям ресурса';
$_lang['setting_seofilter_choose_desc'] = 'Например: "msProductData.price". Несколько значений, через запятую. Перемножится как {choose} с полями из select и образует: min_price_{select}, max_price_{select}. Синонимы через "=", например "msProductData.old_price=op"';
$_lang['setting_seofilter_select'] = 'Какие поля ресурса выбирать';
$_lang['setting_seofilter_select_desc'] = 'Например: "id,msProductData.price". Образуют плейсхолдеры: min_{choose}_id, min_{choose}_price, max_{choose}_price и т.д. Допустимы синонимы "msProductData.old_price as old"';

$_lang['setting_seofilter_templates'] = 'Шаблоны ресурсов для отслеживания';
$_lang['setting_seofilter_templates_desc'] = 'Для работы плагина по добавлению значений в SEO словарь после сохранения ресурса';
$_lang['setting_seofilter_classes'] = 'Class_key ресурсов для отслеживания';
$_lang['setting_seofilter_classes_desc'] = 'По умолчанию "msProduct". Для работы плагина по добавлению значений в SEO словарь после сохранения ресурса';
$_lang['setting_seofilter_snippet'] = 'Сниппет для подготовки/обработки слов для мета-тегов';
$_lang['setting_seofilter_snippet_desc'] = 'prepareSnippet принимает массив слов($row), id правила($rule_id), и страницы($page_id), а также $pdoTools. Должен вернуть serialize($row) !';

$_lang['setting_seofilter_jcontent'] = 'jQuery-селектор для поля: Контент';
$_lang['setting_seofilter_jcontent_desc'] = 'По умолчанию ".sf_content"';
$_lang['setting_seofilter_jdescription'] = 'jQuery-селектор для поля: Описание';
$_lang['setting_seofilter_jdescription_desc'] = 'По умолчанию "meta[name="description"]"';
$_lang['setting_seofilter_jh1'] = 'jQuery-селектор для поля: Заголовок H1';
$_lang['setting_seofilter_jh1_desc'] = 'По умолчанию ".sf_h1"';
$_lang['setting_seofilter_jh2'] = 'jQuery-селектор для поля: Заголовок H2';
$_lang['setting_seofilter_jh2_desc'] = 'По умолчанию ".sf_h1"';
$_lang['setting_seofilter_jintrotext'] = 'jQuery-селектор для поля: Вводный текст';
$_lang['setting_seofilter_jintrotext_desc'] = 'По умолчанию ".sf_introtext"';
$_lang['setting_seofilter_jtext'] = 'jQuery-селектор для поля: Текстовое поле';
$_lang['setting_seofilter_jtext_desc'] = 'По умолчанию ".sf_text"';
$_lang['setting_seofilter_jtitle'] = 'jQuery-селектор для поля: Заголовок страницы';
$_lang['setting_seofilter_jtitle_desc'] = 'По умолчанию "title"';
$_lang['setting_seofilter_replacebefore'] = 'Добавлять SEO-заголовок через разделитель';
$_lang['setting_seofilter_replacebefore_desc'] = 'Если да, то будет добавлять и убирать SEO-заголовок к заголовку страницы, сохраняя вложенность и встроенную ajax-пагинацию mFilter2.';
$_lang['setting_seofilter_replaceseparator'] = 'Разделитель для заголовка';
$_lang['setting_seofilter_replaceseparator_desc'] = 'По умолчанию "/". При использовании pdoTitle и опции SetMeta=1 в mFilter2 будет работать Ajax-пагинация, если включена настройка выше.';

$_lang['setting_seofilter_content'] = 'Поле, где хранится Контент';
$_lang['setting_seofilter_content_desc'] = 'По умолчанию "content". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_description'] = 'Поле, где хранится Описание';
$_lang['setting_seofilter_description_desc'] = 'По умолчанию "description". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_h1'] = 'Поле, где хранится Заголовок H1';
$_lang['setting_seofilter_h1_desc'] = 'По умолчанию "longtitle". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_h2'] = 'Поле, где хранится Заголовок H2';
$_lang['setting_seofilter_h2_desc'] = 'По умолчанию "". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_introtext'] = 'Поле, где хранится Вводный текст';
$_lang['setting_seofilter_introtext_desc'] = 'По умолчанию "introtext". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_pagetpl'] = 'Поле, с шаблоном для пагинации в заголовке';
$_lang['setting_seofilter_pagetpl_desc'] = 'По умолчанию "@INLINE / [[%seofilter_page]] [[+page]]". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_text'] = 'Поле, где хранится Текстовое поле';
$_lang['setting_seofilter_text_desc'] = 'По умолчанию "". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
$_lang['setting_seofilter_title'] = 'Поле, где хранится Заголовок страницы';
$_lang['setting_seofilter_title_desc'] = 'По умолчанию "pagetitle". Можно указывать любые поля ресурса или TV-поля так, как они называются.';
