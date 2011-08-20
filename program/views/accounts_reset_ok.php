<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Pasephrase Reset Form -->

<p>
    <?php $alias = $account->get_default_alias(); ?>
    The passphrase for <strong><?php e($alias->name); ?></strong> (<?php e($alias->email); ?>) has been reset.
    <br /><br />
</p>

<p>
    <a href="<?php u('/settings/accounts'); ?>">Click here to return to the Account List.</a>
</p>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>