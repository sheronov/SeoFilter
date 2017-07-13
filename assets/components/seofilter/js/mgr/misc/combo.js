SeoFilter.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    SeoFilter.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(SeoFilter.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('seofilter-combo-search', SeoFilter.combo.Search);
Ext.reg('seofilter-field-search', SeoFilter.combo.Search);


SeoFilter.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'page',
        hiddenName: 'page',
        displayField: 'pagetitle',
        valueField: 'id',
        editable: true,
        fields: ['id', 'pagetitle'],
        pageSize: 20,
        emptyText: _('seofilter_combo_select'),
        hideMode: 'offsets',
        url: SeoFilter.config.connector_url,
        baseParams: {
            action: 'mgr/system/getlist',
            combo: true
        }
    });
    SeoFilter.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.combo.Resource, MODx.combo.ComboBox);
Ext.reg('seofilter-combo-resource', SeoFilter.combo.Resource);

var classes = [
    ['msProductData', _('seofilter_msProductData')],
    ['modResource', _('seofilter_modResource')],
    ['msVendor', _('seofilter_msVendor')],
    ['msProductOption', _('seofilter_msProductOption')],
    ['modTemplateVar', _('seofilter_modTemplateVar')],
];
SeoFilter.combo.Class = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'class',
        hiddenName: 'class',
        displayField: 'class',
        valueField: 'cls',
        editable: true,
        store: new Ext.data.SimpleStore({
            id:0,
            fields:
                [
                    'cls',   //числовое значение - номер элемента
                    'class' //текст
                ],
            data:classes
        }),
       // fields: ['id', 'pagetitle'],
        pageSize: 20,
        emptyText: _('seofilter_combo_select'),
        hideMode: 'offsets',
        mode: 'local',
        forceSelection: false,

        // url: SeoFilter.config.connector_url,
        // baseParams: {
        //     action: 'mgr/system/getlist',
        //     combo: true
        // }
    });
    SeoFilter.combo.Class.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.combo.Class, MODx.combo.ComboBox);
Ext.reg('seofilter-combo-class', SeoFilter.combo.Class);


SeoFilter.combo.Field = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'field_id',
        hiddenName: 'field_id',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 20,
        emptyText: _('seofilter_combo_select'),
        allowBlank: false,
        url: SeoFilter.config.connector_url,
        baseParams: {
            action: 'mgr/field/getlist',
            combo: true,
            id: config.value
        },
        tpl: new Ext.XTemplate(''
            +'<tpl for="."><div class="x-combo-list-item seofilter-field-list-item">'
            +'<span><small>({id})</small> <b>{name}</b></span>'
            +'</div></tpl>',{
            compiled: true
        }),
        itemSelector: 'div.seofilter-field-list-item'
    });
    SeoFilter.combo.Field.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.combo.Field, MODx.combo.ComboBox);
Ext.reg('seofilter-combo-field', SeoFilter.combo.Field);

SeoFilter.combo.Rule = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'multi_id',
        hiddenName: 'multi_id',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 20,
        emptyText: _('seofilter_combo_select'),
        allowBlank: false,
        url: SeoFilter.config.connector_url,
        baseParams: {
            action: 'mgr/rule/getlist',
            combo: true,
            id: config.value
        },
        tpl: new Ext.XTemplate(''
            +'<tpl for="."><div class="x-combo-list-item seofilter-rule-list-item">'
            +'<span><small>({id})</small> <b>{name}</b></span>'
            +'</div></tpl>',{
            compiled: true
        }),
        itemSelector: 'div.seofilter-rule-list-item'
    });
    SeoFilter.combo.Rule.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.combo.Rule, MODx.combo.ComboBox);
Ext.reg('seofilter-combo-rule', SeoFilter.combo.Rule);