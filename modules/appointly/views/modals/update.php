<div class="modal fade" id="appointmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('appointment_edit_appointment'); ?></h4>
            </div>
            <?php echo form_open('appointly/appointments/update', array('id' => 'appointment-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <input type="text" hidden value="<?= $history['appointment_id']; ?>" name="appointment_id">
                    <input type="text" hidden value="<?= $history['source']; ?>" name="source">
                    <input type="text" hidden value="<?= $history['approved']; ?>" name="approved">
                    <input type="text" hidden value="<?= $history['google_added_by_id']; ?>" name="google_added_by_id">
                    <div class="col-md-12">
                        <?php if (appointlyGoogleAuth()) { ?>
                            <?php if ($history['google_event_id'] !== NULL && $history['google_added_by_id'] == get_staff_user_id()) { ?>
                                <input type="text" hidden value="<?= $history['google_event_id']; ?>" name="google_event_id">
                            <?php } ?>

                            <?php if ($history['google_event_id'] && $history['google_added_by_id'] == get_staff_user_id()) : ?>
                                <div class="checkbox pull-right mleft10 mtop1">
                                    <input disabled data-toggle="tooltip" title="<?= _l('appointments_added_to_google_calendar'); ?>" type="checkbox" id="google" checked />
                                    <label data-toggle="tooltip" title="<?= _l('appointments_added_to_google_calendar'); ?>" for="google"> <i class="fa fa-google" aria-hidden="true"></i></label>
                                </div>
                            <?php endif; ?>

                        <?php } ?>


                        <?php if ($history['source'] == 'external' && !isset($history['details'])) : ?>
                            <div class="pull-right"><span class="label label-info"><?= _l('appointment_source_external'); ?></span></div>
                            <div class="clearfix"></div>
                        <?php elseif ($history['source'] == 'external' && isset($history['details']) && isset($history['contact_id'])) :  ?>
                            <div class="pull-right"><span class="label label-info"><?= _l('appointment_source_external_clients_area'); ?></span></div>
                        <?php endif; ?>
                       <!--  <label for="subject"><?= _l('appointment_subject'); ?></label><br>
                        <input type="text" class="form-control" name="subject" id="subject" value="<?= $history['subject']; ?>"> -->

                        



                        <!-- <div class="form-group">
                            <label for="rel_type" class="control-label">Services</label>
                            <select name="service_id" id="service_id" class="selectpicker" data-width="100%">
                                <option value="">Select Service</option>

                                <?php foreach ($services as $key => $value) { ?>
                                    <option  value="<?php echo $value['id']; ?>" data-id="<?php echo $value['price']; ?>" <?php if($history['service_id'] == $value['id']){ echo 'selected';} ?>><?php echo $value['title']; ?></option>
                                <?php } ?>
                              
                            </select>
                        </div> -->

                        <div style="text-align: left;">
                            <br> <button class="btn btn-success ml-1 btn-add-special" data-toggle="tooltip" title="" data-original-title="Add Service" type="button"><i class="fa fa-plus-circle"></i> Add Service</button>
                        </div><br>


                        <?php if(!empty($appointment_services)){

                        foreach ($appointment_services as $key => $a_service) { ?>

                        <div class="extra_service_tab_row">
                        
                            <div class="form-group cstm-address-form">
                           
                            <div class="row">
                               
                                   <div class="form-group col-md-3">
                                    <label for="service_id" class="control-label">Services</label>
                                        <select name="service_id[]" id="service_id" class="selectpicker service_id" required="">
                                            <option value="">Select Service</option>

                                            <?php foreach ($services as $key => $value) { ?>
                                                <option <?= ($a_service['service_id'] == $value['id']) ? 'selected' : ''; ?> value="<?php echo $value['id']; ?>" data-id="<?php echo $value['price']; ?>"><?php echo $value['title']; ?></option>
                                            <?php } ?>
                                          
                                        </select>
                                    </div>
                                
                                
                              
                                    <div class="form-group col-md-5">
                                        <label for="qty">Quantity</label>
                                        <input type="text" id="qty" class="form-control qty" required="" value="<?php echo $a_service['qty']; ?>" name="qty[]" placeholder="Enter quantity"  />
                                    </div>

                                    <div class="form-group col-lg-2">
                                        <label for=""></label>
                                        <button style="margin-top: 25px;" class="btn btn-danger ml-1 btn-delete-tab_restaurant_time btn" type="button"><i class="fa fa-trash"></i></button>
                                    </div>

                                <div class="clear"></div>
                            </div>
                            </div>


                        </div>

                    <?php } } else{ ?>
                        <div class="extra_service_tab_row">
                        
                            <div class="form-group cstm-address-form">
                           
                            <div class="row">
                               
                                   <div class="form-group col-md-3">
                                    <label for="rel_type" class="control-label">Services</label>
                                        <select name="service_id[]" id="service_id" class="selectpicker service_id" required="">
                                            <option value="">Select Service</option>

                                            <?php foreach ($services as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>" data-id="<?php echo $value['price']; ?>"><?php echo $value['title']; ?></option>
                                            <?php } ?>
                                          
                                        </select>
                                    </div>
                                
                                
                              
                                        <div class="form-group col-md-5">
                                            <label for="qty">Quantity</label>
                                            <input type="text" id="qty" required="" class="form-control qty" name="qty[]" placeholder="Enter quantity"  />
                                        </div>

                                    <div class="form-group col-lg-2">
                                        <label for=""></label>
                                        <button style="margin-top: 25px;" class="btn btn-danger ml-1 btn-delete-tab_restaurant_time btn" type="button"><i class="fa fa-trash"></i></button>
                                    </div>

                                <div class="clear"></div>
                            </div>
                            </div>


                        </div>
                    <?php } ?>


                        <div class="form-group mtop20">
                            <label for="description"><?= _l('appointment_description'); ?></label>
                            <textarea name="description" class="form-control" id="description" rows="5"><?= $history['description']; ?></textarea>
                        </div>
                        <div class="form-group select-placeholder">
                            <label for="rel_type" class="control-label">Type Of Client</label>
                            <select <?= isset($history['details']) ? 'disabled' : ''; ?> name="rel_type" id="rel_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php if (isset($history['details'])) : ?>
                                    <option value=""></option>
                                <?php endif; ?>
                                <option <?= ('external' == $history['source']) ? 'selected' : ''; ?> value="external" id="new_client">New Client</option>
                                
                                <option <?= ('internal' == $history['source']) ? 'selected' : ''; ?> value="internal" id="existing_client">Existing Client</option>
                               
                            </select>
                        </div>
                        <div class="form-group" id="div_name">
                            <label for="name"><?= _l('appointment_name'); ?></label>
                            <input type="text" <?= isset($history['details']) ? 'disabled' : ''; ?> value="<?= isset($history['name']) ? $history['name'] : $history['details']['full_name']; ?>" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group" id="div_email">
                            <label for="email"><?= _l('appointment_email'); ?></label>
                            <input type="email" <?= isset($history['details']) ? 'disabled' : ''; ?> value="<?= isset($history['email']) ? $history['email'] : $history['details']['email']; ?>" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group" id="div_phone">
                            <label for="number"><?= _l('appointment_phone'); ?> (Ex: <?= _l('appointment_your_phone_example'); ?>)</label>
                            <input type="text" <?= isset($history['details']) ? 'disabled' : ''; ?> value="<?= isset($history['phone']) ? $history['phone'] : $history['details']['phone']; ?>" class="form-control" name="phone" id="phone">
                        </div>
                        <div class="form-group hidden" id="select_contacts">
                            <?php echo render_select('contact_id', $contacts, array('contact_id', array('firstname', 'lastname')), 'appointment_select_single_contact', $history['selected_contact'], array(),  array(), '', '', true); ?>
                        </div>
                        <div class="pull-right available_times_labels_edit">
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
                            <?php echo render_datetime_input(
                                'date',
                                'appointment_date_and_time',
                                _dt($history['date'] . ' ' . $history['start_hour']),
                                ["readonly" => "readonly"],
                                [],
                                '',
                                'appointment-date'
                            ); ?>
                        </div>

                        <div class="clearfix"></div>

                        <!-- <div class="form-group" >
                            <label for="qty">QTY</label>
                            <input type="text"  class="form-control" name="qty" id="qty" value="<?= isset($history['qty']) ? $history['qty'] : '0'; ?>">
                        </div> -->

                        <div class="form-group" >
                            <label for="total_price">Total Price</label>
                            <input type="text" readonly="" value="<?= isset($history['total_price']) ? $history['total_price'] : '0'; ?>" class="form-control" name="total_price" id="total_price">
                        </div>


                        <div class="form-group">
                                <label for="room_id">Room</label>
                                <select class="form-control selectpicker" name="room_id" id="room_id">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($rooms as $room) { ?>
                                        <option  <?= ($room['id'] == $history['room_id']) ? 'selected' : ''; ?> class="form-control"  value="<?= $room['id']; ?>"><?= $room['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                              
                        </div>
                         <div class="clearfix mtop15"></div>


                     <!--    <div class="form-group">
                            <label for="address"><?= _l('appointment_meeting_location') . ' ' . _l('appointment_optional'); ?></label>
                            <input type="text" class="form-control" value="<?= isset($history['address']) ? $history['address'] : ''; ?>" name="address" id="address">
                        </div> -->
                        <div class="form-group">
                            <?php echo render_select('attendees[]', $staff_members, array('staffid', array('firstname', 'lastname')), 'appointment_select_attendees', $history['selected_staff'], array('multiple' => true), array(), '', '', false); ?>
                        </div>
                        <?php
                        $appointment_types = get_appointment_types();
                        if (
                            count($appointment_types) > 0
                            && isset($history['type_id'])
                        ) {
                            $app_color = get_appointment_color_type($history['type_id']);
                        ?>
                            <div class="form-group appointment_type_holder">
                                <label for="appointment_select_type" class="control-label"><?= _l('appointments_type_heading'); ?></label>
                                <select class="form-control selectpicker" name="type_id" id="appointment_select_type">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($appointment_types as $app_type) { ?>
                                        <option <?= ($app_type['id'] == $history['type_id']) ? 'selected' : ''; ?> class="form-control" data-color="<?= $app_type['color']; ?>" value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small id="appointment_color_type" class="pull-right appointment_color_type" style="background: <?= ($app_color) ? $app_color : '#e1e6ec'; ?>"></small>
                            </div>
                            <div class=" clearfix mtop15"></div>
                            <hr>
                        <?php } ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 mbot5">
                                    <?= _l('appointment_modal_notification_info'); ?> </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_sms" id="by_sms" <?= $history['by_sms'] == 1 ? 'checked' : '' ?>>
                                        <label for="by_sms"><?= _l('appointment_sms_notification_text'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_email" id="by_email" <?= $history['by_email'] == 1 ? 'checked' : '' ?>>
                                        <label for="by_email"><?= _l('appointment_email_notification_text'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group appointment-reminder<?php if ($history['by_sms'] == null && $history['by_email'] == null) {
                                                                        echo ' hide';
                                                                    } ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="reminder_before"><?php echo _l('event_notification'); ?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="reminder_before" value="<?php echo $history['reminder_before']; ?>" id="reminder_before">
                                        <span class="input-group-addon"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('reminder_notification_placeholder'); ?>"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select name="reminder_before_type" id="reminder_before_type" class="selectpicker" data-width="100%">
                                        <option value="minutes" <?php if ($history['reminder_before_type'] == 'minutes') {
                                                                    echo ' selected';
                                                                } ?>><?php echo _l('minutes'); ?></option>
                                        <option value="hours" <?php if ($history['reminder_before_type'] == 'hours') {
                                                                    echo ' selected';
                                                                } ?>><?php echo _l('hours'); ?></option>
                                        <option value="days" <?php if ($history['reminder_before_type'] == 'days') {
                                                                    echo ' selected';
                                                                } ?>><?php echo _l('days'); ?></option>
                                        <option value="weeks" <?php if ($history['reminder_before_type'] == 'weeks') {
                                                                    echo ' selected';
                                                                } ?>><?php echo _l('weeks'); ?></option>
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
                                    <?= isset($history['notes']) ? htmlentities($history['notes']) : ''; ?>
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php
                if (appointlyGoogleAuth()) {
                    if ((get_option('appointly_responsible_person') !== '')
                        && $history['google_event_id'] === null
                        && $history['google_calendar_link'] === null
                        && $history['google_added_by_id'] === null
                    ) { ?>
                        <button type="button" data-toggle="tooltip" title="<?= _l('appointment_google_not_added_yet'); ?>" onClick="addEventToGoogleCalendar(this)" class="btn btn-primary"><?= _l('appointment_add_to_calendar'); ?>&nbsp;<i class="fa fa-google" aria-hidden="true"></i></button>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require('modules/appointly/assets/js/modals/update_js.php'); ?>

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