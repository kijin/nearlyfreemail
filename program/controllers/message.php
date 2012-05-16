<?php

namespace Controllers;

class Message extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Read.
    
    public function read($message_id)
    {
        // Find the requested message, and check if it belongs to this user.
        
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Don't display drafts.
        
        if ($message->is_draft == 1) \Common\AJAX::redirect(\Common\Router::get_url('/mail/edit', $message->id));
        
        // Mark the message as read.
        
        $message->mark_as_read();
        
        // List the user's folders, and find the one where this message belongs.
        
        $folders = \Models\Folder::get_folders($this->user->id);
        $selected_folder = null;
        foreach ($folders as $folder)
        {
            if ($folder->id === $message->folder_id) $selected_folder = $folder;
        }
        
        // Do we have any folder ID, search ID, or page number to remember?
        
        $selected_folder_id = \Common\Request::get('folder_id', 'int');
        $selected_search_id = \Common\Request::get('search_id');
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
        $view->selected_folder_id = $selected_folder_id;
        $view->selected_search_id = $selected_search_id;
        $view->selected_page = $selected_page;
        $view->message = $message;
        $view->displayed_encoding = $displayed_encoding;
        $view->render();
    }
    
    // Change encoding (AJAX version).
    
    public function change_encoding($message_id)
    {
        // Find the requested message, and check if it belongs to this user.
        
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check if the requested encoding is valid.
        
        $encoding = \Common\Request::post('encoding');
        $encodings = mb_list_encodings();
        if (!in_array($encoding, $encodings)) \Common\AJAX::error('Encoding \'' . $encoding . '\' is not supported.');
        
        // Transcode and return.
        
        list($subject, $content) = $message->get_original_subject_and_content();
        if ($subject === null) \Common\AJAX::error('The character encoding of this message cannot be changed.');
        $subject = mb_convert_encoding($subject, 'UTF-8', $encoding);
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        \Common\AJAX::content(array(
            'subject' => htmlspecialchars($subject, ENT_COMPAT, 'UTF-8', false),
            'content' => nl2br(htmlspecialchars($content, ENT_COMPAT, 'UTF-8', false)),
        ));
    }
    
    // Message actions.
    
    public function do_action($message_id)
    {
        // Grab user input.
        
        $folder_id = \Common\Request::post('folder_id', 'int');
        $search_id = \Common\Request::post('search_id');
        $page = \Common\Request::post('page', 'int');
        $csrf_token = \Common\Request::post('csrf_token');
        $button = \Common\Request::post('button');
        $move = \Common\Request::post('move');
        
        // Find the requested message, and check if it belongs to this user.
        
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
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
                \Common\AJAX::redirect(\Common\Router::get_url('/mail/compose?reply=' . $message->id));
            
            case 'reply_all':
                \Common\AJAX::redirect(\Common\Router::get_url('/mail/compose?reply_all=' . $message->id));
            
            case 'forward':
                \Common\AJAX::redirect(\Common\Router::get_url('/mail/compose?forward=' . $message->id));
            
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
        
        // Redirect to the original folder or search list.
        
        if ($folder_id && $current_folder = \Models\Folder::get($folder_id))
        {
            if ($current_folder->account_id == $this->user->id)
            {
                \Common\AJAX::redirect(\Common\Router::get_url('/mail/list', $current_folder->name . '?page=' . $page));
            }
            else
            {
                \Common\AJAX::redirect(\Common\Router::get_url('/mail'));
            }
        }
        elseif ($search_id !== '')
        {
            \Common\AJAX::redirect(\Common\Router::get_url('/mail/search?search_id=' . $search_id . '&page=' . $page));
        }
        else
        {
            \Common\AJAX::redirect(\Common\Router::get_url('/mail'));
        }
    }
    
    // Download an attachment.
    
    public function download_attachment($message_id, $file_id, $file_name)
    {
        // Check the message ID.
        
        $message = \Models\Message::get($message_id);
        if (!$message || $message->account_id !== $this->user->id) \Common\AJAX::error('Message not found, or access denied.');
        
        // Check the file ID.
        
        list($filename, $filesize, $content) = $message->get_attachment($file_id);
        if ($filename === null || $filename !== $file_name) \Common\Response::not_found();
        
        // Stream the contents to the client.
        
        header('Content-Type: ' . \Common\MIME::from_filename($filename));
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
    
    public function download_source($message_id)
    {
        // Check the message ID.
        
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