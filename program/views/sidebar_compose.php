
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail'); ?>">Return to Inbox</a></li>
    <li><a href="<?php u('/settings'); ?>">Settings</a></li>
    <li><form id="logout" action="<?php u('/account/logout'); ?>" method="post" accept-charset="UTF-8" onsubmit="return ajax(this)">
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="logout_token" value="<?php e(\Common\Session::get_logout_token()); ?>" />
        <button type="submit">Logout</button>
    </form></li>
</ul>

<h3>Contacts <span class="info"><a href="<?php u('/settings/contacts'); ?>">(edit)</a></span></h3>

<ul id="compose_contacts">
<?php $recent_contacts = \Models\Contact::get_recent($user->id, $user->get_setting('show_recent_contacts')); ?>
<?php foreach ($recent_contacts as $contact): ?>
    <li><div>
        <p class="name"><?php e($contact->name); ?></p>
        <p class="buttons">
            <button type="button" onclick="return add_to(<?php e($contact->id); ?>);">To</button>
            <button type="button" onclick="return add_cc(<?php e($contact->id); ?>);">Cc</button>
            <button type="button" onclick="return add_bcc(<?php e($contact->id); ?>);">Bcc</button>
            <input type="hidden" id="compose_contact_<?php e($contact->id); ?>" value="<?php e($contact->get_profile()); ?>" />
        </p>
        <noscript><p class="email"><?php e($contact->email); ?></p></noscript>
    </div></li>
<?php endforeach; ?>
<?php if (!$recent_contacts): ?>
    <li>None</li>
<?php endif; ?>
</ul>
