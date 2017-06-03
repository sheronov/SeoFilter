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
        return [{
            xtype: 'textfield',
            fieldLabel: _('seofilter_multifield_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        },{
            xtype: 'textfield',
            fieldLabel: _('seofilter_multifield_url'),
            name: 'url',
            id: config.id + '-url',
            anchor: '99%',
            allowBlank: false,
        }, {
            layout:'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [
                    {
                        xtype: 'seofilter-combo-resource',
                        fieldLabel: _('seofilter_multifield_page'),
                        name: 'page',
                        id: config.id + '-page',
                        anchor: '99%',
                    }
                ]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [
                     {
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_multifield_active'),
                        name: 'active',
                        id: config.id + '-active',
                        checked: true,
                    }
                ]
            }]
        }];
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
        width: 550,
        autoHeight: true,
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
        return [{
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
        },{
            xtype: 'textfield',
            fieldLabel: _('seofilter_multifield_url'),
            name: 'url',
            id: config.id + '-url',
            anchor: '99%',
        }, {
            layout:'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [
                    {
                        xtype: 'seofilter-combo-resource',
                        fieldLabel: _('seofilter_field_page'),
                        name: 'page',
                        id: config.id + '-page',
                        anchor: '99%',
                    }
                ]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [
                     {
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_field_active'),
                        name: 'active',
                        id: config.id + '-active',
                    }
                ]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-multifield-window-update', SeoFilter.window.UpdateMultiField);