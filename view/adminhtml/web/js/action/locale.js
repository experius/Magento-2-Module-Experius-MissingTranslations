define(
    [
        'jquery',
        'mage/storage',
        'mage/url',
    ],
    function ($, storage, urlBuilder) {
        'use strict';


        return function (deferred, localeCode) {
            var param = 'ajax=1';

            var extendedUrl = window.location.href;
            var urlString = extendedUrl.split("experius_missingtranslations")[0];
            var adminArray = urlString.split("/");
            var adminUrl = "/" + adminArray[adminArray.length-2];
            var url = urlBuilder.build(adminUrl + "/experius_missingtranslations/ajax/phrases/locale/" + localeCode);

            $.ajax({
                showLoader: true,
                url: url,
                data: param,
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                var selectBox = $('.admin__field-missingtranslation select[name="string"]');
                var chosenResults = $('.admin__field-missingtranslation ul.chosen-results');

                chosenResults.empty();
                selectBox.empty();

                selectBox.append(new Option("", ""), false);
                chosenResults.append("<li></li>", false);
                $.each(data,function(index,itemData) {
                    chosenResults.append("<li data-option-array-index='" + index + "'>" + itemData[0] + " (" + itemData[3] + ")</li>",false);
                    selectBox.append(new Option(itemData[0] + ' (' + itemData[3] + ")", index), false);
                });
                selectBox.val('').trigger("chosen:updated");
            })
        };
    }
);
