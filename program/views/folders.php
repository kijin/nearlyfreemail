<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- New Folders Form -->

<form id="settings_add" class="rounded ajax_capable" action="<?php u('/settings/folders/add'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="" /> &nbsp;
        <button type="submit" class="rounded">Create a New Folder</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Existing Folders -->

<form id="settings_existing" class="rounded" action="<?php u('/settings/folders/action'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Contacts List -->
    
    <fieldset>
    <?php foreach ($folders as $folder): ?>
    <p class="item">
        <input type="checkbox" name="selected_folders[]" value="<?php e($folder->id); ?>" <?php if (in_array($folder->name, \Config\Defaults::$folders)): ?>disabled="disabled"<?php endif; ?> />
        <a href="<?php u('/mail/list', $folder->name); ?>"><?php e($folder->name); ?></a> &nbsp;
        <span class="messages">(<?php e($folder->messages_all); ?> messages, <?php e($folder->messages_new); ?> new)</span>
        <span class="actions">
            <?php if (in_array($folder->name, \Config\Defaults::$folders)): ?>
                <span class="gray">System Folder</span>
            <?php else: ?>
                <a href="<?php u('/settings/folders/edit', $folder->id); ?>">Rename</a>
            <?php endif; ?>
            &nbsp; <a href="<?php u('/settings/folders/export', $folder->id); ?>">Export</a>
        </span>
    </p>
    <?php endforeach; ?>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <input type="hidden" name="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Visible Buttons -->
    
    <fieldset id="settings_actions_buttons">
        <button type="submit" class="rounded" name="button" value="delete">Delete Selected Folders</button>
    </fieldset>
    
</div>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>