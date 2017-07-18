SeoFilter.panel.Seoedit = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-panel-seoedit';
    }
    config.record = config.record || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        baseParams: {},
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/update',
        defaults: { collapsible: false ,autoHeight: true }
        ,bodyStyle: ''
        ,forceLayout: true
        ,cls: 'container form-with-labels'
        ,useLoadingMask: true
        ,listeners: {
             'setup': {fn:this.setup,scope:this},
             'success': {fn:this.success,scope:this},
           // 'render': {fn:this.setup,scope:this},
            // 'failure': {fn:this.failure,scope:this},
            'beforeSubmit': {fn:this.beforeSubmit,scope:this},
            // 'fieldChange': {fn:this.onFieldChange,scope:this},
        }
        ,hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('seofilter') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('seofilter_urls'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_urls_intro'),
                    cls: 'panel-desc',
                }, {
                    layout: 'anchor',
                    items: this.getFields(config),
                    //xtype: 'seofilter-grid-urls',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    SeoFilter.panel.Seoedit.superclass.constructor.call(this, config);
    //console.log(this.getForm());
};
Ext.extend(SeoFilter.panel.Seoedit, MODx.FormPanel, {
    initialized: false

    ,setup: function() {
        if (!this.initialized) {
            this.getForm().setValues({'active':parseInt(this.record.active)});
        }
        this.initialized = true;
    }

    ,beforeSubmit: function(o) {
        Ext.apply(o.form.baseParams,{"frame":1});
    }
    ,success: function(o) {
        Ext.getCmp('seoedit-btn-save').setDisabled(false);
        var object = o.result.object;
        console.log(o.result);
        if(o.result.message) {
            location.href = o.result.message;
        }
    }

    ,getFields: function (config) {
        return [{
            xtype: 'hidden',
            hideLabel: true,
            fieldLabel: _('seofilter_url_id'),
            anhchor: '99%',
            readOnly:true,
            name: 'id',
            id: config.id + '-id',
            submitValue: true,
            value: config.record.id
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
                    id: config.id + '-multi_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                    value: config.record.multi_id
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_old_url'),
                    name: 'old_url',
                    id: config.id + '-old_url',
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                    anchor: '99%',
                    value: config.record.old_url
                }, {
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_count'),
                    name: 'count',
                    //readOnly: true,
                    id: config.id + '-count',
                    anchor: '99%',
                    value: config.record.count
                }
                ]
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
                    id: config.id + '-page_id',
                    anchor: '99%',
                    allowBlank: false,
                    readOnly: true,
                    style: 'background:#f9f9f9;color:#999;',
                    value: config.record.page_id
                },{
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_new_url'),
                    name: 'new_url',
                    id: config.id + '-new_url',
                    anchor: '99%',
                    value: config.record.new_url
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_createdon'),
                    name: 'createdon',
                    id: config.id + '-createdon',
                    anchor: '99%',
                    value: config.record.createdon
                }, {
                    xtype: 'textfield',
                    fieldLabel: _('seofilter_url_editedon'),
                    name: 'editedon',
                    id: config.id + '-editedon',
                    anchor: '99%',
                    value: config.record.editedon

                }, {
                    xtype: 'numberfield',
                    fieldLabel: _('seofilter_url_ajax'),
                    name: 'ajax',
                    id: config.id + '-ajax',
                    //readOnly: true,
                    anchor: '99%',
                    value: config.record.ajax
                }]
            }]
        },{
            xtype: 'xcheckbox',
            boxLabel: _('seofilter_url_active_more'),
            name: 'active',
            style: 'padding-top:20px;position:relative;',
            id: config.id + '-active',
        }];
    }
});
Ext.reg('seofilter-panel-seoedit', SeoFilter.panel.Seoedit);


