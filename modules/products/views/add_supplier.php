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
                        <?php echo form_open_multipart($this->uri->uri_string()); ?>
                        <?php echo render_input('name', 'Name', $suppliers->name ?? ''); ?>
                        <?php echo render_textarea('address', 'Address', $suppliers->address ?? ''); ?>
                        <div class="row">
                            <div class="col-md-6">
                                 <?php echo render_input('phone','Phone', $suppliers->phone ?? ''); ?>
                            </div>
                            <div class="col-md-6">
                               <?php echo render_input('contact','Contact', $suppliers->contact ?? ''); ?>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-6">
                               <!--  <label>Email</label> -->
                                 <?php echo render_input('email_address','Email', $suppliers->email_address ?? ''); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo render_input('whatsapp_no','Whatsapp no', $suppliers->whatsapp_no ?? ''); ?>
                                </div>
                         </div>
                           
                        <div class="row">
                                <div class="col-md-12">
                                     <?php echo render_textarea('description', 'Description', $suppliers->description ?? ''); ?>
                                </div>
                         </div>
                        <!-- </div> -->
                      
                       
                      
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