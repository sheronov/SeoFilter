var SeoFilterLoadAce = function (config) {
    if(!config.height) {
        config.height = 250;
    }
    Ext.getCmp(config.id).setHeight(config.height);
    window.setTimeout(function() {
        MODx.ux.Ace.replaceComponent(config.id, 'text/x-smarty', 1);
        MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
    }, 100);
}

var objectMerge = function(obj1,obj2) {
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
}

var getMetaFields = function (config, scope) {
    var fields = [{
        xtype: 'xcheckbox',
        boxLabel: '<b>'+_('seofilter_seo_custom')+'</b>',
        name: 'custom',
        id: config.id + '-custom'
    }];
    var fields_name = {
        title : {xtype:'textfield', maxLength: 255},
        h1 : {xtype:'textfield' ,maxLength: 255},
        h2: {xtype:'textfield', maxLength: 255},
        description: {},
        introtext: {},
        keywords: {},
        text: {},
        content: {}
    };

    var richtexts = [], aces = [];
    if(SeoFilter.config.content_richtext) {
        var rts = SeoFilter.config.content_richtext.replace(' ','').split(',');
        for(rt_field in rts) {
            if(rts.hasOwnProperty(rt_field)) {
                var field = rts[rt_field];
                if(field.indexOf('Rule.') === -1) {
                    field = field.split(':');
                    if (!field[1]) {
                        field[1] = 0
                    }
                    richtexts[field[0]] = field[1];
                }
            }
        }
    }
    if(SeoFilter.config.content_ace) {
        var ats = SeoFilter.config.content_ace.replace(' ','').split(',');
        for(at_field in ats) {
            if(ats.hasOwnProperty(at_field)) {
                var field = ats[at_field];
                if(field.indexOf('Rule.') === -1 && richtexts.hasOwnProperty(field) === false) {
                    field = field.split(':');
                    if (!field[1]) {
                        field[1] = 0
                    }
                    aces[field[0]] = field[1];
                }
            }
        }
    }

    for(field in fields_name) {
        var field_data = objectMerge({
            xtype: 'textarea',
            name: field,
            fieldLabel: _('seofilter_seometa_'+field),
            id: config.id +'-'+field,
            anchor: '99%',
            description: "[[!+sf."+field+"]] / {$_modx->getPlaceholder('sf."+field+"')}"
        },fields_name[field]);

        if(richtexts.hasOwnProperty(field)) {
            if(richtexts[field]) {field_data['height'] = richtexts[field];}
            field_data['listeners'] = {
                'render': function (config) {
                    if(!config.height) {
                        config.height = 250;
                    }
                    Ext.getCmp(config.id).setHeight(config.height);
                    if(MODx.loadRTE !== 'undefined') {
                        window.setTimeout(function() {
                            if(MODx.loadRTE(config.id)) {
                                var editor =  MODx.loadedRTEs[config.id];;
                                editor.editor.on('change', function() {
                                    Ext.defer(function() {
                                        Ext.getCmp(config.id).el.dom.value = editor.getValue();
                                    }, 10);
                                });
                            }

                        }, 100);
                    }
                },
                // 'render': {fn:this.loadRte,scope:this}
            };
        }
        if(aces.hasOwnProperty(field)) {
            if(aces[field]) {field_data['height'] = aces[field];}
            field_data['listeners'] = {'render': {fn:SeoFilterLoadAce,scope:scope}};
        }

        fields.push(field_data);
    }

    return fields;
}

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
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,autoHeight: false
            ,items: [{
                title: _('seofilter_url_data') || 'Свойства страницы'
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_link'),
                    name: 'link',
                    id: config.id + '-link',
                    anchor: '99%',
                    description: _('seofilter_url_link_help'),
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
                            anchor: '99%',
                            style: 'background:#f9f9f9;color:#aaa;',
                            description: "[[!+sf.url]] / {$_modx->getPlaceholder('sf.url')}",
                        }, {
                            xtype: 'seofilter-combo-rule',
                            fieldLabel: _('seofilter_url_multi_id'),
                            name: 'multi_id',
                            id: config.id + '-multi_id',
                            anchor: '99%',
                            allowBlank: false,
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
                        }, {
                            xtype: 'seofilter-combo-resource',
                            fieldLabel: _('seofilter_url_page_id'),
                            name: 'page_id',
                            hiddenName: 'page_id',
                            id: config.id + '-page_id',
                            anchor: '99%',
                            allowBlank: false,
                        }]
                    }]
                }, {
                    title: _('seofilter_url_urlword')
                    ,xtype: 'seofilter-grid-urlword'
                    ,record: {id: 0}
                    ,anchor: '99%'
                }, {
                    title: _('seofilter_url_menu_data') || 'Параметры для использования в sfMenu'
                    , xtype: 'fieldset'
                    , forceLayout: true
                    , autoHeight: true
                    , border: false
                    , items: [{
                        layout: 'column'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , anchor: '99%'
                        , items: [{
                            columnWidth: .64
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
                            },{
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_url_active_more'),
                                name: 'active',
                                id: config.id + '-active',
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
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_url_menu_on'),
                                name: 'menu_on',
                                id: config.id + '-menu_on',
                                labelStyle:'margin-top:-5px;',
                                description: _('seofilter_url_menu_on_help'),

                            },{
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_url_recount') || 'Пересчитать результаты',
                                name: 'recount',
                                id: config.id + '-recount',
                                labelStyle:'margin-top:-6px;',
                                description: _('seofilter_url_recount_help'),
                            }]
                        }]
                    }]
                }]
            },{
                title: _('seofilter_url_meta') || 'Индивидуальные мета-теги'
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border: false
                ,items: getMetaFields(config, this)
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
        //     'failure': {fn:this.failure,scope:this},
        //     'success': {fn:this.successLoad,scope:this},
        //     'beforeSubmit': {fn:this.beforeSubmit,scope:this}
        },
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });

    SeoFilter.window.UpdateUrls.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateUrls, MODx.Window, {


    loadRte: function (config) {
        if(!config.height) {
            config.height = 250;
        }
        Ext.getCmp(config.id).setHeight(config.height);
        if(MODx.loadRTE !== 'undefined') {
            window.setTimeout(function() {
                MODx.loadRTE(config.id);
            }, 300);
        }
    },

    getFields: function (config) {
        return [{
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,autoHeight: false
            ,items: [{
                title: _('seofilter_url_data')  || 'Свойства страницы'
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    xtype: 'hidden',
                    name: 'id',
                    id: config.id + '-id',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_link'),
                    name: 'link',
                    id: config.id + '-link',
                    anchor: '99%',
                    description: _('seofilter_url_link_help'),
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
                },{
                    title: _('seofilter_url_menu_data') || 'Параметры для использования в sfMenu'
                    , xtype: 'fieldset'
                    , forceLayout: true
                    , autoHeight: true
                    , border: false
                    , items: [{
                        layout: 'column'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , anchor: '99%'
                        , items: [{
                            columnWidth: .64
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
                            },{
                                xtype: 'numberfield',
                                fieldLabel: _('seofilter_url_total_more') || 'Результатов',
                                name: 'total',
                                //readOnly: true,
                                id: config.id + '-total',
                                description: '{$total}',
                                anchor: '99%',
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
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_url_menu_on'),
                                name: 'menu_on',
                                id: config.id + '-menu_on',
                                labelStyle:'margin-top:-5px;',
                                description: _('seofilter_url_menu_on_help'),

                            },{
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_url_recount') || 'Пересчитать результаты',
                                name: 'recount',
                                id: config.id + '-recount',
                                labelStyle:'margin-top:-6px;',
                                description: _('seofilter_url_recount_help'),
                            }]
                        }]
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
                            xtype: 'numberfield',
                            fieldLabel: _('seofilter_url_count'),
                            name: 'count',
                            //readOnly: true,
                            id: config.id + '-count',
                            anchor: '99%',
                        },{
                            xtype: 'seofilter-combo-rule',
                            fieldLabel: _('seofilter_url_multi_id'),
                            name: 'multi_id',
                            id: config.id + '-multi_id',
                            anchor: '99%',
                            allowBlank: false,
                            readOnly: true,
                            style: 'background:#f9f9f9;color:#aaa;',
                        }, {
                            xtype: 'textfield',
                            fieldLabel: _('seofilter_url_createdon'),
                            name: 'createdon',
                            id: config.id + '-createdon',
                            anchor: '99%',
                            readOnly: true,
                            style: 'background:#f9f9f9;color:#aaa;',
                            description: "[[!+sf.createdon]] / {$_modx->getPlaceholder('sf.createdon')}",
                        }]
                    }, {
                        columnWidth: .5
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: 'numberfield',
                            fieldLabel: _('seofilter_url_ajax'),
                            name: 'ajax',
                            id: config.id + '-ajax',
                            //readOnly: true,
                            anchor: '99%',
                        },{
                            xtype: 'seofilter-combo-resource',
                            fieldLabel: _('seofilter_url_page_id'),
                            name: 'page_id',
                            hiddenName: 'page_id',
                            id: config.id + '-page_id',
                            anchor: '99%',
                            allowBlank: false,
                            readOnly: true,
                            style: 'background:#f9f9f9;color:#aaa;',
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
                            boxLabel: _('seofilter_url_active_more'),
                            name: 'active',
                            id: config.id + '-active',
                        }]
                    }]
                // },{
                //     html: '<b>' + _('seofilter_url_urlword') + '</b>' + ' <span style="float:right;">'+ _('seofilter_urlword_word_edit') +'</span>',
                //     cls: '',
                //     style: {margin: '15px 0 5px',color: '#555',width: '99%'}
                //     ,anchor: '99%'
                }, {
                    title: _('seofilter_url_urlword')
                    ,xtype: 'seofilter-grid-urlword'
                    ,record: config.record.object
                    ,anchor: '99%'
                }]
            },{
                title: _('seofilter_url_meta') || 'Индивидуальные мета-теги'
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border: false
                ,items: getMetaFields(config, this)
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-url-window-update', SeoFilter.window.UpdateUrls);
