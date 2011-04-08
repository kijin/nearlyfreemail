
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail'); ?>">Return to Inbox</a></li>
    <li><a href="<?php u('/mail/compose'); ?>">New Message</a></li>
    <li><form id="logout" action="<?php u('/account/logout'); ?>" method="post" accept-charset="UTF-8" onsubmit="return ajax(this)">
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="logout_token" value="<?php e(\Common\Session::get_logout_token()); ?>" />
        <button type="submit">Logout</button>
    </form></li>
</ul>

<h3>Settings</h3>

<ul>
    <li><a href="<?php u('/settings'); ?>">Account Settings</a></li>
    <li><a href="<?php u('/settings/aliases'); ?>">Aliases</a></li>
    <li><a href="<?php u('/settings/contacts'); ?>">Contacts</a></li>
    <li><a href="<?php u('/settings/folders'); ?>">Folders</a></li>
    <li><a href="<?php u('/settings/rules'); ?>">Rules</a></li>
</ul>
