// ------------------------------------------------------------------------------
// Функции miniShop2: mSearch2, SeoFilter
// ------------------------------------------------------------------------------

if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = function(callback, thisArg) {
        thisArg = thisArg || window;
        for (var i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    };
}

if (!Object.keys) {
    Object.keys = (function() {
        var hasOwnProperty = Object.prototype.hasOwnProperty,
            hasDontEnumBug = !({
                toString: null
            }).propertyIsEnumerable('toString'),
            dontEnums = [
                'toString',
                'toLocaleString',
                'valueOf',
                'hasOwnProperty',
                'isPrototypeOf',
                'propertyIsEnumerable',
                'constructor'
            ],
            dontEnumsLength = dontEnums.length;

        return function(obj) {
            if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) throw new TypeError('Object.keys called on non-object');

            var result = [];

            for (var prop in obj) {
                if (hasOwnProperty.call(obj, prop)) result.push(prop);
            }

            if (hasDontEnumBug) {
                for (var i = 0; i < dontEnumsLength; i++) {
                    if (hasOwnProperty.call(obj, dontEnums[i])) result.push(dontEnums[i]);
                }
            }
            return result;
        }
    })()
};

// ------------------------------------------------------------------------------
// SeoFilter
// ------------------------------------------------------------------------------

$(document).ready(function(g) {

    var h = {
        config: seoFilterConfig || {},
        count: Object.keys(seoFilterConfig.params).length || 0,
        ajax_post: function(t, a, l, c) {
            g.ajax({
                type: "POST",
                url: this.config.actionUrl,
                dataType: "json",
                data: {
                    sf_action: "getmeta",
                    data: t,
                    pageId: this.config.page,
                    hash: a,
                    aliases: this.config.aliases
                },
                success: function(t) {
                    var e = a,
                        i = h.config.url || document.location.pathname;
                    if (g(document).find(".fm-seofilter").length) {
                        var n = g(document).find(".fm-seofilter");
                        t.data.seo_id ? (n.attr("href", n.data("url") + t.data.seo_id), g(document).find(".fm-seofilter").removeClass("hidden")) : g(document).find(".fm-seofilter").addClass("hidden")
                    }
                    if (t.data.title) {
                        var s = t.data.title.toString();
                        if (parseInt(h.config.replacebefore)) {
                            var o = h.config.replaceseparator || " / ",
                                r = g("title").text().split(o);
                            if (1 < r.length) {
                                if (!h.count && t.data.seo_id) r.unshift(s), h.count++;
                                else if (r[1].toString() === s) {
                                    r.shift();
                                    h.count = 0
                                } else r[0] = s;
                                g(h.config.jtitle).text(r.join(o)), g('[property="og:title"]').attr("content", r.join(o))
                            } else t.data.seo_id ? (r.unshift(s), h.count++, g(h.config.jtitle).text(r.join(o)), g('[property="og:title"]').attr("content", r.join(o))) : (g(h.config.jtitle).text(s), g('[property="og:title"]').attr("content", s))
                        } else g(h.config.jtitle).text(s), g('[property="og:title"]').attr("content", s)
                    }

                    if (typeof t.data.url == 'undefined') {
                        window.location = '/homes/'
                        return false
                    }

                    url_info = t.data.url.split('?page=');


                    console.log(url_info[1]);

                    if (!url_info[1]) {

                        if (t.data.filterTopText) {
                            $('#filterTopText').html(t.data.filterTopText);
                        } else {
                            $('#filterTopText').html('<p>Предлагаем вам комплекты типовых проектов, полностью подготовленные для успешного строительства ' +
                                'вашего дома и&#160;последующей его регистрации в&#160;гос. органах. Каждый из&#160;представленных проектов вы&#160;можете заказать с&#160;доставкой за&#160;1-3 дня курьерской службой EMS в' +
                                'любой регион России и&#160;СНГ с&#160;оплатой проекта курьеру при получении.</p><p>' +
                                'В&nbsp;Санкт-Петербурге и&nbsp;Москве Вы&nbsp;можете у&nbsp;нас не&nbsp;только приобрести проект, но, при желании, еще и&nbsp;заказать <a href="/stroitelstvo.html">строительство дома</a>. Будем рады проконсультировать Вас в&nbsp;наших офисах!' +
                                '</p>');
                        }
                    } else {
                        if (url_info[1] > 1)
                            $('#filterTopText').html('');
                    }
                    if (t.data.url && t.data.h1 && typeof mSearch2 != 'undefined') {
                        mSearch2.categoryUrl = t.data.url;
                        mSearch2.categoryTitle = t.data.h1;
                    }

                    t.data.links ? g("#links").html(t.data.links) : g("#links").empty(), t.data.description && (g(h.config.jdescription).attr("content", t.data.description), g('[property="og:description"]').attr("content", t.data.description)), t.data.link && g(h.config.jlink).html(t.data.link), t.data.h1 && g(h.config.jh1).html(t.data.h1), t.data.hasOwnProperty("rule_id") && t.data.rule_id && (t.data.h1 || t.data.h2) ? g(".bread").html('<span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="http://www.project-home.ru/" itemprop="url" rel="Архитектурно-строительная группа компаний Хорошие Дома. Продажа готовых проектов домов и коттеджей с полной рабочей документацией для строительства.  Разработка индивидуальных проектов домов по традиционным и энергосберегающим технологиям строительства.">ГК «Хорошие дома»</a></span><span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="http://www.project-home.ru/homes" itemprop="url" rel="">Проекты коттеджей</a></span><span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="sf_h2">' + (t.data.h2 ? t.data.h2 : t.data.h1) + "</span>") : g(".bread").html('<span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="http://www.project-home.ru/" itemprop="url" rel="Архитектурно-строительная группа компаний Хорошие Дома. Продажа готовых проектов домов и коттеджей с полной рабочей документацией для строительства.  Разработка индивидуальных проектов домов по традиционным и энергосберегающим технологиям строительства.">ГК «Хорошие дома»</a></span><span itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">Проекты коттеджей</span>'), t.data.introtext && g(h.config.jintrotext).html(t.data.introtext), t.data.text && g(h.config.jtext).html(t.data.text), t.data.url && (e = t.data.url, g('[property="og:url"]').attr("content", i + "/" + e), g('[link="canonical"]').attr("href", i + "/" + e)), void 0 !== c && c ? window.location = i + "/" + e : l ? window.history.pushState({
                        mSearch2: i + "/" + e
                    }, "", i + "/" + e) : window.location.hash = e.substr(1)

                    if (t.data.content) {
                        g(h.config.jcontent).html(t.data.content);
                        $(document).find(h.config.jcontent + ' img.lazy').each(function(index, img) {
                            img.setAttribute('src', img.getAttribute('data-src'));
                        });
                    }



                    $(function() {

                        var length_col_a = $('.r_tt #links a').length;
                        console.log(length_col_a);

                        if (length_col_a > 18) {
                            $('.r_tt #links').append('<a href="homes#dis_all" class="dis_all">Показать все категории</a><a href="homes#dis_no_all" class="dis_no_all" style="display:none;">Скрыть категории</a>');
                            $('.dis_all').css('display', 'inline-block');
                            for (i = 18; i < length_col_a - 2; i++) {
                                $('.r_tt #links a.tag-text:eq(' + i + ')').css('display', 'none');
                            }
                        }

                        $('.r_tt #links a.dis_all').on('click', function(e) {
                            $('.r_tt #links a.tag-text').css('display', 'inline-block');
                            $('.r_tt #links a.dis_all ').css('display', 'none');
                            $('.r_tt #links a.dis_no_all').css('display', 'inline-block');
                            return false;
                        });

                        $('.r_tt #links a.dis_no_all').on('click', function(e) {
                            for (i = 18; i < length_col_a - 2; i++) {
                                $('.r_tt #links a.tag-text:eq(' + i + ')').css('display', 'none');
                            }

                            $('.r_tt #links a.dis_all ').css('display', 'inline-block');
                            $('.r_tt #links a.dis_no_all').css('display', 'none');
                            return false;
                        });

                    });


                },
                error: function(t) {}
            })
        }
    };

// ------------------------------------------------------------------------------
// mSearch2
// ------------------------------------------------------------------------------

    void 0 !== mSearch2 && (mSearch2.Hash.set = function(t, e) {
        var i = "",
            n = (h.config.aliases, 0);
        h.config.url || document.location.pathname;
        for (var s in t) t.hasOwnProperty(s) && (i += "&" + s + "=" + t[s]);
        this.oldbrowser() || (0 != i.length && (i = "?" + i.substr(1).replace("%", "%25").replace("+", "%2B")), n = 1), h.ajax_post(t, i, n, e)
    }, parseInt(seoFilterConfig.slider) && (mSearch2.handleSlider = function() {
        return !!g(mSearch2.options.slider).length && (g.ui && g.ui.slider ? (g(mSearch2.options.slider).each(function() {
            var e = g(this),
                t = g(this).parents("fieldset"),
                i = t.find("input:first"),
                n = t.find("input:last"),
                s = Number(i.val()),
                o = Number(n.val());
            null != i.data("original-value") && (s = Number(i.data("original-value"))), null != n.data("original-value") && (o = Number(n.data("original-value")));
            for (var r = i.val(), a = -1 != r.indexOf("."), l = a ? Number(r.substr(r.indexOf(".") + 1).length) : 0, c = 1, h = 1; h <= l; h++) c *= 10;
            var u = e.parent().find(".sl_ot"),
                d = e.parent().find(".sl_do");
            "shirina" !== i.prop("name") && "dlina" !== i.prop("name") || (c = 2), e.slider({
                min: s,
                max: o,
                values: [s, o],
                range: !0,
                step: 1 / c,
                create: function() {
                    u.text(g(this).slider("values")[0].toLocaleString("ru-RU")), d.text(g(this).slider("values")[1].toLocaleString("ru-RU"))
                }
            }), e.on("stop", function(t, e) {
                a ? (i.val(e.values[0].toFixed(l)), n.val(e.values[1].toFixed(l))) : (i.val(e.values[0]), n.val(e.values[1])), i.trigger("change")
            }), e.on("slide", function(t, e) {
                "obschayaploschad" === i.prop("name") && e.values[1] < 10 && (e.values[1] = 10), u.text(e.values[0].toLocaleString("ru-RU")), d.text(e.values[1].toLocaleString("ru-RU")), a ? (i.val(e.values[0].toFixed(l)), n.val(e.values[1].toFixed(l))) : (i.val(e.values[0]), n.val(e.values[1]))
            });
            var p = i.prop("name"),
                f = mSearch2.Hash.get();
            if (f[p] || seoFilterConfig.params[p]) {
                if (seoFilterConfig.params[p]) var m = seoFilterConfig.params[p].split(mse2Config.values_delimeter);
                else m = f[p].split(mse2Config.values_delimeter);
                m[0].match(/(?!^-)[^0-9\.]/g) && (m[0] = m[0].replace(/(?!^-)[^0-9\.]/g, "")), 1 < m.length && m[1].match(/(?!^-)[^0-9\.]/g) && (m[1] = m[1].replace(/(?!^-)[^0-9\.]/g, "")), i.val(m[0]), n.val(1 < m.length ? m[1] : m[0])
            }
            i.on("change keyup input click", function(t) {
                this.value.match(/(?!^-)[^0-9\.]/g) && (this.value = this.value.replace(/(?!^-)[^0-9\.]/g, "")), "keyup" != t.type && "input" != t.type && (this.value > o ? this.value = o : this.value < s && (this.value = s)), e.slider("values", 0, this.value), u.text(e.slider("values", 0).toLocaleString("ru-RU"))
            }), n.on("change keyup input click", function(t) {
                this.value.match(/(?!^-)[^0-9\.]/g) && (this.value = this.value.replace(/(?!^-)[^0-9\.]/g, "")), "keyup" != t.type && "input" != t.type && (this.value > o ? this.value = o : this.value < s && (this.value = s)), e.slider("values", 1, this.value), d.text(e.slider("values", 1).toLocaleString("ru-RU"))
            }), (f[p] || seoFilterConfig.params[p]) && i.add(n).trigger("click"), mSearch2.sliders[p] = [s, o]
        }), !0) : mSearch2.loadJQUI(mSearch2.handleSlider))
    }, mSearch2.handleSlider()))
});

