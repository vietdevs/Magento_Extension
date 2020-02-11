/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'rjsResolver',
    'jquery'
], function (resolver,$) {
    'use strict';

    /**
     * Removes provided loader element from DOM.
     *
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function hideLoader($loader) {
        $loader.parentNode.removeChild($loader);
        var miniCart = $('[data-block=\'summary-minicart\']');
        if (miniCart.data('forixScrollData')) {
            miniCart.scrollData('updateScroll');
        }
    }

    /**
     * Initializes assets loading process listener.
     *
     * @param {Object} config - Optional configuration
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function init(config, $loader) {
        resolver(hideLoader.bind(null, $loader));
    }

    return init;
});
