(function ($, Drupal, drupalSettings) {
  "use strict";

  Drupal.fullcalendar.fullcalendar.prototype.dateChange = function (start, end, fields) {
    var fullcalendar = this.$calendar.find('.fullcalendar');
    var view = fullcalendar.fullCalendar('getView');

    if (view.start && view.end) {
      // Update the select values for the start and end dates. First we format
      // the dates into values we can use to directly change the selects.
      var date_parts = {
        min: $.fullCalendar.moment(start).format('YYYY-MM-DD'),
        max: $.fullCalendar.moment(end).format('YYYY-MM-DD')
      };

      var $cal = this.$calendar;

      for (var i in fields) {
        $cal.find('.views-widget-filter-' + i).hide();

        $.each(['min', 'max'], function (_, type) {
          $cal.find('#edit-' + fields[i] + '-' + type).val(date_parts[type]);
        });
      }
    }
  };

  Drupal.fullcalendar.fullcalendar.prototype.submitInit = function (settings) {
    var domId = this.dom_id.replace('.js-view-dom-id-', '');
    var ajaxView = drupalSettings.views['ajaxViews']['views_dom_id:' + domId];
    this.tm = settings.theme ? 'ui' : 'fc';
    var $submit = this.$calendar.find('.views-exposed-form .form-actions');

    if (this.$calendar.find('.form-item').length == settings['fullcalendar_fields_count'] + 1) {
      $submit.hide();
    }

    var $submit_button = $submit.find('.form-submit');

    var url = '/';
    if (Drupal.hasOwnProperty('currentLanguage')) {
      url += Drupal['currentLanguage'] + '/';
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
    $submit_button.click($.proxy(this.fetchEvents, this));
  };

  Drupal.fullcalendar.fullcalendar.prototype.fetchEvents = function () {
    this.$calendar.find('.fc-button').addClass(this.tm + '-state-disabled');
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
      .find('.fullcalendar')
      .fullCalendar('refetchEvents');
  };

})(jQuery, Drupal, drupalSettings);
