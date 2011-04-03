<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Compose View Title -->

<h3><?php e($title); ?></h3>

<!-- Compose Form -->

<form id="compose" action="index.php" method="post" accept-charset="UTF-8" enctype="multipart/form-data">

    <!-- Standard Fields -->
    
    <fieldset id="standard_fields">
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
                    <a href="index.php?action=download_attachment&amp;message_id=<?php e($message->id); ?>&amp;file_id=<?php e($attachment->id); ?>"><?php e($attachment->filename); ?></a>
                    <span class="filesize">(<?php e(f($attachment->filesize)); ?>)</span></p>
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
        <input type="hidden" name="action" value="compose" />
        <input type="hidden" name="message_id" id="message_id" value="<?php e($message ? $message->id : ''); ?>" />
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php e($token); ?>" />
        <input type="hidden" name="references" id="references" value="<?php e(isset($references) ? $references : ''); ?>" />
        <input type="hidden" name="notes" id="notes" value="<?php e(isset($notes) ? $notes : ''); ?>" />
    </fieldset>
    
    <!-- Save and Send Buttons -->
    
    <fieldset class="save">
        <div id="autosave" class="status">Autosave: <span id="as_enabled">Disabled</span>.</div>
        <button type="submit" class="rounded" name="button" value="save" onclick="autosave_override()">Save Draft</button>
        <button type="submit" class="rounded" name="button" value="send" onclick="autosave_override()">Send Message</button>
    </fieldset>

</form>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>