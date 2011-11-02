
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail'); ?>">Inbox</a></li>
    <li><a href="<?php u('/mail/compose'); ?>">New Message</a></li>
    <li class="selected"><a href="<?php u('/settings'); ?>">Settings</a></li>
    <li><a href="<?php u('/account/logout'); ?>?token=<?php e(\Common\Session::get_logout_token()); ?>">Logout</a></li>
</ul>

<h3>Settings</h3>

<ul>
    <li class="<?php if ($current_menu == 'preferences'): ?>selected<?php endif; ?>"><a href="<?php u('/settings'); ?>">Preferences</a></li>
    <li class="<?php if ($current_menu == 'aliases'): ?>selected<?php endif; ?>"><a href="<?php u('/settings/aliases'); ?>">Aliases</a></li>
    <li class="<?php if ($current_menu == 'contacts'): ?>selected<?php endif; ?>"><a href="<?php u('/settings/contacts'); ?>">Contacts</a></li>
    <li class="<?php if ($current_menu == 'folders'): ?>selected<?php endif; ?>"><a href="<?php u('/settings/folders'); ?>">Folders</a></li>
    <?php if ($user->is_admin): ?>
        <li class="<?php if ($current_menu == 'accounts'): ?>selected<?php endif; ?>"><a href="<?php u('/settings/accounts'); ?>">Manage Accounts</a></li>
    <?php endif; ?>
    <li class="<?php if ($current_menu == 'rules'): ?>selected<?php endif; ?>"><a href="<?php u('/settings/rules'); ?>">Rules</a></li>
</ul>
