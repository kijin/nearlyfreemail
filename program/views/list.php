<?php $BODY_CLASS='mail_screen'; ?>
<?php include 'common_header.php'; ?>
<?php include 'mail_header.php'; ?>

<!-- List View Title -->

<h3>
    <?php e($title); ?>
    <?php if (isset($selected_folder)): ?>
        <span class="info">Page <?php e($page); ?> of <?php e($pages); ?>&nbsp; (<?php e($selected_folder->messages_all); ?> messages in this folder)</span>
    <?php else: ?>
        <span class="info">Page <?php e($page); ?></span>
    <?php endif; ?>
</h3>

<!-- Form Begins -->

<form id="list_actions" action="<?php u('/mail/list/action'); ?>" method="post" accept-charset="UTF-8">
    
    <!-- List View Table -->

    <table id="message_list">
    
    <thead>
    <tr>
        <th class="select"><input type="checkbox" id="select_all" name="select_all" value="" disabled="disabled" /></th>
        <th class="fromto">
            <?php if (isset($selected_folder) && in_array($selected_folder->name, array('Drafts', 'Sent'))): ?>
                To
            <?php else: ?>
                From
            <?php endif; ?>
        </th>
        <th class="subject">Subject</th>
        <th class="time">
            <?php if (isset($selected_folder) && $selected_folder->name === 'Drafts'): ?>
                Saved Time
            <?php elseif (isset($selected_folder) && $selected_folder->name === 'Sent'): ?>
                Sent Time
            <?php else: ?>
                Received Time
            <?php endif; ?>
        </th>
    </tr>
    </thead>
    
    <tbody>
    
    <?php foreach ($messages as $message): ?>
    <tr>
        <td class="select">
            <input type="checkbox" name="selected_messages[]" value="<?php e($message->id); ?>" />
        </td>
        <td class="fromto <?php if (!$message->is_draft && !$message->is_read): ?>unread<?php endif; ?>">
            <?php if (isset($selected_folder) && in_array($selected_folder->name, array('Drafts', 'Sent')) && $message->is_draft > 0): /* Drafts & Sent Mail */ ?>
                <?php $recipients = \Models\Contact::extract($message->recipient); ?>
                <?php $recipient = $recipients ? $recipients[0] : new \Models\Contact(); ?>
                <a href="<?php u('/mail/compose?to=' . $recipient->get_profile()); ?>"><?php e($recipient->name ?: $recipient->email); ?></a>
                <?php $count = count($recipients) + count(\Models\Contact::extract($message->cc)) + count(\Models\Contact::extract($message->bcc)); ?>
                <?php if ($count > 1): ?>&nbsp;(<?php e($count - 1); ?> more)<?php endif; ?>
            <?php elseif (isset($selected_folder) && in_array($selected_folder->name, array('Drafts', 'Sent'))): /* Received Mail in Wrong Folder */ ?>
                <em>Received Mail</em>
            <?php elseif ($message->is_draft == 1): /* Sent Mail in Wrong Folder */ ?>
                <em>Draft</em>
            <?php elseif ($message->is_draft == 2): /* Sent Mail in Wrong Folder */ ?>
                <em>Sent Mail</em>
            <?php else: /* Regular Received Mail */ ?>
                <?php $sender = \Models\Contact::extract($message->sender); ?>
                <?php $sender = $sender ? $sender[0] : new \Models\Contact(); ?>
                <a href="<?php u('/mail/compose?to=' . $sender->get_profile()); ?>"><?php e($sender->name ?: $sender->email); ?></a>
            <?php endif; ?>
        </td>
        <td class="subject <?php if (!$message->is_draft && !$message->is_read): ?>unread<?php endif; ?>">
            <?php if ($message->is_draft == 1): /* Unsent Drafts */ ?>
                <a href="<?php u('/mail/edit', $message->id); ?>"><?php e($message->subject ?: '(no subject)'); ?></a>
            <?php else: /* All Other Messages */ ?>
                <?php if (isset($selected_folder)): $param = '?folder_id=' . $selected_folder->id . '&page=' . $page; ?>
                <?php elseif (isset($search_id)): $param = '?search_id=' . $search_id . '&page=' . $page; ?>
                <?php else: $param = ''; endif; ?>
                <a href="<?php u('/mail/read', $message->id); ?><?php e($param); ?>"><?php e($message->subject ?: '(no subject)'); ?></a>
                <?php if ($message->is_replied): ?><span class="replied">[<?php if ($message->is_replied & 1): ?>R<?php endif; ?><?php if ($message->is_replied & 2): ?>F<?php endif; ?>]</span><?php endif; ?>
            <?php endif; ?>
        </td>
        <td class="time <?php if (!$message->is_draft && !$message->is_read): ?>unread<?php endif; ?>">
            <?php if ($message->is_draft == 2): /* Sent Mail */ ?>
                <?php t($message->sent_time); ?>
            <?php else: /* Drafts & All Other Messages */ ?>
                <?php t($message->received_time); ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    
    <?php if (!$messages): ?>
    <tr><td class="none" colspan="4"><p>No Messages</p></td></tr>
    <?php endif; ?>
    
    </tbody>
    </table>
    
    <!-- Hidden Fields -->
    
    <fieldset>
        <?php \Common\Session::add_token($token = \Common\Security::get_random(16)); ?>
        <input type="hidden" name="folder_id" value="<?php if (isset($selected_folder)): ?><?php e($selected_folder->id); ?><?php endif; ?>" />
        <input type="hidden" name="search_id" value="<?php if (isset($search_id)): ?><?php e($search_id); ?><?php endif; ?>" />
        <input type="hidden" name="page" value="<?php e($page); ?>" />
        <input type="hidden" name="csrf_token" value="<?php e($token); ?>" />
    </fieldset>
    
    <!-- Visible Buttons -->
    
    <div id="list_actions_buttons">
        
        <?php if (isset($selected_folder) && $selected_folder->name === 'Drafts'): ?>
            
            <button type="submit" class="rounded" name="button" value="delete_permanently">Discard</button>
            
        <?php else: ?>
            
            <!-- Shortcuts to Inbox and Archive -->
            
            <?php if (isset($selected_folder) && $selected_folder->name === 'Inbox'): ?>
                <button type="submit" class="rounded" name="button" value="archive">Archive</button>
            <?php elseif (isset($selected_folder) && $selected_folder->name === 'Spam'): ?>
                <button type="submit" class="rounded" name="button" value="to_inbox">Not Spam</button>
            <?php else: ?>
                <button type="submit" class="rounded" name="button" value="to_inbox">To Inbox</button>
            <?php endif; ?>
            
            <!-- Mark Read, Mark Unread -->
            
            <button type="submit" class="rounded" name="button" value="mark_read">Mark as Read</button>
            <button type="submit" class="rounded" name="button" value="mark_unread">Mark as Unread</button>
            
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
                <option value="<?php e($folder->id); ?>"<?php if (isset($selected_folder) && $folder->id == $selected_folder->id): ?> selected="selected"<?php endif; ?>><?php e($folder->name); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="rounded" name="button" value="move">Move</button>
            
            <!-- Empty -->
            
            <?php if (isset($selected_folder) && in_array($selected_folder->name, array('Spam', 'Trash'))): ?>
                <button type="submit" class="rounded" name="button" value="empty">Empty</button>
            <?php endif; ?>
            
        <?php endif; ?>
        
    </div>
    
