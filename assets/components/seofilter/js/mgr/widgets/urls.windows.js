SeoFilter.window.CreateUrls = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-url-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_url_create'),
        width: 600,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateUrls.superclass.constructor.call(this, config);
};

Ext.extend(SeoFilter.window.CreateUrls, MODx.Window, {

    getFields: function (config) {
        return [{
            layout: 'column',
            border: false,
            anchor: '100%',
            items: [{
                xtype: 'textfield',
                fieldLabel: _('seofilter_url_link'),
                name: 'link',
                id: config.id + '-link',
                anchor: '99%',
            },{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_multi_id'),
                        name: 'multi_id',
                        id: config.id + '-multi_id',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_old_url'),
                        name: 'old_url',
                        id: config.id + '-old_url',
                        anchor: '99%',
                    }, {

                        xtype: 'numberfield',
                        fieldLabel: _('seofilter_url_count'),
                        name: 'count',
                        id: config.id + '-count',
                        anchor: '99%',
                    }
                ]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'xcheckbox',
                    boxLabel: _('seofilter_url_active'),
                    name: 'active',
                    id: config.id + '-active',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_new_url'),
                    name: 'new_url',
                    id: config.id + '-new_url',
                    anchor: '99%',
                }, {
                //     xtype: 'textfield',
                //     fieldLabel: _('seofilter_url_createdon'),
                //     name: 'createdon',
                //     id: config.id + '-createdon',
                //     anchor: '99%',
                // }, {
                //     xtype: 'textfield',
                //     fieldLabel: _('seofilter_url_editedon'),
                //     name: 'editedon',
                //     id: config.id + '-editedon',
                //     anchor: '99%',
                // }, {
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_ajax'),
                    name: 'ajax',
                    id: config.id + '-ajax',
                    anchor: '99%',
                }
                ]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-url-window-create', SeoFilter.window.CreateUrls);

SeoFilter.window.UpdateUrls = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-url-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_url_update'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/update',
        fields: this.getFields(config),
        buttons: [{
            text: config.cancelBtnText || _('cancel')
            ,scope: this
            ,handler: function() { config.closeAction !== 'close' ? this.hide() : this.close(); }
        },{
            text: config.saveBtnText || _('save')
            ,cls: 'primary-button'
            ,scope: this
            ,handler: this.submit
        }],

        listeners: {
            // render: function () {
            //     console.log('render');
            //     MODx.loadRTE('ta'); // id поля
            // }
        //     'failure': {fn:this.failure,scope:this},
        //     'success': {fn:this.success,scope:this},
        //     'beforeSubmit': {fn:this.beforeSubmit,scope:this}
        },
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateUrls.superclass.constructor.call(this, config);

    // // this.addEvents('beforeSubmit');
    // this.on('show', function() {
    //     if(MODx.loadRTE !== 'undefined') {
    //         MODx.loadRTE('ta');
    //         this.rteLoaded = true;
    //     }
    //     MODx.sleep(4);
    // }.bind(this));
};
Ext.extend(SeoFilter.window.UpdateUrls, MODx.Window, {
    rteLoaded: false,

    /*
    beforeSubmit: function (config,values) {
        console.log(this.rteLoaded);
        console.log(this.fp.rteLoaded);
        console.log('beforeSubmit',config,values);
        if(this.rteLoaded) {
            var content = Ext.get(config.scope.options.id + '-content');
            if (content) {
                var v = content.dom.value;
                console.log(content);
                console.log(v);
                var hc = Ext.getCmp(config.scope.options.id + '-hiddenContent');
                console.log(hc);
                if (hc) {
                    hc.setValue(v);
                }
            }
        }
        return true;
    },

    submit: function(close) {
        console.log('submit',close);
        var config = close;
        close = close === false ? false : true;
        var f = this.fp.getForm();
        if (f.isValid() && this.beforeSubmit(config,f.getValues())) {
            f.submit({
                waitMsg: _('saving')
                ,submitEmptyText: this.config.submitEmptyText !== false
                ,scope: this
                ,failure: function(frm,a) {
                    if (this.fireEvent('failure',{f:frm,a:a})) {
                        MODx.form.Handler.errorExt(a.result,frm);
                    }
                    this.doLayout();
                }
                ,success: function(frm,a) {
                    if (this.config.success) {
                        Ext.callback(this.config.success,this.config.scope || this,[frm,a]);
                    }
                    this.fireEvent('success',{f:frm,a:a});
                    if (close) { this.config.closeAction !== 'close' ? this.hide() : this.close(); }
                    this.doLayout();
                }
            });
        }
    },

    */

    loadRte: function (config) {
        if(SeoFilter.config.richtext) {
            if(MODx.loadRTE !== 'undefined') {
                window.setTimeout(function() {
                    MODx.loadRTE(config.id);
                }, 300);
                // console.log(config);
                this.rteLoaded = true;
                // config.rteLoaded = true;
            }
        } else {
            window.setTimeout(function() {
                MODx.ux.Ace.replaceComponent(config.id, 'text/x-smarty', 1);
                MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
                Ext.getCmp(config.id).setHeight(300);
            }, 100);
        }
    },

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('seofilter_url_link'),
            name: 'link',
            id: config.id + '-link',
            anchor: '99%',
            description: "[[!+sf.link]] / {$_modx->getPlaceholder('sf.link')}",
        },{
            layout: 'column',
            border: false,
            anchor: '99%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_old_url'),
                    name: 'old_url',
                    id: config.id + '-old_url',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#aaa;',
                    anchor: '99%',
                    description: "[[!+sf.url]] / {$_modx->getPlaceholder('sf.url')}",
                }]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_new_url'),
                    name: 'new_url',
                    id: config.id + '-new_url',
                    anchor: '99%',
                    description: "[[!+sf.url]] / {$_modx->getPlaceholder('sf.url')}",
                }]
            }]
        }, {
            html: '<b>' + _('seofilter_url_urlword') + '</b>' + ' <span style="float:right;">'+ _('seofilter_urlword_word_edit') +'</span>',
            cls: '',
            style: {margin: '15px 0 5px',color: '#555',width: '99%'}
            ,anchor: '99%'
        }, {
            title: _('seofilter_url_urlword')
            ,xtype: 'seofilter-grid-urlword'
            ,record: config.record.object
            ,anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_seo_custom'),
            name: 'custom',
            id: config.id + '-custom',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        }, {
            layout:'form'
            ,defaults: { msgTarget: 'under' }
            ,border:false
            ,items: [{
                xtype: 'textfield',
                fieldLabel: _('seofilter_seometa_title'),
                name: 'title',
                id: config.id + '-title',
                anchor: '99%',
                description: "[[!+sf.title]] / {$_modx->getPlaceholder('sf.title')}",
            }, {
                xtype: 'textfield',
                fieldLabel: _('seofilter_seometa_h1'),
                name: 'h1',
                id: config.id + '-h1',
                anchor: '99%',
                description: "[[!+sf.h1]] / {$_modx->getPlaceholder('sf.h1')}",
            },{
                xtype: 'textfield',
                fieldLabel: _('seofilter_seometa_h2'),
                name: 'h2',
                id: config.id + '-h2',
                anchor: '99%',
                description: "[[!+sf.h2]] / {$_modx->getPlaceholder('sf.h2')}",
            }, {
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_description'),
                name: 'description',
                id: config.id + '-description',
                anchor: '99%',
                description: "[[!+sf.description]] / {$_modx->getPlaceholder('sf.description')}",
            }, {
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_introtext'),
                name: 'introtext',
                id: config.id + '-introtext',
                anchor: '99%',
                description: "[[!+sf.introtext]] / {$_modx->getPlaceholder('sf.introtext')}",
            },{
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_keywords'),
                name: 'keywords',
                id: config.id + '-keywords',
                anchor: '99%',
                description: "[[!+sf.keywords]] / {$_modx->getPlaceholder('sf.keywords')}",
            },{
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_text'),
                name: 'text',
                id: config.id + '-text',
                anchor: '99%',
                description: "[[!+sf.text]] / {$_modx->getPlaceholder('sf.text')}",
            // }, {
            //     layout: 'form',
            //     border:false,
            //     style: {margin: 0},
            //     columnWidth: 1,
            //     items: [{
            //         xtype: 'hidden'
            //         ,name: 'content'
            //         ,id: config.id + '-hiddenContent'
            //     },{
            //         xtype: 'textarea',
            //         fieldLabel: '',
            //         name: 'ta',
            //         grow: 'false',
            //         height:300,
            //         id: 'ta',
            //         anchor: '99%',
            //         listeners: {
            //             render: function () {
            //                 // MODx.loadRTE("ta");
            //             }
            //         }
            //     }]
            }, {
                xtype: SeoFilter.config.content_xtype || 'textarea',
                heght:300,
                fieldLabel: _('seofilter_seometa_content'),
                name: 'content',
                id: config.id + '-content',
                listeners: {
                    'render': {fn:this.loadRte,scope:this},
                    // render: function (e,v) {
                    //
                    // }
                },
                anchor: '99%',
                description: "[[!+sf.content]] / {$_modx->getPlaceholder('sf.content')}",
            }]
        }, {
            layout: 'column',
            border: false,
            anchor: '99%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'seofilter-combo-rule',
                    fieldLabel: _('seofilter_url_multi_id'),
                    name: 'multi_id',
                    id: config.id + '-multi_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#aaa;',
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_count'),
                    name: 'count',
                    //readOnly: true,
                    id: config.id + '-count',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_createdon'),
                    name: 'createdon',
                    id: config.id + '-createdon',
                    anchor: '99%',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#aaa;',
                    description: "[[!+sf.createdon]] / {$_modx->getPlaceholder('sf.createdon')}",
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_total_more'),
                    name: 'total',
                    //readOnly: true,
                    id: config.id + '-total',
                    anchor: '99%',
                }]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'seofilter-combo-resource',
                    fieldLabel: _('seofilter_url_page_id'),
                    name: 'page_id',
                    hiddenName: 'page_id',
                    id: config.id + '-page_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#aaa;',
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_ajax'),
                    name: 'ajax',
                    id: config.id + '-ajax',
                    //readOnly: true,
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_editedon'),
                    name: 'editedon',
                    id: config.id + '-editedon',
                    anchor: '99%',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#aaa;',
                    description: "[[!+sf.editedon]] / {$_modx->getPlaceholder('sf.editedon')}",
                },{
                    xtype: 'xcheckbox',
                    boxLabel: _('seofilter_url_recount'),
                    name: 'recount',
                    id: config.id + '-recount',
                },{
                    xtype: 'xcheckbox',
                    boxLabel: _('seofilter_url_active_more'),
                    name: 'active',
                    id: config.id + '-active',
                }]
            }]
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_url_menu_on'),
            name: 'menu_on',
            id: config.id + '-menu_on',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        }, {
            layout: 'column'
            , defaults: {msgTarget: 'under'}
            , border: false
            , anchor: '99%'
            , items: [{
                columnWidth: .65
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_menutitle'),
                    name: 'menutitle',
                    id: config.id + '-menutitle',
                    anchor: '99%',
                    description: "{$menutitle}",
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_image'),
                    name: 'image',
                    id: config.id + '-image',
                    anchor: '99%',
                    description: "{$image}",
                }]
            },{
                columnWidth: .35
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_link_attributes'),
                    name: 'link_attributes',
                    id: config.id + '-link_attributes',
                    anchor: '99%',
                    description: "{$link_attributes}",
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_menuindex'),
                    name: 'menuindex',
                    id: config.id + '-menuindex',
                    anchor: '99%',
                    description: "{$menuindex}",

                }]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-url-window-update', SeoFilter.window.UpdateUrls);
