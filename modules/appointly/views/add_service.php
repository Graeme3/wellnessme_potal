<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo htmlspecialchars($title); ?>
                        </h4>
                        <hr class="hr-panel-heading" />


                        <?php echo form_open_multipart($this->uri->uri_string(), 'id="edit-service"'); ?>
                        <?php echo render_input('title', 'Title', $service->title ?? ''); ?>

                        <?php $appointment_types = get_appointment_types();
                        if (count($appointment_types) > 0) { ?>

                            <div class="form-group appointment_type_holder">
                                <label for="appointment_select_type" class="control-label">Category</label>
                                <select class="form-control selectpicker" name="category" id="appointment_select_type">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($appointment_types as $app_type) { ?>
                                        <option <?= ($app_type['id'] == $service->category) ? 'selected' : ''; ?> class="form-control" data-color="<?= $app_type['color']; ?>" value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small id="appointment_color_type" class="pull-right appointment_color_type" style="background:#e1e6ec"></small>
                            </div>
                            <div class=" clearfix mtop15"></div>
                           
                        <?php } ?>
                        <div class=" clearfix mtop15"></div>
                        <div class="form-group">
                            <?php

                             echo render_select('provider[]', $staff_members, array('staffid', array('firstname', 'lastname')), 'provider',explode(",",$service->provider), array('multiple' => true), array(), '', '', false); ?>

                         


                        </div>


                        <div class="row">
                            <div class="col-md-2">
                                Color
                            </div>
                            <div class="col-md-4">
                               <input type="color" placeholder="Select Color" id="favcolor" name="color" value="<?php echo $service->color ?? '#ff0000' ?>">
                            </div>
                            
                             <div class="col-md-2">
                                <label for="appointment_select_type" class="control-label">Service Duration</label>
                            </div>
                            <div class="col-md-4">
                               <select class="form-control selectpicker" name="service_duration" id="service_duration">
                                    <option value="">Select Service Duration</option>
                                    <?php
                                        $start=strtotime('00:15');
                                        $end=strtotime('08:30');

                                        for ($i=$start;$i<=$end;$i = $i + 15*60){ ?>
                                             
                                                <option <?= ($service->service_duration == date('H:i',$i)) ? 'selected' : ''; ?> value="<?php echo date('H:i',$i) ?>"><?php echo date('G ',$i) ?> hour <?php echo date('i',$i) ?> min</option>
                                            

                                    <?php } ?>

                                </select>
                            </div>

                             
                            <div class=" clearfix mtop15"></div>

                            
                        </div>
                        <div class=" clearfix mtop15"></div>

                        <div class="form-group mtop10">
                            <div class="row">
                                <div class="col-md-12 mbot5">
                                    Visibility
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="radio" name="visibility" id="by_public" value="public" <?= $service->visibility == 'public' ? 'checked' : '' ?>>
                                        <label for="by_public">Public</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="radio" name="visibility" id="by_private" value="private" <?= $service->visibility == 'private' ? 'checked' : '' ?>>
                                        <label for="by_private">Private</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        
                        <?php echo render_input('price','Price', $service->price ?? ''); ?>

                        <br>


                       <div style="text-align: left;">
                            <br> <button class="btn btn-success ml-1 btn-add-special" data-toggle="tooltip" title="" data-original-title="Add Extra Service" type="button"><i class="fa fa-plus-circle"></i> Add Extra Service</button>
                        </div><br>

                        <?php if(empty($extra_services)){ ?>
                        <div class="extra_service_tab_row">
                        
                            <div class="form-group cstm-address-form">
                           
                            <div class="row">

                                <div class="form-group col-lg-3">
                                    <label for="extra_title">Title</label>
                                    <input type="text" id="extra_title" class="form-control" name="extra_title[]" placeholder="Enter Title"  />
                                </div>

                                <div class="form-group col-lg-3">
                                    <label for="extra_price">Price</label>
                                    <input type="text" id="extra_price" class="form-control" name="extra_price[]" placeholder="Enter Price"  />
                                </div>


                                <div class="form-group col-lg-3">
                                    <label for="extra_duration">Duration</label>
                                    
                                    <select class="form-control selectdrop1" name="extra_duration[]" >
                                        <option value="">Select service duration</option>
                                        <?php
                                        $start=strtotime('00:15');
                                        $end=strtotime('08:30');

                                        for ($i=$start;$i<=$end;$i = $i + 15*60){ ?>
                                             
                                                <option value="<?php echo date('H:i',$i) ?>"><?php echo date('G ',$i) ?> hour <?php echo date('i',$i) ?> min</option>
                                    <?php } ?>
                                    </select>
                                </div>
                               


                                <div class="form-group col-lg-2">
                                    <label for="extra_quantity">Max quantity</label>
                                    <input type="text" id="extra_quantity" class="form-control" name="extra_quantity[]" placeholder="Enter quantity"  />
                                </div>
                            
                                

                                <div class="form-group col-lg-1">
                                    <label for=""></label>
                                    <button class="btn btn-danger ml-1 btn-delete-tab_restaurant_time btn" type="button"><i class="fa fa-trash"></i></button>
                                </div>
                                <div class="clear"></div>
                            </div>
                            </div>


                        </div>
                        <?php }else{ 
                            foreach ($extra_services as $key => $value) { ?>
                                <div class="extra_service_tab_row">
                        
                            <div class="form-group cstm-address-form">
                           
                            <div class="row">

                                <div class="form-group col-lg-3">
                                    <label for="extra_title">Title</label>
                                    <input type="text" id="extra_title" class="form-control" value="<?php echo $value['title']; ?>" name="extra_title[]" placeholder="Enter Title"  />
                                </div>

                                <div class="form-group col-lg-3">
                                    <label for="extra_price">Price</label>
                                    <input type="text" id="extra_price" class="form-control" value="<?php echo $value['price']; ?>" name="extra_price[]" placeholder="Enter Price"  />
                                </div>


                                <div class="form-group col-lg-3">
                                    <label for="extra_duration">Duration</label>
                                    
                                    <select class="form-control selectdrop1" name="extra_duration[]" >
                                        <option value="">Select service duration</option>
                                        <?php
                                        $start=strtotime('00:15');
                                        $end=strtotime('08:30');

                                        for ($i=$start;$i<=$end;$i = $i + 15*60){ ?>
                                             
                                                <option <?= ($value['service_duration'] == date('H:i',$i)) ? 'selected' : ''; ?> value="<?php echo date('H:i',$i) ?>"><?php echo date('G ',$i) ?> hour <?php echo date('i',$i) ?> min</option>
                                    <?php } ?>
                                    </select>
                                </div>
                               


                                <div class="form-group col-lg-2">
                                    <label for="extra_quantity">Max quantity</label>
                                    <input type="text" id="extra_quantity" class="form-control" value="<?php echo $value['quantity']; ?>" name="extra_quantity[]" placeholder="Enter quantity"  />
                                </div>
                            
                                

                                <div class="form-group col-lg-1">
                                    <label for=""></label>
                                    <button class="btn btn-danger ml-1 btn-delete-tab_restaurant_time btn" type="button"><i class="fa fa-trash"></i></button>
                                </div>
                                <div class="clear"></div>
                            </div>
                            </div>


                        </div>
                        <?php } } ?>
                      
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    var mode = '<?php echo $this->uri->segment(3, 0); ?>';
    // (mode == 'add_supplier') ? $('input[type="file"]').prop('required',true) : $('input[type="file"]').prop('required',false);
    $(function () {
    appValidateForm($('form'), {
      name        : "required"
    });
    });


//    function addCounter() {     
// var cust_count = 0;
//     return function() {
//         cust_count++;
//         return cust_count;   
//     };
// }


// var countVar = addCounter();


$('body').on('click','.btn-add-special',function() {

    // var j = countVar(); 
    
     var total_row= $('#edit-service').find('.extra_service_tab_row').length;
    
   
        


        var clone = $(".extra_service_tab_row:first").clone();
        $('.extra_service_tab_row:first')
        .find("textarea,select").removeAttr("id");
        clone.insertBefore('.extra_service_tab_row:first');

        $('#edit-service').find('.extra_service_tab_row:first')
        .find("input,textarea,select").val("");
    
    
    
        
});

$('body').on("click", ".btn-delete-tab_restaurant_time", function (event) {
    var total_row= $('#edit-service').find('.extra_service_tab_row').length;
    //$(this).closest(".extra_service_tab_row").remove();
    if(total_row==1)
    {
       $('#edit-service').find('.extra_service_tab_row:first')
        .find("input,textarea,select").val("");
    }else{
        $(this).closest(".extra_service_tab_row").remove();
    }

});

</script>