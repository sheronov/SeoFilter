SeoFilter.panel.Settings = function (config) {
    config = config || {};

    Ext.apply(config, {

        baseCls: 'modx-formpanel',
        baseParams: {
            action: 'mgr/system/settings',
        },
        id: 'seofilter-panel-settings',
        url: SeoFilter.config.connector_url,
        defaults: { collapsible: false, autoHeight: true },
        bodyStyle: '',
        tbar: this.getTopBar(config),
        // buttons: this.getButtons(config),
        forceLayout: true,
        cls: 'main-wrapper seofilter-settings-wrapper',
        // cls: 'container form-with-labels main-wrapper',
        // useLoadingMask: true,
        listeners: {
            // 'setup': {fn:this.setup,scope:this},
            // 'success': {fn:this.success,scope:this},
            // 'failure': {fn:this.failure,scope:this},
            // 'beforeSubmit': {fn:this.beforeSubmit,scope:this},
        },
        hideMode: 'offsets',
        items: this.getItems(config)
    });
    SeoFilter.panel.Settings.superclass.constructor.call(this, config);

    // this.getForm().setValues(this.config.record);
};
Ext.extend(SeoFilter.panel.Settings, MODx.FormPanel, {

    initialized: false,

    setup: function() {
        if (!this.initialized) {
            this.initialized = true;
        }
    },

    success: function(o) {
        console.log('Успешно сохранены',o);
    },

    getButtons: function(cfg) {
        var btns = [];
        btns.push({
            process: 'mgr/system/settings'
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
        return btns;
    },

    getTopBar: function (config) {
        return [
            '->',
            {
                xtype: 'button'
                ,id: 'seofilter-settings-save'
                ,text: '<i class="icon icon-save"></i> ' + _('seofilter_settings_save')
                ,cls: 'primary-button'
                ,listeners: {
                    click: {fn: this.saveSettings, scope: this}
                }
        }];
    },

    saveSettings: function (btn,e) {
        this.getForm().submit({
            success: function(form, action) {
                Ext.Msg.alert(_('success'), _('seofilter_settings_success') + ': '+action.result.message);
            },
            failure: function(form, action) {
                switch (action.failureType) {
                    case Ext.form.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                        break;
                    case Ext.form.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                        break;
                    case Ext.form.Action.SERVER_INVALID:
                        Ext.Msg.alert('Failure', action.result.msg);
                }
            }
        });
    },

    objectMerge: function(obj1,obj2) {
        var obj3 = {};
        for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
        for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
        return obj3;
    },

    getItems:function (config) {
        var data = SeoFilter.config;
        var settings = {};
        var settings_fields = {
            url: {
                url_suffix: {},
                url_redirect: {xtype: 'combo-boolean', hiddenName: 'main_alias', value: data['redirect']},
                url_scheme: {value: data['scheme']},
                separator: {},
                between_urls: {},
                level_separator: {},
                main_alias: {xtype: 'combo-boolean', hiddenName: 'main_alias'},
            },
            count: {
                count: {xtype:'combo-boolean',hiddenName:'count',value:data['count_childrens']},
                hide_empty: {xtype:'combo-boolean',hiddenName:'hide_empty',value:data['hideEmpty']},
                default_where: {value:data['defaultWhere']},
                count_handler_class: {value:data['count_class']},
                select: {value:data['count_select']},
                choose: {value:data['count_choose']},
            },
            main: {
                ajax:{xtype: 'combo-boolean', hiddenName: 'ajax'},
                base_get: {},
                classes: {},
                templates: {},
                decline: {xtype: 'combo-boolean', hiddenName: 'decline'},
                morpher_token: {},
                page_key: {},
                crumbs_nested: {xtype: 'combo-boolean', hiddenName: 'crumbs_nested',value:data['crumbsNested']},
                crumbs_tpl_current: {value:data['crumbsCurrent']},
                content_ace: {},
                content_richtext: {},
                collect_words: {xtype: 'combo-boolean', hiddenName: 'collect_words', value:data['collect_words']},

            },
            ajax: {
                crumbs_replace: {xtype: 'combo-boolean', hiddenName: 'crumbs_replace',value:data['crumbsReplace']},
                replacebefore: {xtype: 'combo-boolean', hiddenName: 'replacebefore'},
                replaceseparator:{},
                jtitle:{},
                jh1:{},
                jh2:{},
                jdescription:{},
                jkeywords:{},
                jintrotext:{},
                jtext:{},
                jcontent:{},
                jlink: {}
            },
            pro: {
                pro_mode: {xtype: 'combo-boolean', hiddenName: 'pro_mode',value:data['proMode']},
                snippet: {value:data['prepareSnippet']},
                last_modified: {xtype: 'combo-boolean', hiddenName: 'last_modified',value:data['lastModified']},
                replace_host: {xtype: 'combo-boolean', hiddenName: 'replace_host'},
                mfilter_words: {xtype: 'combo-boolean', hiddenName: 'mfilter_words',value:data['mfilterWords']},
                hidden_tab: {xtype: 'combo-boolean', hiddenName: 'hidden_tab',value:data['hiddenTab']},
                admin_version: {xtype: 'combo-boolean', hiddenName: 'admin_version'},
            },
            default: {
                title:{},
                h1:{},
                h2:{},
                description:{},
                keywords:{},
                introtext:{},
                text:{},
                content:{},
                link: {}
            }
        };

        for(group in settings_fields) {
            for (key in settings_fields[group]) {
                if(typeof(settings[group]) == 'undefined') {
                    settings[group] = [];
                }
                settings[group].push(this.objectMerge({
                    xtype: 'textfield'
                    , id: 'sf-' + key
                    , fieldLabel: _('setting_seofilter_' + key)
                    , description: _('setting_seofilter_' + key + '_desc')
                    , name: key
                    , value: data[key]
                    , anchor: '100%'
                }, settings_fields[group][key]));
            }
        }

        return {
            layout: 'column',
            border: false,
            anchor: '100%',
            items: [{
                columnWidth: .5
                , layout: 'form'
                , defaults: {msgTarget: 'under'}
                , labelAlign: 'top'
                , cls: 'seofilter-settings-column'
                , border: false
                , items: [{
                    title: _('seofilter_settings_main')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-main'
                    , autoHeight: true
                    , border: false
                    , items: settings['main']
                },{
                    title: _('seofilter_settings_url')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-url'
                    , autoHeight: true
                    , border: false
                    , items: settings['url']
                }]
            },{
                columnWidth: .5
                , layout: 'form'
                , labelAlign: 'top'
                , defaults: {msgTarget: 'under'}
                , border: false
                , items: [{
                    title: _('seofilter_settings_count')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-count'
                    , autoHeight: true
                    , border: false
                    , items: settings['count']
                },{
                    title: _('seofilter_settings_pro')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-pro'
                    , autoHeight: true
                    , border: false
                    , items: settings['pro']
                },{
                    title: _('seofilter_settings_ajax')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , collapsed:true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-ajax'
                    , autoHeight: true
                    , border: false
                    , items: settings['ajax']
                },{
                    title: _('seofilter_settings_default')
                    , xtype: 'fieldset'
                    , cls: 'x-fieldset-checkbox-toggle seofilter-fieldset'
                    , collapsible: true
                    , collapsed:true
                    , stateful: true
                    , forceLayout: true
                    , stateEvents: ['collapse', 'expand']
                    , id: 'seofilter-fieldset-default'
                    , autoHeight: true
                    , border: false
                    , items: settings['default']
                }]
            }]
        };
    }
});

Ext.reg('seofilter-settings', SeoFilter.panel.Settings);
