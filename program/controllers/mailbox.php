<?php

namespace Controllers;

class Mailbox extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Inbox shortcut.
    
    public function inbox()
    {
        $this->show('Inbox');
    }
    
    // Show the contents of a mailbox.
    
    public function show($folder_name)
    {
        // Get a list of this user's folders, and check if the requested folder is valid.
        
        $folders = \Models\Folder::get_folders($this->user->id);
        $selected_folder = null;
        foreach ($folders as $folder)
        {
            if ($folder->name === $folder_name) $selected_folder = $folder;
        }
        if (!$selected_folder) \Common\Response::not_found();
        
        // Get a list of messages in this folder.
        
        $page = \Common\Request::get('page', 'int') ?: 1;
        if ($page < 1) $page = 1;
        $pages = ceil($selected_folder->messages_all / $this->user->get_setting('messages_per_page'));
        if ($pages < 1) $pages = 1;
        $messages = \Models\Message::get_list($this->user->id, $selected_folder->id, $this->user->get_setting('messages_per_page'), $page);
        
        // Display the view.
        
        $view = new \Common\View('list');
        $view->title = $folder_name;
        $view->user = $this->user;
        $view->folders = $folders;
        $view->selected_folder = $selected_folder;
        $view->page = $page;
        $view->pages = $pages;
        $view->messages = $messages;
        $view->render();
    }
    
    // Search view.
    
    public function search()
    {
        // Get the keywords and parse them.
        
        if ($keywords = \Common\Request::get('keywords'))
        {
            $search_id = substr(md5($keywords), 0, 10);
            $keywords = explode(' ', $keywords);
            $_SESSION['searches'][$search_id] = $keywords;
        }
        elseif ($search_id = \Common\Request::get('search_id'))
        {
            if (isset($_SESSION['searches'][$search_id]))
            {
                $keywords = $_SESSION['searches'][$search_id];
            }
            else
            {
                $keywords = array();
            }
        }
        else
        {
            $keywords = array();
        }
        
        //if (!count($keywords)) \Common\AJAX::redirect(\Common\Router::get_url('/'));
        
        // Get the page number.
        
        $page = \Common\Request::get('page', 'int') ?: 1;
        if ($page < 1) $page = 1;
        
        // Perform the search.
        
        header('Content-Type: text/plain; charset=UTF-8');
        $messages = \Models\Message::search($this->user->id, null, $keywords, $this->user->get_setting('messages_per_page'), $page);
        
        // Display the view.
        
        $view = new \Common\View('list');
        $view->title = 'Search: "' . implode(' ', $keywords) . '"';
        $view->user = $this->user;
        $view->folders = \Models\Folder::get_folders($this->user->id);
        $view->keywords = $keywords;
        $view->search_id = $search_id;
        $view->page = $page;
        $view->messages = $messages;
        $view->render();
    }
    
    // Mailbox actions.
    
    public function do_action()
    {
        // Grab user input.
        
        $selected_messages = isset($_POST['selected_messages']) ? $_POST['selected_messages'] : array();
        $csrf_token = \Common\Request::post('csrf_token');
        $folder_id = \Common\Request::post('folder_id', 'int');
        $search_id = \Common\Request::post('search_id');
        $page = \Common\Request::post('page', 'int');
        $button = \Common\Request::post('button');
        $move = \Common\Request::post('move');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check if at least 1 message is selected.
        
        if (!$selected_messages && $button !== 'empty') \Common\AJAX::error('No message selected.');
        
        // Compile a list of valid messages.
        
        $messages = array();
        foreach ($selected_messages as $message_id)
        {
            $message = \Models\Message::get($message_id);
            if ($message && $message->account_id == $this->user->id) $messages[] = $message;
        }
        
        // Do various other things with the selected messages.
        
        switch ($button)
        {
            case 'archive':
                $destination_folder = \Models\Folder::get_folder($this->user->id, 'Archives');
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->move_to_folder($destination_folder->id);
                \Common\DB::commit();
                break;
            
            case 'to_inbox':
                $destination_folder = \Models\Folder::get_folder($this->user->id, 'Inbox');
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->move_to_folder($destination_folder->id);
                \Common\DB::commit();
                break;
            
            case 'mark_read':
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->mark_as_read();
                \Common\DB::commit();
                break;
            
            case 'mark_unread':
                \Common\DB::begin_transaction();
                foreach ($messages as $message) if (!$message->is_draft) $message->mark_as_unread();
                \Common\DB::commit();
                break;
            
            case 'spam':
                $destination_folder = \Models\Folder::get_folder($this->user->id, 'Spam');
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->move_to_folder($destination_folder->id);
                \Common\DB::commit();
                break;
            
            case 'trash':
                $destination_folder = \Models\Folder::get_folder($this->user->id, 'Trash');
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->move_to_folder($destination_folder->id);
                \Common\DB::commit();
                break;
            
            case 'delete_permanently':
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->delete();
                \Common\DB::commit();
                break;
            
            case 'move':
                $destination_folder = \Models\Folder::get($move);
                if (!$destination_folder || $destination_folder->account_id !== $this->user->id) \Common\AJAX::error('Operation not permitted.');
                \Common\DB::begin_transaction();
                foreach ($messages as $message) $message->move_to_folder($destination_folder->id);
                \Common\DB::commit();
                break;
                
            case 'empty':
                $current_folder = \Models\Folder::get($folder_id);
                if (!in_array($current_folder->name, array('Spam', 'Trash'))) \Common\AJAX::error('Operation not permitted.');
                $current_folder->empty_folder();
                break;
                
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
        
        // Redirect to the original list and page.
        
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
}