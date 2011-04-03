
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
