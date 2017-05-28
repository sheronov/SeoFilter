var SeoFilter = function (config) {
    config = config || {};
    SeoFilter.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('seofilter', SeoFilter);

SeoFilter = new SeoFilter();