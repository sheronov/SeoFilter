SeoFilter.window.CreateRule = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-rule-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_rule_create'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateRule.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateRule, MODx.Window, {

    getFields: function (config) {
        var xtype_count = 'hidden';
        if(parseInt(SeoFilter.config.count_childrens)!==0) {
            xtype_count = 'textfield';
        }
        return {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,items: [{
                title: _('seofilter_rule')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_name'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_link_tpl'),
                    name: 'link_tpl',
                    id: config.id + '-link_tpl',
                    anchor: '99%',
                    description: "[[!+sf.link]] / {$_modx->getPlaceholder('sf.link')}",
                    maxLength: 255,
                },{
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    style: 'margin-top:-10px',
                    items: [{
                        columnWidth: .55
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count==='hidden'?'hidden':'xcheckbox',
                            boxLabel: _('seofilter_rule_recount_new'),
                            name: 'recount',
                            id: config.id + '-recount',
                        }]
                    }]
                }, {
                    title: _('seofilter_rule_fields')
                    ,xtype: 'seofilter-grid-fieldids'
                    ,id: config.id+'_gridfield'
                    ,idNumber: config.id
                    ,record: {id:0}
                },{
                    xtype: 'textfield',
                    hideLabel:true,
                    fieldLabel: _('seofilter_rule_url_more'),
                    name: 'url',
                    readOnly: true,
                    disabled:true,
                    style: 'margin-top:-11px;',
                    emptyText: _('seofilter_rule_url_more'),
                    id: config.id + '-url',
                    anchor: '100%',
                    description: "[[!+sf.url]] / {$_modx->getPlaceholder('sf.url')}",
                // },{
                //     title: _('seofilter_fieldids')
                //     , xtype: 'displayfield'
                //     , html: _('seofilter_fieldids_after_save')
                },{
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    items: [{
                        columnWidth: .7
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                                xtype: xtype_count,
                                fieldLabel: _('seofilter_rule_count_where'),
                                name: 'count_where',
                                id: config.id + '-count_where',
                                anchor: '99%',
                                maxLength: 255,
                            }, {
                                xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
                                fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
                                name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
                                allowBlank: false,
                                id: parseInt(SeoFilter.config.proMode)? config.id + '-pages':config.id + '-page',
                                anchor: '99%',
                                maxLength: 255,
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_rule_base_more'),
                                name: 'base',
                                id: config.id + '-base',
                            }
                        ]
                    }, {
                        columnWidth: .3
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                                xtype: xtype_count,
                                fieldLabel: _('seofilter_rule_count_parents'),
                                name: 'count_parents',
                                id: config.id + '-count_parents',
                                anchor: '99%',
                                maxLength: 255,
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('seofilter_rule_rank'),
                                name: 'rank',
                                id: config.id + '-rank',
                                anchor: '99%',
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_rule_active'),
                                name: 'active',
                                id: config.id + '-active',
                            }
                        ]
                    }]
                }]
            }, {
                title: _('seofilter_seo')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    html: _('seofilter_multiseo_intro'),
                    cls: 'panel-desc',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_title'),
                    name: 'title',
                    id: config.id + '-title',
                    anchor: '99%',
                    description: "[[!+sf.title]] / {$_modx->getPlaceholder('sf.title')}",
                    maxLength: 255,
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_h1'),
                    name: 'h1',
                    id: config.id + '-h1',
                    anchor: '99%',
                    description: "[[!+sf.h1]] / {$_modx->getPlaceholder('sf.h1')}",
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_h2'),
                    name: 'h2',
                    id: config.id + '-h2',
                    anchor: '99%',
                    description: "[[!+sf.h2]] / {$_modx->getPlaceholder('sf.h2')}",
                    maxLength: 255,
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
                }, {
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
                }, {
                    xtype: 'textarea',
                    heght:300,
                    fieldLabel: _('seofilter_seometa_content'),
                    name: 'content',
                    id: config.id + '-content',
                    listeners: {
                        render: function () {
                            window.setTimeout(function() {
                                MODx.ux.Ace.replaceComponent(config.id+'-content', 'text/x-smarty', 1);
                                MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
                                Ext.getCmp(config.id+'-content').setHeight(200);
                            }, 100);
                        }
                    },
                    anchor: '99%',
                    description: "[[!+sf.content]] / {$_modx->getPlaceholder('sf.content')}",
                }]
            }]
        };
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-rule-window-create', SeoFilter.window.CreateRule);


SeoFilter.window.UpdateRule = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-rule-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_rule_update'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });

    SeoFilter.window.UpdateRule.superclass.constructor.call(this, config)
};

