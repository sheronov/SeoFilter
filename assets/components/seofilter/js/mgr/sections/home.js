SeoFilter.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'seofilter-panel-home',
            renderTo: 'seofilter-panel-home-div'
        }]
    });
    SeoFilter.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.page.Home, MODx.Component);
Ext.reg('seofilter-page-home', SeoFilter.page.Home);