<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Account Modification Form -->

<form id="settings_account_admin" class="rounded" action="<?php u('/settings/accounts/admin-revoke'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset>
        <?php $alias = $account->get_default_alias(); ?>
        Do you want to <strong>REVOKE</strong> administration rights from <strong><?php e($alias->name); ?></strong> (<?php e($alias->email); ?>) ?
        <br />
        <?php e($alias->name); ?> will no longer be able to manage any account other than his or her own.
        <br /><br />
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="account_id" value="<?php e($account->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <fieldset>
        <button type="submit" class="rounded" name="button" value="yes">Yes</button>
        <button type="submit" class="rounded" name="button" value="no">No</button>
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>