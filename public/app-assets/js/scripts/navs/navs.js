/*=========================================================================================
    File Name: nav.js
    Description: Navigation available in Bootstrap share general markup and styles,
                 from the base .nav class to the active and disabled states.
                 Swap modifier classes to switch between each style.
    ----------------------------------------------------------------------------------------
    Item Name: Vuesax HTML Admin Template
    Version: 1.0
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function(window, document, $) {
    'use strict';

    var heightLeft = $('.nav-left + .tab-content').height();
    $('ul.nav-left').height(heightLeft);
    var heightRight = $('.nav-right + .tab-content').height();
    $('ul.nav-right').height(heightRight);
})(window, document, jQuery);