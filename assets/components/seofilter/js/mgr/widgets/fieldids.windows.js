SeoFilter.window.CreateFieldIds = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-fieldids-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_field_create'),
        width: 450,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/fieldids/create',
        bodyStyle: 'padding-top:10px;',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateFieldIds.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateFieldIds, MODx.Window, {
    getFields: function (config) {
        return  [{
            xtype: 'hidden',
            name: 'multi_id',
            id: config.id + '-multi_id',
            //value:  config.record.id,
        },{
            xtype: 'seofilter-combo-field',
            fieldLabel: _('seofilter_fieldids_field_id'),
            name: 'field_id',
            id: config.id + '-field_id',
            anchor: '99%',
            allowBlank: false,
        },{
            xtype: 'numberfield',
            fieldLabel: _('seofilter_fieldids_priority'),
            name: 'priority',
            id: config.id + '-priority',
            anchor: '99%',
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_fieldids_where'),
            name: 'where',
            id: config.id + '-where',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        },{
            layout:'column',
            border: false,
            anchor: '99%',
            items: [{
                columnWidth: 1
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_compare'),
                    name: 'compare',
                    id: config.id + '-compare',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_value'),
                    name: 'value',
                    id: config.id + '-value',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_condition'),
                    name: 'condition',
                    id: config.id + '-condition',
                    anchor: '99%',
                }]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-fieldids-window-create', SeoFilter.window.CreateFieldIds);


SeoFilter.window.UpdateFieldIds = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-fieldids-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_fieldids_update'),
        width: 450,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/fieldids/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateFieldIds.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateFieldIds, MODx.Window, {

    getFields: function (config) {
        return  [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        },{
            xtype: 'hidden',
            name: 'multi_id',
            id: config.id + '-multi_id',
            //value:  config.record.id,
        },{
            xtype: 'seofilter-combo-field',
            fieldLabel: _('seofilter_fieldids_field_id'),
            name: 'field_id',
            id: config.id + '-field_id',
            anchor: '99%',
            allowBlank: false,
        },{
            xtype: 'numberfield',
            fieldLabel: _('seofilter_fieldids_priority'),
            name: 'priority',
            id: config.id + '-priority',
            anchor: '99%',
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_fieldids_where'),
            name: 'where',
            id: config.id + '-where',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        },{
            layout:'column',
            border: false,
            anchor: '99%',
            items: [{
                columnWidth: 1
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_compare'),
                    name: 'compare',
                    id: config.id + '-compare',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_value'),
                    name: 'value',
                    id: config.id + '-value',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_fieldids_condition'),
                    name: 'condition',
                    id: config.id + '-condition',
                    anchor: '99%',
                }]
            }]
        }];

    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-fieldids-window-update', SeoFilter.window.UpdateFieldIds);