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