<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Pasephrase Reset Form -->

<form id="settings_account_reset" class="rounded" action="<?php u('/settings/accounts/reset'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset>
        <?php $alias = $account->get_default_alias(); ?>
        Do you want to <strong>RESET</strong> the passphrase for <strong><?php e($alias->name); ?></strong> (<?php e($alias->email); ?>) ?
        <br />
        <?php e($alias->name); ?> will no longer be able to log in using the old passphrase.
        <br /><br />
    </fieldset>
    
    <fieldset>
        <p style="margin-bottom: 16px">
            <label for="pass1">New Passphrase</label><br />
            <input type="password" id="pass1" name="pass1" value="" />
        </p>
        <p style="margin-bottom: 32px">
            <label for="pass2">New Passphrase (Repeat)</label><br />
            <input type="password" id="pass2" name="pass2" value="" />
        </p>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="account_id" value="<?php e($account->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <fieldset>
        <button type="submit" class="rounded" name="button" value="yes">Reset Passphrase</button>
        <button type="submit" class="rounded" name="button" value="no">Cancel</button>
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>