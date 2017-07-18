SeoFilter.page.Seoedit = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        //url: SeoFilter.config.connector_url,
        //action: 'mgr/urls/update',
        formpanel: 'seofilter-panel-seoedit',
        id: 'modx-page-seoedit',
        components: [{
            xtype: 'seofilter-panel-seoedit',
            renderTo: 'seofilter-panel-seoedit-div',
            url_id: config.url_id,
            record: config.record || {}
        }],
        buttons: this.getButtons(config)
    });
    SeoFilter.page.Seoedit.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.page.Seoedit, MODx.Component, {

    cancel: function(btn,e) {
        var fp = Ext.getCmp(this.config.formpanel);
        console.log(fp);
        // if (fp && fp.isDirty()) {
        //     Ext.Msg.confirm(_('warning'),_('resource_cancel_dirty_confirm'),function(e) {
        //         if (e == 'yes') {
        //             fp.warnUnsavedChanges = false;
        //             MODx.releaseLock(MODx.request.id);
        //             MODx.sleep(400);
        //             MODx.loadPage('?');
        //         }
        //     },this);
        // } else {
        //     MODx.releaseLock(MODx.request.id);
        //     MODx.loadPage('?');
        // }
        MODx.loadPage('?');
    },

    getButtons: function(cfg) {
        var btns = [];
        btns.push({
            process: 'mgr/urls/update'
            ,url: SeoFilter.config.connector_url
            ,text: _('save')
            ,id: 'seoedit-btn-save'
            ,cls: 'primary-button'
            ,method: 'remote'
            //,checkDirty: MODx.request.reload ? false : true
            ,keys: [{
                key: MODx.config.keymap_save || 's'
                ,ctrl: true
            }]
        });
        btns.push({
            text: _('cancel')
            ,id: 'modx-abtn-cancel'
            ,handler: this.cancel
            ,scope: this
        });
        return btns;
    }
});
Ext.reg('seofilter-page-seoedit', SeoFilter.page.Seoedit);