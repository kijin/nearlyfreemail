
<h3>Folders <span class="info"><a href="index.php?action=folders">(edit)</a></span></h3>

<ul>
<?php foreach ($folders as $folder): ?>
    <li class="<?php if (isset($selected_folder) && $folder === $selected_folder): ?>selected<?php endif; ?>">
        <a href="index.php?action=list&amp;folder=<?php e($folder->name); ?>"><?php e($folder->name); ?></a>
        <span class="new"><?php if ($folder->messages_new): ?>(<?php e($folder->messages_new); ?>)<?php endif; ?></span>
    </li>
<?php endforeach; ?>
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
