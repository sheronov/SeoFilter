SeoFilter.window.CreateRule = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-rule-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_rule_create'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateRule.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateRule, MODx.Window, {

    loadRte: function (config) {
        if(!config.height) {
            config.height = 250;
        }
        Ext.getCmp(config.id).setHeight(config.height);
        if(MODx.loadRTE !== 'undefined') {
            window.setTimeout(function() {
                MODx.loadRTE(config.id);
            }, 300);
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
            html: _('seofilter_multiseo_intro'),
            cls: 'panel-desc',
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
                    if(field.indexOf('Rule.') != -1) {
                        field = field.replace('Rule.','').split(':');
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
                    if(field.indexOf('Rule.') != -1 && richtexts.hasOwnProperty(field.replace('Rule.','')) === false) {
                        field = field.replace('Rule.','').split(':');
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
                id: config.id +'-'+field,
                anchor: '99%',
                description: "[[!+sf."+field+"]] / {$_modx->getPlaceholder('sf."+field+"')}"
            },fields_name[field]);

            if(richtexts.hasOwnProperty(field)) {
                if(richtexts[field]) {field_data['height'] = richtexts[field];}
                field_data['listeners'] = {'render': {fn:this.loadRte,scope:this}};
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
        var xtype_count = 'hidden';
        if(parseInt(SeoFilter.config.count_childrens)!==0) {
            xtype_count = 'textfield';
        }
        return {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,items: [{
                title: _('seofilter_rule')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,autoHeight: true
                ,autoScroll: false
                ,style:'overflow:hidden;'
                ,border:false
                ,items: [{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_name'),
                    description: _('seofilter_rule_name_help'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_link_tpl'),
                    name: 'link_tpl',
                    id: config.id + '-link_tpl',
                    anchor: '99%',
                    description: _('seofilter_rule_link_tpl_help'),
                    maxLength: 255,
                },{
                    layout: 'column',
                    border: false,
                    anchor: '99%',
                    style: 'margin-top:-10px',
                    items: [{
                        columnWidth: .55
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count==='hidden'?'hidden':'xcheckbox',
                            boxLabel: _('seofilter_rule_recount_new'),
                            description: _('seofilter_rule_recount_help'),
                            name: 'recount',
                            id: config.id + '-recount',
                        }]
                    }]
                }, {
                    title: _('seofilter_rule_fields')
                    ,xtype: 'seofilter-grid-fieldids'
                    ,id: config.id+'_gridfield'
                    ,idNumber: config.id
                    ,record: {id:0}
                },{
                    xtype: 'textfield',
                    hideLabel:true,
                    fieldLabel: _('seofilter_rule_url_more'),
                    name: 'url',
                    readOnly: true,
                    disabled:true,
                    style: 'margin-top:-11px;',
                    emptyText: _('seofilter_rule_url_more'),
                    id: config.id + '-url',
                    anchor: '99%',
                    description: _('seofilter_rule_url_help'),
                // },{
                //     title: _('seofilter_fieldids')
                //     , xtype: 'displayfield'
                //     , html: _('seofilter_fieldids_after_save')
                },{
                    layout: 'column',
                    border: false,
                    anchor: '99%',
                    items: [{
                        columnWidth: .7
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
                            fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
                            name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
                            allowBlank: false,
                            id: parseInt(SeoFilter.config.proMode)? config.id + '-pages':config.id + '-page',
                            description: parseInt(SeoFilter.config.proMode)? _('seofilter_rule_parents_help'):_('seofilter_rule_parent_help'),
                            anchor: '99%',
                            maxLength: 255,
                        },{
                            xtype: xtype_count,
                            fieldLabel: _('seofilter_rule_count_where'),
                            name: 'count_where',
                            description: _('seofilter_where_help'),
                            id: config.id + '-count_where',
                            anchor: '99%',
                            maxLength: 255,
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_base_more'),
                            description: _('seofilter_rule_base_help'),
                            name: 'base',
                            id: config.id + '-base',
                        }]
                    }, {
                        columnWidth: .3
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                                xtype: xtype_count,
                                fieldLabel: _('seofilter_rule_count_parents'),
                                name: 'count_parents',
                                description: _('seofilter_rule_count_parents_help'),
                                id: config.id + '-count_parents',
                                anchor: '99%',
                                maxLength: 255,
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: _('seofilter_rule_rank'),
                                description: _('seofilter_rule_rank_help'),
                                name: 'rank',
                                id: config.id + '-rank',
                                anchor: '99%',
                            }, {
                                xtype: 'xcheckbox',
                                boxLabel: _('seofilter_rule_active'),
                                description: _('seofilter_rule_active_help'),
                                name: 'active',
                                id: config.id + '-active',
                            }
                        ]
                    }]
                }]
            }, {
                title: _('seofilter_seo')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: this.getMetaFields(config)
            }]
        };
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-rule-window-create', SeoFilter.window.CreateRule);