</form>

<!-- Pagination -->

<div id="pagination">

<?php if (isset($pages)): /* Regular Folder */ ?>

    <a href="<?php u('/mail/list', $selected_folder->name); ?>">&laquo;</a>
    
    <?php
        $min = $page - 5;
        $max = $page + 5;
        if ($min < 1) { $min = 1; $max = $min + 10; }
        if ($max > $pages) { $max = $pages; $min = $max - 10; }
        if ($min < 1) { $min = 1; }
    ?>
    
    <?php for ($i = $min; $i <= $max; $i++): ?>
        <a href="<?php u('/mail/list', $selected_folder->name); ?>?page=<?php e($i); ?>" class="<?php if ($i == $page): ?>selected<?php endif; ?>"><?php e($i); ?></a>
    <?php endfor; ?>
    
    <a href="<?php u('/mail/list', $selected_folder->name); ?>?page=<?php e($pages); ?>">&raquo;</a>

<?php else: /* Search Result */ ?>

    <a href="<?php u('/mail/search?search_id=' . $search_id); ?>">&laquo;</a>
    
    <?php for ($i = 1; $i <= $page; $i++): ?>
        <a href="<?php u('/mail/search?search_id=' . $search_id); ?>&amp;page=<?php e($i); ?>" class="<?php if ($i == $page): ?>selected<?php endif; ?>"><?php e($i); ?></a>
    <?php endfor; ?>
    
    <?php if (count($messages) < $user->get_setting('messages_per_page')): ?>
        &nbsp;(end)
    <?php else: ?>
        &nbsp;<a href="<?php u('/mail/search?search_id='. $search_id); ?>&amp;page=<?php e($page + 1); ?>">(more)</a>
    <?php endif; ?>
    
<?php endif; ?>

</div>

<!-- Standard Footers -->

<?php include 'mail_footer.php'; ?>
<?php include 'common_footer.php'; ?>