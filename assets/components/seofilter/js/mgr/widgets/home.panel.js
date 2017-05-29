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
            html: '<h2>' + _('seofilter') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('seofilter_fields'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_fields_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-fields',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    SeoFilter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.panel.Home, MODx.Panel);
Ext.reg('seofilter-panel-home', SeoFilter.panel.Home);
