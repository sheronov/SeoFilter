SeoFilter.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'seofilter-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: false,
            hideMode: 'offsets',
            items: [{
                title: _('seofilter_items'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    SeoFilter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.panel.Home, MODx.Panel);
Ext.reg('seofilter-panel-home', SeoFilter.panel.Home);
