<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('rooms', '', 'create')) { ?>
                            <a href="<?php echo admin_url('appointly/appointments/add_room'); ?>" class="btn btn-info pull-left display-block">
                              <?php echo _l('new room'); ?>
                            </a>
                            <?php } ?>
                            
                      <!--   <h4><?php echo htmlspecialchars($title); ?></h4>  -->
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                     <?php render_datatable([
                        _l('Name'),
                        _l('Services'),
                        _l('Staff Member'),
                        ], 'rooms'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-rooms', window.location.href,'undefined','undefined','');
   });
</script>
</body>
</html>
