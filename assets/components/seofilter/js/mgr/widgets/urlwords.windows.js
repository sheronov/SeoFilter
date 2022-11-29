SeoFilter.window.CreateUrlWord = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-urlword-window-create';
    }
    Ext.applyIf(config, {
        title: _('seofilter_url_word_add') || 'Добавить слово',
        width: 450,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/urlword/create',
        bodyStyle: 'padding-top:10px;',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.CreateUrlWord.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.CreateUrlWord, MODx.Window, {
    getFields: function (config) {
        return  [{
            xtype: 'hidden',
            name: 'url_id',
            id: config.id + '-url_id',
            //value:  config.record.id,
        },{
            xtype: 'seofilter-combo-field',
            fieldLabel: _('seofilter_urlword_field') || 'Поле',
            name: 'field_id',
            id: config.id + '-field_id',
            anchor: '99%',
            allowBlank: false,
            listeners: {
                select: {
                    fn: function (element, rec, idx) {
                        var comboWord = Ext.getCmp(config.id + '-word_id');
                        comboWord.setValue('')
                        comboWord.baseParams.field = rec.id;
                        comboWord.reload();
                    }
                }
            }
        }, {
            xtype: 'seofilter-combo-word',
            fieldLabel: _('seofilter_urlword_word') || 'Слово',
            name: 'word_id',
            emptyText: _('seofilter_combo_select'),
            valueNotFoundText: '',
            id: config.id + '-word_id',
            anchor: '99%',
            baseParams: {
                action: 'mgr/dictionary/getlist',
                combo: true,
                field: -1,
            },
            allowBlank: false,
        },{
            xtype: 'numberfield',
            fieldLabel: _('seofilter_urlword_priority'),
            name: 'priority',
            id: config.id + '-priority',
            anchor: '99%',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-urlword-window-create', SeoFilter.window.CreateUrlWord);


SeoFilter.window.UpdateUrlWord = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-urlword-window-update';
    }
    Ext.applyIf(config, {
        title: _('seofilter_urlword_update') || 'Редактирование слова',
        width: 450,
        autoHeight: true,
        url: SeoFilter.config.connector_url,
        action: 'mgr/urls/urlword/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.UpdateUrlWord.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.window.UpdateUrlWord, MODx.Window, {

    getFields: function (config) {
        return  [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        },{
            xtype: 'hidden',
            name: 'url_id',
            id: config.id + '-url_id',
            //value:  config.record.id,
        },{
            xtype: 'seofilter-combo-field',
            fieldLabel: _('seofilter_urlword_field') || 'Поле',
            name: 'field_id',
            id: config.id + '-field_id',
            anchor: '99%',
            allowBlank: false,
            listeners: {
                select: {
                    fn: function (element, rec, idx) {
                        var comboWord = Ext.getCmp(config.id + '-word_id');
                        comboWord.setValue('')
                        comboWord.baseParams.field = rec.id;
                        comboWord.reload();
                    }
                }
            }
        }, {
            xtype: 'seofilter-combo-word',
            fieldLabel: _('seofilter_urlword_word') || 'Слово',
            name: 'word_id',
            emptyText: _('seofilter_combo_select'),
            id: config.id + '-word_id',
            anchor: '99%',
            baseParams: {
                action: 'mgr/dictionary/getlist',
                combo: true,
                field: config.record.object.field_id,
                id: config.record.object.word_id
            },
            allowBlank: false,
        }, {
            xtype: 'numberfield',
            fieldLabel: _('seofilter_urlword_priority'),
            name: 'priority',
            id: config.id + '-priority',
            anchor: '99%',
        }];

    },

    loadDropZones: function () {
    }

});
Ext.reg('seofilter-urlword-window-update', SeoFilter.window.UpdateUrlWord);