var mSearch2 = {
    initialized: false,
    loading: false,
    reached: false,
    options: {
        wrapper: '#mse2_mfilter',

        results: '#mse2_results',
        total: '#mse2_total',
        limit: '#mse2_limit',
        slider: '.mse2_number_slider',
        selectmenu: '.mse2_selectmenu',
        filters: '#mse2_filters',
        filter_title: '.filter_title',
        filter_wrapper: 'fieldset',

        pagination: '.mse2_pagination',
        pagination_link: '.mse2_pagination a',

        sort: '#mse2_sort',
        sort_link: '#mse2_sort a',

        tpl: '#mse2_tpl',
        tpl_link: '#mse2_tpl a',

        selected: '#mse2_selected',
        selected_tpl: '<a href="#" data-id="_id_" class="mse2_selected_link"><em>_title_</em><sup>x</sup></a>',
        selected_wrapper_tpl: '<strong>_title_:</strong>',
        selected_filters_delimeter: '; ',
        selected_values_delimeter: ' ',

        more: '.btn_more',
        more_tpl: '<button class="btn btn-default btn_more">' + mse2Config['moreText'] + '</button>',

        active_class: 'active',
        disabled_class: 'disabled',
        disabled_class_fieldsets: 'disabled_fieldsets',
        loading_class: 'loading',
        prefix: 'mse2_',
        suggestion: 'sup', // inside filter item, e.g. #mse2_filters
        autoLoad: true
    },
    sliders: {},
    selections: {},
    elements: ['filters', 'results', 'pagination', 'total', 'sort', 'selected', 'limit', 'tpl'],
    initialize: function (selector) {
        if (this.initialized) {
            return false;
        }
        var i;
        if (mse2Config['filterOptions'] != undefined && Object.keys(mse2Config['filterOptions']).length > 0) {
            for (i in mse2Config['filterOptions']) {
                if (mse2Config['filterOptions'].hasOwnProperty(i)) {
                    this.options[i] = mse2Config['filterOptions'][i];
                }
            }
        }
        for (i in this.elements) {
            if (this.elements.hasOwnProperty(i)) {
                var elem = this.elements[i];
                this[elem] = $(selector).find(this.options[elem]);
                if (!this[elem].length) {
                    //console.log('Error: could not initialize element "' + elem + '" with selector "' + this.options[elem] + '".');
                }
            }
        }
        // Get ready for old chunk
        if (this['pagination'].length == 0) {
            this.options['pagination'] = '#mse2_pagination';
            this.options['pagination_link'] = '#mse2_pagination a';
            this['pagination'] = $(selector).find(this.options['pagination']);
        }
        this.handlePagination();
        this.handleSort();
        this.handleTpl();
        this.handleNumbers();
        this.handleSlider();
        this.handleSelectmenu();
        this.handleLimit();

        var action = $(this.options.filters).attr('action');
        var re = new RegExp(action + '$');

        var submit = $(this.options.filters + ' [type="submit"]');
        if (this.options.autoLoad) {
            $(document).on('change', this.options.filters, function () {
                return mSearch2.submit();
            });
            submit.addClass('hidden');
        }
        else {
            submit.removeClass('hidden');
        }

        $(document).on('submit', this.options.filters, function () {
            return mSearch2.submit();
        });

        $(document).on('submit', submit, function (e) {
            if ($(e.target).closest('#mse2_mfilter').length) {
                return mSearch2.submit();
            }
        });

        this.btn_reset = $(this.options.filters + ' [type="reset"]');

        $(document).on('reset', this.options.filters, function () {
            mSearch2.reset();
        });

        if (Object.keys(this.startParams).length) {
            this.btn_reset.removeClass('hidden');
        }

        $(window).on('popstate', function (e) {
            if (e.originalEvent.state && e.originalEvent.state['mSearch2']) {
                var params = {};
                var tmp = e.originalEvent.state['mSearch2'].split('?');
                if (tmp[1]) {
                    tmp = tmp[1].split('&');
                    for (var i in tmp) {
                        if (!tmp.hasOwnProperty(i)) {
                            continue;
                        }
                        var tmp2 = tmp[i].split('=');
                        params[tmp2[0]] = tmp2[1];
                    }
                }
                mSearch2.setFilters(params);
                mSearch2.load(params);
            }
        });
        if (!mSearch2.Hash.oldbrowser()) {
            history.replaceState({mSearch2: window.location.href}, '');
        }

        if (this.selected) {
            this.selections[this.selected.text().replace(/\s+$/, '').replace(/:$/, '')] = [];
            var selectors = [
                this.options.filters + ' input[type="checkbox"]',
                this.options.filters + ' input[type="radio"]',
                this.options.filters + ' select'
            ];
            $(document).on('change', selectors.join(', '), function () {
                mSearch2.handleSelected($(this));
            });

            selectors = [
                'input[type="checkbox"]:checked',
                'input[type="radio"]:checked',
                'select'
            ];
            this.filters.find(selectors.join(', ')).each(function () {
                mSearch2.handleSelected($(this));
            });

            $(document).on('click', this.options.selected + ' a', function () {
                var id = $(this).data('id').replace(mse2Config.filter_delimeter, "\\" + mse2Config.filter_delimeter);
                var elem = $('#' + id);
                if (elem[0]) {
                    switch (elem[0].tagName) {
                        case 'INPUT':
                            elem.attr('checked', false).trigger('change');
                            break;
                        case 'SELECT':
                            elem.val(elem.find('option:first').prop('value')).trigger('change');
                            break;
                    }
                }
                return false;
            });
        }
        mSearch2.setEmptyFieldsets();
        mSearch2.setTotal(this.total.text());

        this.initialized = true;
    },

    handlePagination: function () {
        var pcre = new RegExp(mse2Config['pageVar'] + '[=|\/](\\d+)');
        switch (mse2Config['mode']) {
            case 'button':
                this.pagination.hide();
                // Add more button
                this.results.after(this.options['more_tpl']);
                var more = $(this.options['more']);

                var has_results = false;
                $(this.options['pagination_link']).each(function () {
                    var href = $(this).prop('href');
                    var match = href.match(pcre);
                    var page = !match ? 1 : match[1];
                    if (page > mse2Config['page']) {
                        has_results = true;
                        return false;
                    }
                });
                if (!has_results) {
                    more.hide();
                }
                if (mse2Config['page'] > 1) {
                    mse2Config['page'] = '';
                    mSearch2.Hash.remove('page');
                    mSearch2.load();
                }

                $(document).on('click', this.options['wrapper'] + ' ' + this.options['more'], function (e) {
                    e.preventDefault();
                    mSearch2.addPage();
                });
                break;

            case 'scroll':
                this.pagination.hide();
                var wrapper = $(this.options['wrapper']);
                var $window = $(window);
                $window.on('scroll', function () {
                    if (!mSearch2.reached && $window.scrollTop() > wrapper.height() - $window.height()) {
                        mSearch2.reached = true;
                        mSearch2.addPage();
                    }
                });

                if (mse2Config['page'] > 1) {
                    mse2Config['page'] = '';
                    mSearch2.Hash.remove('page');
                    mSearch2.load();
                }
                break;

            default:
                $(document).on('click', this.options.pagination_link, function () {
                    if (!$(this).hasClass(mSearch2.options.active_class)) {
                        $(mSearch2.options.pagination).removeClass(mSearch2.options.active_class);
                        $(this).addClass(mSearch2.options.active_class);

                        var tmp = $(this).prop('href').match(pcre);
                        var page = tmp && tmp[1] ? Number(tmp[1]) : 1;
                        mse2Config.page = (page != mse2Config.start_page) ? page : '';

                        var params = mSearch2.getFilters();
                        mSearch2.Hash.set(params);
                        var $this = $(this);
                        if ($this.closest('.pagi').hasClass('pagi-bottom')) {
                            $('html,body').animate({scrollTop: $('.pagi', '.catalog-subtitle-block').offset().top - 15}, 750);
                        }
                        mSearch2.load(params);
                    }

                    return false;
                });
        }
    },

    handleSort: function () {
        var params = this.Hash.get();
        if (params.sort) {
            var sorts = params.sort.split(mse2Config.values_delimeter);
            for (var i = 0; i < sorts.length; i++) {
                var tmp = sorts[i].split(mse2Config.method_delimeter);
                if (tmp[0] && tmp[1]) {
                    $(this.options.sort_link + '[data-sort="' + tmp[0] + '"]').data('dir', tmp[1]).attr('data-dir', tmp[1]).addClass(this.options.active_class);
                }
            }
        }

        $(document).on('click', this.options.sort_link, function () {
            if ($(this).hasClass(mSearch2.options.active_class) && $(this).data('dir') == '') {
                return false;
            }
            $(mSearch2.options.sort_link).removeClass(mSearch2.options.active_class);
            $(this).addClass(mSearch2.options.active_class);
            var dir;
            if ($(this).data('dir') == '') {
                dir = $(this).data('default');
            }
            else {
                dir = $(this).data('dir') == 'desc'
                    ? 'asc'
                    : 'desc';
            }
            $(mSearch2.options.sort_link).data('dir', '').attr('data-dir', '');
            $(this).data('dir', dir).attr('data-dir', dir);

            var sort = $(this).data('sort');
            if (dir) {
                sort = sort.replace(
                    new RegExp(mse2Config.method_delimeter + '.*?' + mse2Config.values_delimeter),
                    mse2Config.method_delimeter + dir + mse2Config.values_delimeter
                );
                sort += mse2Config.method_delimeter + dir;
            }
            mse2Config.sort = (sort != mse2Config.start_sort) ? sort : '';
            var params = mSearch2.getFilters();
            if (mse2Config['page'] > 1 && (mse2Config['mode'] == 'scroll' || mse2Config['mode'] == 'button')) {
                mse2Config['page'] = '';
                delete(params['page']);
            }
            mSearch2.Hash.set(params);
            mSearch2.load(params);

            return false;
        });
    },

    handleTpl: function () {
        $(document).on('click', this.options.tpl_link, function () {
            if (!$(this).hasClass(mSearch2.options.active_class)) {
                $(mSearch2.options.tpl_link).removeClass(mSearch2.options.active_class);
                $(this).addClass(mSearch2.options.active_class);

                var tpl = $(this).data('tpl');
                mse2Config.tpl = (tpl != mse2Config.start_tpl && tpl != 0) ? tpl : '';

                var params = mSearch2.getFilters();
                if (mse2Config['page'] > 1 && (mse2Config['mode'] == 'scroll' || mse2Config['mode'] == 'button')) {
                    mse2Config['page'] = '';
                    delete(params['page']);
                }
                mSearch2.Hash.set(params);
                mSearch2.load(params);
            }

            return false;
        });
    },

    handleSlider: function () {
        if (!$(mSearch2.options.slider).length) {
            return false;
        }
        else if (!$.ui || !$.ui.slider) {
            return mSearch2.loadJQUI(mSearch2.handleSlider);
        }
        $(mSearch2.options.slider).each(function () {
            var $this = $(this);
            var fieldset = $(this).parents('fieldset');
            var imin = fieldset.find('input:first');
            var imax = fieldset.find('input:last');
            var vmin = Number(imin.val());
            var vmax = Number(imax.val());
            if (imin.data('original-value') != undefined) {
                vmin = Number(imin.data('original-value'));
            }
            if (imax.data('original-value') != undefined) {
                vmax = Number(imax.data('original-value'));
            }
            // Check for decimals
            var ival = imin.val();
            var decimal = ival.indexOf('.') != -1;
            var decimals = decimal
                ? Number(ival.substr(ival.indexOf('.') + 1).length)
                : 0;
            var delimiter = 1;
            for (var i = 1; i <= decimals; i++) {
                delimiter *= 10;
            }

            if (imin.prop('name') === 'shirina' || imin.prop('name') === 'dlina') {
                delimiter = 2;
            }

            // if (imin.prop('name') === 'obschayaploschad') {
            //     delimiter = 0.1;
            //     vmin = 0;
            //     vmax = vmax + (10 - vmax % 10);
            // }

            var handleFrom = $this.parent().find('.sl_ot');
            var handleTo = $this.parent().find('.sl_do');

            $this.slider({
                min: vmin,
                max: vmax,
                values: [vmin, vmax],
                range: true,
                animate: "fast",
                step: 1 / delimiter,
                create: function () {
                    handleFrom.text($(this).slider("values")[0].toLocaleString('ru-RU'));
                    handleTo.text($(this).slider("values")[1].toLocaleString('ru-RU'));
                }
            });

            $this.on('stop', function (e, ui) {
                if (decimal) {
                    imin.val(ui.values[0].toFixed(decimals));
                    imax.val(ui.values[1].toFixed(decimals));
                } else {
                    imin.val(ui.values[0]);
                    imax.val(ui.values[1]);
                }
                imin.trigger('change');
            });

            $this.on('slide', function (e, ui) {
                handleFrom.text(ui.values[0].toLocaleString('ru-RU'));
                handleTo.text(ui.values[1].toLocaleString('ru-RU'));
                if (decimal) {
                    imin.val(ui.values[0].toFixed(decimals));
                    imax.val(ui.values[1].toFixed(decimals));
                } else {
                    imin.val(ui.values[0]);
                    imax.val(ui.values[1]);
                }
            });

            var name = imin.prop('name');
            var values = mSearch2.Hash.get();
            if (values[name]) {
                var tmp = values[name].split(mse2Config.values_delimeter);
                if (tmp[0].match(/(?!^-)[^0-9\.]/g)) {
                    tmp[0] = tmp[0].replace(/(?!^-)[^0-9\.]/g, '');
                }
                if (tmp.length > 1) {
                    if (tmp[1].match(/(?!^-)[^0-9\.]/g)) {
                        tmp[1] = tmp[1].replace(/(?!^-)[^0-9\.]/g, '');
                    }
                }
                imin.val(tmp[0]);
                imax.val(tmp.length > 1 ? tmp[1] : tmp[0]);
            }

            //imin.attr('readonly', true);
            imin.on('change keyup input click', function (e) {
                if (this.value.match(/(?!^-)[^0-9\.]/g)) {
                    this.value = this.value.replace(/(?!^-)[^0-9\.]/g, '');
                }
                if (e.type != 'keyup' && e.type != 'input') {
                    if (this.value > vmax) {
                        this.value = vmax;
                    }
                    else if (this.value < vmin) {
                        this.value = vmin;
                    }
                }
                $this.slider('values', 0, this.value);
                handleFrom.text($this.slider('values', 0).toLocaleString('ru-RU'));
            });
            // imax.attr('readonly', true);
            imax.on('change keyup input click', function (e) {
                if (this.value.match(/(?!^-)[^0-9\.]/g)) {
                    this.value = this.value.replace(/(?!^-)[^0-9\.]/g, '');
                }
                if (e.type != 'keyup' && e.type != 'input') {
                    if (this.value > vmax) {
                        this.value = vmax;
                    }
                    else if (this.value < vmin) {
                        this.value = vmin;
                    }
                }
                $this.slider('values', 1, this.value);
                handleTo.text($this.slider('values', 1).toLocaleString('ru-RU'));
            });

            if (values[name]) {
                imin.add(imax).trigger('click');
            }

            mSearch2.sliders[name] = [vmin, vmax];
        });
        return true;
    },

    handleSelectmenu: function () {
        if (!$(mSearch2.options.selectmenu).length) {
            return false;
        }
        else if (!$.ui || !$.ui.selectmenu) {
            return mSearch2.loadJQUI(mSearch2.handleSelectmenu);
        }
        $(mSearch2.options.selectmenu).each(function () {
            var $this = $(this);
            var fieldset = $(this).parents('fieldset');

            $this.selectmenu();
        });
        return true;
    },

    handleLimit: function () {
        $(document).on('selectmenuchange', this.options.limit, function () {
            var limit = $(this).val();
            mse2Config.page = '';
            if (limit == mse2Config.start_limit) {
                mse2Config.limit = '';
            }
            else {
                mse2Config.limit = limit;
            }
            var params = mSearch2.getFilters();
            if (mse2Config['page'] > 1 && (mse2Config['mode'] == 'scroll' || mse2Config['mode'] == 'button')) {
                mse2Config['page'] = '';
                delete(params['page']);
            }
            mSearch2.Hash.set(params);
            mSearch2.load(params);
        });
    },

    handleSelected: function (input) {
        if (!input[0]) {
            return;
        }
        var id = input.prop('id');
        var title = '';
        var elem;

        var filter = input.parents(this.options['filter_wrapper']);
        var filter_title = '';
        var tmp;
        if (filter.length) {
            tmp = filter.find(this.options['filter_title']);
            if (tmp.length > 0) {
                filter_title = tmp.text();
            }
        }
        if (filter_title == '') {
            //noinspection LoopStatementThatDoesntLoopJS
            for (filter_title in this.selections) {
                break;
            }
        }

        switch (input[0].tagName) {
            case 'INPUT':
                var label = mSearch2.filters.find('label[for="' + input.prop('id') + '"]');
                var match = label.html().match(/>(.*?)</);
                if (match && match[1]) {
                    title = match[1].replace(/(\s+$)/, '');
                }
                $('[data-id="' + id + '"]', this.selected).remove();
                if (input.is(':checked')) {
                    elem = this.options['selected_tpl']
                        .replace('[[+id]]', id).replace('[[+title]]', title)
                        .replace('_id_', id).replace('_title_', title);
                }
                break;

            case 'SELECT':
                var option = input.find('option:selected');
                $('[data-id="' + id + '"]', this.selected).remove();
                if (input.val() != '') {
                    title = ' ' + option.text().replace(/(\(.*\)$)/, '');
                    elem = this.options['selected_tpl']
                        .replace('[[+id]]', id).replace('[[+title]]', title)
                        .replace('_id_', id).replace('_title_', title);
                }
                break;
        }

        if (elem != undefined) {
            if (this.selections[filter_title] == undefined || input[0].type == 'radio') {
                this.selections[filter_title] = {};
            }
            this.selections[filter_title][id] = elem;
        }
        else if (this.selections[filter_title] != undefined && this.selections[filter_title][id] != undefined) {
            delete this.selections[filter_title][id];
        }

        this.selected.html('');
        var count = 0;
        var selected = [];
        for (var i in this.selections) {
            if (!this.selections.hasOwnProperty(i) || !Object.keys(this.selections).length) {
                continue;
            }
            if (Object.keys(this.selections[i]).length) {
                tmp = [];
                for (var i2 in this.selections[i]) {
                    if (!this.selections[i].hasOwnProperty(i2)) {
                        continue;
                    }
                    tmp.push(this.selections[i][i2]);
                    count++;
                }
                title = this.options['selected_wrapper_tpl']
                    .replace('[[+title]]', i)
                    .replace('_title_', i);
                selected.push(title + tmp.join(this.options['selected_values_delimeter']));
            }
        }

        if (count) {
            this.selected.append(selected.join(this.options['selected_filters_delimeter'])).show();
        }
        else {
            this.selected.hide();
        }
    },

    handleNumbers: function () {
        var data = this.Hash.get();
        for (var key in data) {
            if (!data.hasOwnProperty(key) || mSearch2.sliders[key] != undefined) {
                continue;
            }
            var values = data[key].split(mse2Config['values_delimeter']);
            var inputs = $('[name="' + key + '"]', mSearch2.filters);
            if (inputs.length == 2 && values.length == 2) {
                var $min = $(inputs[0]);
                var $max = $(inputs[1]);
                $min.attr('data-original-value', $min.val()).val(values[0]);
                $max.attr('data-original-value', $max.val()).val(values[1]);
            }
        }
    },

    load: function (params, callback, append) {
        if (this.loading) {
            return false;
        } else {
            this.loading = true;
        }
        if (Object.keys(params).length) {
            this.btn_reset.removeClass('hidden');
        } else {
            this.btn_reset.addClass('hidden');
        }
        if (!params || !Object.keys(params).length) {
            params = this.getFilters();
        }
        params.action = 'filter';
        params.pageId = mse2Config.pageId;

        this.beforeLoad();
        params.key = mse2Config.key;
        //noinspection JSUnresolvedFunction
        delay(function(){
            $.post(mse2Config.actionUrl, params, function (response) {
                mSearch2.loading = false;
                mSearch2.afterLoad();
                if (response.success) {
                    mSearch2.Message.success(response.message);
                    mSearch2.pagination.html(response.data['pagination']);
                    if (append) {
                        mSearch2.results.append(response.data['results']);
                    }
                    else {
                        mSearch2.results.html(response.data['results']);
                    }

                    if (mse2Config['mode'] == 'button') {
                        if (response.data['pages'] == response.data['page']) {
                            $(mSearch2.options['more']).hide();
                        }
                        else {
                            $(mSearch2.options['more']).show();
                        }
                    }
                    else if (mse2Config['mode'] == 'scroll') {
                        mSearch2.reached = response.data['pages'] == response.data['page'];
                    }

                    mSearch2.setTotal(response.data.total);
                    if (callback && $.isFunction(callback)) {
                        callback.call(this, response, params);
                    }
                    mSearch2.setSuggestions(response.data.suggestions);
                    mSearch2.setEmptyFieldsets();
                    if (response.data.log) {
                        $('.mFilterLog').html(response.data.log);
                    }
                    mSearch2.updateTitle(response.data);
                    $(document).trigger('mse2_load', response);

                    $(".fancy").fancybox({
                        // Options will go here
                    });
                }
                else {
                    mSearch2.Message.error(response.message);
                }
            }, 'json');
        }, 500);
    },

    getFilters: function () {
        var params = {};
        // Disabled friendly urls
        var hash = this.Hash.get();
        if (hash[mse2Config.idVar] != undefined) {
            params[mse2Config.idVar] = hash[mse2Config.idVar];
        }
        // Other params
        if (mse2Config[mse2Config.queryVar] != '') {
            params[mse2Config.queryVar] = mse2Config[mse2Config.queryVar];
        }
        if (mse2Config[mse2Config.parentsVar] != '') {
            params[mse2Config.parentsVar] = mse2Config[mse2Config.parentsVar];
        }
        if (mse2Config.sort != '') {
            params.sort = mse2Config.sort;
        }
        if (mse2Config.tpl != '') {
            params.tpl = mse2Config.tpl;
        }
        if (mse2Config.page > 0) {
            params.page = mse2Config.page;
        }
        if (mse2Config.limit > 0) {
            params.limit = mse2Config.limit;
        }
        // Filters
        $.map(this.filters.serializeArray(), function (n) {
            if (n['value'].match(/^[0-9]+$/)) {
                var $number = $('[name="' + n['name'] + '"]', mSearch2.filters);
                var original = $number.data('original-value');
                if (original != undefined && original == $number.val()) {
                    return;
                }
            }
            if (n['value'] === '') {
                return;
            }
            if (params[n['name']]) {
                params[n['name']] += mse2Config.values_delimeter + n['value'];
            }
            else {
                params[n['name']] = n['value'];
            }
        });
        for (var i in this.sliders) {
            if (this.sliders.hasOwnProperty(i) && params[i]) {
                if (this.sliders[i].join(mse2Config.values_delimeter) == params[i]) {
                    delete params[i];
                }
            }
        }

        return params;
    },

    setFilters: function (params) {
        if (!params) {
            params = {};
        }
        for (var i in this.elements) {
            if (!this.elements.hasOwnProperty(i)) {
                continue;
            }
            var elem = this.elements[i];
            if (typeof(this[elem]) == 'undefined') {
                continue;
            }
            var item, name, values, val, type;
            switch (elem) {
                case 'limit':
                    if (params['limit'] == undefined) {
                        this.limit.val(mse2Config['start_limit']);
                        mse2Config.limit = '';
                    }
                    else {
                        this.limit.val(params['limit']);
                    }
                    break;
                case 'pagination':
                    mse2Config.page = params['page'] == undefined
                        ? ''
                        : params['page'];
                    break;
                case 'sort':
                    var sorts = {};
                    values = params['sort'];
                    if (values == undefined) {
                        values = mse2Config['start_sort'];
                        mse2Config.sort = '';
                    }
                    if (typeof(values) != 'object' && values != '') {
                        values = values.split(mse2Config['values_delimeter']);
                        for (i in values) {
                            if (!values.hasOwnProperty(i)) {
                                continue;
                            }
                            name = values[i].split(mse2Config['method_delimeter']);
                            if (name[0] && name[1]) {
                                sorts[name[0]] = name[1];
                            }
                        }
                    }
                    $(document).find(this.options['sort_link']).each(function () {
                        item = $(this);
                        name = item.data('sort');
                        if (sorts[name]) {
                            item.data('dir', sorts[name]).attr('data-dir', sorts[name]);
                            item.addClass(mSearch2.options['active_class']);
                        }
                        else {
                            item.data('dir', '').attr('data-dir', '');
                            item.removeClass(mSearch2.options['active_class']);
                        }
                    });
                    break;
                case 'tpl':
                    values = params['tpl'] != undefined
                        ? params['tpl']
                        : 0;
                    $(document).find(this.options['tpl_link']).each(function () {
                        item = $(this);
                        if (item.data('tpl') == values) {
                            item.addClass(mSearch2.options['active_class']);
                            mse2Config.tpl = values;
                        }
                        else {
                            item.removeClass(mSearch2.options['active_class']);
                        }
                    });
                    break;
                case 'filters':
                    this['filters'].find('input').each(function () {
                        item = $(this);
                        name = item.prop('name');
                        type = item.prop('type');
                        values = params[name];
                        if (values != undefined && typeof(values) != 'object') {
                            values = values.split(mse2Config['values_delimeter']);
                        }
                        switch (type) {
                            case 'checkbox':
                            case 'radio':
                                var checked = item.is(':checked');
                                if (params[name] != undefined) {
                                    item.prop('checked', values.indexOf(String(item.val())) != -1);
                                }
                                else {
                                    item.prop('checked', false);
                                }
                                if (item.is(':checked') != checked) {
                                    mSearch2.handleSelected(item);
                                }
                                break;
                            default:
                                if (mSearch2.sliders[name]) {
                                    if (item.prop('id').match(/.*?_0$/)) {
                                        val = (values != undefined && values[0] != undefined)
                                            ? values[0]
                                            : mSearch2.sliders[name][0];
                                    }
                                    else {
                                        val = (values != undefined && values[1] != undefined)
                                            ? values[1]
                                            : mSearch2.sliders[name][1];
                                    }
                                    if (val != item.val()) {
                                        item.val(val).trigger('click');
                                    }
                                } else {
                                    var original = item.data('original-value');
                                    if (original != undefined) {
                                        item.val(original);
                                    }
                                }
                        }
                    });
                    this['filters'].find('select').each(function () {
                        item = $(this);
                        name = item.prop('name');
                        type = item.prop('type');
                        values = params[name];
                        if (values != undefined) {
                            if (typeof(values) != 'object') {
                                values = values.split(mse2Config['values_delimeter']);
                            }
                            item.find('option').each(function () {
                                var option = $(this);
                                val = option.prop('value');
                                var selected = option.is(':selected');
                                $(this).prop('selected', values.indexOf(String(val)) != -1);
                                if (option.is(':selected') != selected) {
                                    mSearch2.handleSelected(item);
                                }
                            });
                        }
                        else {
                            item.val('');
                            item.selectmenu('refresh');
                        }
                        /*
                         if (params[name] != undefined) {
                         item.prop('checked', values.indexOf(String(item.val())) != -1);
                         }
                         else {
                         item.prop('checked', false);
                         }
                         */
                    });
                    break;
            }
        }
    },

    setSuggestions: function (suggestions) {
        var aliases = mse2Config.aliases || {};
        for (var filter in suggestions) {
            if (suggestions.hasOwnProperty(filter)) {
                var arr = suggestions[filter];
                if (typeof(aliases[filter]) != 'undefined') {
                    filter = aliases[filter];
                }
                for (var value in arr) {
                    if (arr.hasOwnProperty(value)) {
                        var count = arr[value];
                        var selector = filter.replace(mse2Config.filter_delimeter, "\\" + mse2Config.filter_delimeter);
                        var input = $('#' + mSearch2.options.prefix + selector, mSearch2.filters).find('[value="' + value.replace(/&quot;/g, '\\"') + '"]');
                        if (!input[0]) {
                            continue;
                        }

                        switch (input[0].tagName) {
                            case 'INPUT':
                                var proptype = input.prop('type');
                                if (proptype != 'checkbox' && proptype != 'radio') {
                                    continue;
                                }
                                var label = $('#' + mSearch2.options.prefix + selector, mSearch2.filters).find('label[for="' + input.prop('id') + '"]');
                                var elem = input.parent().find(mSearch2.options.suggestion);
                                elem.text(count);

                                if (count == 0) {
                                    if (input.is(':not(:checked)')) {
                                        input.prop('disabled', true);
                                        label.addClass(mSearch2.options.disabled_class);
                                        mSearch2.handleSelected(input);
                                    }
                                }
                                else {
                                    input.prop('disabled', false);
                                    label.removeClass(mSearch2.options.disabled_class);
                                }

                                if (input.is(':checked')) {
                                    elem.hide();
                                }
                                else {
                                    elem.show();
                                }
                                break;

                            case 'OPTION':
                                var text = input.text();
                                var matches = text.match(/\([^\)]+\)$/);
                                var src = matches
                                    ? matches[0]
                                    : '';
                                var dst = '';

                                if (!count) {
                                    input.prop('disabled', true).addClass(mSearch2.options.disabled_class);
                                    /*
                                     if (input.is(':selected')) {
                                     input.prop('selected', false);
                                     mSearch2.handleSelected(input);
                                     }
                                     */
                                }
                                else {
                                    dst = ' (' + count + ')';
                                    input.prop('disabled', false).removeClass(mSearch2.options.disabled_class);
                                }

                                if (input.is(':selected')) {
                                    dst = '';
                                }

                                if (src) {
                                    text = text.replace(src, dst);
                                }
                                else {
                                    text += dst;
                                }
                                input.text(text);

                                mSearch2.handleSelected(input.parent());
                                break;
                        }
                    }
                }
            }
        }
    },

    setEmptyFieldsets: function () {
        this.filters.find('fieldset').each(function () {
            var all_children_disabled = $(this).find('label:not(.' + mSearch2.options.disabled_class + ')').length == 0;
            if (all_children_disabled) {
                $(this).addClass(mSearch2.options.disabled_class_fieldsets);
            }
            if (!all_children_disabled && $(this).hasClass(mSearch2.options.disabled_class_fieldsets)) {
                $(this).removeClass(mSearch2.options.disabled_class_fieldsets);
            }
        });
    },

    setTotal: function (total) {
        if (this.total.length != 0) {
            if (!total || total == 0) {
                this.total.parent().hide();
                this.limit.parent().hide();
                this.sort.hide();
                this.total.text(0);
            }
            else {
                this.total.parent().show();
                this.limit.parent().show();
                this.sort.show();
                this.total.text(total);
            }
        }

        for (i = 0; i < $('#mse2_results > div').length; i++) {
            //console.log($('#mse2_results > div:eq(' + i + ') .catalog-item-img').attr('data-src'));
            $('#mse2_results > div:eq(' + i + ') .catalog-item-img.lazy').attr('src', $('#mse2_results > div:eq(' + i + ') .catalog-item-img.lazy').attr('data-src'));
        }

        if (this.total.text() == 0) {
            $('#pdopage').css('display', 'none');
            $('.sort-container .v-line.hidden-xs-down').css('display', 'none');
            $('.sort-container .v-line.hidden-sm-down').css('display', 'none');

            $('.sort-container').css('background', 'no-repeat');
            $('#filterTopText').css('display', 'none');
            $('.sort-block').css('display', 'none');

            $('.catalog-text p').css('font-size', '18px');
        } else {
            $('#pdopage').css('display', '');
            $('.sort-container .v-line.hidden-xs-down').css('display', '');
            $('.sort-container .v-line.hidden-sm-down').css('display', '');

            $('.sort-container').css('background', '');
            $('#filterTopText').css('display', '');
            $('.sort-block').css('display', '');

            $('.catalog-text p').css('font-size', '');
        }

    },

    beforeLoad: function () {
        $(this.options['wrapper']).addClass(this.options['loading_class']);
        this.results.css('opacity', .5);
        $(this.options.pagination_link).addClass(this.options.active_class);
        this.filters.find('input, select').prop('disabled', true).addClass(this.options.disabled_class);
    },

    afterLoad: function () {
        $(this.options['wrapper']).removeClass(this.options['loading_class']);
        this.results.css('opacity', 1);
        this.filters.find('.' + this.options.disabled_class).prop('disabled', false).removeClass(this.options.disabled_class);
        // $('html,body').animate({scrollTop: $('#mse2_results').offset().top - 50}, 'fast');
    },

    loadJQUI: function (callback, parameters) {
        $('<link/>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: mse2Config.cssUrl + 'jquery-ui/jquery-ui.min.css'
        }).appendTo('head');

        return $.getScript(mse2Config.jsUrl + 'lib/jquery-ui.min.js', function () {
            if (typeof callback == 'function') {
                callback(parameters);
            }
        });
    },

    submit: function (params) {
        if (this.loading) {
            return false;
        }
        else if (!params || !Object.keys(params).length) {
            params = this.getFilters();
        }
        else {
            delete(params['action']);
            delete(params['key']);
            delete(params['pageId']);
        }
        delete(params['page']);

        var action = $(this.options.filters).attr('action');
        /*
                if ($(this.options.filters + ' [type="submit"]').hasClass('home_filter_submit')) {
                    mse2Config.page = '';
                    mSearch2.Hash.set(params, true);
                    return false;
                } else {
                    mse2Config.page = '';
                    if (params.hasOwnProperty('dopetagi') && params.hasOwnProperty('etagi')) {
                        var etagiArray = params.etagi.split(',');
                        var imax = $('[id="mse2_msoption|etagi_1"]');
                        var imin = $('[id="mse2_msoption|etagi_0"]');
                        if (params.dopetagi === 'С мансардным этажом' || params.dopetagi === 'С цокольным этажом') {
                            etagiArray[0] = 2;
                            if (parseInt(imax.val()) < 2) {
                                imax.val(2).trigger('change');
                            }
                            imin.val(2).trigger('change');
                        } else if (params.dopetagi === 'С мансардным этажом,С цокольным этажом' || params.dopetagi === 'С цокольным этажом,С мансардным этажом') {
                            etagiArray[0] = 3;
                            if (parseInt(imax.val()) < 3) {
                                imax.val(3).trigger('change');
                            }
                            imin.val(3).trigger('change');
                        }
                        params.etagi = etagiArray.join(',');
                    }
                    mSearch2.Hash.set(params);
                    mSearch2.load(params);
                    return false;
                }
        */
    },

    reset: function () {
        if (this.loading) {
            return false;
        }
        window.location = '/homes/';
        // this.Hash.clear();
        // this.setFilters();

        // return this.submit();
    },

    addPage: function () {
        var pcre = new RegExp(mse2Config['pageVar'] + '[=|\/](\\d+)');
        var current = mse2Config['page'] || 1;
        $(this.options['pagination_link']).each(function () {
            var href = $(this).prop('href');
            var match = href.match(pcre);
            var page = !match ? 1 : Number(match[1]);
            if (page > current) {
                mse2Config.page = (page != mse2Config.start_page) ? page : '';
                var tmp = mSearch2.getFilters();
                delete(tmp['page']);
                mSearch2.Hash.set(tmp);

                var params = mSearch2.getFilters();
                mSearch2.load(params, null, true);
                return false;
            }
        });
    },

    updateTitle: function (response) {
        if (typeof(pdoTitle) == 'undefined') {
            return;
        }
        var $title = $('title');
        var separator = pdoTitle.separator || ' / ';
        var tpl = pdoTitle.tpl;

        var title = [];
        var items = $title.text().split(separator);
        var pcre = new RegExp('^' + tpl.split(' ')[0] + ' ');
        for (var i = 0; i < items.length; i++) {
            if (i === 1 && response.page && response.page > 1) {
                title.push(tpl.replace('{page}', response.page).replace('{pageCount}', response.pages));
            }
            if (!items[i].match(pcre)) {
                title.push(items[i]);
            }
        }
        $title.text(title.join(separator));
    },

};

