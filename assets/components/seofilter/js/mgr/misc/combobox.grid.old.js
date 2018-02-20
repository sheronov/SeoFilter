SeoFilter.grid.ComboboxOptions = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-grid-combobox-options';
    }
    if (!config.name) {
        config.name = 'properties';
    }


    Ext.applyIf(config, {
        autoHeight: false,
        height: 250,
        hideHeaders: true,
        anchor: '100%',
        layout: 'anchor',
        viewConfig: {
            forceFit: true
        },
        fields: ['dd', 'value', 'actions'],
        columns: this.getColumns(config),
        plugins: this.getPlugins(config),
        listeners: this.getListeners(config),
        bbar: this.getBottomBar(config),
        bodyCssClass: 'x-menu',
        cls: 'seofilter-grid',
    });
    SeoFilter.grid.ComboboxOptions.superclass.constructor.call(this, config);
    // setTimeout(function () {
        this.prepareValues();
    // },100);
};

Ext.extend(SeoFilter.grid.ComboboxOptions, MODx.grid.LocalGrid, {

    getColumns: function () {
        return [{
            header: _('sort'),
            dataIndex: 'dd',
            width: 10,
            align: 'center',
            renderer: function () {
                return String.format(
                    '<div class="sort icon icon-sort" style="cursor:move;" title="{0}"></div>',
                    _('move')
                );
            }
        }, {
            header: _('value'),
            dataIndex: 'value',
            editor: {
                xtype: 'textfield',
                listeners: {
                    change: {fn: this.prepareProperties, scope: this}
                }
            }
        }, {
            header: _('actions'),
            dataIndex: 'actions',
            width: 20,
            id: 'actions',
            align: 'center',
            renderer: function () {
                return String.format('\
                    <ul class="seofilter-row-actions">\
                       <li>\
                            <button class="btn btn-default icon icon-edit" title="{0}" action="editOption"></button>\
                       </li>\
                       <li>\
                            <button class="btn btn-default icon icon-remove action-red" title="{1}" action="removeOption"></button>\
                        </li>\
                    </ul>',
                    _('edit'),
                    _('remove')
                );
            }
        }];
    },

    getBottomBar: function (config) {
        return [{
            xtype: 'hidden',
            id: config.id + '-' + config.name,
            name: config.name
        },'->',{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_add_value'),
            handler: this.createOption,
            scope: this,
        }];
    },

    getPlugins: function () {
        return [new Ext.ux.dd.GridDragDropRowOrder({
            copy: false,
            scrollable: true,
            targetCfg: {},
            listeners: {
                afterrowmove: {fn: this.prepareProperties, scope: this}
            }
        })]
    },

    getListeners: function () {


        return {
            // viewready: {fn: this.prepareValues, scope: this},
            afteredit: {
                fn: function (e) {
                    this.prepareProperties();
                    this.addOption();
                }, scope: this
            }
        };
    },

    prepareValues: function () {
        var cname = this.config.name;
        if (this.record[cname] && this.record[cname]['values']) {
            Ext.each(this.record[cname]['values'], function (item) {
                this.store.add(new Ext.data.Record({
                    value: item
                }));
            }, this);
            this.store.add(new Ext.data.Record({
                value: ''
            }));
        }
        else {
            this.store.add(new Ext.data.Record({
                value: ''
            }));
            this.focusValueCell(0);
        }
        this.prepareProperties();
    },

    prepareProperties: function () {
        var datas = [];
        Ext.each(this.store.data.items,function (item) {
            if(item.data.value) {
                datas.push(item.data.value);
            }
        });
        var properties = {
            // values: this.store.collect('value')
            values: datas
        };
        properties = Ext.util.JSON.encode(properties);
        Ext.getCmp(this.config.id + '-' + this.config.name).setValue(properties);
    },

    createOption:function () {
        var record = new Ext.data.Record({
            value: ''
        });
        this.store.add(record) ;
        this.editWindow(record);
        // this.focusValueCell(this.store.data.length - 1);
    },

    addOption: function () {
        var datas = [];
        Ext.each(this.store.data.items,function (item) {
            if(item.data.value) {
                datas.push(item.data.value);
            }
        });
        // if (this.store.collect('value').length == this.store.data.length) {
        if (datas.length == this.store.data.length) {
            this.store.add(new Ext.data.Record({
                value: ''
            }));
            this.focusValueCell(this.store.data.length - 1);
        } else {
            // Ext.Msg.alert(_('error'), _('sf_err_value_duplicate'), function () {
            //     this.focusValueCell(this.store.data.length - 1);
            // }, this);
        }

        this.prepareProperties();
    },

    removeOption: function () {
        var record = this.getSelectionModel().getSelected();
        if (!record) {
            return false;
        }

        if (this.store.data.length == 1) {
            this.store.getAt(0).set('value', '');
            this.focusValueCell(0);
        }
        // else if ((datas.length != this.store.data.length) && record.data['value'] == '') {
        //     this.focusValueCell(this.store.data.length - 1);
        // }
        else {
            this.store.remove(record);
        }
        this.prepareProperties();
    },

    editOption: function (btn,e) {
        var record = this.getSelectionModel().getSelected();
        if(!record) {
            return false;
        }
        this.editWindow(record);
    },

    editWindow: function (record) {
        var w = MODx.load({
            xtype: 'seofilter-option-window-edit',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function (e) {
                        var obj = e.a.result.object;
                        record.set('value',obj.value);
                        record.commit();
                        this.prepareProperties();
                    }, scope: this,
                }
            }
        });
        w.reset();
        w.setValues({
            id: record.id,
            value: record.data.value,
            //rule_id:this.record.id,
            //name:this.name
        });
        w.show();
    },

    focusValueCell: function (row) {
        this.startEditing(row, 1);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this);
                }
            }
        }

        return this.processEvent('click', e);
    },


});
Ext.reg('seofilter-grid-combobox-options', SeoFilter.grid.ComboboxOptions);

