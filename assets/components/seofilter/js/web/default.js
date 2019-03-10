if(typeof(seoFilterConfig) === 'undefined') {var seoFilterConfig = {};}
if(typeof(mse2Config) === 'undefined') {var mse2Config = {};}
if(typeof(sfConfig) === 'undefined') {var sfConfig = {};}
var SeoFilter = {
    config: seoFilterConfig,
    mse_config: mse2Config,
    other_config: sfConfig,
    count :  Object.keys(seoFilterConfig.params).length || 0,
    initialized: false,

    initialize: function () {
        if(this.initialized) {
            return false;
        }

        this.mFilter2Handlers();
        this.seoFilterHandler();
        this.initialized = true;

    },

    getCustomFilters: function (element) {
        var params = {};
        if(!element) {
            element = $(document).find('.sf_filters');
        }
        $(element).find('input, select').each(function () {
            if(!this.name || this.name == 'undefined') {
                return;
            }
            var name = this.name.replace('[]','');
            if(this.tagName == 'SELECT') {
                if($(this).find('option:selected').length) {
                    if($(this).find('option:selected').attr('value')) {
                        params[name] = $(this).find('option:selected').attr('value');
                    }
                }
            } else {
                if($(this).is(':checked')) {
                    if(params[name]) {
                        params[name] += ','+$(this).val();
                    } else {
                        params[name] = $(this).val();
                    }
                }
            }
        });
        var sort = this.getCustomSort();
        if(Object.keys(sort).length) {
            params = $.extend(params,sort);
        }
        if (typeof(pdoPage) != 'undefined') {
            params['hash'] = pdoPage.configs.page.hash;
            if(pdoPage.keys.page && pdoPage.keys.page > 1) {
                // params['page'] = pdoPage.keys.page;
            }
            this.loadMeta(params,'','meta_results');
        } else {
            this.loadMeta(params);
        }
        // console.log(params);
    },

    getCustomSort: function (submit) {
        var sort = {};
        var elems = $(document).find('.sf_sorting [name="sort"]');
        if(elems.length) {
            $.each(elems,function (i) {
                if(this.tagName == 'SELECT') {
                    if($(this).find('option:selected').length) {
                        if($(this).find('option:selected').attr('value')) {
                            sort['sort'] = $(this).find('option:selected').attr('value');
                        }
                        return false;
                    }
                } else {
                    if($(this).is(':checked')) {
                        if($(this).val()) {
                            sort['sort'] = $(this).val();
                        }
                        return false;
                    }
                }
            });
        }

        return sort;
    },

    setFilters: function (filters) {
       // console.log(filters);
    },

    seoFilterHandler: function() {
        if($(document).find('.sf_filters').length) {
            $(document).on('change','.sf_filters',function (e) {
                SeoFilter.getCustomFilters(this);
            });

            $(document).on('change','.sf_sorting [name="sort"]',function (e) {
                SeoFilter.getCustomFilters();
            });

            this.registerPopstate('SeoFilter');

            if (!this.oldbrowser()) {
                history.replaceState({SeoFilter: window.location.href}, '');
            }

        }
        //for override custom filters
    },

    registerPopstate: function (name) {
        window.setTimeout(function() {
            $(window).unbind('popstate');
            $(window).on('popstate', function (e) {
                if (e.originalEvent.state && e.originalEvent.state[name]) {
                    var data = {};
                    var params = {};
                    var hash = '';
                    var tmp = e.originalEvent.state[name].split('?');
                    if (tmp[1]) {
                        hash = tmp[1];
                        tmp = tmp[1].split('&');
                        for (var i in tmp) {
                            if (tmp.hasOwnProperty(i)) {
                                var tmp2 = tmp[i].split('=');
                                params[tmp2[0]] = tmp2[1];
                            }
                        }
                    }
                    data['params'] = params;
                    data['full_url'] = e.originalEvent.state[name];
                    data['page_url'] = SeoFilter.config.url;
                    SeoFilter.loadMeta(data,hash,'metabyurl');
                }
            });
        }, 1100);
    },

    mFilter2Handlers: function () {
        if(typeof(mSearch2) !== 'undefined') {
            mSearch2.Hash.set = function(vars){SeoFilter.mFilter2HashSet(vars);};

            if(parseInt(this.config.slider)) {
                mSearch2.handleSlider = function () {SeoFilter.mFilter2HandleSlider()};
                mSearch2.handleSlider();
            }

            this.registerPopstate('mSearch2');
        }

    },

    oldbrowser: function () {
        return !(window.history && history.pushState);
    },


    loadMeta: function (params,hash,action) {
        if(!action) {action = 'getmeta';}
        var pageUrl = this.config.url || document.location.pathname;
        var data = {data: params, sf_action: action, pageId: this.config.page};
        $('body').addClass('sf_loading');
        $.post(this.config.actionUrl,data,function (response) {
            $('body').removeClass('sf_loading');
            response.params = params;
            response.action = action;
            $(document).trigger('seofilter_load', response);
            if(response.success) {
                var url = response.data.url;
                SeoFilter.prepareResponseData(response.data);
                switch(action) {
                    case 'metabyurl':
                        var filters = params['params'];
                        if(response.data.params) {
                            filters = $.extend(filters, response.data.params);
                        }
                        if(typeof(mSearch2) !== 'undefined') {
                            mSearch2.setFilters(filters);
                            mSearch2.load(filters);
                        } else {
                            this.setFilters(filters);
                        }
                        break;
                    case 'getmeta':
                        SeoFilter.changeUrl(pageUrl + url);
                        break;
                    case 'meta_results':
                        var all_url = pageUrl + url;
                        SeoFilter.changeUrl(all_url);
                        if (typeof(pdoPage) != 'undefined') {
                            var tmp = all_url.split('?');
                            // if(!params['page']) {
                                pdoPage.keys.page = 0;
                            // }
                            var pdo_config = pdoPage.configs.page;
                            if(response.data.full_url) {
                                // pdo_config = $.extend(pdo_config,{q:response.data.full_url});
                            }
                            pdoPage.loadPage(tmp[0], pdo_config);
                        }
                        break;
                }
            } else {
                SeoFilter.changeUrl(document.location.pathname + hash,hash);
            }
        });
    },

    changeUrl: function (url,hash) {
        if(!this.oldbrowser()) {
            if(typeof(mSearch2) !== 'undefined') {
                window.history.pushState({mSearch2: url}, '', url);
            } else {
                window.history.pushState({SeoFilter: url}, '', url);
            }
        } else if(!hash) {
            window.location = url;
        } else {
            window.location.hash = hash.substr(1);
        }
    },

    updateTitle: function (data) {
        if(data.title) {
            var newtitle = data.title.toString();
            if (parseInt(this.config.replacebefore)) {
                var separator = this.config.replaceseparator || ' / ';
                var title = $('title').text();
                var arr_title = title.split(separator);
                if (arr_title.length > 1) {
                    if (!this.count && data.seo_id) {
                        arr_title.unshift(newtitle);
                        this.count++;
                    } else {
                        if (arr_title[1].toString() === newtitle) {
                            var shift = arr_title.shift();
                            this.count = 0;
                        } else {
                            arr_title[0] = newtitle;
                        }
                    }
                    $(this.config.jtitle).text(arr_title.join(separator));
                } else {
                    if (data.seo_id) {
                        arr_title.unshift(newtitle);
                        this.count++;
                        $(this.config.jtitle).text(arr_title.join(separator));
                    } else {
                        $(this.config.jtitle).text(newtitle);
                    }
                }
            } else {
                $(this.config.jtitle).text(newtitle);
            }
        }
    },

    updateTexts: function (data) {
        if(data.description) {$(this.config.jdescription).attr("content", data.description);}
        if(data.link) {$(this.config.jlink).html(data.link);}
        if(data.h1) {$(this.config.jh1).html(data.h1);}
        if(data.h2) {$(this.config.jh2).html(data.h2);}
        if(data.introtext) {$(this.config.jintrotext).html(data.introtext);}
        if(data.keywords) {$(this.config.jkeywords).html(data.keywords);}
        if(data.text) {$(this.config.jtext).html(data.text);}
        if(data.content) {$(this.config.jcontent).html(data.content);}
        $(document).find('.sf_total').html(data.total);
    },

    updateCrumbs:function (data) {
        if(data.crumbs && parseInt(this.config.crumbs)) {
            var crumbs_separator = $(document).find('.sf_crumb').data('separator');
            var crumbs = $(data.crumbs);
            $.each(crumbs,function (index,value) {
                if($(value).hasClass('sf_crumb_nested')) {
                    $(document).find('.sf_crumb').parent().append(value);
                    if(crumbs_separator) {
                        $(document).find(value).before(crumbs_separator);
                    }
                }
                if($(value).hasClass('sf_crumb')) {
                    $(document).find('.sf_crumb').replaceWith(value);
                    $(document).find('.sf_crumb').data('separator',crumbs_separator);
                    var find_to_del = false;
                    $.each($(document).find('.sf_crumb').parent().contents(),function (i,val) {
                        if(find_to_del) {
                            $(val).remove();
                        }
                        if($(val).hasClass('sf_crumb')) {
                            find_to_del = true;
                        }
                    });
                }
                if($(value).hasClass('sf_crumbs')) {
                    $(document).find('.sf_crumb').parent().append(value);
                    if(crumbs_separator) {
                        $(document).find(value).before(crumbs_separator);
                    }
                }
            });
        }
    },

    prepareResponseData: function (data) {
        this.toggleFrontendManagerButton(data);
        this.updateTitle(data);
        this.updateTexts(data);
        this.updateCrumbs(data);
    },

    toggleFrontendManagerButton: function (data) {
        if($(document).find('.fm-seofilter').length) {
            var fm_link = $(document).find('.fm-seofilter');
            if(data.seo_id) {
                fm_link.attr('href',fm_link.data('url')+data.seo_id);
                $(document).find('.fm-seofilter').removeClass('hidden');
            } else {
                $(document).find('.fm-seofilter').addClass('hidden');
            }
        }
    },

    mFilter2HashSet: function (vars) {
        var hash = '';
        var i;
        for (i in vars) {
            if (vars.hasOwnProperty(i)) {
                hash += '&' + i + '=' + vars[i].toString();
            }
        }
        if (!this.oldbrowser()) {
            if (hash.length !== 0) {
                hash = '?' + hash.substr(1);
                var specialChars = {"%": "%25", "+": "%2B", "&" : "%26"}; //add last char
                for (i in specialChars) {
                    if (specialChars.hasOwnProperty(i) && hash.indexOf(i) !== -1) {
                        hash = hash.replace(new RegExp('\\' + i, 'g'), specialChars[i]);
                    }
                }
            }
        }
        this.loadMeta(vars,hash);
    },

    mFilter2HandleSlider: function () {
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
            var vmin = Number(imin.attr('value'));
            var vmax = Number(imax.attr('value'));
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

            var name = imin.prop('name');
            $this.slider({
                min: vmin,
                max: vmax,
                values: [vmin, vmax],
                range: true,
                step: 1 / delimiter,
                stop: function (e, ui) {
                    imin.val(ui.values[0].toFixed(decimals));
                    imax.val(ui.values[1].toFixed(decimals));
                    imin.add(imax).trigger('change');
                    mSearch2.sliders[name]['user_changed'] = true;
                },
                change: function (e, ui) {
                    if (mSearch2.sliders[name] != undefined && mSearch2.sliders[name]['values'] != undefined) {
                        mSearch2.sliders[name]['changed'] = mSearch2.sliders[name]['values'][0] != ui.values[0].toFixed(decimals) ||
                            mSearch2.sliders[name]['values'][1] != ui.values[1].toFixed(decimals);
                    }
                },
                slide: function (e, ui) {
                    if (decimal) {
                        imin.val(ui.values[0].toFixed(decimals));
                        imax.val(ui.values[1].toFixed(decimals));
                    } else {
                        imin.val(ui.values[0]);
                        imax.val(ui.values[1]);
                    }
                }
            });

            var changed = mSearch2.Hash.get()[name] !== undefined;
            //replace #1
            if(!changed) {
                changed = SeoFilter.config.params[name] !== undefined;
            }
            mSearch2.sliders[name] = {
                changed: changed,
                user_changed: changed
            };

            var values = mSearch2.Hash.get();
            // replace #2
            if (values[name] || SeoFilter.config.params[name]) {
                if (SeoFilter.config.params[name]) {
                    var tmp = SeoFilter.config.params[name].split(mse2Config.values_delimeter);
                } else {
                    var tmp = values[name].split(mse2Config['values_delimeter']);
                }

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
            imin.attr('data-decimal', decimals);
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
                if (e.type == 'change') {
                    mSearch2.sliders[name]['user_changed'] = true;
                }
                $this.slider('values', 0, this.value);
            });
            //imax.attr('readonly', true);
            imax.attr('data-decimal', decimals);
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
            });

            // replace #3
            if (values[name] || SeoFilter.config.params[name]) {
                imin.add(imax).trigger('click');
            }

            mSearch2.sliders[name]['values'] = [vmin, vmax];
        });
        return true;
    }
};
jQuery(document).ready(function ($) {
   SeoFilter.initialize();
});
