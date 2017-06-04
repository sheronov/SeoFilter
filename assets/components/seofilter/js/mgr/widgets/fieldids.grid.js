SeoFilter.grid.FieldIds = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'seofilter-grid-fieldids',
        url: SeoFilter.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/multifield/fieldids/getlist'
            ,multi_id: config.record.id
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateFieldIds(grid, e, row);
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
        limit: 0,
        pageSize: 0,
        paging: false,
        remoteSort: true,
        autoHeight: true,
        multi_select: true,
        stateful: true,
        stateId: config.id,
        autosave: true,
        save_action: 'mgr/multifield/fieldids/updatefromgrid',
        plugins: this.getPlugins(config),
        ddGroup: 'dd-ids-grid',
        enableDragDrop: true,
    });
    SeoFilter.grid.FieldIds.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(SeoFilter.grid.FieldIds, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = SeoFilter.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    getPlugins: function () {
        return [new Ext.ux.dd.GridDragDropRowOrder({
            copy: false,
            scrollable: true,
            targetCfg: {},
            listeners: {
                afterrowmove: {
                    fn: this.onAfterRowMove,
                    scope: this
                }
            }
        })];
    },

    createFieldIds: function (btn, e) {
        var w = MODx.load({
            xtype: 'seofilter-fieldids-window-create',
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
        w.setValues({active: true,multi_id:this.config.record.id,priority:this.config.store.totalLength});
        w.show(e.target);
    },

    updateFieldIds: function (btn, e, row) {
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
                action: 'mgr/multifield/fieldids/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'seofilter-fieldids-window-update',
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

    removeFieldIds: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('seofilter_fieldids_remove')
                : _('seofilter_fieldids_remove'),
            text: ids.length > 1
                ? _('seofilter_fieldids_remove_confirm')
                : _('seofilter_fieldids_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/multifield/fieldids/remove',
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

    disableFieldIds: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/multifield/fieldids/disable',
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

    enableFieldIds: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/multifield/fieldids/enable',
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
        return ['id','field_id','name','multi_id','priority','where','compare','value','condition','compare_string','condition_bool','actions','active'];
    },

    getColumns: function () {
        return [{
            //     header: _('seofilter_fieldids_id'),
            //     dataIndex: 'id',
            //     width: 50
            // },{
            header: _('seofilter_fieldids_field_id'),
            dataIndex: 'name',
            width: 100
        },{
            header: _('seofilter_fieldids_priority'),
            dataIndex: 'priority',
            width: 50
        },{
            header: _('seofilter_fieldids_where'),
            dataIndex: 'where',
            width: 50,
            renderer: SeoFilter.utils.renderBoolean,
        },{
            header: _('seofilter_fieldids_compare'),
            dataIndex: 'compare',
            width: 50
        },{
            header: _('seofilter_fieldids_value'),
            dataIndex: 'value',
            width: 100
            // },{
            //     header: _('seofilter_fieldids_condition'),
            //     dataIndex: 'condition',
            //     width: 100
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
            handler: this.createFieldIds,
            scope: this
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

    onAfterRowMove: function () {
        var s = this.getStore();
        var start = 0;
        var size = s.getTotalCount();
        for (var x = 0; x < size; x++) {
            var brec = s.getAt(x);
            brec.set('priority', start + x);
            brec.commit();
            this.saveRecord({record: brec});
        }
        return true;
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

});
Ext.reg('seofilter-grid-fieldids', SeoFilter.grid.FieldIds);
