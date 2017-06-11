jQuery(document).ready(function ($) {
    var seoFilter = {
        config : seoFilterConfig || {},
        ajax_post: function (array,hash,browser) {
            console.log(array,hash);
            $.ajax({
                type: 'POST',
                url: this.config.actionUrl,
                dataType: 'json',
                data: {
                    sf_action: 'getmeta',
                    data: array,
                    pageId: this.config.page,
                    hash: hash,
                    aliases: this.config.aliases,
                },
                success: function(response) {
                    var url = hash;
                    var origin = seoFilter.config.url || document.location.pathname;
                    if(response.data.title) {$('title').text(response.data.title);}
                    if(response.data.description) {$('meta[name="description"]').attr("content", response.data.description);}
                    if(response.data.h1) {$('#sf_h1').text(response.data.h1);}
                    if(response.data.url) {url = response.data.url;}
                    //TODO: добавить замену всех тегов

                    if(browser) {
                        window.history.pushState({mSearch2: origin + url}, '', origin + url);
                    }
                    else {
                        window.location.hash = url.substr(1);
                    }

                    console.log(url);
                    console.log(response);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    };





    if(typeof mSearch2 != 'undefined') {
       // mSearch2.afterLoad = function () {
           // console.log('afterload');
            // $(this.options['wrapper']).removeClass(this.options['loading_class']);
            // this.results.css('opacity', 1);
            // this.filters.find('.' + this.options.disabled_class).prop('disabled', false).removeClass(this.options.disabled_class);
            // var name='',value='';
            //
            // if($('#sklad_search input:checked').length == 1) {
            //     name = $('#sklad_search input:checked').attr('name');
            //     value = $('#sklad_search input:checked').val();
            // }
            // ajaxMeta(name,value);
            //
            // setTimeout(function(){
            //     var search = window.location.search.substr(1),
            //         arr = {};
            //     if(search!="") {
            //         search.split('&').forEach(function(item) {
            //             item = item.split('=');
            //             if(item[0] != 'sort' && item[0] != 'page') {
            //                 arr[item[0]] = item[1];
            //             }
            //         });
            //     }
            //     var lengtharr = Object.keys(arr).length;
            //     if($('#mse2_selected').html() != "" || lengtharr) {
            //         $('#total_sklads').removeClass('hidden');
            //         $('#mse2_mfilter select').trigger('refresh');
            //     } else {
            //         $('#total_sklads').addClass('hidden');
            //         $('#mse2_mfilter select').trigger('refresh');
            //     }
            // },10);
        //};

        mSearch2.getFilters =  function () {
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
                        console.log(hash);
                        if (typeof hash[1] == 'undefined') {
                            vars['anchor'] = hash[0];

                            //hash = hashes[i].split('-');
                            //vars[hash[0]] = hashes[i].replace(hash[0]+'-','');
                        }
                        else {
                            vars[hash[0]] = hash[1];
                        }
                    }
                }
                console.log(vars);
                return vars;
            },
            set: function (vars) {
                var hash = '';
                var aliases = seoFilter.config.aliases;
                var count = 0;
                var browser = 0;
                var origin = seoFilter.config.url || document.location.pathname;
                console.log(origin);

                for (var i in vars) {
                    if (vars.hasOwnProperty(i)) {
                        if(aliases.indexOf(i) != -1) {
                            if(count || origin[origin.length-1] != '/') {
                                hash += '/' + i + seoFilter.config.separator + vars[i];
                            } else {
                                hash += i + seoFilter.config.separator + vars[i];
                            }
                            count++;
                        } else {
                            hash += '&' + i + '=' + vars[i];
                        }
                    }
                }
                console.log(document.location.pathname);
                if (!this.oldbrowser()) {
                    if (hash.length != 0) {
                        if(count) {
                            hash = hash.replace('%', '%25').replace('+', '%2B');
                        } else {
                            hash = '?' + hash.substr(1).replace('%', '%25').replace('+', '%2B');
                        }
                    }
                    console.log(hash);
                    console.log(document.location.href);
                    console.log(document.location.origin);
                    //window.history.pushState({mSearch2: document.location.pathname + hash}, '', document.location.pathname + hash);
                    //window.history.pushState({mSearch2: origin + hash}, '', origin + hash); //перемещено в ajax_post
                    browser = 1;
                }
                else {
                    //window.location.hash = hash.substr(1);
                }
                seoFilter.ajax_post(vars, hash, browser);
            },
            set2: function (vars) {
                var hash = '';
                var aliases = seoFilter.config.aliases;
                var check = false;
                console.log(vars);
                for (var i in vars) {
                    if (vars.hasOwnProperty(i)) {
                        if(aliases.indexOf(i) != -1) {
                            if(seoFilter.config.params.indexOf(i) != -1) {
                                if( seoFilter.config.params[i] != vars[i]) {
                                    hash += '&' + i + '=' + vars[i];
                                }
                            }
                            if(seoFilter.config.valuefirst) {
                                hash += vars[i].toLowerCase() + seoFilter.config.params['separator'] + i;
                            } else {
                                hash += i + seoFilter.config.separator + vars[i].toLowerCase();
                            }
                            check = true;

                        } else {
                            hash += '&' + i + '=' + vars[i];
                        }

                    }
                }
                console.log(hash);
                if (!this.oldbrowser()) {
                    if (hash.length != 0) {
                        if(check) {
                            hash = hash.replace('%', '%25').replace('+', '%2B');
                        } else {
                            hash = '?' + hash.substr(1).replace('%', '%25').replace('+', '%2B');
                        }

                    }
                    window.history.pushState({mSearch2: document.location.pathname + hash}, '', document.location.pathname + hash);
                }
                else {
                    window.location.hash = hash.substr(1);
                }

                console.log(hash);
                console.log(vars);
            },
            add: function (key, val) {
                var hash = this.get();
                hash[key] = val;
                this.set(hash);
                console.log(hash);
            },
            remove: function (key) {
                var hash = this.get();
                delete hash[key];
                this.set(hash);
                console.log(hash);
            },
            clear: function () {
                this.set({});
            },
            oldbrowser: function () {
                return !(window.history && history.pushState);
            }
        };
    }
});