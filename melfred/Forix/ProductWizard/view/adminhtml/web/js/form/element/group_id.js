define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            var currentUrl = window.location.href;
            var urlAjax = currentUrl.substring(0, currentUrl.indexOf("forix_productwizard")) + 'forix_productwizard/wizard/ajax';
            jQuery.ajax({
                url: urlAjax,
                type: 'post',
                data: {
                    group_id: value,
                },
                dataType: 'json',
                success: function (result) {
                    jQuery("select[name='item_set_id']").html('');
                    var optionSelect = new Option('Select Option', '');
                    jQuery(optionSelect).html('Select Option');
                    jQuery("select[name='item_set_id']").append(optionSelect);
                    if(result) {
                        for (var key in result) {
                            if (result.hasOwnProperty(key)) {
                                var optionAdd = new Option(result[key], key);
                                jQuery(optionAdd).html(result[key]);
                                jQuery("select[name='item_set_id']").append(optionAdd);
                            }
                        }
                    }
                    // location.reload();
                }
            });
        },
    });
});