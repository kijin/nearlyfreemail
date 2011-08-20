<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- New Account Form -->

<form id="settings_add" class="rounded ajax_capable" action="<?php u('/settings/accounts/add'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
    <fieldset class="category rounded">
        <p style="margin-bottom: 12px; margin-top: -4px;"><strong>Create a New Account</strong></p>
        <p style="margin-bottom: 12px">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="" /> &nbsp;
            <label for="email" class="email">E-mail</label>
            <input type="text" id="email" name="email" value="" />
        </p>
        <p>
            <label for="pass1">Pass1</label>
            <input type="password" id="pass1" name="pass1" value="" /> &nbsp;
            <label for="pass2" class="email">Pass2</label>
            <input type="password" id="pass2" name="pass2" value="" /> &nbsp;
            <button type="submit" class="rounded">Add</button>
        </p>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Existing Accounts -->

<form id="settings_existing" class="rounded" action="<?php u('/settings/accounts/action'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <fieldset>
    <?php foreach ($accounts as $account): ?>
    <p class="item">
    
        <?php $aliases = $account->get_aliases(); /* N+1 design pattern LOL */ ?>
        <?php $default_alias = $account->get_default_alias(); ?>
        
        <strong><?php e($default_alias->name); ?></strong> <span class="email"><?php e($default_alias->email); ?></span> &nbsp;
        <?php if ($account->is_admin): ?> (Admin) <?php endif; ?> &nbsp;
        
        <?php if ($account->id === $user->id): ?>
            <span class="actions">This Is You!</span>
        <?php else: ?>
            <span class="actions">
                <?php if ($account->is_admin): ?>
                    <a href="<?php u('/settings/accounts/admin-revoke', $account->id); ?>">Revoke Admin</a> &nbsp;
                <?php else: ?>
                    <a href="<?php u('/settings/accounts/admin-grant', $account->id); ?>">Grant Admin</a> &nbsp;
                <?php endif; ?>
                <a href="<?php u('/settings/accounts/reset', $account->id); ?>">Reset Passphrase</a>
            </span>
        <?php endif; ?>
        <br />
        
        <?php foreach ($aliases as $alias): ?>
            <?php if ($alias->id !== $default_alias->id): ?>
                &nbsp; &nbsp; &raquo; &nbsp; <?php e($alias->name); ?> <span class="email"><?php e($alias->email); ?></span><br />
            <?php endif; ?>
        <?php endforeach; ?>
        
    </p>
    <?php endforeach; ?>
    </fieldset>
    
</div>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>