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
        stateful: true,
        stateId: config.id,
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
        var values = btn.initialConfig.data;
        if(!values) {
            values = {active: true,exact:true};
        }
        var ext_id = Ext.id();
        var w = MODx.load({
            xtype: 'seofilter-field-window-create',
            id: ext_id,
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                        Ext.getCmp('seofilter-grid-dictionaries').refresh();
                        Ext.getCmp('seofilter-grid-rules').refresh();
                        Ext.getCmp('seofilter-grid-urls').refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(values);
        w.show(e.target);
        if(btn.initialConfig.focus_name) {
            var focus = Ext.getCmp(ext_id+'-'+btn.initialConfig.focus_name);
            setTimeout(function () {
                focus.focus();
            },500);
        }
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
                                        Ext.getCmp('seofilter-grid-dictionaries').refresh();
                                        Ext.getCmp('seofilter-grid-rules').refresh();
                                        Ext.getCmp('seofilter-grid-urls').refresh();
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
                        Ext.getCmp('seofilter-grid-dictionaries').refresh();
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
        return ['id', 'name', 'page', 'pages','class', 'key', 'alias', 'translit', 'baseparam', 'priority', 'xpdo', 'xpdo_where', 'relative', 'active', 'rank', 'actions','pagetitle','slider','exact','valuefirst','hideparam'];
    },

    getColumns: function () {
        return [{
            header: _('seofilter_field_id'),
            dataIndex: 'id',
            sortable: true,
            width: 40
        }, {
            header: _('seofilter_field_name'),
            dataIndex: 'name',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_field_class'),
            dataIndex: 'class',
            sortable: true,
            width: 100,
        }, {
            header: _('seofilter_field_key'),
            dataIndex: 'key',
            sortable: true,
            width: 100,
        }, {
            header: _('seofilter_field_alias'),
            dataIndex: 'alias',
            sortable: true,
            width: 100,
        }, {
            header: _('seofilter_field_hideparam'),
            dataIndex: 'hideparam',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 50
        }, {
            header: _('seofilter_field_exact'),
            dataIndex: 'exact',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 50
        }, {
            header: _('seofilter_field_slider_title'),
            dataIndex: 'slider',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 50
        }, {
            header: _('seofilter_field_condition'),
            dataIndex: 'xpdo_where',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 50
        // }, {
        //     header: _('seofilter_field_xpdo_title'),
        //     dataIndex: 'xpdo',
        //     renderer: SeoFilter.utils.renderBoolean,
        //     sortable: true,
        //     width: 50
        // }, {
        //     header: _('seofilter_field_relation_title'),
        //     dataIndex: 'relation',
        //     renderer: SeoFilter.utils.renderBoolean,
        //     sortable: true,
        //     width: 50
        // }, {
        //     header: _('seofilter_field_active'),
        //     dataIndex: 'active',
        //     renderer: SeoFilter.utils.renderBoolean,
        //     sortable: true,
        //     width: 50,
        }, {
            header: _('seofilter_grid_actions'),
            dataIndex: 'actions',
            renderer: SeoFilter.utils.renderActions,
            sortable: true,
            width: 90,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return [ {
            id: 'seofilter-add-new-field',
            text: '<i class="icon icon-plus"></i> ' + _('seofilter_field_create'),
            menu: [{
                text:  '<i class="icon icon-plus"></i> ' + _('seofilter_field_manually'),
                cls: 'seofilter-menu-li',
                data: {active: true,exact:true},
                handler: this.createField,
                scope: this
            },'-',{
                text:  '<i class="icon icon-folder"></i> ' + _('seofilter_field_resource_parent'),
                cls: 'seofilter-menu-li',
                data: {
                    name: _('seofilter_field_parent'),
                    class: 'modResource',
                    key: 'parent',
                    xpdo:true,
                    xpdo_class: 'modResource',
                    xpdo_id: 'id',
                    xpdo_name: 'pagetitle',
                    active: true,
                    exact:true
                },
                focus_name: 'alias',
                handler: this.createField,
                scope: this
            },{
                text: '<i class="icon icon-building"></i> ' + _('seofilter_field_ms_vendor'),
                cls: 'seofilter-menu-li',
                data: {
                    name: _('seofilter_field_parent'),
                    class: 'msProductData',
                    key: 'vendor',
                    xpdo:true,
                    xpdo_package: 'minishop2',
                    xpdo_class: 'msVendor',
                    xpdo_id: 'id',
                    xpdo_name: 'name',
                    active: true,
                    exact:true
                },
                focus_name: 'alias',
                handler: this.createField,
                scope: this
            // },{
            //     text: '<i class="icon icon-barcode"></i> ' +  _('seofilter_field_ms_category'),
            //     cls: 'seofilter-menu-li',
            //     data: {
            //         name: _('seofilter_field_parent'),
            //         class: 'modResource',
            //         key: 'parent',
            //         xpdo:true,
            //         xpdo_package: '',
            //         xpdo_class: 'modResource',
            //         xpdo_id: 'id',
            //         xpdo_name: 'pagetitle',
            //         active: true,
            //         exact:true
            //     },
            //     focus_name: 'alias',
            //     handler: this.createField,
            //     scope: this
            // },{
            //     text:  '<i class="icon icon-zip"></i> ' + _('seofilter_field_ms_option'),
            //     cls: 'seofilter-menu-li',
            //     data: {
            //         name: _('seofilter_field_ms_option'),
            //         class: 'msProductData',
            //         key: 'color',
            //         alias: 'cvet',
            //         active: true,
            //         exact:true
            //     },
            //     handler: this.createField,
            //     scope: this
            }]
        // },{
        //     text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_field_create'),
        //     handler: this.createField,
        //     scope: this
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
