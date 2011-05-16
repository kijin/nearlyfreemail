<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Edit Folders Form -->

<form id="settings_add" class="rounded" action="<?php u('/settings/folders/export'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <?php e($folder->name); ?> (<?php e($folder->messages_all); ?> messages) &nbsp; &nbsp;
        <button type="submit" class="rounded">Export (mbox)</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="folder_id" value="<?php e($folder->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>