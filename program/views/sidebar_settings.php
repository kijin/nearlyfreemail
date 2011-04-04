
<h3>Actions</h3>

<ul>
    <li><a href="index.php?action=inbox">Return to Inbox</a></li>
    <li><a href="index.php?action=compose">New Message</a></li>
    <li><form id="logout" action="index.php" method="post" accept-charset="UTF-8" onsubmit="return ajax(this)">
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="logout_token" value="<?php e(\Common\Session::get_logout_token()); ?>" />
        <button type="submit">Logout</button>
    </form></li>
</ul>

<h3>Settings</h3>

<ul>
    <li><a href="index.php?action=settings">Account Settings</a></li>
    <li><a href="index.php?action=aliases">Aliases</a></li>
    <li><a href="index.php?action=contacts">Contacts</a></li>
    <li><a href="index.php?action=folders">Folders</a></li>
    <li><a href="index.php?action=rules">Rules</a></li>
</ul>
