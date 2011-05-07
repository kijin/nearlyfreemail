<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Edit Contacts Form -->

<form id="settings_add" class="rounded ajax_capable" action="<?php u('/settings/contacts/edit'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php e($contact->name); ?>" /> &nbsp;
        <label for="email" class="email">E-mail</label>
        <input type="text" id="email" name="email" value="<?php e($contact->email); ?>" /> &nbsp;
        <button type="submit" class="rounded">Save</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="contact_id" value="<?php e($contact->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>