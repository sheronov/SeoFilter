SeoFilter.window.CreateField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-field-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_field_create'),
        width: 550,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/field/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateField.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateField, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('seofilter_field_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('seofilter_field_description'),
            name: 'description',
            id: config.id + '-description',
            height: 150,
            anchor: '99%'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_field_active'),
            name: 'active',
            id: config.id + '-active',
            checked: true,
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-field-window-create', SeoFilter.window.CreateField);


SeoFilter.window.UpdateField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-field-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_field_update'),
        width: 550,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/field/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateField.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateField, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('seofilter_field_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('seofilter_field_description'),
            name: 'description',
            id: config.id + '-description',
            anchor: '99%',
            height: 150,
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
Ext.reg('seofilter-field-window-update', SeoFilter.window.UpdateField);