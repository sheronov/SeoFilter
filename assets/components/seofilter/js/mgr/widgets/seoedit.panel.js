SeoFilter.panel.Seoedit = function (config) {
    config = config || {};
    config.record = config.record || {};


    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        baseParams: {},
        id: 'seofilter-panel-seoedit',
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/update',
        defaults: { collapsible: false ,autoHeight: true }
        ,bodyStyle: ''
       // ,forceLayout: true
       // ,cls: 'container form-with-labels'
       // ,useLoadingMask: true
        ,listeners: {
             'setup': {fn:this.setup,scope:this},
             'success': {fn:this.success,scope:this},
            // 'failure': {fn:this.failure,scope:this},
            'beforeSubmit': {fn:this.beforeSubmit,scope:this},
        }
        ,hideMode: 'offsets',
        items: this.getTabs(config)
    });
    SeoFilter.panel.Seoedit.superclass.constructor.call(this, config);

    this.getForm().setValues(this.config.record);



};

Ext.extend(SeoFilter.panel.Seoedit, MODx.FormPanel, {
    initialized: false
    ,setup: function() {
        //console.log(this);
        //this.enableBubble('change');
       // console.log(this.record);
        if (!this.initialized) {
            // this.getForm().setValues(this.record);
            // this.getForm().setValues({'active':parseInt(this.record.active)});
            // this.getForm().setValues({'custom':parseInt(this.record.custom)});
        }
        this.initialized = true;
    }

    ,beforeSubmit: function(o) {
        Ext.apply(o.form.baseParams,{"frame":1});
    }
    ,success: function(o) {
        Ext.getCmp('seoedit-btn-save').setDisabled(false);
        var object = o.result.object;
        if(o.result.message) {
            location.href = o.result.message;
        }
    }

    ,getTabs: function (config) {
        var tabs = {
            //     html: '<h2>' + _('seofilter') + '</h2>',
            //     cls: '',
            //     style: {margin: '15px 0'}
            // }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('seofilter_url_meta'),
                layout: 'anchor',
                items: [{
                    layout: 'form'
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,autoHeight: true
                    ,defaults: {
                        border: false
                        ,msgTarget: 'under'
                    }
                    ,cls: 'main-wrapper'
                    ,items:this.getMetaFields(config)
                }]
            },{
                title: _('seofilter_url_data'),
                layout: 'anchor',
                items: [ {
                    layout: 'form',
                    items: this.getFields(config)
                    ,labelAlign: 'top'
                    ,labelSeparator: ''
                    ,autoHeight: true
                    ,defaults: {
                        border: false
                        ,msgTarget: 'under'
                        ,width: '100%'
                    }
                    ,cls: 'main-wrapper'
                }]

            }]
        };

        if(parseInt(SeoFilter.config.hiddenTab)) {
            tabs.items.push({
                title: _('seofilter_rule_properties')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: [{
                    layout: 'form',
                    items: [{
                        xtype: parseInt(SeoFilter.config.superHiddenProps) === 1 ? 'seofilter-combo-tpls' : 'hidden',
                        fieldLabel: _('seofilter_rule_tpl'),
                        name: 'tpl',
                        id: 'seoedit-tpl',
                        // value: config.record.tpl,
                        anchor: '99%',
                    }, {
                        html: '',
                        style: 'margin-bottom:10px'
                    }, {
                        html: _('seofilter_rule_properties_intro'),
                        cls: 'panel-desc',
                        anchor: '99%',
                    }, {
                        xtype: 'seofilter-grid-combobox-options',
                        anchor: '99%',
                        name: 'properties',
                        record: config.record,
                        id:'properties',
                    }, {
                        html: '',
                        style: 'margin-bottom:10px'
                    }, {
                        html: _('seofilter_rule_properties_introtexts'),
                        cls: 'panel-desc',
                        anchor: '99%',
                    }, {
                        xtype: 'seofilter-grid-combobox-options',
                        anchor: '99%',
                        name: 'introtexts',
                        record: config.record,
                        id: 'seoedit-introtexts',
                    }, {
                        xtype: 'numberfield',
                        fieldLabel: _('seofilter_rule_introlength'),
                        name: 'introlength',
                        id: 'seoedit-introlength',
                        anchor: '99%',
                    }]
                    , labelAlign: 'top'
                    , labelSeparator: ''
                    , autoHeight: true
                    , defaults: {
                        border: false
                        , msgTarget: 'under'
                        , width: 400
                    }
                    , cls: 'main-wrapper'
                }]
            });
        }

        return tabs;

    },

    loadRte: function (config) {
        if(!config.height) {
            config.height = 250;
        }
        Ext.getCmp(config.id).setHeight(config.height);
        if(MODx.loadRTE !== 'undefined') {
            window.setTimeout(function() {
                MODx.loadRTE(config.id);
            }, 100);
        }
    },

    loadAce: function (config) {
        if(!config.height) {
            config.height = 250;
        }
        Ext.getCmp(config.id).setHeight(config.height);
        window.setTimeout(function() {
            MODx.ux.Ace.replaceComponent(config.id, 'text/x-smarty', 1);
            MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
        }, 100);
    },

    getMetaFields: function (config) {
        var fields = [{
            xtype: 'hidden',
            hideLabel: true,
            fieldLabel: _('seofilter_url_id'),
            anhchor: '99%',
            readOnly:true,
            name: 'id',
            id: 'seoedit-id',
            value: config.record.id
        },{
            html: _('seofilter_url_seoedit_intro'),
            cls: 'panel-desc',
        },{
            xtype: 'xcheckbox',
            boxLabel: '<b>'+_('seofilter_seo_custom')+'</b>',
            name: 'custom',
            id: 'seoedit-custom',
        }];
        var fields_name = {
            title : {xtype:'textfield',maxLength: 255},
            h1 : {xtype:'textfield',maxLength: 255},
            h2: {xtype:'textfield',maxLength: 255},
            description: {},
            introtext: {},
            keywords: {},
            text: {},
            content: {}
        };

        var richtexts = [], aces = [];
        if(SeoFilter.config.content_richtext) {
            var rts = SeoFilter.config.content_richtext.replace(' ','').split(',');
            for(rt_field in rts) {
                if(rts.hasOwnProperty(rt_field)) {
                    var field = rts[rt_field];
                    if(field.indexOf('Rule.') === -1) {
                        field = field.split(':');
                        if (!field[1]) {
                            field[1] = 0
                        }
                        richtexts[field[0]] = field[1];
                    }
                }
            }
        }
        if(SeoFilter.config.content_ace) {
            var ats = SeoFilter.config.content_ace.replace(' ','').split(',');
            for(at_field in ats) {
                if(ats.hasOwnProperty(at_field)) {
                    var field = ats[at_field];
                    if(field.indexOf('Rule.') === -1 && richtexts.hasOwnProperty(field) === false) {
                        field = field.split(':');
                        if (!field[1]) {
                            field[1] = 0
                        }
                        aces[field[0]] = field[1];
                    }
                }
            }
        }

        for(field in fields_name) {
            var field_data = this.objectMerge({
                xtype: 'textarea',
                name: field,
                fieldLabel: _('seofilter_seometa_'+field),
                id: 'seoedit'+'-'+field,
                anchor: '99%',
                description: "[[!+sf."+field+"]] / {$_modx->getPlaceholder('sf."+field+"')}",
                listeners: {
                    'keypress': function (w, e) {Ext.getCmp('seoedit-custom').setValue(true);},
                    'keyup': function (w, e) {if(e.button == 7) {Ext.getCmp('seoedit-custom').setValue(true);}}
                }
            },fields_name[field]);

            if(richtexts.hasOwnProperty(field)) {
                if(richtexts[field]) {field_data['height'] = richtexts[field];}
                field_data['listeners'] = {
                    'render': function (config) {
                        if(!config.height) {
                            config.height = 250;
                        }
                        Ext.getCmp(config.id).setHeight(config.height);
                        if(MODx.loadRTE !== 'undefined') {
                            window.setTimeout(function() {
                                if(MODx.loadRTE(config.id)) {
                                    var editor =  MODx.loadedRTEs[config.id];;
                                    editor.editor.on('change', function() {
                                        Ext.defer(function() {
                                            Ext.getCmp(config.id).el.dom.value = editor.getValue();
                                        }, 10);
                                    });
                                }

                            }, 100);
                        }
                    },
                    // 'render': {fn:this.loadRte,scope:this},
                };
            }
            if(aces.hasOwnProperty(field)) {
                if(aces[field]) {field_data['height'] = aces[field];}
                field_data['listeners'] = {'render': {fn:this.loadAce,scope:this}};
            }

            fields.push(field_data);
        }

        return fields;
    },

    objectMerge: function(obj1,obj2) {
        var obj3 = {};
        for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
        for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
        return obj3;
    },

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('seofilter_url_link'),
            name: 'link',
            id: 'seoedit-link',
            description: _('seofilter_url_link_help'),
            anchor: '99%',
            value: config.record.link
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
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_old_url'),
                    name: 'old_url',
                    id: 'seoedit-old_url',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                    anchor: '99%',
                },{
                    xtype: 'xcheckbox',
                    boxLabel: _('seofilter_url_active_more'),
                    name: 'active',
                    id: 'seoedit-active',
                }]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_new_url'),
                    name: 'new_url',
                    id: 'seoedit-new_url',
                    anchor: '99%',
                }]
            }]
        },{
            title: _('seofilter_url_menu_data')
            , xtype: 'fieldset'
            , forceLayout: true
            , autoHeight: true
            ,anchor: '100%'
            , border: false
            , items: [{
                layout: 'column'
                , defaults: {msgTarget: 'under'}
                , border: false
                , anchor: '99%'
                , items: [{
                    columnWidth: .64
                    , layout: 'form'
                    , defaults: {msgTarget: 'under'}
                    , border: false
                    , items: [{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_menutitle'),
                        name: 'menutitle',
                        id: 'seoedit-menutitle',
                        anchor: '99%',
                        description: "{$menutitle}",
                    },{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_image'),
                        name: 'image',
                        id: 'seoedit-image',
                        anchor: '99%',
                        description: "{$image}",
                    },{
                        xtype: 'numberfield',
                        fieldLabel: _('seofilter_url_total_more'),
                        name: 'total',
                        //readOnly: true,
                        id: 'seoedit-total',
                        description: '{$total}',
                        anchor: '99%',
                    }]
                },{
                    columnWidth: .35
                    , layout: 'form'
                    , defaults: {msgTarget: 'under'}
                    , border: false
                    , items: [{
                        xtype: 'textfield',
                        fieldLabel: _('seofilter_url_link_attributes'),
                        name: 'link_attributes',
                        id: 'seoedit-link_attributes',
                        anchor: '99%',
                        description: "{$link_attributes}",
                    },{
                        xtype: 'numberfield',
                        fieldLabel: _('seofilter_url_menuindex'),
                        name: 'menuindex',
                        id: 'seoedit-menuindex',
                        anchor: '99%',
                        description: "{$menuindex}",
                    }, {
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_url_menu_on'),
                        name: 'menu_on',
                        id: 'seoedit-menu_on',
                        labelStyle:'margin-top:-5px;',
                        description: _('seofilter_url_menu_on_help'),

                    },{
                        xtype: 'xcheckbox',
                        boxLabel: _('seofilter_url_recount'),
                        name: 'recount',
                        id: 'seoedit-recount',
                        labelStyle:'margin-top:-6px;',
                        description: _('seofilter_url_recount_help'),
                    }]
                }]
            }]
        }, {
            layout: 'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'seofilter-combo-rule',
                    fieldLabel: _('seofilter_url_multi_id'),
                    name: 'multi_id',
                    id: 'seoedit-multi_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',

                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_count'),
                    name: 'count',
                    //readOnly: true,
                    id: 'seoedit-count',
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_createdon'),
                    name: 'createdon',
                    id: 'seoedit-createdon',
                    anchor: '99%',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                }]
            }, {
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    xtype: 'seofilter-combo-resource',
                    fieldLabel: _('seofilter_url_page_id'),
                    name: 'page_id',
                    hiddenName: 'page_id',
                    id: 'seoedit-page_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                },{
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_ajax'),
                    name: 'ajax',
                    id: 'seoedit-ajax',
                    //readOnly: true,
                    anchor: '99%',
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_editedon'),
                    name: 'editedon',
                    id: 'seoedit-editedon',
                    anchor: '99%',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                }]
            }]
        }, {
            html: '<b>' + _('seofilter_url_urlword') + '</b>' + ' <span style="float:right;">'+ _('seofilter_urlword_word_edit') +'</span>',
            cls: '',
            style: {margin: '10px 0 5px',color: '#555'}
        }, {
            title: _('seofilter_url_urlword')
            , xtype: 'seofilter-grid-urlword'
            , record: config.record
        }];
    }
});

Ext.reg('seofilter-panel-seoedit', SeoFilter.panel.Seoedit);
