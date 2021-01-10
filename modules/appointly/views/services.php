<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('services', '', 'create')) { ?>
                            <a href="<?php echo admin_url('appointly/appointments/add_service'); ?>" class="btn btn-info pull-left display-block">
                              <?php echo _l('new service'); ?>
                            </a>
                            <?php } ?>
                            
                      <!--   <h4><?php echo htmlspecialchars($title); ?></h4>  -->
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                     <?php render_datatable([
                        _l('Title'),
                        _l('Category'),
                        _l('Color'),
                        _l('Service duration'),
                        _l('Visibility'),
                        _l('Price'),
                        _l('Provider'),
                        ], 'services'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-services', window.location.href,'undefined','undefined','');
   });
</script>
</body>
</html>
