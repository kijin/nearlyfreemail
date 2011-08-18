<?php include 'common_header.php'; ?>

<!-- Compose View Title -->

<h3><?php e($title); ?></h3>

<!-- Compose Form -->

<form id="compose" action="<?php u('/mail/compose'); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Standard Fields -->
    
    <fieldset id="standard_fields">
        <div class="field">
            <label for="alias_id">From:</label>
            <div class="box">
                <select id="alias_id" name="alias_id">
                    <?php $aliases = $user->get_aliases(); ?>
                    <?php foreach ($aliases as $alias): ?>
                        <option value="<?php e($alias->id); ?>" <?php if ($alias->id == $selected_alias->id): ?>selected="selected"<?php endif; ?>><?php e($alias->name); ?> &lt;<?php e($alias->email); ?>&gt;</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label for="recipient">To:</label>
            <div class="box"><input type="text" name="recipient" id="recipient" value="<?php e($message ? $message->recipient : $recipient); ?>" class="width" /></div>
        </div>
        <div class="field">
            <label for="cc">Cc:</label>
            <div class="box"><input type="text" name="cc" id="cc" value="<?php e($message ? $message->cc : $cc); ?>" class="width" /></div>
        </div>
        <div class="field">
            <label for="bcc">Bcc:</label>
            <div class="box"><input type="text" name="bcc" id="bcc" value="<?php e($message ? $message->bcc : ''); ?>" class="width" /></div>
        </div>
        <div class="field">
            <label for="subject">Subject:</label>
            <div class="box"><input type="text" name="subject" id="subject" value="<?php e($message ? $message->subject : $subject); ?>" class="width" /></div>
        </div>
        <div class="message_content_container">
            <textarea name="message_content" id="message_content"><?php e($message ? $message->content : $content); ?></textarea>
        </div>
    </fieldset>
    
    <!-- Attachments -->
    
    <fieldset id="attachments">
        <label for="attach1">Files:</label>
        <div id="file_list">
            <?php $attachments = $message ? $message->get_attachments() : array(); ?>
            <?php if ($attachments): ?>
                <div class="existing">
                <?php foreach ($attachments as $attachment): ?>
                    <p><input type="checkbox" name="attach_delete_<?php e($attachment->id); ?>" value="yes" /><span class="delete">Delete</span>
                    <a href="<?php u('/mail/attachment', $message->id, $attachment->id, $attachment->filename); ?>"><?php e($attachment->filename); ?></a>
                    <span class="filesize">(<?php f($attachment->filesize); ?>)</span></p>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="newfiles" id="add_files_here">
                <p>
                    <input type="file" name="attach_1" />
                    <span id="attachments_buttons"><button class="rounded" onclick="return add_attachment()">Add Another File</button></span>
                    <noscript>
                        <input type="file" name="attach_2" />
                        <style> #attachments_buttons { display: none; } </style>
                    </noscript>
                </p>
            </div>
        </div>
    </fieldset>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="message_id" id="message_id" value="<?php e($message ? $message->id : ''); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
        <input type="hidden" name="references" id="references" value="<?php e(isset($references) ? $references : ''); ?>" />
        <input type="hidden" name="notes" id="notes" value="<?php e(isset($notes) ? $notes : ''); ?>" />
    </fieldset>
    
    <!-- Save and Send Buttons -->
    
    <fieldset class="save">
        <div id="autosave" class="status">Autosave: <span id="as_enabled">Disabled.</span> <span id="as_changed"></span></div>
        <script type="text/javascript"> autosave_url = '<?php u('/mail/compose'); ?>'; </script>
        <button type="submit" class="rounded" name="button" value="save" onclick="autosave_override()">Save Draft</button>
        <button type="submit" class="rounded" name="button" value="send" onclick="autosave_override()">Send Message</button>
    </fieldset>

</form>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>