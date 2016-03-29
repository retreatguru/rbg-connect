<div style="clear:left; margin:20px 20px 20px 0;">
    <a href="<?php echo add_query_arg( array('page' => 'options-mbm'), admin_url('admin.php')); ?>" class="button">Retreat Guru Settings</a>
    &nbsp; &nbsp; &nbsp;
    <a href="<?php echo RS_Connect_Api::get_base_url(); ?>/wp-admin/admin.php?page=rs-programs" class="button">View All Programs</a>
    <a href="<?php echo RS_Connect_Api::get_base_url(); ?>/wp-admin/admin.php?page=registrations" class="button">View All Registrations</a>
    <a href="<?php echo RS_Connect_Api::get_base_url(); ?>/wp-admin/admin.php?page=rs-transactions" class="button">View All Transactions</a>
</div>

<div style="width: 49%; float: left;">
    <?php include($this->plugin_dir . '/views/admin-programs.php'); ?>
</div>

<div style="width: 49%; float: right;">
    <?php include($this->plugin_dir . '/views/admin-help.php'); ?>
</div>