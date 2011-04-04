
<h3>Actions</h3>

<ul>
    <li><a href="index.php?action=inbox">Return to Inbox</a></li>
    <li><a href="index.php?action=settings">Settings</a></li>
    <li><form id="logout" action="index.php" method="post" accept-charset="UTF-8" onsubmit="return ajax(this)">
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="logout_token" value="<?php e(\Common\Session::get_logout_token()); ?>" />
        <button type="submit">Logout</button>
    </form></li>
</ul>

<h3>Contacts <span class="info"><a href="index.php?action=contacts">(edit)</a></span></h3>

<ul>
<?php $recent_contacts = \Models\Contact::get_recent($user->id, $user->get_setting('show_recent_contacts')); ?>
<?php foreach ($recent_contacts as $contact): ?>
    <li><a href="index.php?action=compose&amp;to=<?php e($contact->get_profile()); ?>"><?php e($contact->name); ?></a></li>
<?php endforeach; ?>
<?php if (!$recent_contacts): ?>
    <li>None</li>
<?php endif; ?>
</ul>
