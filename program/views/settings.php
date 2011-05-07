<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Settings Form -->

<form id="settings" class="ajax_capable" action="<?php u('/settings/account'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
    
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
            <label for="newpass2">New Passphrase, Again</label><br />
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
            <label for="show_sidebar_contacts">Show Contacts in Default Sidebar</label><br />
            <span class="explain">Enter an integer between 0 and 20.</span>
            <input type="text" id="show_sidebar_contacts" name="show_sidebar_contacts" value="<?php e($user->get_setting('show_sidebar_contacts')); ?>" />
        </div>
        <div class="item">
            <label for="show_compose_contacts">Show Contacts in Compose Screen</label><br />
            <span class="explain">Enter an integer between 0 and 20.</span>
            <input type="text" id="show_compose_contacts" name="show_compose_contacts" value="<?php e($user->get_setting('show_compose_contacts')); ?>" />
        </div>
        <div class="item">
            <label for="content_display_font">Display Font for Message Content</label><br />
            <span class="explain">For those of you who like fixed-width fonts.</span>
            <select id="content_display_font" name="content_display_font">
                <?php $current = $user->get_setting('content_display_font'); ?>
                <option value="serif" <?php if ($current == 'serif'): ?>selected="selected"<?php endif; ?>>Serif</option>
                <option value="sans-serif" <?php if ($current == 'sans-serif'): ?>selected="selected"<?php endif; ?>>Sans-Serif (Default)</option>
                <option value="monospace" <?php if ($current == 'monospace'): ?>selected="selected"<?php endif; ?>>Monospace</option>
            </select>
        </div>
        <div class="item">
            <label for="spam_threshold">Spam Filtering</label><br />
            <span class="explain">Be careful, this is experimental.</span>
            <select id="spam_threshold" name="spam_threshold">
                <?php $current = $user->get_setting('spam_threshold'); ?>
                <option value="8.00" <?php if ($current == 8.00): ?>selected="selected"<?php endif; ?>>8.00 (Disabled)</option>
                <option value="7.50" <?php if ($current == 7.50): ?>selected="selected"<?php endif; ?>>7.50</option>
                <option value="7.00" <?php if ($current == 7.00): ?>selected="selected"<?php endif; ?>>7.00</option>
                <option value="6.50" <?php if ($current == 6.50): ?>selected="selected"<?php endif; ?>>6.50</option>
                <option value="6.00" <?php if ($current == 6.00): ?>selected="selected"<?php endif; ?>>6.00 (Permissive)</option>
                <option value="5.50" <?php if ($current == 5.50): ?>selected="selected"<?php endif; ?>>5.50</option>
                <option value="5.00" <?php if ($current == 5.00): ?>selected="selected"<?php endif; ?>>5.00</option>
                <option value="4.50" <?php if ($current == 4.50): ?>selected="selected"<?php endif; ?>>4.50</option>
                <option value="4.00" <?php if ($current == 4.00): ?>selected="selected"<?php endif; ?>>4.00 (Moderate)</option>
                <option value="3.50" <?php if ($current == 3.50): ?>selected="selected"<?php endif; ?>>3.50</option>
                <option value="3.00" <?php if ($current == 3.00): ?>selected="selected"<?php endif; ?>>3.00</option>
                <option value="2.75" <?php if ($current == 2.75): ?>selected="selected"<?php endif; ?>>2.75</option>
                <option value="2.50" <?php if ($current == 2.50): ?>selected="selected"<?php endif; ?>>2.50 (Aggressive)</option>
                <option value="2.25" <?php if ($current == 2.25): ?>selected="selected"<?php endif; ?>>2.25</option>
                <option value="2.00" <?php if ($current == 2.00): ?>selected="selected"<?php endif; ?>>2.00</option>
                <option value="1.75" <?php if ($current == 1.75): ?>selected="selected"<?php endif; ?>>1.75</option>
                <option value="1.50" <?php if ($current == 1.50): ?>selected="selected"<?php endif; ?>>1.50 (Very Aggressive)</option>
                <option value="1.25" <?php if ($current == 1.25): ?>selected="selected"<?php endif; ?>>1.25</option>
                <option value="1.00" <?php if ($current == 1.00): ?>selected="selected"<?php endif; ?>>1.00 (Spartan)</option>
            </select>
        </div>
        <div class="item">
            <label for="timezone">Timezone</label><br />
            <span class="explain">Please select the nearest city in the same timezone as yours.</span>
            <select id="timezone" name="timezone">
                <?php $current = $user->get_setting('timezone'); ?>
                <?php $zones = DateTimeZone::listIdentifiers(); sort($zones); ?>
                <?php foreach ($zones as $zone): ?>
                    <option value="<?php e($zone); ?>" <?php if($current === $zone): ?>selected="selected"<?php endif; ?>><?php e($zone); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
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