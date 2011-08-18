
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail'); ?>">Return to Inbox</a></li>
    <li><a href="<?php u('/settings'); ?>">Preferences</a></li>
    <li><a href="<?php u('/account/logout'); ?>?token=<?php e(\Common\Session::get_logout_token()); ?>">Logout</a></li>
</ul>

<?php $show_compose_contacts = $user->get_setting('show_compose_contacts'); ?>
<?php if ($show_compose_contacts > 0): ?>

    <h3>Contacts <span class="info"><a href="<?php u('/settings/contacts'); ?>">(edit)</a></span></h3>
    
    <ul id="compose_contacts">
    <?php $recent_contacts = \Models\Contact::get_recent($user->id, $show_compose_contacts); ?>
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
    
<?php endif; ?>
