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


                        <?php echo form_open_multipart($this->uri->uri_string(), 'id="edit-room"'); ?>

                        <?php echo render_input('name', 'Name', $room->name ?? ''); ?>

                      
                        <div class="form-group">
                            <?php

                             echo render_select('service_id[]', $services, array('id', array('title')), 'Services',explode(",",$room->service_id), array('multiple' => true), array(), '', '', false); ?>

                        </div>
                            
                           
                        <div class="form-group">
                            <?php

                             echo render_select('staff_id[]', $staff_members, array('staffid', array('firstname', 'lastname')), 'Staffs',explode(",",$room->staff_id), array('multiple' => true), array(), '', '', false); ?>

                        </div>
                      
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

</script>