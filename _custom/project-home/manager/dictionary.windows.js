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
                        allowBlank: false,
                    }
                ]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [
                    {
                        xtype: 'seofilter-combo-field',
                        fieldLabel: _('seofilter_dictionary_field_id'),
                        name: 'field_id',
                        id: config.id + '-field_id',
                        anchor: '99%',
                        allowBlank: false,
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_alias'),
                        name: 'alias',
                        id: config.id + '-alias',
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
            title: _('seofilter_dictionary_decline')
            , style: 'border-top:1px solid #ccc;padding-top:5px;'
            , xtype: 'displayfield'
            , html: _('seofilter_dictionary_decline_desc_save')
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
                        description: '{$input}',
                        allowBlank: false,
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_value'),
                        name: 'value',
                        id: config.id + '-value',
                        anchor: '99%',
                        description: '{$value}',
                        allowBlank: false,
                    }
                ]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [
                    {
                        xtype: 'seofilter-combo-field',
                        fieldLabel: _('seofilter_dictionary_field_id'),
                        name: 'field_id',
                        // readOnly: true,
                        // style: 'background:#f9f9f9;color:#aaa;',
                        id: config.id + '-field_id',
                        anchor: '99%',
                    }, {
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_dictionary_alias'),
                        name: 'alias',
                        id: config.id + '-alias',
                        description: '{$alias}',
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
            title: _('seofilter_dictionary_decline')
            , xtype: 'displayfield'
            , style: 'border-top:1px solid #ccc;padding-top:5px;'
            , html: _('seofilter_dictionary_decline_desc')
        },{
            layout: 'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    //     xtype: 'textfield',
                    //     fieldLabel: _('seofilter_dictionary_value_i'),
                    //     name: 'value_i',
                    //     id: config.id + '-value_i',
                    //     anchor: '99%',
                    // },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_r'),
                    description: '{$value_r}',
                    name: 'value_r',
                    id: config.id + '-value_r',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_d'),
                    description: '{$value_d}',
                    name: 'value_d',
                    id: config.id + '-value_d',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_v'),
                    description: '{$value_v}',
                    name: 'value_v',
                    id: config.id + '-value_v',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_t'),
                    description: '{$value_t}',
                    name: 'value_t',
                    id: config.id + '-value_t',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_p'),
                    description: '{$value_p}',
                    name: 'value_p',
                    id: config.id + '-value_p',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_o'),
                    description: '{$value_o}',
                    name: 'value_o',
                    id: config.id + '-value_o',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_in'),
                    description: '{$value_in}',
                    name: 'value_in',
                    id: config.id + '-value_in',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_to'),
                    description: '{$value_to}',
                    name: 'value_to',
                    id: config.id + '-value_to',
                    anchor: '99%',
                }
                ]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_i'),
                    name: 'm_value_i',
                    description: '{$m_value_i}',
                    id: config.id + '-m_value_i',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_r'),
                    name: 'm_value_r',
                    description: '{$m_value_r}',
                    id: config.id + '-m_value_r',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_d'),
                    name: 'm_value_d',
                    description: '{$m_value_d}',
                    id: config.id + '-m_value_d',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_v'),
                    name: 'm_value_v',
                    description: '{$m_value_v}',
                    id: config.id + '-m_value_v',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_t'),
                    name: 'm_value_t',
                    description: '{$m_value_t}',
                    id: config.id + '-m_value_t',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_p'),
                    name: 'm_value_p',
                    description: '{$m_value_p}',
                    id: config.id + '-m_value_p',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_m_value_o'),
                    name: 'm_value_o',
                    description: '{$m_value_o}',
                    id: config.id + '-m_value_o',
                    anchor: '99%',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_dictionary_value_from'),
                    name: 'value_from',
                    description: '{$value_from}',
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
