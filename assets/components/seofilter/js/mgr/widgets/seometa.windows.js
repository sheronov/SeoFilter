SeoFilter.window.CreateSeoMeta = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-seometa-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_seometa_create'),
        width: 650,
        //autoHeight: true,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/seometa/create',
        fields: this.getFields(config),
        bodyStyle: 'padding-top:10px;',
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateSeoMeta.superclass.constructor.call(this, config);
};

Ext.extend(SeoFilter.window.CreateSeoMeta, MODx.Window, {

    getFields: function (config) {
        return [{
                //     xtype: 'textfield',
                //     fieldLabel: _('seofilter_seometa_name'),
                //     name: 'name',
                //     id: config.id + '-name',
                //     anchor: '99%',
                //     allowBlank: false,
                // }, {
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
                    xtype: 'htmleditor',
                    enableFont:false,
                    enableColors: false,
                    enableFontSize : false,
                    cls: 'modx-richtext',
                    fieldLabel: _('seofilter_seometa_content'),
                    name: 'content',
                    id: config.id + '-content',
                    // listeners: {
                    //     render: function () {
                    //         if(MODx.loadRTE) {
                    //                 MODx.loadRTE(config.id + '-contenttext');
                    //         }
                    //     }
                    // },
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
Ext.reg('seofilter-seometa-window-create', SeoFilter.window.CreateSeoMeta);

SeoFilter.window.UpdateSeoMeta = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-seometa-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_seometa_update'),
        width: 650,
        //autoHeight: true,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/seometa/update',
        bodyStyle: 'padding-top:10px;',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateSeoMeta.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateSeoMeta, MODx.Window, {

    getFields: function (config) {
        return [{
                xtype: 'hidden',
                name: 'id',
                id: config.id + '-id',
            },{
            //     xtype: 'textfield',
            //     fieldLabel: _('seofilter_seometa_name'),
            //     name: 'name',
            //     id: config.id + '-name',
            //     anchor: '99%',
            //     allowBlank: false,
            // }, {
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
                xtype: 'htmleditor',
                enableFont:false,
                enableColors: false,
                enableFontSize : false,
                fieldLabel: _('seofilter_seometa_content'),
                name: 'content',
                id: config.id + '-content',
                // listeners: {
                //     render: function () {
                //         if(MODx.loadRTE) MODx.loadRTE(config.id + '-contenttext_upd');
                //     }
                // },
                anchor: '99%',
            },{
                xtype: 'xcheckbox',
                boxLabel: _('seofilter_field_active'),
                name: 'active',
                id: config.id + '-active',
            }
        ];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-seometa-window-update', SeoFilter.window.UpdateSeoMeta);