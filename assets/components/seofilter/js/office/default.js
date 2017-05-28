Ext.onReady(function () {
    SeoFilter.config.connector_url = OfficeConfig.actionUrl;

    var grid = new SeoFilter.panel.Home();
    grid.render('office-seofilter-wrapper');

    var preloader = document.getElementById('office-preloader');
    if (preloader) {
        preloader.parentNode.removeChild(preloader);
    }
});