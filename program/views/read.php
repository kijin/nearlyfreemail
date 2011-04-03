<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- Read View Title -->

<h3><?php e($message->subject ?: '(no subject)'); ?></h3>

<!-- Read View Meta -->

<dl id="read_meta">
    
    <!-- Sender and Recipients -->
    
    <div class="item">
        <dt>From:</dt>
        <dd>
            <?php echo \Models\Contact::extract_and_link($message->sender, '<a class="addr" href="index.php?action=compose&amp;to=ADDRESS">ADDRESS</a>'); ?>
            <?php if ($message->is_draft == 0 && strpos($message->notes, 'NOSOURCE') === false): ?>
                <div class="source"><a href="index.php?action=download_source&amp;message_id=<?php e($message->id); ?>">View Source</a></div>
            <?php endif; ?>
        </dd>
    </div>
    
    <div class="item">
        <dt>To:</dt>
        <dd><?php echo \Models\Contact::extract_and_link($message->recipient, '<a class="addr" href="index.php?action=compose&amp;to=ADDRESS">ADDRESS</a>'); ?></dd>
    </div>
    
    <?php if ($message->reply_to): ?>
    <div class="item">
        <dt>Reply-To:</dt>
        <dd><?php echo \Models\Contact::extract_and_link($message->reply_to, '<a class="addr" href="index.php?action=compose&amp;to=ADDRESS">ADDRESS</a>'); ?></dd>
    </div>
    <?php endif; ?>
    
    <?php if ($message->cc): ?>
    <div class="item">
        <dt>Cc:</dt>
        <dd><?php echo \Models\Contact::extract_and_link($message->cc, '<a class="addr" href="index.php?action=compose&amp;to=ADDRESS">ADDRESS</a>'); ?></dd>
    </div>
    <?php endif; ?>
    
    <?php if ($message->bcc): ?>
    <div class="item">
        <dt>Bcc:</dt>
        <dd><?php echo \Models\Contact::extract_and_link($message->bcc, '<a class="addr" href="index.php?action=compose&amp;to=ADDRESS">ADDRESS</a>'); ?></dd>
    </div>
    <?php endif; ?>
    
    <!-- Sent and/or Received Timestamps -->
    
    <?php if ($message->is_draft > 0): ?>
    <div class="item">
        <dt>Sent At:</dt><?php date_default_timezone_set($user->get_setting('timezone')); ?>
        <dd><?php e(date('D, d M Y H:i:s T', $message->sent_time)); ?> (<?php e(d($message->sent_time)); ?>)</dd>
    </div>
    <?php else: ?>
    <div class="item">
        <dt>Received At:</dt><?php date_default_timezone_set($user->get_setting('timezone')); ?>
        <dd><?php e(date('D, d M Y H:i:s T', $message->received_time)); ?> (<?php e(d($message->received_time)); ?>)
            <div class="delivery_time">Delivered in <?php e(number_format($message->received_time - $message->sent_time)); ?> Seconds</div></dd>
    </div>
    <?php endif; ?>
    
    <!-- Attachments -->
    
    <?php if ($message->attachments && $attachments = $message->get_attachments()): ?>
    <div class="item attachments">
        <dt>Attachments:</dt>
        <dd>
            <?php foreach ($attachments as $attachment): ?>
            <div class="attachment">
                <a href="index.php?action=download_attachment&amp;message_id=<?php e($message->id); ?>&amp;file_id=<?php e($attachment->id); ?>"><?php e($attachment->filename); ?></a>
                <span class="filesize">(<?php e(f($attachment->filesize)); ?>)</span>
            </div>
            <?php endforeach; ?>
        </dd>
    </div>
    <?php endif; ?>
    
</dl>

<!-- Read View Actions -->

<form id="read_actions" action="index.php" method="post" accept-charset="UTF-8">
    
    <!-- Shortcuts to Inbox and Archive -->
    
    <?php if ($selected_folder->name === 'Inbox'): ?>
        <button type="submit" class="rounded" name="button" value="archive">Archive</button>
    <?php elseif ($selected_folder->name === 'Spam'): ?>
        <button type="submit" class="rounded" name="button" value="to_inbox">Not Spam</button>
    <?php else: ?>
        <button type="submit" class="rounded" name="button" value="to_inbox">To Inbox</button>
    <?php endif; ?>
    
    <!-- Reply, Reply All, Forward, Mark Unread -->
    
    <button type="submit" class="rounded" name="button" value="reply">Reply</button>
    <button type="submit" class="rounded" name="button" value="reply_all">Reply All</button>
    <button type="submit" class="rounded" name="button" value="forward">Forward</button>
    <button type="submit" class="rounded" name="button" value="mark_unread">Unread</button>
    
    <!-- Report Spam, Delete, Delete Permanently -->
    
    <?php if (isset($selected_folder) && !in_array($selected_folder->name, array('Spam', 'Trash'))): ?>
        <button type="submit" class="rounded" name="button" value="spam">Spam</button>
        <button type="submit" class="rounded" name="button" value="trash">Trash</button>
    <?php else: ?>
        <button type="submit" class="rounded" name="button" value="delete_permanently">Delete Permanently</button>
    <?php endif; ?>
    
    <!-- Move to Folder -->
    
    <select id="action_move" name="move">
        <?php foreach ($folders as $folder): ?>
        <?php if ($folder->name === 'Drafts') continue; ?>
        <option value="<?php e($folder->id); ?>"<?php if ($folder === $selected_folder): ?> selected="selected"<?php endif; ?>><?php e($folder->name); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="rounded" name="button" value="move">Move</button>
    
    <!-- Hidden Fields -->
    
    <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
    <input type="hidden" name="action" value="message_do_action" />
    <input type="hidden" name="folder_page" value="<?php e($selected_page); ?>" />
    <input type="hidden" name="message_id" id="read_actions_message_id" value="<?php e($message->id); ?>" />
    <input type="hidden" name="csrf_token" id="read_actions_csrf_token" value="<?php e($token); ?>" />
    
</form>

<!-- Read View Content -->

<div id="read_content">
    <div id="read_content_text"><?php e($message->content, true); ?></div>
</div>

<!-- Read View Encoding Selector -->

<?php if ($message->is_draft == 0 && strpos($message->notes, 'NOSOURCE') === false): ?>
<form id="read_encoding" action="index.php" method="get" accept-charset="UTF-8">
    
    <!-- Hidden Fields, Only Used for NoScript Requests -->
    
    <input type="hidden" name="action" value="read" />
    <input type="hidden" name="message_id" value="<?php e($message->id); ?>" />
    <input type="hidden" name="page" value="<?php e($selected_page); ?>" />
    
    <!-- Current Encoding -->
    
    Message Encoding: <span><?php e($message->charset ?: 'N/A'); ?></span>
    Displayed Encoding: <span id="displayed_encoding"><?php e($displayed_encoding); ?></span>
    
    <!-- List of Encodings -->
    
    <?php $encodings = mb_list_encodings(); sort($encodings); ?>
    <label id="change_encoding_label" for="change_encoding">Change To</label>
    <select id="change_encoding" name="encoding" onchange="ajax_change_encoding()">
        <?php foreach ($encodings as $encoding): ?>
        <option value="<?php e($encoding); ?>"<?php if ($encoding === $displayed_encoding): ?> selected="selected"<?php endif; ?>><?php e($encoding); ?></option>
        <?php endforeach; ?>
    </select>
    <noscript>
        <button type="submit" class="rounded">Change</button>
    </noscript>
    
</form>
<?php endif; ?>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>