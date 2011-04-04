<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- New Folders Form -->

<form id="settings_add" class="rounded" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return ajax(this)">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="" /> &nbsp;
        <button type="submit" class="rounded">Create a New Folder</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="action" value="folders_add" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Existing Folders -->

<form id="settings_existing" class="rounded" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Contacts List -->
    
    <fieldset>
    <?php foreach ($folders as $folder): ?>
    <p class="item">
        <input type="checkbox" name="selected_folders[]" value="<?php e($folder->id); ?>" <?php if (in_array($folder->name, \Config\Defaults::$folders)): ?>disabled="disabled"<?php endif; ?> />
        <a href="index.php?action=list&amp;folder=<?php e($folder->name); ?>"><?php e($folder->name); ?></a> &nbsp;
        <span class="actions">
            <?php if (in_array($folder->name, \Config\Defaults::$folders)): ?>
                <span class="gray">System Folder</span>
            <?php else: ?>
                <a href="index.php?action=folders_edit&amp;folder_id=<?php e($folder->id); ?>">Rename</a>
            <?php endif; ?>
        </span>
    </p>
    <?php endforeach; ?>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <input type="hidden" name="action" value="folders_do_action" />
        <input type="hidden" name="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Visible Buttons -->
    
    <fieldset id="settings_actions_buttons">
        <button type="submit" class="rounded" name="button" value="delete">Delete Selected Folders</button>
    </fieldset>
    
</div>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>