SeoFilter.window.CreateDictionary = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-dictionary-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_dictionary_create'),
        width: 600,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/dictionary/create',
        fields: this.getFields(config),
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
                        fieldLabel: _('seofilter_dictionary_input'),
                        name: 'input',
                        id: config.id + '-input',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value'),
                        name: 'value',
                        id: config.id + '-value',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_alias'),
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
                        fieldLabel: _('seofilter_dictionary_field_id'),
                        name: 'field_id',
                        id: config.id + '-field_id',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_class'),
                        name: 'class',
                        id: config.id + '-class',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_key'),
                        name: 'key',
                        id: config.id + '-key',
                        anchor: '99%',
                    }
                ]
            }]
        },{
        //     xtype: 'xcheckbox',
        //     boxLabel: _('seofilter_dictionary_active'),
        //     name: 'active',
        //     id: config.id + '-active',
        // }, {
            title: _('seofilter_disctionary_decline')
            , style: 'border-top:1px solid #ccc;padding-top:5px;'
            , xtype: 'displayfield'
            , html: _('seofilter_disctionary_decline_desc')
        },{
            layout: 'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , style: 'padding-bottom:10px;'
                , items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_i'),
                        name: 'value_i',
                        id: config.id + '-value_i',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_r'),
                        name: 'value_r',
                        id: config.id + '-value_r',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_d'),
                        name: 'value_d',
                        id: config.id + '-value_d',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_v'),
                        name: 'value_v',
                        id: config.id + '-value_v',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_t'),
                        name: 'value_t',
                        id: config.id + '-value_t',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_p'),
                        name: 'value_p',
                        id: config.id + '-value_p',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_o'),
                        name: 'value_o',
                        id: config.id + '-value_o',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_to'),
                        name: 'value_to',
                        id: config.id + '-value_to',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_in'),
                        name: 'value_in',
                        id: config.id + '-value_in',
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
                        fieldLabel: _('seofilter_dictionary_values_i'),
                        name: 'values_i',
                        id: config.id + '-values_i',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_r'),
                        name: 'values_r',
                        id: config.id + '-values_r',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_d'),
                        name: 'values_d',
                        id: config.id + '-values_d',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_v'),
                        name: 'values_v',
                        id: config.id + '-values_v',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_t'),
                        name: 'values_t',
                        id: config.id + '-values_t',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_p'),
                        name: 'values_p',
                        id: config.id + '-values_p',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_o'),
                        name: 'values_o',
                        id: config.id + '-values_o',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_from'),
                        name: 'value_from',
                        id: config.id + '-value_from',
                        anchor: '99%',
                    }
                ]
            }]
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
        width: 600,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/dictionary/update',
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
                        fieldLabel: _('seofilter_dictionary_input'),
                        name: 'input',
                        id: config.id + '-input',
                        anchor: '99%',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value'),
                        name: 'value',
                        id: config.id + '-value',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_alias'),
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
                        fieldLabel: _('seofilter_dictionary_field_id'),
                        name: 'field_id',
                        id: config.id + '-field_id',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_class'),
                        name: 'class',
                        id: config.id + '-class',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_key'),
                        name: 'key',
                        id: config.id + '-key',
                        anchor: '99%',
                    }
                ]
            }]
        },{
            //     xtype: 'xcheckbox',
            //     boxLabel: _('seofilter_dictionary_active'),
            //     name: 'active',
            //     id: config.id + '-active',
            // }, {
            title: _('seofilter_disctionary_decline')
            , xtype: 'displayfield'
            , html: _('seofilter_disctionary_decline_desc')
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
                        fieldLabel: _('seofilter_dictionary_value_i'),
                        name: 'value_i',
                        id: config.id + '-value_i',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_r'),
                        name: 'value_r',
                        id: config.id + '-value_r',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_d'),
                        name: 'value_d',
                        id: config.id + '-value_d',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_v'),
                        name: 'value_v',
                        id: config.id + '-value_v',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_t'),
                        name: 'value_t',
                        id: config.id + '-value_t',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_p'),
                        name: 'value_p',
                        id: config.id + '-value_p',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_o'),
                        name: 'value_o',
                        id: config.id + '-value_o',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_to'),
                        name: 'value_to',
                        id: config.id + '-value_to',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_in'),
                        name: 'value_in',
                        id: config.id + '-value_in',
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
                        fieldLabel: _('seofilter_dictionary_values_i'),
                        name: 'values_i',
                        id: config.id + '-values_i',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_r'),
                        name: 'values_r',
                        id: config.id + '-values_r',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_d'),
                        name: 'values_d',
                        id: config.id + '-values_d',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_v'),
                        name: 'values_v',
                        id: config.id + '-values_v',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_t'),
                        name: 'values_t',
                        id: config.id + '-values_t',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_p'),
                        name: 'values_p',
                        id: config.id + '-values_p',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_values_o'),
                        name: 'values_o',
                        id: config.id + '-values_o',
                        anchor: '99%',
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value_from'),
                        name: 'value_from',
                        id: config.id + '-value_from',
                        anchor: '99%',
                    }
                ]
            }]
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-dictionary-window-update', SeoFilter.window.UpdateDictionary);