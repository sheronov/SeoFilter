SeoFilter.window.CreateField = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-field-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_field_create'),
        width: 550,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/field/create',
        fields: this.getFields(config),
        bodyStyle: 'padding-bottom:15px;',
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
            layout:'column',
            border: false,
            anchor: '99%',
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
                        }, {
                            xtype: 'seofilter-combo-class',
                            fieldLabel: _('seofilter_field_class_more'),
                            name: 'class',
                            id: config.id + '-class',
                            anchor: '99%',
                            allowBlank:false,
                        }, {
                            xtype: 'textfield',
                            fieldLabel: _('seofilter_field_key'),
                            name: 'key',
                            id: config.id + '-key',
                            anchor: '99%',
                            allowBlank:false,
                        }
                    ]
                },{
                    columnWidth: .5
                    ,layout: 'form'
                    ,defaults: { msgTarget: 'under' }
                    ,border:false
                    ,items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: _('seofilter_field_alias'),
                            name: 'alias',
                            id: config.id + '-alias',
                            anchor: '99%',
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_field_hideparam'),
                            name: 'hideparam',
                            id: config.id + '-hideparam',
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_field_valuefirst'),
                            name: 'valuefirst',
                            id: config.id + '-valuefirst',
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_field_exact'),
                            name: 'exact',
                            id: config.id + '-exact',
                        // }, {
                        //     xtype: 'numberfield',
                        //     fieldLabel: _('seofilter_field_priority'),
                        //     name: 'priority',
                        //     id: config.id + '-priority',
                        //     anchor: '99%',
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_field_active'),
                            name: 'active',
                            id: config.id + '-active',
                            checked: true,
                        }
                    ]
                }]
        },{
            xtype: 'radiogroup'
            ,fieldLabel: _('seofilter_field_method')
            ,hideLabel: false
            ,columns: 3
            ,allowBlank: false
            ,value: ''
            ,items: [{
                boxLabel: _('seofilter_field_dont')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 0
            },{
                boxLabel: _('seofilter_field_translit')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 1
            },{
                boxLabel: _('seofilter_field_translate')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 2
            }]
        },{
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_field_xpdo'),
            name: 'xpdo',
            id: config.id + '-xpdo',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        },{
            layout:'column',
            border: false,
            anchor: '97%',
            items: [{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_package'),
                    name: 'xpdo_package',
                    id: config.id + '-xpdo_package',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_class'),
                    name: 'xpdo_class',
                    id: config.id + '-xpdo_class',
                    anchor: '99%',
                }]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_id'),
                    name: 'xpdo_id',
                    id: config.id + '-xpdo_id',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_name'),
                    name: 'xpdo_name',
                    id: config.id + '-xpdo_name',
                    anchor: '99%',
                }]
            },{
                columnWidth: 1
                ,layout:'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_field_xpdo_where'),
                        name: 'xpdo_where',
                        id: config.id + '-xpdo_where',
                        anchor: '99%',
                        style:'margin-bottom:15px;'
                    }
                ]
            }]
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
        autoHeight: false,
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
        },{
            xtype: 'textfield',
            fieldLabel: _('seofilter_field_name'),
            name: 'name',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            layout:'column',
            border: false,
            anchor: '99%',
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
                    }, {
                        xtype: 'seofilter-combo-class',
                        fieldLabel: _('seofilter_field_class_more'),
                        name: 'class',
                        id: config.id + '-class',
                        anchor: '99%',
                        allowBlank:false,
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_field_key'),
                        name: 'key',
                        id: config.id + '-key',
                        anchor: '99%',
                        allowBlank:false,
                    }
                ]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_field_alias'),
                        name: 'alias',
                        id: config.id + '-alias',
                        anchor: '99%',
                    },{
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_field_hideparam'),
                        name: 'hideparam',
                        id: config.id + '-hideparam',
                    },{
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_field_valuefirst'),
                        name: 'valuefirst',
                        id: config.id + '-valuefirst',
                    },{
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_field_exact'),
                        name: 'exact',
                        id: config.id + '-exact',
                        // }, {
                        //     xtype: 'numberfield',
                        //     fieldLabel: _('seofilter_field_priority'),
                        //     name: 'priority',
                        //     id: config.id + '-priority',
                        //     anchor: '99%',
                    },{
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_field_active'),
                        name: 'active',
                        id: config.id + '-active',
                        checked: true,
                    }
                ]
            }]
        },{
            xtype: 'radiogroup'
            ,fieldLabel: _('seofilter_field_method')
            ,hideLabel: false
            ,columns: 3
            ,allowBlank: false
           ,name: 'priority'
            ,items: [{
                boxLabel: _('seofilter_field_dont')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 0
            },{
                boxLabel: _('seofilter_field_translit')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 1
            },{
                boxLabel: _('seofilter_field_translate')
                ,hideLabel: true
                ,name: 'priority'
                ,inputValue: 2
            }]
        },{
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_field_xpdo'),
            name: 'xpdo',
            id: config.id + '-xpdo',
            listeners: {
                check: SeoFilter.utils.handleChecked,
                afterrender: SeoFilter.utils.handleChecked
            }
        },{
            layout:'column',
            border: false,
            anchor: '97%',
            items: [{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_package'),
                    name: 'xpdo_package',
                    id: config.id + '-xpdo_package',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_class'),
                    name: 'xpdo_class',
                    id: config.id + '-xpdo_class',
                    anchor: '99%',
                }]
            },{
                columnWidth: .5
                ,layout: 'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_id'),
                    name: 'xpdo_id',
                    id: config.id + '-xpdo_id',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_name'),
                    name: 'xpdo_name',
                    id: config.id + '-xpdo_name',
                    anchor: '99%',
                }]
            },{
                columnWidth: 1
                ,layout:'form'
                ,defaults: { msgTarget: 'under' }
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_field_xpdo_where'),
                    name: 'xpdo_where',
                    id: config.id + '-xpdo_where',
                    anchor: '99%',
                    style:'margin-bottom:15px;'
                }
                ]
            }]
        }];

    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-field-window-update', SeoFilter.window.UpdateField);