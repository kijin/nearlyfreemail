<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Pasephrase Reset Form -->

<form id="settings_account_reset" class="rounded" action="<?php u('/settings/accounts/delete'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset>
        <?php $alias = $account->get_default_alias(); ?>
        Do you want to <strong>DELETE</strong> the account <strong><?php e($alias->name); ?></strong> (<?php e($alias->email); ?>) ?
        <br />
        All messages received by <?php e($alias->name); ?> will be lost!
        <br /><br />
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="account_id" value="<?php e($account->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <fieldset>
        <button type="submit" class="rounded" name="button" value="yes">I Understand, Delete It</button>
        <button type="submit" class="rounded" name="button" value="no">Cancel</button>
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>