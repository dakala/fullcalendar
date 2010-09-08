<?php
/**
 * View to display full calendar
 */
 
?>
<div id="fullcalendar"></div>
<div id="fullcalendar_content" style="display:none">
<?php
for ($i = 0; $i < count($rows); $i++) {
  print $rows[$i];
}
?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('#fullcalendar').fullCalendar({
        defaultView: '<?php echo $options['fullcalendar_view']; ?>',
        theme: <?php echo $options['fullcalendar_theme'] ? 'true' : 'false'; ?>,
        header: {
          left: 'today prev,next',
          center: 'title',
          right: 'month agendaWeek'
        },
        <?php if($options['url_colorbox']){ ?>
        eventClick: function(calEvent, jsEvent, view) {
          //test for colorbox
          if($.colorbox){
            $.colorbox({href:calEvent.url,iframe:true, width:'80%', height: '80%'});
            return false;
          }
        },
        <?php } ?>
        events: function(start, end, callback) {
          var events = [];

          $('.fullcalendar_event').each(function() {
              event_details = $(this).find('.fullcalendar_event_details');
              events.push({
                  title: $(event_details).attr('title'),
                  start: $(event_details).attr('start'),
                  end: $(event_details).attr('end'),
                  url: $(event_details).attr('href'),
                  allDay: false
              });
          });

          callback(events);
        }
    });
});
</script>