mSearch2.Form = {
    initialized: false,
    initialize: function (selector) {
        if (this.initialized || typeof(mse2FormConfig) == 'undefined') {
            return false;
        }
        $(selector).each(function () {
            var form = $(this);
            var config = mse2FormConfig[form.data('key')];
            var cache = {};

            if (config.autocomplete == '0' || config.autocomplete == 'false') {
                return false;
            }
            else if (!$.ui || !$.ui.autocomplete) {
                return mSearch2.loadJQUI(mSearch2.Form.initialize, selector);
            }

            form.find('input[name="' + config.queryVar + '"]').autocomplete({
                source: function (request, callback) {
                    if (request.term in cache) {
                        callback(cache[request.term]);
                        return;
                    }
                    var data = {
                        action: 'search',
                        key: form.data('key'),
                        pageId: config.pageId
                    };
                    data[config.queryVar] = request.term;
                    //noinspection JSUnresolvedFunction
                    $.post(mse2Config.actionUrl, data, function (response) {
                        if (response.data.log) {
                            $('.mSearchFormLog').html(response.data.log);
                        }
                        else {
                            $('.mSearchFormLog').html('');
                        }
                        var res = [
                            {
                                label: "<div class='mse2-ac-item'><small><span class='text-grey'>Результаты: " + response.data.total + "</span></small></div>",
                                value: "Результаты"
                            }
                        ].concat(response.data.results);
                        cache[request.term] = res;
                        callback(res);
                    }, 'json');
                },
                minLength: config.minQuery || 3,
                select: function (event, ui) {
                    if (ui.item.url) {
                        document.location.href = ui.item.url;
                    }
                    else {
                        setTimeout(function () {
                            form.submit();
                        }, 100);
                    }
                }
            }).data("ui-autocomplete")._renderItem =
                function (ul, item) {
                    return $('<li>')
                        .data("item.autocomplete", item)
                        .addClass("mse2-ac-wrapper")
                        .append($("<div>").addClass('mse2-ac-link').html(item.label))
                        //.append("<a class=\"mse2-ac-link\">" + item.label + "</a>")
                        .appendTo(ul);
                };

            //noinspection JSPotentiallyInvalidConstructorUsage
            jQuery.ui.autocomplete.prototype._resizeMenu = function () {
                var ul = this.menu.element;
                ul.outerWidth(this.element.outerWidth());
            };

            return true;
        });
        this.initialized = true;
    }
};

