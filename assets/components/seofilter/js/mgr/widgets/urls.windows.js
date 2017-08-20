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
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateUrls.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateUrls, MODx.Window, {

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
            }, {
                xtype: 'textfield',
                fieldLabel: _('seofilter_seometa_h1'),
                name: 'h1',
                id: config.id + '-h1',
                anchor: '99%',
            },{
                xtype: 'textfield',
                fieldLabel: _('seofilter_seometa_h2'),
                name: 'h2',
                id: config.id + '-h2',
                anchor: '99%',
            }, {
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_description'),
                name: 'description',
                id: config.id + '-description',
                anchor: '99%',
            }, {
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_introtext'),
                name: 'introtext',
                id: config.id + '-introtext',
                anchor: '99%',
            },{
                xtype: 'textarea',
                fieldLabel: _('seofilter_seometa_text'),
                name: 'text',
                id: config.id + '-text',
                anchor: '99%',
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
                anchor: '99%'
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
                }]
            }]
        },{
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_url_active_more'),
            name: 'active',
            id: config.id + '-active',
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
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_image'),
                    name: 'image',
                    id: config.id + '-image',
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
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_menuindex'),
                    name: 'menuindex',
                    id: config.id + '-menuindex',
                    anchor: '99%',

                }]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-url-window-update', SeoFilter.window.UpdateUrls);