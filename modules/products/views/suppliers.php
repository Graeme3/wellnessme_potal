<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('suppliers', '', 'create')) { ?>
                            <a href="<?php echo admin_url('products/add_supplier'); ?>" class="btn btn-info pull-left display-block">
                              <?php echo _l('new supplier'); ?>
                            </a>
                            <?php } ?>
                            
                      <!--   <h4><?php echo htmlspecialchars($title); ?></h4>  -->
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                     <?php render_datatable([
                        _l('Name'),
                        _l('Address'),
                        _l('Phone'),
                        _l('Contact'),
                        _l('Email'),
                        _l('Whatsapp no'),
                        _l('Description'),
                        ], 'suppliers'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-suppliers', window.location.href,'undefined','undefined','');
   });
</script>
</body>
</html>
