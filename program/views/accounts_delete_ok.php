<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Pasephrase Reset Form -->

<p>
    The account <strong><?php e($account_name); ?></strong> (<?php e($account_email); ?>) has been deleted.
    <br /><br />
</p>

<p>
    <a href="<?php u('/settings/accounts'); ?>">Click here to return to the Account List.</a>
</p>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>