jQuery(document).ready(function ($) {
    var seoFilter = {
        config : seoFilterConfig || {},
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

                    if(response.data.title) {
                        if (seoFilter.config.replacebefore) {
                            var separator = seoFilter.config.replaceseparator || ' / ';
                            var title = $('title').text();
                            //console.log(title.indexOf(separator));
                            var arr_title = title.split(separator);
                            if (arr_title.length > 1) {
                                arr_title[0] = response.data.title;
                                $(seoFilter.config.jtitle).text(arr_title.join(separator));
                            } else {
                                $(seoFilter.config.jtitle).text(response.data.title);
                            }
                        } else {
                            $(seoFilter.config.jtitle).text(response.data.title);
                        }
                    }

                    console.log(response);

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
                    //console.log(response);
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
                    // if(aliases.indexOf(i) != -1) {
                    //     if(count || origin[origin.length-1] != '/') {
                    //         hash += '/' + i + seoFilter.config.separator + vars[i];
                    //     } else {
                    //         hash += i + seoFilter.config.separator + vars[i];
                    //     }
                    //     count++;
                    // } else {
                        hash += '&' + i + '=' + vars[i];
                    //}
                }
            }
            //console.log(document.location.pathname);
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