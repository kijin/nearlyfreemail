
<h3>Actions</h3>

<ul>
    <li><a href="<?php u('/mail/compose'); ?>">New Message</a></li>
    <li><a href="<?php u('/settings'); ?>">Settings</a></li>
    <li><form id="logout" action="<?php u('/account/logout'); ?>" method="post" accept-charset="UTF-8">
        <input type="hidden" name="action" value="logout" />
        <input type="hidden" name="logout_token" value="<?php e(\Common\Session::get_logout_token()); ?>" />
        <button type="submit">Logout</button>
    </form></li>
</ul>

<h3>Folders <span class="info"><a href="<?php u('/settings/folders'); ?>">(edit)</a></span></h3>

<ul>
<?php foreach ($folders as $folder): ?>
    <li class="<?php if (isset($selected_folder) && $folder === $selected_folder): ?>selected<?php endif; ?>">
        <a href="<?php u('/mail/list', $folder->name); ?>"><?php e($folder->name); ?></a>
        <span class="new"><?php if ($folder->messages_new): ?>(<?php e($folder->messages_new); ?>)<?php endif; ?></span>
    </li>
<?php endforeach; ?>
</ul>

<?php $show_sidebar_contacts = $user->get_setting('show_sidebar_contacts'); ?>
<?php if ($show_sidebar_contacts > 0): ?>

    <h3>Contacts <span class="info"><a href="<?php u('/settings/contacts'); ?>">(edit)</a></span></h3>

    <ul>
    <?php $recent_contacts = \Models\Contact::get_recent($user->id, $show_sidebar_contacts); ?>
    <?php foreach ($recent_contacts as $contact): ?>
        <li><a href="<?php u('/mail/compose?to=' . $contact->get_profile()); ?>"><?php e($contact->name); ?></a></li>
    <?php endforeach; ?>
    <?php if (!$recent_contacts): ?>
        <li>None</li>
    <?php endif; ?>
    </ul>

<?php endif; ?>