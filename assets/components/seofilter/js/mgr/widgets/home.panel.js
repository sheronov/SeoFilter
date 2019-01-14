SeoFilter.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('seofilter') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            stateful: true,
            stateId: 'seofilter-tabs',
            stateEvents: ['tabchange'],
            cls: 'seofilter-panel',
            getState: function () {
                return {
                    activeTab: this.items.indexOf(this.getActiveTab())
                };
            },
            hideMode: 'offsets',
            items: [{
                title: _('seofilter_fields'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_fields_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-fields',
                    id: 'seofilter-grid-fields',
                    cls: 'main-wrapper',
                }]
            },{
                title: _('seofilter_rules'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_rules_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-rules',
                    id: 'seofilter-grid-rules',
                    cls: 'main-wrapper',
                }]
            },{
                title: _('seofilter_dictionary'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_dictionary_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-dictionaries',
                    id: 'seofilter-grid-dictionaries',
                    cls: 'main-wrapper',
                }]
            },{
                title: _('seofilter_urls'),
                layout: 'anchor',
                items: [{
                    html: _('seofilter_urls_intro'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'seofilter-grid-urls',
                    id: 'seofilter-grid-urls',
                    cls: 'main-wrapper',
                }]
            },{
                title: _('seofilter_settings'),
                layout: 'anchor',
                items: [{
                  html: _('seofilter_settings_intro'),
                  cls : 'panel-desc'
                },{
                    xtype:'seofilter-settings',
                    id: 'seofilter-settings',
                    cls: 'main-wrapper'
                }]
            }]
        }]
    });
    SeoFilter.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(SeoFilter.panel.Home, MODx.Panel);
Ext.reg('seofilter-panel-home', SeoFilter.panel.Home);
