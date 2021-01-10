<div class="modal fade" id="newAppointmentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('appointment_new_appointment'); ?></h4>
            </div>
            <?php echo form_open('appointly/appointments/create', array('id' => 'appointment-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (appointlyGoogleAuth() && get_option('appointly_google_client_secret')) : ?>
                            <div class="checkbox pull-right mtop1">
                                <input type="checkbox" name="google" id="google" checked>
                                <label data-toggle="tooltip" title="<?= _l('appointment_add_to_google_calendar'); ?>" for="google"> <i class="fa fa-google" aria-hidden="true"></i></label>
                            </div>
                        <?php endif; ?>
                        <?php //echo render_input('Treatment Type', ''); ?>

                       

                        <!-- <div class="form-group">
                            <label for="rel_type" class="control-label">Services</label>
                            <select name="service_id" id="service_id" class="selectpicker" data-width="100%">
                                <option value="">Select Service</option>

                                <?php foreach ($services as $key => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>" data-id="<?php echo $value['price']; ?>"><?php echo $value['title']; ?></option>
                                <?php } ?>
                              
                            </select>
                        </div> -->

                          <div style="text-align: left;">
                            <br> <button class="btn btn-success ml-1 btn-add-special" data-toggle="tooltip" title="" data-original-title="Add Service" type="button"><i class="fa fa-plus-circle"></i> Add Service</button>
                        </div><br>


                           <div class="extra_service_tab_row">
                        
                            <div class="form-group cstm-address-form">
                           
                            <div class="row">
                               
                                   <div class="form-group col-md-5">
                                    <label for="rel_type" class="control-label">Services</label>
                                        <select name="service_id[]" id="service_id" class="selectpicker service_id">
                                            <option value="">Select Service</option>

                                            <?php foreach ($services as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>" data-id="<?php echo $value['price']; ?>"><?php echo $value['title']; ?></option>
                                            <?php } ?>
                                          
                                        </select>
                                    </div>
                                
                                
                              
                                    <div class="form-group col-md-5">
                                        <label for="qty">Quantity</label>
                                        <input type="text" id="qty" class="form-control qty" name="qty[]" placeholder="Enter quantity"  />
                                    </div>

                                    <div class="form-group col-lg-2">
                                        <label for=""></label>
                                        <button style="margin-top: 25px;" class="btn btn-danger ml-1 btn-delete-tab_restaurant_time btn" type="button"><i class="fa fa-trash"></i></button>
                                    </div>

                                <div class="clear"></div>
                            </div>
                            </div>


                        </div>
                        
                           
                        

                        <?php echo render_textarea('description', 'appointment_description', '', array('rows' => 5)); ?>
                        <div class="form-group select-placeholder">
                            <label for="rel_type" class="control-label">Type Of Client</label>
                            <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <option value=""></option>
                                <option id="external" value="external">New Client</option>
                                <option id="internal" value="internal">Existing Client</option>
                            </select>
                        </div>
                        <div class="form-group hidden" id="select_contacts">
                            <?php echo render_select('contact_id', $contacts, array('contact_id', array('firstname', 'lastname', 'company')), 'appointment_select_single_contact', '', array(),  array(), '', '', true); ?>
                        </div>
                        <div class="form-group hidden" id="div_name">
                            <label for="name"><?= _l('appointment_name'); ?></label>
                            <input type="text" value="" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group hidden" id="div_email">
                            <label for="email"><?= _l('appointment_email'); ?></label>
                            <input type="email" value="" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group hidden" id="div_phone">
                            <label for="number"><?= _l('appointment_phone'); ?> (Ex: <?= _l('appointment_your_phone_example'); ?>) </label>
                            <input type="text" value="" class="form-control" name="phone" id="phone">
                        </div>
                        <div class="pull-right available_times_labels">
                            <span class="available_time_info">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?= _l('appointment_available_hours'); ?>
                            <span class="busy_time_info">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?= _l('appointment_busy_hours'); ?>
                            <?php if (appointlyGoogleAuth()) : ?>
                                <span class="busy_time_info_google">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <?= _l('appointments_google_calendar'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 no-padding">
                            <?php echo render_datetime_input('date', 'appointment_date_and_time', '', ['readonly' => "readonly"], [], '', 'appointment-date'); ?>
                        </div>


                        

                        
                        <div class="clearfix"></div>

                        <!--  <div class="form-group" >
                            <label for="qty">QTY</label>
                            <input type="text" value="" class="form-control" name="qty" id="qty">
                        </div> -->

                        <div class="form-group" >
                            <label for="total_price">Total Price</label>
                            <input type="text" readonly="" value="" class="form-control" name="total_price" id="total_price">
                        </div>

                         <div class="form-group">
                                <label for="room_id">Room</label>
                                <select class="form-control selectpicker" name="room_id" id="room_id">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($rooms as $room) { ?>
                                        <option class="form-control"  value="<?= $room['id']; ?>"><?= $room['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                              
                        </div>
                         <div class="clearfix mtop15"></div>


                        <!-- <div class="form-group">
                            <label for="address"><?= _l('appointment_meeting_location') . ' ' . _l('appointment_optional'); ?></label>
                            <input type="text" class="form-control" name="address" id="address">
                        </div> -->

                        

                        <div class="form-group">
                            <?php echo render_select('attendees[]', $staff_members, array('staffid', array('firstname', 'lastname')), 'appointment_select_attendees', [get_staff_user_id()], array('multiple' => true), array(), '', '', false); ?>
                        </div>

                        <?php $appointment_types = get_appointment_types();
                        if (count($appointment_types) > 0) { ?>
                            <div class="form-group appointment_type_holder">
                                <label for="appointment_select_type" class="control-label"><?= _l('appointments_type_heading'); ?></label>
                                <select class="form-control selectpicker" name="type_id" id="appointment_select_type">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($appointment_types as $app_type) { ?>
                                        <option class="form-control" data-color="<?= $app_type['color']; ?>" value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small id="appointment_color_type" class="pull-right appointment_color_type" style="background:#e1e6ec"></small>
                            </div>
                            <div class=" clearfix mtop15"></div>
                            <hr>
                        <?php } ?>
                        <div class="form-group mtop10">
                            <div class="row">
                                <div class="col-md-12 mbot5">
                                    <?= _l('appointment_modal_notification_info'); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_sms" id="by_sms">
                                        <label for="by_sms"><?= _l('appoontment_sms_notification'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_email" id="by_email">
                                        <label for="by_email"><?= _l('appoontment_email_notification'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group appointment-reminder hide">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="reminder_before"><?= _l('appointments_reminder_time_value'); ?></label><br>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="reminder_before" value="" id="reminder_before">
                                        <span class="input-group-addon"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('reminder_notification_placeholder'); ?>"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select name="reminder_before_type" id="reminder_before_type" class="selectpicker" data-width="100%">
                                        <option value="minutes"><?php echo _l('minutes'); ?></option>
                                        <option value="hours"><?php echo _l('hours'); ?></option>
                                        <option value="days"><?php echo _l('days'); ?></option>
                                        <option value="weeks"><?php echo _l('weeks'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="font-medium pleft5"><?= _l('appointment_client_notes'); ?></span>
                            </div>
                            <div class="col-md-12 mtop8">
                                <textarea name="notes" id="" cols="30" rows="10">

                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require('modules/appointly/assets/js/modals/create_js.php'); ?>

<script type="text/javascript">

	function calculate(){
		var total_price = 0;
        $(".extra_service_tab_row").each(function( index ) {
           var price =  $(this).find('.service_id').find(':selected').data('id');
           var qty =  $(this).find('.qty').val();
           total_price += price * qty;
           console.log( price + 'qty : ' + qty + ' total_price : ' +total_price);
        });

        var total_price = total_price;
        $('#total_price').val(total_price);
	}
    
    $(document).on("keyup",".qty",function(){
        calculate();
      
    });

    $(document).on("change",".service_id",function(){
        calculate();
    });

    $('body').on('click','.btn-add-special',function() {
    
         var total_row= $('#appointment-form').find('.extra_service_tab_row').length;
            $('.service_id').selectpicker('destroy', true);
            var clone = $(".extra_service_tab_row:first").clone();
            $('.extra_service_tab_row:first')
            .find("textarea,select").removeAttr("id");
            clone.insertBefore('.extra_service_tab_row:first');

            $('#appointment-form').find('.extra_service_tab_row:first')
            .find("input,textarea,select").val("");

            $('.service_id').selectpicker();  
    });

$('body').on("click", ".btn-delete-tab_restaurant_time", function (event) {
    var total_row= $('#appointment-form').find('.extra_service_tab_row').length;
    //$(this).closest(".extra_service_tab_row").remove();
    if(total_row==1)
    {
       // $('#appointment-form').find('.extra_service_tab_row:first')
       //  .find("input,textarea,select").val("");
    }else{
        $(this).closest(".extra_service_tab_row").remove();
    }

    calculate();

});

    
</script>