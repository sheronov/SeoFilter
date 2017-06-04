SeoFilter.grid.Fields = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-grid-fields';
    }
    Ext.applyIf(config, {
        url: SeoFilter.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/field/getlist'
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
    SeoFilter.grid.Fields.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(SeoFilter.grid.Fields, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = SeoFilter.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createField: function (btn, e) {
        var w = MODx.load({
            xtype: 'seofilter-field-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
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
                action: 'mgr/field/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'seofilter-field-window-update',
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
                ? _('seofilter_fields_remove')
                : _('seofilter_field_remove'),
            text: ids.length > 1
                ? _('seofilter_fields_remove_confirm')
                : _('seofilter_field_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/field/remove',
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
                action: 'mgr/field/disable',
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
                action: 'mgr/field/enable',
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
        return ['id', 'name', 'page', 'class', 'key', 'alias', 'translit', 'priority', 'urltpl', 'active', 'ajax','rank', 'seo', 'actions','pagetitle','translate','method'];
    },

    getColumns: function () {
        return [{
            header: _('seofilter_field_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        }, {
            header: _('seofilter_field_name'),
            dataIndex: 'name',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_page'),
            dataIndex: 'page',
            renderer: SeoFilter.utils.renderResource,
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_class'),
            dataIndex: 'class',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_key'),
            dataIndex: 'key',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_alias'),
            dataIndex: 'alias',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_translit'),
            dataIndex: 'translit',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: false,
            width: 75,
        }, {
            header: _('seofilter_field_urltpl'),
            dataIndex: 'urltpl',
            sortable: false,
            width: 200,
        }, {
            header: _('seofilter_field_priority'),
            dataIndex: 'priority',
            sortable: true,
            width: 50
        }, {
            header: _('seofilter_field_active'),
            dataIndex: 'active',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 75,
        }, {
            header: _('seofilter_grid_actions'),
            dataIndex: 'actions',
            renderer: SeoFilter.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_field_create'),
            handler: this.createField,
            scope: this
        }, '->', {
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
                },
            }
        }];
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
Ext.reg('seofilter-grid-fields', SeoFilter.grid.Fields);
