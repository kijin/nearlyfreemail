<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Edit Folders Form -->

<form id="settings_add" class="rounded" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return ajax(this)">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php e($folder->name); ?>" /> &nbsp;
        <button type="submit" class="rounded">Save</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="action" value="folders_edit" />
        <input type="hidden" name="folder_id" value="<?php e($folder->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>