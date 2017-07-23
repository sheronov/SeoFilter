SeoFilter.grid.Urls = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'seofilter-grid-urls';
    }
    Ext.applyIf(config, {
        url: SeoFilter.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/urls/getlist'
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
    SeoFilter.grid.Urls.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(SeoFilter.grid.Urls, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = SeoFilter.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createField: function (btn, e) {
        var w = MODx.load({
            xtype: 'seofilter-url-window-create',
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
                action: 'mgr/urls/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'seofilter-url-window-update',
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
                ? _('seofilter_urls_remove')
                : _('seofilter_url_remove'),
            text: ids.length > 1
                ? _('seofilter_urls_remove_confirm')
                : _('seofilter_url_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/urls/remove',
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
                action: 'mgr/urls/disable',
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
                action: 'mgr/urls/enable',
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
        return ['id', 'multi_id', 'page_id', 'name', 'old_url', 'custom', 'new_url', 'editedon', 'createdon', 'count', 'rank', 'active', 'actions','multi_name','url_preview','page','pagetitle','menu_on','menutitle','menuindex','image','link_attributes'];
    },

    getColumns: function () {
        return [{
            header: _('seofilter_url_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        }, {
            header: _('seofilter_url_multi_id'),
            dataIndex: 'name',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_url_page_id'),
            dataIndex: 'page_id',
            renderer: SeoFilter.utils.renderResource,
            sortable: true,
            width: 100,
        }, {
            header: _('seofilter_url_old_url'),
            dataIndex: 'old_url',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_url_new_url'),
            dataIndex: 'new_url',
            sortable: true,
            width: 150,
        }, {
            header: _('seofilter_url_editedon'),
            dataIndex: 'editedon',
            sortable: true,
            renderer: SeoFilter.utils.formatDate,
            width: 100,
        }, {
            header: _('seofilter_url_createdon'),
            dataIndex: 'createdon',
            sortable: true,
            renderer: SeoFilter.utils.formatDate,
            width: 100,
        }, {
            header: _('seofilter_url_count'),
            dataIndex: 'count',
            sortable: true,
            width: 70,
        }, {
            header: _('seofilter_url_active'),
            dataIndex: 'active',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 75,
        }, {
            header: _('seofilter_url_custom'),
            dataIndex: 'custom',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 75,
        }, {
            header: _('seofilter_url_menu'),
            dataIndex: 'menu_on',
            renderer: SeoFilter.utils.renderBoolean,
            sortable: true,
            width: 75,
        }, {
            header: _('seofilter_grid_actions'),
            dataIndex: 'actions',
            renderer: SeoFilter.utils.renderActions,
            sortable: false,
            width: 125,
            id: 'actions'
        }];
    },

    viewPage: function () {
        //console.log(this);
        window.open(this.menu.record['url_preview']);
        return false;
    },

    getTopBar: function () {
        return [
        //     {
        //     text: '<i class="icon icon-plus"></i>&nbsp;' + _('seofilter_url_create'),
        //     handler: this.createField,
        //     scope: this
        // },
            {
                xtype: 'seofilter-combo-rule'
                ,id: 'tbar-seofilter-combo-rule'
                ,width: 200
                ,addall: true
                ,emptyText: _('seofilter_filter_rule')
                ,listeners: {
                    select: {fn: this.filterByRule, scope:this}
                }
            },{
                xtype: 'seofilter-combo-resource'
                ,id: 'tbar-seofilter-combo-resource'
                ,width: 200
                ,addall: true
                ,emptyText: _('seofilter_filter_resource_or')
                ,listeners: {
                    select: {fn: this.filterByResource, scope:this}
                }
                ,baseParams: {
                    action: 'mgr/system/getlist',
                    combo: true,
                    rules: true,
                }
            },{
                xtype: 'button'
                ,id: 'seofilter-filters-clear'
                ,text: '<i class="icon icon-times"></i>'
                ,listeners: {
                    click: {fn: this.clearFilter, scope: this}
                }
            }, {
                xtype:'button'
                ,id: 'sofilter-clear-counters'
                ,text:  _('seofilter_clear_counters')
                ,handler: this.clearCounters
                ,scope: this
            }, '->', {
            xtype: 'seofilter-field-search',
            width: 200,
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

    clearCounters: function(confirmed) {
        MODx.msg.confirm({
            title: _('seofilter_clear_counters'),
            text:  _('seofilter_clear_counters_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/urls/clear'
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
    },

    filterByRule: function(cb) {
        this.getStore().baseParams['rule'] = cb.value;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },
    filterByResource: function(cb) {
        this.getStore().baseParams['page'] = cb.value;
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },

    clearFilter: function(btn,e) {
        var s = this.getStore();
        s.baseParams['rule'] = '';
        s.baseParams['page'] = '';
        Ext.getCmp('tbar-seofilter-combo-rule').setValue('');
        Ext.getCmp('tbar-seofilter-combo-resource').setValue('');
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
Ext.reg('seofilter-grid-urls', SeoFilter.grid.Urls);
