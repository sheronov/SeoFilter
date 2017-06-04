SeoFilter.window.CreateMultiField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-multifield-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_multifield_create'),
        width: 550,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/multifield/create',
        bodyStyle: 'padding-top:10px;',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateMultiField.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateMultiField, MODx.Window, {

    getFields: function (config) {
        return {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,items: [{
                // Таб №1 - Информация
                title: _('seofilter_multifield')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,bodyStyle: 'margin-top:-10px;padding-bottom:5px;'
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_multifield_name'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_multifield_url_more'),
                    name: 'url',
                    id: config.id + '-url',
                    anchor: '99%',
                }, {
                    layout: 'column',
                    border: false,
                    anchor: '100%',
                    items: [{
                        columnWidth: .5
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [
                            {
                                xtype: 'seofilter-combo-resource',
                                fieldLabel: _('seofilter_field_page'),
                                name: 'page',
                                id: config.id + '-page',
                                anchor: '99%',
                            }
                        ]
                    }, {
                        columnWidth: .5
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [
                            {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_field_active'),
                                name: 'active',
                                id: config.id + '-active',
                            }
                        ]
                    }]
                }]
            }, {
                // Таб №2 - Пользователи
                title: _('seofilter_seo')
                // Здесь должен быть xtype с таблицей подписчиков, пока комментируем
                //,xtype: 'sendex-grid-newsletter-subscribers'
                , xtype: 'displayfield'
                , html: _('seofilter_seo_after_save')
            }]
        };
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-multifield-window-create', SeoFilter.window.CreateMultiField);


SeoFilter.window.UpdateMultiField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-multifield-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_multifield_update'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/multifield/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateMultiField.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateMultiField, MODx.Window, {

    getFields: function (config) {
        return {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: false
            ,items: [{
                // Таб №1 - Информация
                title: _('seofilter_multifield')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,bodyStyle: 'margin-top:-10px;padding-bottom:5px;'
                ,border:false
                ,items: [{
                        xtype: 'hidden',
                        name: 'id',
                        id: config.id + '-id',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_multifield_name'),
                        name: 'name',
                        id: config.id + '-name',
                        anchor: '99%',
                        allowBlank: false,
                    }, {
                       title: _('seofilter_multifield_fields')
                        ,xtype: 'seofilter-grid-fieldids'
                        ,record: config.record.object
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_multifield_url_more'),
                        name: 'url',
                        id: config.id + '-url',
                        anchor: '99%',
                    }, {
                        layout: 'column',
                        border: false,
                        anchor: '100%',
                        items: [{
                            columnWidth: .7
                            , layout: 'form'
                            , defaults: {msgTarget: 'under'}
                            , border: false
                            , items: [
                                {
                                    xtype: 'seofilter-combo-resource',
                                    fieldLabel: _('seofilter_field_page'),
                                    name: 'page',
                                    id: config.id + '-page',
                                    anchor: '99%',
                                }
                            ]
                        }, {
                            columnWidth: .3
                            , layout: 'form'
                            , defaults: {msgTarget: 'under'}
                            , border: false
                            , style: 'padding-top:20px;text-align:right;'
                            , items: [
                                {
                                    xtype: 'xcheckbox',
                                    boxLabel: _('seofilter_field_active'),
                                    name: 'active',
                                    id: config.id + '-active',
                                }
                            ]
                        }]
                    }]
                }, {
                // Таб №2 - Пользователи
                title: _('seofilter_seo')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,style: 'margin-top:-10px;padding-bottom:5px;'
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
                        fieldLabel: _('seofilter_seometa_content'),
                        name: 'content',
                        id: config.id + '-content',
                        listeners: {
                            render: function () {
                                if(MODx.loadRTE) MODx.loadRTE(config.id + '-content');
                            }
                        },
                        anchor: '99%',
                    }
                ]
            }]
        };
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-multifield-window-update', SeoFilter.window.UpdateMultiField);