Ext.extend(SeoFilter.window.UpdateRule, MODx.Window, {
    getFields: function (config) {
        var xtype_count = 'hidden';
        if(parseInt(SeoFilter.config.count_childrens) !== 0) {
            xtype_count = 'textfield';
        }


        var tabs = {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: false
            ,items: [{
                title: _('seofilter_rule')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    xtype: 'hidden',
                    name: 'id',
                    id: config.id + '-id',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_name'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_link_tpl'),
                    name: 'link_tpl',
                    id: config.id + '-link_tpl',
                    description: "[[!+sf.link]] / {$_modx->getPlaceholder('sf.link')}",
                    anchor: '99%',
                    maxLength: 255,
                },{
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    style: 'margin-top:-10px',
                    items: [{
                        columnWidth: .45
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_relinks'),
                            name: 'relinks',
                            id: config.id + '-relinks',
                        }]
                    },{
                        columnWidth: .55
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count==='hidden'?'hidden':'xcheckbox',
                            boxLabel: _('seofilter_rule_recount'),
                            name: 'recount',
                            id: config.id + '-recount',
                        }]
                    }]
                }, {
                    title: _('seofilter_rule_fields')
                    ,xtype: 'seofilter-grid-fieldids'
                    ,id: config.id+'_gridfield'
                    ,idNumber: config.id
                    ,record: config.record.object
                },{
                    xtype: 'textfield',
                    hideLabel:true,
                    fieldLabel: _('seofilter_rule_url_more'),
                    name: 'url',
                    readOnly: true,
                    disabled:true,
                    style: 'margin-top:-11px;',
                    description: "[[!+sf.url]] / {$_modx->getPlaceholder('sf.url')}",
                    emptyText: _('seofilter_rule_url_more'),
                    id: config.id + '-url',
                    anchor: '100%',
                },{
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    items: [{
                        columnWidth: .7
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count,
                            fieldLabel: _('seofilter_rule_count_where'),
                            name: 'count_where',
                            id: config.id + '-count_where',
                            anchor: '99%',
                            maxLength: 255,
                        },{
                            xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
                            fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
                            name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
                            allowBlank: false,
                            id: parseInt(SeoFilter.config.proMode)? config.id + '-pages':config.id + '-page',
                            anchor: '99%',
                            maxLength: 255,

                        }, {
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_base_more'),
                            name: 'base',
                            id: config.id + '-base',
                        }]
                    }, {
                        columnWidth: .3
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count,
                            fieldLabel: _('seofilter_rule_count_parents'),
                            name: 'count_parents',
                            id: config.id + '-count_parents',
                            anchor: '99%',
                            maxLength: 255,
                        }, {
                            xtype: 'numberfield',
                            fieldLabel: _('seofilter_rule_rank'),
                            name: 'rank',
                            id: config.id + '-rank',
                            anchor: '99%',
                        }, {
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_active'),
                            name: 'active',
                            id: config.id + '-active',
                        }]
                    }]

                }]
            }, {
                title: _('seofilter_seo')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    html: _('seofilter_multiseo_intro'),
                    cls: 'panel-desc',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_title'),
                    name: 'title',
                    id: config.id + '-title',
                    description: "[[!+sf.title]] / {$_modx->getPlaceholder('sf.title')}",
                    anchor: '99%',
                    maxLength: 255,
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_h1'),
                    name: 'h1',
                    id: config.id + '-h1',
                    anchor: '99%',
                    description: "[[!+sf.h1]] / {$_modx->getPlaceholder('sf.h1')}",
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_seometa_h2'),
                    name: 'h2',
                    id: config.id + '-h2',
                    anchor: '99%',
                    description: "[[!+sf.h2]] / {$_modx->getPlaceholder('sf.h2')}",
                    maxLength: 255,
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
                }, {
                    xtype: 'textarea',
                    heght:300,
                    fieldLabel: _('seofilter_seometa_content'),
                    name: 'content',
                    id: config.id + '-content',
                    listeners: {
                        render: function () {
                            window.setTimeout(function() {
                                MODx.ux.Ace.replaceComponent(config.id+'-content', 'text/x-smarty', 1);
                                MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
                                Ext.getCmp(config.id+'-content').setHeight(200);
                            }, 100);
                        }
                    },
                    anchor: '99%',
                    description: "[[!+sf.content]] / {$_modx->getPlaceholder('sf.content')}",
                }]
            }]
        };

        if(parseInt(SeoFilter.config.hiddenTab)) {
            tabs.items.push({
                title: _('seofilter_rule_properties')
                , hideMode: 'offsets'
                , layout: 'form'
                , border: false
                , items: [{
                    xtype: parseInt(SeoFilter.config.superHiddenProps) === 1 ? 'seofilter-combo-tpls' : 'hidden',
                    fieldLabel: _('seofilter_rule_tpl'),
                    name: 'tpl',
                    id: config.id + '-tpl',
                    anchor: '99%',
                }, {
                    html: '',
                    style: 'margin-bottom:10px'
                }, {
                    html: _('seofilter_rule_properties_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-combobox-options',
                    anchor: '99%',
                    name: 'properties',
                    record: config.record.object,
                    id: config.id + '-properties',
                }, {
                    html: '',
                    style: 'margin-bottom:10px'
                }, {
                    html: _('seofilter_rule_properties_introtexts'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-combobox-options',
                    anchor: '99%',
                    name: 'introtexts',
                    record: config.record.object,
                    id: config.id + '-introtexts',
                }, {
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_rule_introlength'),
                    name: 'introlength',
                    id: config.id + '-introlength',
                    anchor: '99%',
                }]
            });
        }

        return tabs;
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-rule-window-update', SeoFilter.window.UpdateRule);


SeoFilter.window.duplicateRule = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('seofilter_rule_duplicate'),
        width: 600,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/duplicate',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.duplicateRule.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.duplicateRule, MODx.Window, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            anchor: '99%',
            name: 'id',
        }, {
            fieldLabel: _('seofilter_rule_name'),
            xtype: 'textfield',
            anchor: '99%',
            name: 'name',
            allowBlank: false,
            maxLength: 255,
        }, {
            xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
            fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
            name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
            anchor: '99%',
            allowBlank: false,
            maxLength: 255,
        }, {
            boxLabel: _('seofilter_rule_copy_fields'),
            xtype: 'xcheckbox',
            anchor: '99%',
            name: 'copy_fields',
        }, {
            xtype: parseInt(SeoFilter.config.count_childrens)===0?'hidden':'xcheckbox',
            boxLabel: _('seofilter_rule_recount'),
            name: 'recount',
        },{
            boxLabel: _('seofilter_rule_active'),
            xtype: 'xcheckbox',
            anchor: '99%',
            name: 'active',
        }];
    },
});
Ext.reg('seofilter-window-copy-rule', SeoFilter.window.duplicateRule);