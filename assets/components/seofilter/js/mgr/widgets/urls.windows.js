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
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_input'),
                        name: 'input',
                        id: config.id + '-input',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_value'),
                        name: 'value',
                        id: config.id + '-value',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_alias'),
                        name: 'alias',
                        id: config.id + '-alias',
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
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_field_id'),
                        name: 'field_id',
                        id: config.id + '-field_id',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_class'),
                        name: 'class',
                        id: config.id + '-class',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_key'),
                        name: 'key',
                        id: config.id + '-key',
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
        width: 600,
        autoHeight: true,
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
        },{
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
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_input'),
                        name: 'input',
                        id: config.id + '-input',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_value'),
                        name: 'value',
                        id: config.id + '-value',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_alias'),
                        name: 'alias',
                        id: config.id + '-alias',
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
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_field_id'),
                        name: 'field_id',
                        id: config.id + '-field_id',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_class'),
                        name: 'class',
                        id: config.id + '-class',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_key'),
                        name: 'key',
                        id: config.id + '-key',
                        anchor: '99%',
                    }
                ]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-url-window-update', SeoFilter.window.UpdateUrls);