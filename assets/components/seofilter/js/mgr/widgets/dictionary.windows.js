SeoFilter.window.CreateDictionary = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-dictionary-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_dictionary_create'),
        width: 650,
        //autoHeight: true,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/dictionary/create',
        fields: this.getFields(config),
        bodyStyle: 'padding-top:10px;',
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateDictionary.superclass.constructor.call(this, config);
};

Ext.extend(SeoFilter.window.CreateDictionary, MODx.Window, {

    getFields: function (config) {
        return [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_name'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_title'),
                    name: 'title',
                    id: config.id + '-title',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_h1'),
                    name: 'h1',
                    id: config.id + '-h1',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_h2'),
                    name: 'h2',
                    id: config.id + '-h2',
                    anchor: '99%',
                }, {
                    xtype: 'textarea',
                    fieldLabel: _('seofilter_dictionary_description'),
                    name: 'description',
                    id: config.id + '-description',
                    anchor: '99%',
                }, {
                    xtype: 'textarea',
                    fieldLabel: _('seofilter_dictionary_introtext'),
                    name: 'introtext',
                    id: config.id + '-introtext',
                    anchor: '99%',
                },{
                    xtype: 'textarea',
                    fieldLabel: _('seofilter_dictionary_text'),
                    name: 'text',
                    id: config.id + '-text',
                    anchor: '99%',
                }, {
                    xtype: 'textarea',
                    fieldLabel: _('seofilter_dictionary_content'),
                    name: 'content',
                    id: 'seofilter-dictionary-content',
                    listeners: {
                        render: function () {
                            if(MODx.loadRTE) MODx.loadRTE('seofilter-dictionary-content');
                        }
                    },
                    anchor: '99%',
                }, {
                    xtype: 'xcheckbox',
                    boxLabel: _('seofilter_field_active'),
                    name: 'active',
                    id: config.id + '-active',
                }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-dictionary-window-create', SeoFilter.window.CreateDictionary);

SeoFilter.window.UpdateDictionary = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-dictionary-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_dictionary_update'),
        width: 650,
        //autoHeight: true,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/dictionary/update',
        bodyStyle: 'padding-top:10px;',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateDictionary.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateDictionary, MODx.Window, {

    getFields: function (config) {
        return {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,items: [{
                // Таб №1 - Информация
                title: _('seofilter_dictionary')
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
                        fieldLabel: _('seofilter_dictionary_name'),
                        name: 'name',
                        id: config.id + '-name',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_url'),
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
                , html: '<p>SEO вкладка</p>'
            }]
        };
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-dictionary-window-update', SeoFilter.window.UpdateDictionary);