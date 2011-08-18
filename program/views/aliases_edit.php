<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Edit Alias Form -->

<form id="settings_add" class="rounded ajax_capable" action="<?php u('/settings/aliases/edit'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php e($alias->name); ?>" /> &nbsp;
        <label for="email" class="email">E-mail</label>
        <input type="text" id="email" name="email" value="<?php e($alias->email); ?>" /> &nbsp;
        <button type="submit" class="rounded">Save</button>
    </fieldset>
    
    <fieldset>
        <br />
        <?php if ($alias->id == $default_alias->id): ?>
            This is currently your default alias.
        <?php else: ?>
            <input type="checkbox" class="checkbox" id="make_default" name="make_default" value="yes" />
            <label for="make_default">Make Default</label> &nbsp; Remember that you can only log in using your default alias!
        <?php endif; ?>
        <br /><br />
    </fieldset>
    
    <fieldset>
        <label for="signature">Signature</label>
        <textarea id="signature" name="signature"><?php e($alias->signature); ?></textarea>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="alias_id" value="<?php e($alias->id); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>