SeoFilter.window.Option = function (config) {
    config = config || {};
    var btype = 'hidden';

    if(SeoFilter.config.url_help) {
        btype = 'button';
    }

    Ext.applyIf(config, {
        title: _('edit'),
        autoHeight:true,
        width:550,
        url: SeoFilter.config.connector_url,
        action: 'mgr/rule/option/update',
        fields: [{
            hideMode: 'offsets'
            ,layout: 'form'
            ,border:false
            ,items: [{
                xtype: 'hidden',
                anchor: '99%',
                name: 'id'
            }, {
                xtype: 'hidden',
                anchor: '99%',
                name: 'rule_id'
            }, {
                xtype: 'hidden',
                anchor: '99%',
                name: 'name'
            }, {
                xtype: 'textarea',
                name: 'value',
                fieldLabel: _('seofilter_dictionary_value'),
                anchor: '99%',
                height: 200,
                id: config.id + '-value',
                listeners: {
                    render: function () {
                        window.setTimeout(function() {
                            MODx.ux.Ace.replaceComponent(config.id+'-value', 'text/x-smarty', 1);
                            MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));
                            Ext.getCmp(config.id+'-value').setHeight(200);
                        }, 100);
                    }
                },
            }, {
                xtype: 'hidden',
                name: 'jdata',
                anchor: '99%',
            },{
                xtype: btype,
                style: 'margin-top:10px',
                text: '<i class="icon icon-question"></i> '+_('seofilter_help_window_open'),
                listeners: {
                    click: { fn: this.helpWindow, scope:this}
                },
               // handler: this.helpWindow,
               //  scope:this,
            }]
        }],
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    SeoFilter.window.Option.superclass.constructor.call(this, config); // Магия
};
Ext.extend(SeoFilter.window.Option, MODx.Window, {
    helpWindow: function() {
        var url = SeoFilter.config.url_help;
        if (!url) return;
        var helpWindow = new Ext.Window({
            title: _('seofilter_help_window')
            ,width: 400
            ,height: 500
            ,layout: 'fit'
            ,html: '<iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>'
        });
        helpWindow.setPosition(50,250);
        helpWindow.show();
    }
}); // Расширяем MODX.Window
Ext.reg('seofilter-option-window-edit', SeoFilter.window.Option); // Регистрируем новый xtype
