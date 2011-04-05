<?php

namespace Controllers;

class Message extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Read.
    
    public function read()
    {
        // Find the requested message, and check if it belongs to this user.
        
        $message_id = \Common\Request::get('message_id', 'int');
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Don't display drafts.
        
        if ($message->is_draft == 1) \Common\AJAX::redirect('index.php?action=edit&message_id=' . $message->id);
        
        // Mark the message as read.
        
        $message->mark_as_read();
        
        // List the user's folders, and find the one where this message belongs.
        
        $folders = \Models\Folder::get_folders($this->user->id);
        $selected_folder = null;
        foreach ($folders as $folder)
        {
            if ($folder->id === $message->folder_id) $selected_folder = $folder;
        }
        
        // Which page were we on? This is useful when redirecting later on.
        
        $selected_page = \Common\Request::get('page', 'int');
        if ($selected_page < 1) $selected_page = 1;
        
        // Use a different encoding?
        
        $encoding = \Common\Request::get('encoding');
        if (in_array($encoding, mb_list_encodings()))
        {
            list($subject, $content) = $message->get_original_subject_and_content();
            if ($subject !== null)
            {
                $message->subject = mb_convert_encoding($subject, 'UTF-8', $encoding);
                $message->content = mb_convert_encoding($content, 'UTF-8', $encoding);
                $displayed_encoding = $encoding;
            }
            else
            {
                $displayed_encoding = $message->charset ?: 'UTF-8';
            }
        }
        else
        {
            $displayed_encoding = $message->charset ?: 'UTF-8';
        }
        
        // Display the view.
        
        $view = new \Common\View('read');
        $view->title = $message->subject;
        $view->user = $this->user;
        $view->folders = $folders;
        $view->selected_folder = $selected_folder;
        $view->selected_page = $selected_page;
        $view->message = $message;
        $view->displayed_encoding = $displayed_encoding;
        $view->render();
    }
    
    // Change encoding (AJAX version).
    
    public function change_encoding()
    {
        // Find the requested message, and check if it belongs to this user.
        
        $message_id = \Common\Request::get('message_id', 'int');
        $message = \Models\Message::get($message_id, false, true);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check if the requested encoding is valid.
        
        $encoding = \Common\Request::get('encoding');
        $encodings = mb_list_encodings();
        if (!in_array($encoding, $encodings)) \Common\AJAX::error('Encoding \'' . $encoding . '\' is not supported.');
        
        // Transcode and return.
        
        list($subject, $content) = $message->get_original_subject_and_content();
        if ($subject === null) \Common\AJAX::error('The character encoding of this message cannot be changed.');
        $subject = mb_convert_encoding($subject, 'UTF-8', $encoding);
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        \Common\AJAX::content(array(
            'subject' => htmlentities($subject, ENT_COMPAT | ENT_IGNORE, 'UTF-8', false),
            'content' => nl2br(htmlentities($content, ENT_COMPAT | ENT_IGNORE, 'UTF-8', false)),
        ));
    }
    
    // Message actions.
    
    public function do_action()
    {
        // Grab user input.
        
        $message_id = \Common\Request::post('message_id', 'int');
        $csrf_token = \Common\Request::post('csrf_token');
        $folder_page = \Common\Request::post('folder_page', 'int');
        $button = \Common\Request::post('button');
        $move = \Common\Request::post('move');
        
        // Find the requested message, and check if it belongs to this user.
        
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Most of the actions below will redirect to the current folder.
        
        $current_folder = \Models\Folder::get($message->folder_id);
        
        // Do various other things with the message.
        
        switch ($button)
        {
            case 'archive':
                $message->move_to_folder(\Models\Folder::get_folder($this->user->id, 'Archives')->id);
                break;
            
            case 'to_inbox':
                $message->move_to_folder(\Models\Folder::get_folder($this->user->id, 'Inbox')->id);
                break;
            
            case 'mark_unread':
                if (!$message->is_draft) $message->mark_as_unread();
                break;
            
            case 'spam':
                $message->move_to_folder(\Models\Folder::get_folder($this->user->id, 'Spam')->id);
                break;
            
            case 'trash':
                $message->move_to_folder(\Models\Folder::get_folder($this->user->id, 'Trash')->id);
                break;
            
            case 'delete_permanently':
                $message->delete();
                break;
            
            case 'move':
                $folder = \Models\Folder::get($move);
                if (!$folder || $folder->account_id !== $this->user->id) \Common\AJAX::error('Operation not permitted.');
                $message->move_to_folder($folder->id);
                break;
            
            case 'reply':
                \Common\AJAX::redirect('index.php?action=compose&reply=' . $message->id);
            
            case 'reply_all':
                \Common\AJAX::redirect('index.php?action=compose&reply_all=' . $message->id);
            
            case 'forward':
                \Common\AJAX::redirect('index.php?action=compose&forward=' . $message->id);
            
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
        
        // Redirect to the original folder.
        
        \Common\AJAX::redirect('index.php?action=list&folder=' . $current_folder->name . '&page=' . $folder_page);
    }
    
    // Download an attachment.
    
    public function download_attachment()
    {
        // Check the message ID.
        
        $message_id = \Common\Request::get('message_id', 'int');
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check the file ID.
        
        $file_id = \Common\Request::get('file_id', 'int');
        list($filename, $filesize, $content) = $message->get_attachment($file_id);
        if ($filename === null) \Common\Response::not_found();
        
        // Stream the contents to the client.
        
        header('Content-Type: ' . \Common\MIME::from_filename($filename));
		header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        
        if (is_resource($content))
        {
            fpassthru($content);
        }
        else
        {
            echo $content;
        }
        exit;
    }
    
    // Download the message source.
    
    public function download_source()
    {
        // Check the message ID.
        
        $message_id = \Common\Request::get('message_id', 'int');
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check if the message has a source stored with us.
        
        $source = $message->get_source();
        if ($source === false) \Common\AJAX::error('This message does not have a source associated with it.');
        
        // Stream the contents to the client.
        
        header('Content-Type: text/plain; charset=' . ($message->charset ?: 'UTF-8'));
        
        if (is_resource($source))
        {
            fpassthru($source);
        }
        else
        {
            echo $source;
        }
        exit;
    }
}