<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- New Contacts Form -->

<form id="contacts_add" class="rounded" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return ajax(this)">
    
    <fieldset class="category rounded">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="" /> &nbsp;
        <label for="email" class="email">E-mail</label>
        <input type="text" id="email" name="email" value="" /> &nbsp;
        <button type="submit" class="rounded">Add</button>
    </fieldset>
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="action" value="contacts_add" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
</form>

<!-- Existing Contacts -->

<form id="contacts_existing" class="rounded" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Contacts List -->
    
    <fieldset>
    <?php foreach ($contacts as $contact): ?>
    <p class="item">
        <input type="checkbox" name="selected_contacts[]" value="<?php e($contact->id); ?>" />
        <a href="index.php?action=compose&amp;to=<?php e($contact->get_profile()); ?>"><?php e($contact->name); ?> <span class="email"><?php e($contact->email); ?></span></a> &nbsp;
        <span class="actions"><a href="index.php?action=contacts_edit&amp;contact_id=<?php e($contact->id); ?>">Edit</a></span>
    </p>
    <?php endforeach; ?>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <input type="hidden" name="action" value="contacts_do_action" />
        <input type="hidden" name="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Visible Buttons -->
    
    <fieldset id="contacts_actions_buttons">
        <button type="submit" class="rounded" name="button" value="send_message">Send a Message to Selected Contacts</button>
        <button type="submit" class="rounded" name="button" value="delete">Delete Selected Contacts</button>
    </fieldset>
    
</div>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>