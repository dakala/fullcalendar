<?php
// $Id$
/**
 * @file views-view-fullcalendar.tpl.php
 * View to display the fullcalendar
 *
 * Variables available:
 * - $rows: The results of the view query, if any
 * - $options: Options for the fullcalendar style plugin
 * -   fullcalendar_view : the default view for the calendar
 * -   fullcalendar_url_colorbox : whether or not to attempt to use colorbox to open events
 * -   fullcalendar_theme : whether or not to apply a loaded jquery-ui theme
 * -   fullcalendar_header_left : values for the header left region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_header_center : values for the header center region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_header_right : values for the header right region : http://arshaw.com/fullcalendar/docs/display/header/
 * -   fullcalendar_weekmode : number of week rows : http://arshaw.com/fullcalendar/docs/display/weekMode/
 */
 
?>
<div id="fullcalendar"></div>
<div id="fullcalendar_content">
<?php
for ($i = 0; $i < count($rows); $i++) {
  print $rows[$i];
}
?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#fullcalendar_content').hide(); //hide the failover display
    $('#fullcalendar').fullCalendar({
        defaultView: '<?php echo $options['fullcalendar_view']; ?>',
        theme: <?php echo $options['fullcalendar_theme'] ? 'true' : 'false'; ?>,
        header: {
          left: '<?php echo $options['fullcalendar_header_left']; ?>',
          center: '<?php echo $options['fullcalendar_header_center']; ?>',
          right: '<?php echo $options['fullcalendar_header_right']; ?>'
        },
        <?php if($options['fullcalendar_url_colorbox']){ ?>
        eventClick: function(calEvent, jsEvent, view) {
          //test for colorbox
          if($.colorbox){
            $.colorbox({href:calEvent.url,iframe:true, width:'80%', height: '80%'});
            return false;
          }
        },
        <?php } ?>
        <?php if (!empty($options['fullcalendar_defaultyear'])): ?>
          year: <?php echo $options['fullcalendar_defaultyear']; ?>,
        <?php endif; ?>
        <?php if (!empty($options['fullcalendar_defaultmonth'])): ?>
          month: <?php echo $options['fullcalendar_defaultmonth'] - 1; ?>,
        <?php endif; ?>
        <?php if (!empty($options['fullcalendar_defaultday'])): ?>
          day: <?php echo $options['fullcalendar_defaultday']; ?>,
        <?php endif; ?>
        timeFormat: {
          agenda: '<?php echo $options['fullcalendar_timeformat']; ?>'
        },
        weekMode: '<?php echo $options['fullcalendar_weekmode']; ?>',
        events: function(start, end, callback) {
          var events = [];

          $('.fullcalendar_event').each(function() {
              event_details = $(this).find('.fullcalendar_event_details');
              events.push({
                  title: $(event_details).attr('title'),
                  start: $(event_details).attr('start'),
                  end: $(event_details).attr('end'),
                  url: $(event_details).attr('href'),
                  allDay: ($(event_details).attr('allDay') == '1'),
                  className: $(event_details).attr('cn'),
              });
          });

          callback(events);
        }
    });
    //trigger a window resize so that calendar will redraw itself as it loads funny in some browsers occasionally
    $(window).resize();
});
</script>
