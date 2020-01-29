SeoFilter.page.Seoedit = function (config) {
    config = config || {};
    Ext.applyIf(config, {
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

    cancel: function (btn, e) {
        var fp = Ext.getCmp(this.config.formpanel);
        MODx.loadPage('?');
    },

    preview: function (btn, e) {
        if(this.config.record.hasOwnProperty('url_preview')) {
            document.location.href = this.config.record['url_preview'];
        } else {
            this.cancel();
        }
    },

    getButtons: function (cfg) {
        var btns = [];
        btns.push({
            process: 'mgr/urls/update'
            , url: SeoFilter.config.connector_url
            , text: _('save')
            , id: 'seoedit-btn-save'
            , cls: 'primary-button'
            , method: 'remote'
            //,checkDirty: MODx.request.reload ? false : true
            , keys: [{
                key: MODx.config.keymap_save || 's'
                , ctrl: true
            }]
        });
        btns.push({
            text: _('cancel')
            , id: 'modx-abtn-cancel'
            , handler: this.cancel
            , scope: this
        });
        btns.push({
            text: '<i class="icon icon-eye"></i>'
            , id: 'modx-abtn-preview'
            , handler: this.preview
            , scope: this
        });
        return btns;
    }
});
Ext.reg('seofilter-page-seoedit', SeoFilter.page.Seoedit);