
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail'); ?>">Return to Inbox</a></li>
    <li><a href="<?php u('/mail/compose'); ?>">New Message</a></li>
    <li><a href="<?php u('/account/logout'); ?>?token=<?php e(\Common\Session::get_logout_token()); ?>">Logout</a></li>
</ul>

<h3>Settings</h3>

<ul>
    <li><a href="<?php u('/settings'); ?>">Account Settings</a></li>
    <li><a href="<?php u('/settings/aliases'); ?>">Aliases</a></li>
    <li><a href="<?php u('/settings/contacts'); ?>">Contacts</a></li>
    <li><a href="<?php u('/settings/folders'); ?>">Folders</a></li>
    <li><a href="<?php u('/settings/rules'); ?>">Rules</a></li>
</ul>
