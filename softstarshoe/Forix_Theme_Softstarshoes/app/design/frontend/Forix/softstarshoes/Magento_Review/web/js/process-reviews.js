/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    function processReviews(url, fromPages) {
        $.ajax({
            url: url,
            cache: true,
            dataType: 'html'
        }).done(function (data) {
            $('#product-review-container').html(data);
            $('[data-role="product-review"] .pages a').each(function (index, element) {
                $(element).click(function (event) {
                    processReviews($(element).attr('href'), true);
                    event.preventDefault();
                });
            });
        }).complete(function () {
            if (fromPages == true) {
                $('html, body').animate({
                    scrollTop: $('#reviews').offset().top - 50
                }, 300);
            }
        });
    }

    return function (config, element) {
        processReviews(config.productReviewUrl);
        $(function () {
            //scroll to review
            $('.product-container .reviews-actions a').click(function (event) {
                event.preventDefault();
                var acnchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                $(".product.data.items [data-role='content']").each(function(index){
                    if (this.id == 'reviews') {
                        var seft = this;

                        $('.product.data.items').tabs('activate', index);
                        $('html, body').animate({
                            scrollTop: $(seft).offset().top - 50
                        }, 300);
                    }
                });
            });
            //scroll to sizing guide
            $('.product-container .size-guide').click(function (event) {
                event.preventDefault();
                var acnchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                $(".product.data.items [data-role='content']").each(function(index){
                    if (this.id == 'sizing.guide.tab') {
                        var seft = this;

                        $('.product.data.items').tabs('activate', index);
                        $('html, body').animate({
                            scrollTop: $(seft).offset().top - 50
                        }, 300);
                    }
                });
            });
        });
    };
});
