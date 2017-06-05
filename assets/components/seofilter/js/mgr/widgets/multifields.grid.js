SeoFilter.grid.MultiFields = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-grid-multifields';
    }
    Ext.applyIf(config, {
        url: SeoFilter.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/multifield/getlist'
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
    SeoFilter.grid.MultiFields.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(SeoFilter.grid.MultiFields, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = SeoFilter.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createField: function (btn, e) {
        var w = MODx.load({
            xtype: 'seofilter-multifield-window-create',
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
                action: 'mgr/multifield/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        if(this.windows.updateField) {
                            this.windows.updateField.close();
                            this.windows.updateField.destroy();
                        }
                        this.windows.updateField = MODx.load({
                            xtype: 'seofilter-multifield-window-update',
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

                        this.windows.updateField.reset();
                        this.windows.updateField.setValues(r.object);
                        this.windows.updateField.show(e.target);
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
                ? _('seofilter_multifields_remove')
                : _('seofilter_multifield_remove'),
            text: ids.length > 1
                ? _('seofilter_multifields_remove_confirm')
                : _('seofilter_multifield_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/multifield/remove',
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
                action: 'mgr/multifield/disable',
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
                action: 'mgr/multifield/enable',
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
        return ['id', 'name', 'page', 'url', 'active', 'count','rank', 'fields', 'actions','pagetitle','seo_id'];
    },

    getColumns: function () {
        return [{
            header: _('seofilter_multifield_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        }, {
            header: _('seofilter_multifield_name'),
            dataIndex: 'name',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_multifield_page'),
            dataIndex: 'page',
            renderer: SeoFilter.utils.renderResource,
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_multifield_url'),
            dataIndex: 'url',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_multifield_seo_id'),
            dataIndex: 'seo_id',
            sortable: true,
            width: 70,
        }, {
            header: _('seofilter_multifield_count'),
            dataIndex: 'count',
            sortable: true,
            width: 70,
        }, {
            header: _('seofilter_multifield_fields'),
            dataIndex: 'fields',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_multifield_active'),
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
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_multifield_create'),
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
Ext.reg('seofilter-grid-multifields', SeoFilter.grid.MultiFields);

