(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.fullcalendar.fullcalendar.prototype.dateChange = function (start, end, fields) {
    if (start && end) {
      // Update the select values for the start and end dates. First we format
      // the dates into values we can use to directly change the selects.
      var date_parts = {
        min: $.fullCalendar.moment(start).format('YYYY-MM-DD'),
        max: $.fullCalendar.moment(end).format('YYYY-MM-DD')
      };

      var $cal = this.$calendar;

      for (var i in fields) {
        $.each(['min', 'max'], function (_, type) {
          var $input = $cal.find('#edit-' + fields[i] + '-' + type);

          $input.val(date_parts[type]);
          $input.parent().hide();
        });
      }
    }
  };

  Drupal.fullcalendar.fullcalendar.prototype.submitInit = function (settings) {
    var domId = this.dom_id.replace('.js-view-dom-id-', '');
    var ajaxView = drupalSettings.views['ajaxViews']['views_dom_id:' + domId];
    this.tm = settings.theme ? 'ui' : 'fc';
    var $exposedForm = this.$calendar.find('.views-exposed-form');
    var $submit = this.$calendar.find('.views-exposed-form .form-actions');

    // If exposed form only has the date widgets we added to the form in
    // fullcalendar_views_pre_view(), we can hide the whole exposed form.
    if ($exposedForm.find('.form-item').length == settings['fullcalendar_fields_count'] + 1) {
      $exposedForm.hide();
    }
    // Otherwise, hide only the date widgets we added to the form.
    else {
      var $cal = this.$calendar;

      for (var i in settings['fullcalendar_fields']) {
        $.each(['min', 'max'], function (_, type) {
          var $input = $cal.find('#edit-' + settings['fullcalendar_fields'][i] + '-' + type);
          $input.parent().hide();
        });
      }
    }

    var $submit_button = $submit.find('.form-submit');

    // Request URL.
    var url = '/';

    // Prepend current language to URL.
    if (drupalSettings.path.pathPrefix) {
      url += drupalSettings.path.pathPrefix;
    }

    var ajaxSettings = {
      url: url + 'fullcalendar/ajax/results/' + settings['view_name'] + '/' + settings['view_display'],
      base: 'main',
      element: $submit_button[0],
      fullcalendar: this,
      submit: ajaxView,
      event: 'fullcalendar_submit'
    };

    this.$submit = new Drupal.ajax(ajaxSettings);
    // Remove previously-attached click events (we do not want the view to be
    // replaced), then attach our handler.
    $submit_button.unbind( "click" ).click($.proxy(this.fetchEvents, this));
  };

  Drupal.fullcalendar.fullcalendar.prototype.fetchEvents = function () {
    this.$calendar.find('.fc-toolbar button')
      .addClass(this.tm + '-state-disabled');
    $(this.$submit.element).trigger('fullcalendar_submit');
  };

  /**
   * Ajax command handler for updating events in the calendar.
   */
  Drupal.AjaxCommands.prototype.ResultsCommand = function (ajax, response, status) {
    // Avoid to make another request when calling refetchEvents().
    ajax.element_settings.fullcalendar.refetch = true;

    // Default content for '.fullcalendar-content'. Empty content means that
    // there is no event in the selected date-range.
    var content = '';

    // Get new views content from the ajax response.
    if ($(response.data).find('.fullcalendar-content').length > 0) {
      content = $(response.data).find('.fullcalendar-content').html();
    }

    // Update '.fullcalendar-content' element, then re-fetch events in the
    // calendar.
    ajax.element_settings.fullcalendar.$calendar.find('.fullcalendar-content')
      .html(content)
      .end()
      .find('.fc-toolbar button')
      .removeClass(ajax.element_settings.fullcalendar.tm + '-state-disabled')
      .end()
      .find('.fullcalendar')
      .fullCalendar('refetchEvents');
  };

})(jQuery, Drupal, drupalSettings);
