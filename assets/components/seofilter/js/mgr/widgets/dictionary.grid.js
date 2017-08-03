SeoFilter.grid.Dictionary = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-grid-dictionaries';
    }
    Ext.applyIf(config, {
        url: SeoFilter.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/dictionary/getlist'
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateField(grid, e, row);
            }
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return !rec.data.active
                    ? 'seofilter-grid-row-disabled'
                    : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    SeoFilter.grid.Dictionary.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(SeoFilter.grid.Dictionary, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = SeoFilter.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createField: function (btn, e) {
        var w = MODx.load({
            xtype: 'seofilter-dictionary-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();

                    }, scope: this,
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },

    updateField: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/dictionary/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'seofilter-dictionary-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeField: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('seofilter_dictionaries_remove')
                : _('seofilter_dictionary_remove'),
            text: ids.length > 1
                ? _('seofilter_dictionaries_remove_confirm')
                : _('seofilter_dictionary_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/dictionary/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    disableField: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/dictionary/disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    enableField: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/dictionary/enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    getFields: function () {
        return ['id', 'field_id', 'field', 'input', 'value', 'alias', 'active', 'class', 'rank', 'key', 'actions','menu_on','menutitle','menuindex','link_attributes','fieldtitle','editedon'];
    },

    getColumns: function () {
        return [{
            header: _('seofilter_dictionary_id'),
            dataIndex: 'id',
            sortable: true,
            width: 50
        }, {
            header: _('seofilter_dictionary_field_id'),
            dataIndex: 'fieldtitle',
            sortable: true,
            width: 125,
        }, {
            header: _('seofilter_dictionary_input'),
            dataIndex: 'input',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_dictionary_value'),
            dataIndex: 'value',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_dictionary_alias'),
            dataIndex: 'alias',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_url_editedon'),
            dataIndex: 'editedon',
            sortable: true,
            renderer: SeoFilter.utils.formatDate,
            width: 100,
        }, {
            header: _('seofilter_dictionary_active'),
            dataIndex: 'active',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 50,
        }, {
            header: _('seofilter_grid_actions'),
            dataIndex: 'actions',
            renderer: SeoFilter.utils.renderActions,
            sortable: false,
            width: 75,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return [{
                text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_dictionary_create'),
                handler: this.createField,
                scope: this
            }, {
                xtype: 'seofilter-combo-field'
                ,id: 'tbar-seofilter-combo-field2'
                ,width: 200
                ,addall: true
                ,emptyText: _('seofilter_filter_field')
                ,listeners: {
                    select: {fn: this.filterByField, scope:this}
                }
            // },{
            //     xtype: 'seofilter-combo-class'
            //     ,id: 'tbar-seofilter-combo-class'
            //     ,width: 200
            //     ,addall: true
            //     ,emptyText: _('seofilter_filter_class_or')
            //     ,listeners: {
            //         select: {fn: this.filterByClass, scope:this}
            //     }
            },{
                xtype: 'button'
                ,id: 'seofilter-url-filters-clear'
                ,text: '<i class="icon icon-times"></i>'
                ,listeners: {
                    click: {fn: this.clearFilter, scope: this}
                }
            },
            '->', {
            xtype: 'seofilter-field-search',
            width: 250,
            listeners: {
                search: {
                    fn: function (field) {
                        this._doSearch(field);
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._clearSearch();
                    }, scope: this
                }
            }
        }];
    },

    filterByField: function(cb) {
        this.getStore().baseParams['field'] = cb.value;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    filterByClass: function(cb) {
        this.getStore().baseParams['class'] = cb.value;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },

    clearFilter: function(btn,e) {
        var s = this.getStore();
        s.baseParams['field'] = '';
       // s.baseParams['class'] = '';
        Ext.getCmp('tbar-seofilter-combo-field2').setValue('');
        //Ext.getCmp('tbar-seofilter-combo-class').setValue('');
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },
});
Ext.reg('seofilter-grid-dictionaries', SeoFilter.grid.Dictionary);