mSearch2.Message = {
    success: function (message) {
        console.log(message);
    },
    error: function (message) {
        alert(message);
    }
};

mSearch2.Hash = {
    get: function () {
        var vars = {}, hash, splitter, hashes;
        if (!this.oldbrowser()) {
            var pos = window.location.href.indexOf('?');
            hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
            splitter = '&';
        }
        else {
            hashes = decodeURIComponent(window.location.hash.substr(1));
            splitter = '/';
        }

        if (hashes.length == 0) {
            return vars;
        }
        else {
            hashes = hashes.split(splitter);
        }

        for (var i in hashes) {
            if (hashes.hasOwnProperty(i)) {
                hash = hashes[i].split('=');
                if (typeof hash[1] == 'undefined') {
                    vars['anchor'] = hash[0];
                }
                else {
                    vars[hash[0]] = hash[1];
                }
            }
        }
        return vars;
    },
    set: function (vars, callback) {
        var hash = '';
        for (var i in vars) {
            if (vars.hasOwnProperty(i)) {
                hash += '&' + i + '=' + vars[i];
            }
        }
        if (!this.oldbrowser()) {
            if (hash.length != 0) {
                hash = '?' + hash.substr(1).replace('%', '%25').replace('+', '%2B');
            }
            window.history.pushState({mSearch2: document.location.pathname + hash}, '', document.location.pathname + hash);
        }
        else {
            window.location.hash = hash.substr(1);
        }
    },
    add: function (key, val) {
        var hash = this.get();
        hash[key] = val;
        this.set(hash);
    },
    remove: function (key) {
        var hash = this.get();
        delete hash[key];
        this.set(hash);
    },
    clear: function () {
        this.set({});
    },
    oldbrowser: function () {
        return !(window.history && history.pushState);
    }
};

mSearch2.startParams = mSearch2.Hash.get();

var delay = (function(){
    var timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();
