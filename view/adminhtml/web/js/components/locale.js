define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'Experius_MissingTranslations/js/action/locale'
], function ($,Abstract,registry, actionLocale) {
    return Abstract.extend({
        initialize: function () {
            this._super();
            this.applyDependencies();
            return this;
        },
        hasChanged: function () {
            this.applyDependencies();
            return this;
        },

        applyDependencies: function(){
            var self = this;
            //registry.get(registry.get(self.parentName).parentName).isLoading('isLoading',true);
            self.isLocaleCheckComplete = $.Deferred();
            self.checkRequest = actionLocale(self.isLocaleCheckComplete, this.value());

            $.when(this.isLocaleCheckComplete).done(function (data) {
                console.log(data);
            }).fail(function () {
                self.error('request failed');
            }).always(function () {
                //registry.get(registry.get(self.parentName).parentName).set('isLoading',false);
            });

            console.log(this.value());

        }
    })
});