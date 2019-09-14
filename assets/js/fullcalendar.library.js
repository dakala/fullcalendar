/**
 * @file
 * Provides FullCalendar defaults and functions.
 */

(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.fullcalendar = Drupal.fullcalendar || {};
  Drupal.fullcalendar.plugins = Drupal.fullcalendar.plugins || {};
  Drupal.fullcalendar.cache = Drupal.fullcalendar.cache || {};

  /*
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'dayGrid' ]
  });

  calendar.render();
*/


  Drupal.fullcalendar.fullcalendar = function (dom_id) {
    var calendarEl = document.getElementsByClassName(dom_id.substring(1))[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap',
      plugins: [ 'dayGrid', 'bootstrap', 'weekGrid' ]
    });

    calendar.render();
  //
  //   // this.dom_id = dom_id;
  //   // this.$calendar = $(dom_id);
  //   // this.$options = {};
  //   // this.navigate = false;
  //   // this.refetch = false;
  //   //
  //   // // Allow other modules to overwrite options.
  //   // var $plugins = Drupal.fullcalendar.plugins;
  //   //
  //   // for (var i = 0; i < drupalSettings.fullcalendar[dom_id]['weights'].length; i++) {
  //   //   var $plugin = drupalSettings.fullcalendar[dom_id]['weights'][i];
  //   //
  //   //   if ($plugins.hasOwnProperty($plugin) && $.isFunction($plugins[$plugin].options)) {
  //   //     $.extend(this.$options, $plugins[$plugin].options(this, drupalSettings.fullcalendar[this.dom_id]));
  //   //   }
  //   // }
  //   //
  //   // this.$calendar.find('.fullcalendar').once().fullCalendar(this.$options);
  //
  //   // $(this.$calendar)
  //   //   .delegate('.fullcalendar-status-close', 'click', function () {
  //   //     $(this).parent().slideUp();
  //   //     return false;
  //   //   });
  //
  };

  // Drupal.fullcalendar.fullcalendar.prototype.update = function (result) {
  //   var fcStatus = $(result.dom_id).find('.fullcalendar-status');
  //   if (fcStatus.is(':hidden')) {
  //     fcStatus.html(result.msg).slideDown();
  //   }
  //   else {
  //     fcStatus.effect('highlight');
  //   }
  //   Drupal.attachBehaviors();
  //   return false;
  // };

})(jQuery, Drupal, drupalSettings);


/**

 document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'dayGrid' ]
  });

  calendar.render();
});

 */