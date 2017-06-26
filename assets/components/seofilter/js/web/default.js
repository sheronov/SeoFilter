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
        mSearch2.Hash.set =  function (vars) {
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
        }
    };
});