SeoFilter.window.UpdateRule = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-rule-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_rule_update'),
        width: 650,
        autoHeight: false,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });

    SeoFilter.window.UpdateRule.superclass.constructor.call(this, config);

    this.addEvents({
        success: true
        ,failure: true
        ,beforeSubmit: true
    });

    this.on('show',function (e) {
        var form = Ext.getCmp(this.id+'-rule_form');
        // form.syncSize();
    });

    this.on('success',function (e,k) {
        // console.log(e,this);
    });



};

Ext.extend(SeoFilter.window.UpdateRule, MODx.Window, {

    f_submit: function (f,messageBox,close,offset) {
        if(offset) {f.baseParams['offset'] = offset;}
        f.submit({
            scope:this,
            failutre: function(frm,a) {
                if (this.fireEvent('failure',{f:frm,a:a})) {
                    MODx.form.Handler.errorExt(a.result,frm);
                }
                this.doLayout();
            },
            success: function (frm,a) {
                var response = Ext.decode(a.response.responseText);
                var data = response.object.data;
                if(!data.done) {
                    messageBox.updateProgress(data.value,data.text);
                    this.f_submit(f,messageBox,close,data.offset);
                } else {
                    messageBox.hide();
                    if (this.config.success) {
                        Ext.callback(this.config.success, this.config.scope || this, [frm, a]);
                    }
                    this.fireEvent('success', {f: frm, a: a});
                    if (close) {
                        this.config.closeAction !== 'close' ? this.hide() : this.close();
                    }
                    this.doLayout();
                }
            }
        });
    },

    //TODO: метод для пошагового ajax-добавления ссылок
    _submit: function (close) {
        close = close === false ? false : true;
        var f = this.fp.getForm();
        if(f.isValid() && this.fireEvent('beforeSubmit',f.getValues())) {
            var messageBox = Ext.MessageBox.progress(_ ('seofilter_rule_create_links'), _('seofilter_rule_create_links_wait'), '...');
            this.f_submit(f,messageBox,close);
        }
    },


    successLoad:function (config) {
        // console.log(config);
    },

    loadRte: function (config) {
        if(!config.height) {
            config.height = 250;
        }
        Ext.getCmp(config.id).setHeight(config.height);
        if(MODx.loadRTE !== 'undefined') {
            window.setTimeout(function() {
                MODx.loadRTE(config.id);
            }, 300);
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
            html: _('seofilter_multiseo_intro'),
            cls: 'panel-desc',
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
                    if(field.indexOf('Rule.') != -1) {
                        field = field.replace('Rule.','').split(':');
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
                    if(field.indexOf('Rule.') != -1 && richtexts.hasOwnProperty(field.replace('Rule.','')) === false) {
                        field = field.replace('Rule.','').split(':');
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
                id: config.id +'-'+field,
                anchor: '99%',
                description: "[[!+sf."+field+"]] / {$_modx->getPlaceholder('sf."+field+"')}"
            },fields_name[field]);

            if(richtexts.hasOwnProperty(field)) {
                if(richtexts[field]) {field_data['height'] = richtexts[field];}
                field_data['listeners'] = {'render': {fn:this.loadRte,scope:this}};
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
        var xtype_count = 'hidden';
        if(parseInt(SeoFilter.config.count_childrens) !== 0) {
            xtype_count = 'textfield';
        }

        var tabs = {
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,border: true
            ,items: [{
                title: _('seofilter_rule')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,id:config.id + '-rule_form'
                ,autoHeight: true
                ,autoScroll: false
                ,style:'overflow:hidden;'
                ,border:false
                ,items: [{
                    xtype: 'hidden',
                    name: 'id',
                    id: config.id + '-id',
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_name'),
                    description: _('seofilter_rule_name_help'),
                    name: 'name',
                    id: config.id + '-name',
                    anchor: '99%',
                    allowBlank: false,
                    maxLength: 255,
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_rule_link_tpl'),
                    name: 'link_tpl',
                    id: config.id + '-link_tpl',
                    description: _('seofilter_rule_link_tpl_help'),
                    anchor: '99%',
                    maxLength: 255,
                },{
                    layout: 'column',
                    border: false,
                    anchor: '99%',
                    style: 'margin-top:-10px',
                    items: [{
                        columnWidth: .45
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_relinks'),
                            description: _('seofilter_rule_relinks_help'),
                            name: 'relinks',
                            id: config.id + '-relinks',
                        }]
                    },{
                        columnWidth: .55
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count==='hidden'?'hidden':'xcheckbox',
                            boxLabel: _('seofilter_rule_recount'),
                            description: _('seofilter_rule_recount_help'),
                            name: 'recount',
                            id: config.id + '-recount',
                        }]
                    }]
                }, {
                    title: _('seofilter_rule_fields')
                    ,xtype: 'seofilter-grid-fieldids'
                    ,anchor:'99%'
                    ,id: config.id+'_gridfield'
                    ,idNumber: config.id
                    ,record: config.record.object
                },{
                    xtype: 'textfield',
                    hideLabel:true,
                    fieldLabel: _('seofilter_rule_url_more'),
                    name: 'url',
                    readOnly: true,
                    disabled:true,
                    style: 'margin-top:-11px;',
                    description: _('seofilter_rule_url_help'),
                    emptyText: _('seofilter_rule_url_more'),
                    id: config.id + '-url',
                    anchor: '99%',
                },{
                    layout: 'column',
                    border: false,
                    anchor: '99%',
                    items: [{
                        columnWidth: .7
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
                            fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
                            name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
                            description: parseInt(SeoFilter.config.proMode)? _('seofilter_rule_parents_help'):_('seofilter_rule_parent_help'),
                            allowBlank: false,
                            id: parseInt(SeoFilter.config.proMode)? config.id + '-pages':config.id + '-page',
                            anchor: '99%',
                            maxLength: 255,

                        },{
                            xtype: xtype_count,
                            fieldLabel: _('seofilter_rule_count_where'),
                            description: _('seofilter_where_help'),
                            name: 'count_where',
                            id: config.id + '-count_where',
                            anchor: '99%',
                            maxLength: 255,
                        },{
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_base_more'),
                            description: _('seofilter_rule_base_help'),
                            name: 'base',
                            id: config.id + '-base',
                            labelStyle: 'margin-top:-3px;'
                        }]
                    }, {
                        columnWidth: .3
                        , layout: 'form'
                        , defaults: {msgTarget: 'under'}
                        , border: false
                        , items: [{
                            xtype: xtype_count,
                            fieldLabel: _('seofilter_rule_count_parents'),
                            description: _('seofilter_rule_count_parents_help'),
                            name: 'count_parents',
                            id: config.id + '-count_parents',
                            anchor: '99%',
                            maxLength: 255,
                        }, {
                            xtype: 'numberfield',
                            fieldLabel: _('seofilter_rule_rank'),
                            description: _('seofilter_rule_rank_help'),
                            name: 'rank',
                            id: config.id + '-rank',
                            anchor: '99%',
                        }, {
                            xtype: 'xcheckbox',
                            boxLabel: _('seofilter_rule_active'),
                            description: _('seofilter_rule_active_help'),
                            name: 'active',
                            id: config.id + '-active',
                            labelStyle: 'margin-top:-3px;'
                        }]
                    }]

                }]
            }, {
                title: _('seofilter_seo')
                ,hideMode: 'offsets'
                ,layout: 'form'
                ,border:false
                ,items: this.getMetaFields(config)
            }]
        };

        if(parseInt(SeoFilter.config.hiddenTab)) {
            tabs.items.push({
                title: _('seofilter_rule_properties')
                , hideMode: 'offsets'
                , layout: 'form'
                , border: false
                , items: [{
                    xtype: parseInt(SeoFilter.config.superHiddenProps) === 1 ? 'seofilter-combo-tpls' : 'hidden',
                    fieldLabel: _('seofilter_rule_tpl'),
                    name: 'tpl',
                    id: config.id + '-tpl',
                    anchor: '99%',
                }, {
                    html: '',
                    style: 'margin-bottom:10px'
                }, {
                    html: _('seofilter_rule_properties_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-combobox-options',
                    anchor: '99%',
                    name: 'properties',
                    record: config.record.object,
                    id: config.id + '-properties',
                }, {
                    html: '',
                    style: 'margin-bottom:10px'
                }, {
                    html: _('seofilter_rule_properties_introtexts'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-combobox-options',
                    anchor: '99%',
                    name: 'introtexts',
                    record: config.record.object,
                    id: config.id + '-introtexts',
                }, {
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_rule_introlength'),
                    name: 'introlength',
                    id: config.id + '-introlength',
                    anchor: '99%',
                }]
            });
        }

        return tabs;
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-rule-window-update', SeoFilter.window.UpdateRule);


SeoFilter.window.duplicateRule = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('seofilter_rule_duplicate'),
        width: 600,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/duplicate',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.duplicateRule.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.duplicateRule, MODx.Window, {
    getFields: function (config) {
        return [{
            xtype: 'hidden',
            anchor: '99%',
            name: 'id',
        }, {
            fieldLabel: _('seofilter_rule_name'),
            xtype: 'textfield',
            anchor: '99%',
            name: 'name',
            allowBlank: false,
            maxLength: 255,
        }, {
            xtype: parseInt(SeoFilter.config.proMode)?'textfield':'seofilter-combo-resource',
            fieldLabel: parseInt(SeoFilter.config.proMode)? _('seofilter_field_pages') : _('seofilter_field_page'),
            name: parseInt(SeoFilter.config.proMode)? 'pages':'page',
            anchor: '99%',
            allowBlank: false,
            maxLength: 255,
        }, {
            boxLabel: _('seofilter_rule_copy_fields'),
            xtype: 'xcheckbox',
            anchor: '99%',
            name: 'copy_fields',
        }, {
            xtype: parseInt(SeoFilter.config.count_childrens)===0?'hidden':'xcheckbox',
            boxLabel: _('seofilter_rule_recount'),
            name: 'recount',
        },{
            boxLabel: _('seofilter_rule_active'),
            xtype: 'xcheckbox',
            anchor: '99%',
            name: 'active',
        }];
    },
});
Ext.reg('seofilter-window-copy-rule', SeoFilter.window.duplicateRule);