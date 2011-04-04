<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Settings Form -->

<form id="settings" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return ajax(this)">
    
    <!-- Incoming URL -->
    
    <fieldset class="category rounded" id="incoming_url">
        
        <p>Your e-mail forwarding URL is as follows. Give it to NFSN and nobody else.</p>
        <p class="url"><strong><?php e($user->get_default_alias()->get_incoming_url()); ?></strong></p>
        
    </fieldset>
    
    <!-- Name, E-mail, and Password -->
    
    <fieldset class="category rounded">
        <div class="item">
            <label for="email">Change Your E-mail Address</label><br />
            <span class="explain">Be sure to update NFSN's e-mail forwarding setting as well.</span>
            <input type="text" id="email" name="email" value="<?php e($user->get_default_alias()->email); ?>" />
        </div>
        <div class="item">
            <label for="name">Change Your Name</label><br />
            <span class="explain">This is the name that you want your recipients to see.</span>
            <input type="text" id="name" name="name" value="<?php e($user->get_default_alias()->name); ?>" />
        </div>
        <div class="item">
            <label for="pass">Current Passphrase</label><br />
            <span class="explain">Enter if you want to change your passphrase.</span>
            <input type="password" id="pass" name="pass" value="" />
        </div>
        <div class="item">
            <label for="newpass1">New Passphrase</label><br />
            <span class="explain">Enter if you want to change your passphrase.</span>
            <input type="password" id="newpass1" name="newpass1" value="" />
        </div>
        <div class="item">
            <label for="newpass2">New Passphrase</label><br />
            <span class="explain">Just to make sure you didn't make any typos the first time.</span>
            <input type="password" id="newpass2" name="newpass2" value="" />
        </div>
    </fieldset>
    
    <!-- Signature -->
    
    <fieldset class="category rounded">
        <div class="item">
            <label for="signature">Signature</label>
            <textarea id="signature" name="signature"><?php e($user->get_default_alias()->signature); ?></textarea>
        </div>
    </fieldset>
    
    <!-- Other Settings -->
    
    <fieldset class="category rounded">
        <div class="item">
            <label for="messages_per_page">Number of Messages to Show in Each Page</label><br />
            <span class="explain">Enter an integer between 5 and 50.</span>
            <input type="text" id="messages_per_page" name="messages_per_page" value="<?php e($user->get_setting('messages_per_page')); ?>" />
        </div>
        <div class="item">
            <label for="show_recent_contacts">Number of Recently Used Contacts in Sidebar</label><br />
            <span class="explain">Enter an integer between 0 and 20.</span>
            <input type="text" id="show_recent_contacts" name="show_recent_contacts" value="<?php e($user->get_setting('show_recent_contacts')); ?>" />
        </div>
        <div class="item">
            <label for="spam_threshold">Spam Filtering</label><br />
            <span class="explain">Be careful, this is experimental.</span>
            <select id="spam_threshold" name="spam_threshold">
                <option value="7" <?php if ($user->get_setting('spam_threshold') > 6): ?>selected="selected"<?php endif; ?>>Permissive</option>
                <option value="5" <?php if ($user->get_setting('spam_threshold') == 5): ?>selected="selected"<?php endif; ?>>Moderate (Default)</option>
                <option value="3" <?php if ($user->get_setting('spam_threshold') == 3): ?>selected="selected"<?php endif; ?>>Aggressive</option>
                <option value="1.5" <?php if ($user->get_setting('spam_threshold') < 2): ?>selected="selected"<?php endif; ?>>Very Aggressive</option>
            </select>
        </div>
        <div class="item">
            <label for="timezone">Timezone</label><br />
            <span class="explain">Please select the nearest city in the same timezone as yours.</span>
            <select id="timezone" name="timezone">
                <?php $zones = DateTimeZone::listIdentifiers(); sort($zones); ?>
                <?php foreach ($zones as $zone): ?>
                    <option value="<?php e($zone); ?>" <?php if($user->get_setting('timezone') === $zone): ?>selected="selected"<?php endif; ?>><?php e($zone); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="action" value="settings" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Save Button -->
    
    <fieldset>
        <button type="submit" class="rounded">Save Settings</button>
    </fieldset>

</form>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>