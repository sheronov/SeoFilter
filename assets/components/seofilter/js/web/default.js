jQuery(document).ready(function ($) {
    var seoFilter = {
        config : seoFilterConfig || {},
        count :  Object.keys(seoFilterConfig.params).length || 0,
        ajax_post: function (array,hash,browser) {
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
                        if (seoFilter.config.replacebefore) {
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
                    if(response.data.h1) {$(seoFilter.config.jh1).html(response.data.h1);}
                    if(response.data.h2) {$(seoFilter.config.jh2).html(response.data.h2);}
                    if(response.data.introtext) {$(seoFilter.config.jintrotext).html(response.data.introtext);}
                    if(response.data.text) {$(seoFilter.config.jtext).html(response.data.text);}
                    if(response.data.content) {$(seoFilter.config.jcontent).html(response.data.content);}
                    if(response.data.url) {url = response.data.url;}

                    if(browser) {
                        window.history.pushState({mSearch2: origin + url}, '', origin + url);
                    }
                    else {
                        window.location.hash = url.substr(1);
                    }
                },
                error: function (response) {
                    //console.log(response);
                }
            });
        }
    };


    if(typeof mSearch2 != 'undefined') {

        mSearch2.Hash.set =  function (vars) {
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
                    if(count) {
                        hash = hash.replace('%', '%25').replace('+', '%2B');
                    } else {
                        hash = '?' + hash.substr(1).replace('%', '%25').replace('+', '%2B');
                    }
                }
                browser = 1;
            }
            seoFilter.ajax_post(vars, hash, browser);
        }
    };
});