jQuery(document).ready(function ($) {
    var seoFilter = {
        config : seoFilterConfig || {},
        count :  Object.keys(seoFilterConfig.params).length || 0,
        ajax_post: function (array,hash,browser,action) {
            $.ajax({
                type: 'POST',
                url: this.config.actionUrl,
                dataType: 'json',
                data: {
                    sf_action: action,
                    data: array,
                    pageId: this.config.page,
                    hash: hash,
                    aliases: this.config.aliases,
                },
                success: function(response) {
                    var url = hash;
                    var origin = seoFilter.config.url || document.location.pathname;
                    //для frontendmanager
                    if($(document).find('.fm-seofilter').length) {
                        var fm_link = $(document).find('.fm-seofilter');
                        if(response.data.seo_id) {
                            fm_link.attr('href',fm_link.data('url')+response.data.seo_id);
                            $(document).find('.fm-seofilter').removeClass('hidden');
                        } else {
                            $(document).find('.fm-seofilter').addClass('hidden');
                        }
                    }

                    if(response.data.title) {
                        var newtitle = response.data.title.toString();
                        if (parseInt(seoFilter.config.replacebefore)) {
                            var separator = seoFilter.config.replaceseparator || ' / ';
                            var title = $('title').text();
                            var arr_title = title.split(separator);
                            if (arr_title.length > 1) {
                                if(!seoFilter.count && response.data.seo_id) {
                                    arr_title.unshift(newtitle);
                                    seoFilter.count++;
                                } else {
                                    if(arr_title[1].toString() === newtitle) {
                                        var shift = arr_title.shift()
                                        seoFilter.count = 0;
                                    } else {
                                        arr_title[0] = newtitle;
                                    }
                                }
                                $(seoFilter.config.jtitle).text(arr_title.join(separator));
                            } else {
                                if(response.data.seo_id) {
                                    arr_title.unshift(newtitle);
                                    seoFilter.count++;
                                    $(seoFilter.config.jtitle).text(arr_title.join(separator));
                                } else {
                                    $(seoFilter.config.jtitle).text(newtitle);
                                }
                            }
                        } else {
                            $(seoFilter.config.jtitle).text(newtitle);
                        }
                    }

                    if(response.data.description) {$(seoFilter.config.jdescription).attr("content", response.data.description);}
                    if(response.data.link) {$(seoFilter.config.jlink).html(response.data.link);}
                    if(response.data.h1) {$(seoFilter.config.jh1).html(response.data.h1);}
                    if(response.data.h2) {$(seoFilter.config.jh2).html(response.data.h2);}
                    if(response.data.introtext) {$(seoFilter.config.jintrotext).html(response.data.introtext);}
                    if(response.data.keywords) {$(seoFilter.config.jkeywords).html(response.data.keywords);}
                    if(response.data.text) {$(seoFilter.config.jtext).html(response.data.text);}
                    if(response.data.content) {$(seoFilter.config.jcontent).html(response.data.content);}
                    if(response.data.url) {url = response.data.url;}

                    if(response.data.crumbs && parseInt(seoFilterConfig.crumbs)) {
                        var crumbs_separator = $(document).find('.sf_crumb').data('separator');
                        var crumbs = $(response.data.crumbs);
                        $.each(crumbs,function (index,value) {
                            if($(value).hasClass('sf_crumb')) {
                                $(document).find('.sf_crumb').replaceWith(value);
                                $(document).find('.sf_crumb').data('separator',crumbs_separator);
                                // if(!response.data.find) {
                                    var find_to_del = false;
                                    $.each($(document).find('.sf_crumb').parent().contents(),function (i,val) {
                                        if(find_to_del) {
                                            $(val).remove();
                                        }
                                       if($(val).hasClass('sf_crumb')) {
                                           find_to_del = true;
                                       }
                                    });
                                // }
                            }
                            if($(value).hasClass('sf_crumbs')) {
                                $(document).find('.sf_crumb').after(value);
                                if(crumbs_separator) {
                                    $(document).find('.sf_crumb').after(crumbs_separator);
                                }
                            }
                        });
                    }

                    if(action === 'metabyurl') {
                        var params = array.params;
                        if(response.data.params) {
                            params = $.extend(params, response.data.params);
                        }
                        mSearch2.setFilters(params);
                        mSearch2.load(params);
                    } else {
                        if (browser) {
                            window.history.pushState({mSearch2: origin + url}, '', origin + url);
                        }
                        else {
                            window.location.hash = url.substr(1);
                        }
                    }
                },
                error: function (response) {
                    //console.log(response);
                }
            });
        }
    };


    if(typeof mSearch2 != 'undefined') {


        window.setTimeout(function() {
            $(window).unbind('popstate');
            $(window).on('popstate', function (e) {
                if (e.originalEvent.state && e.originalEvent.state['mSearch2']) {
                    var data = {};
                    var params = {};
                    var hash = '';
                    var browser = mSearch2.Hash.oldbrowser();
                    var tmp = e.originalEvent.state['mSearch2'].split('?');
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
                    data['full_url'] = e.originalEvent.state['mSearch2'];
                    data['page_url'] = seoFilter.config.url;

                    seoFilter.ajax_post(data,hash,browser,'metabyurl');

                    // mSearch2.setFilters(params);
                    // mSearch2.load(params);
                    // console.log(params);
                }
            });
        }, 1100);

        mSearch2.Hash.set = function (vars) {
            var hash = '';
            var aliases = seoFilter.config.aliases;
            var count = 0;
            var browser = 0;
            var origin = seoFilter.config.url || document.location.pathname;

            for (var i in vars) {
                if (vars.hasOwnProperty(i)) {
                    hash += '&' + i + '=' + vars[i];
                }
            }

            if (!this.oldbrowser()) {
                if (hash.length != 0) {
                    if (count) {
                        hash = hash.replace('%', '%25').replace('+', '%2B');
                    } else {
                        hash = '?' + hash.substr(1).replace('%', '%25').replace('+', '%2B');
                    }
                }
                browser = 1;
            }
            seoFilter.ajax_post(vars, hash, browser, 'getmeta');
        };

        if (parseInt(seoFilterConfig.slider)) {
            mSearch2.handleSlider = function () {
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
                    mSearch2.sliders[name] = {
                        changed: changed,
                        user_changed: changed
                    };

                    var values = mSearch2.Hash.get();
                    // замена #1
                    if (values[name] || seoFilterConfig.params[name]) {
                        if (seoFilterConfig.params[name]) {
                            var tmp = seoFilterConfig.params[name].split(mse2Config.values_delimeter);
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

                    // замена #2
                    if (values[name] || seoFilterConfig.params[name]) {
                        imin.add(imax).trigger('click');
                    }

                    mSearch2.sliders[name]['values'] = [vmin, vmax];
                });
                return true;
            }
            mSearch2.handleSlider();
            /*
            mSearch2.handleSlider = function () {
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

                    $this.slider({
                        min: vmin,
                        max: vmax,
                        values: [vmin, vmax],
                        range: true,
                        step: 1 / delimiter,
                        stop: function (e, ui) {
                            if (decimal) {
                                imin.val(ui.values[0].toFixed(decimals));
                                imax.val(ui.values[1].toFixed(decimals));
                            } else {
                                imin.val(ui.values[0]);
                                imax.val(ui.values[1]);
                            }
                            imin.trigger('change');
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

                    var name = imin.prop('name');
                    var values = mSearch2.Hash.get();
                    // if (values[name]) {
                    //     var tmp = values[name].split(mse2Config.values_delimeter);
                    if (values[name] || seoFilterConfig.params[name]) {
                        if (seoFilterConfig.params[name]) {
                            var tmp = seoFilterConfig.params[name].split(mse2Config.values_delimeter);
                        } else {
                            var tmp = values[name].split(mse2Config.values_delimeter);
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
                    });
                    //imax.attr('readonly', true);
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

                    if (values[name] || seoFilterConfig.params[name]) {
                        imin.add(imax).trigger('click');
                    }

                    mSearch2.sliders[name] = [vmin, vmax];
                });
                return true;
            };
            mSearch2.handleSlider();
            */
        }
    };
});