<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- New Alias Form -->

<form id="settings_add" class="rounded ajax_capable" action="<?php u('/settings/aliases/add'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="" /> &nbsp;
        <label for="email" class="email">E-mail</label>
        <input type="text" id="email" name="email" value="" /> &nbsp;
        <button type="submit" class="rounded">Add</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Existing Aliases -->

<form id="settings_existing" class="rounded" action="<?php u('/settings/aliases/action'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Contacts List -->
    
    <fieldset>
    <?php foreach ($aliases as $alias): ?>
    <p class="item">
        <input type="checkbox" name="selected_aliases[]" value="<?php e($alias->id); ?>" <?php if ($alias->id == $default_alias->id): ?>disabled="disabled"<?php endif; ?> />
        <?php e($alias->name); ?> <span class="email"><?php e($alias->email); ?></span> &nbsp;
        <?php if ($alias->id == $default_alias->id): ?>(Default)<?php endif; ?> &nbsp;
        <span class="actions">
            <a href="<?php u('/settings/aliases/howto', $alias->id); ?>">Setup</a> &nbsp;
            <a href="<?php u('/settings/aliases/edit', $alias->id); ?>">Edit</a>
        </span>
        <br />
        <span class="url">Forwarding URL: <strong><?php e($alias->get_incoming_url()); ?></strong></span>
    </p>
    <?php endforeach; ?>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <input type="hidden" name="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Visible Buttons -->
    
    <fieldset id="settings_actions_buttons">
        <button type="submit" class="rounded" name="button" value="delete">Delete Selected Aliases</button>
    </fieldset>
    
</div>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>