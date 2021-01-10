<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();


// $appointly_default_table_filter = get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter');
// $appointly_show_summary = get_meta('staff', get_staff_user_id(), 'appointly_show_summary');
?>

<style type="text/css">
  .fc-media-screen .fc-timegrid-event {
       position: initial !important; 
  }
</style>


<div id="wrapper">
    <div class="content">
          <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <!-- <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span> -->
                            <h4>Appointments </h4>
                        </div>
                    </div>
                </div>
            </div>


        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/index_main_js.php'); ?>
<link href='http://wellnessme.co.za/assets/plugins/fullcalendar-scheduler/lib/main.css' rel='stylesheet' />
<script src='http://wellnessme.co.za/assets/plugins/fullcalendar-scheduler/lib/main.js'></script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'resourceTimeGridTwoDay',
      initialDate: '<?php echo date('Y-m-d'); ?>',
      editable: false,
      selectable: true,
      dayMaxEvents: true, // allow "more" link when too many events
      dayMinWidth: 200,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'resourceTimeGridDay,resourceTimeGridTwoDay,resourceTimeGridWeek,dayGridMonth'
      },
      views: {
        resourceTimeGridTwoDay: {
          type: 'resourceTimeGrid',
          duration: { days: 2 },
          buttonText: '2 days',
        }
      },

      //// uncomment this line to hide the all-day slot
      //allDaySlot: false,

      resources: <?php echo $room; ?>,

      events:<?php echo $appointment; ?>,
      // events: [
      //   { id: '1', resourceId: '3', start: '2020-09-06T10:00:00', title: 'event 1' },
      //   { id: '2', resourceId: '3', start: '2020-09-07T09:00:00', title: 'event 2' },
      //   { id: '3', resourceId: '4', start: '2020-09-07T12:00:00', title: 'event 3' },
      //   { id: '4', resourceId: '4', start: '2020-09-07T07:30:00', title: 'event 4' },
      //   { id: '5', resourceId: '3', start: '2020-09-07T10:00:00', title: 'event 5' },
      //   { id: '6', resourceId: '4', start: '2020-09-07T10:00:00', title: 'event 6' }
      // ],

      eventClick:function(info){
        var appointment_id = info.event.id;

         $("#modal_wrapper").load("http://wellnessme.co.za/admin/appointly/appointments/modal", {
               slug: 'update',
               appointment_id: appointment_id
          }, function() {
               if ($('.modal-backdrop.fade').hasClass('in')) {
                    $('.modal-backdrop.fade').remove();
               }
               if ($('#appointmentModal').is(':hidden')) {
                    $('#appointmentModal').modal({
                         show: true
                    });
               }
          });
      },
      select: function(arg) {


         $("#modal_wrapper").load("http://wellnessme.co.za/admin/appointly/appointments/modal", {
                    slug: 'create'
               }, function() {

                console.log(arg.resource._resource.id);
                $('#date').val(moment(arg.start).format('DD-MM-YYYY HH:mm'));
                $('#room_id').val(arg.resource._resource.id);
                $('#room_id').selectpicker('refresh')
                    if ($('.modal-backdrop.fade').hasClass('in')) {
                         $('.modal-backdrop.fade').remove();
                    }
                    if ($('#newAppointmentModal').is(':hidden')) {
                         $('#newAppointmentModal').modal({
                              show: true
                         });
                    }
               });
      },
      // dateClick: function(arg) {
      //   console.log(
      //     'dateClick',
      //     arg.date,
      //     arg.resource ? arg.resource.id : '(no resource)'
      //   );
      // }
    });

    calendar.render();
  });
</script>
</body>
</html>