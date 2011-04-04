<?php

namespace Controllers;

class Folder extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        $view = new \Common\View('folders');
        $view->title = 'Folders';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->folders = \Models\Folder::get_folders($this->user->id);
        $view->render();
    }
    
    // Add a new folder.
    
    public function add()
    {
        // Get user input.
        
        $name = \Common\Request::post('name');
        $csrf_token = \Common\Request::post('csrf_token');
    
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=30'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 30 characters.');
        }
        
        // Add.
        
        $folder = new \Models\Folder();
        $folder->account_id = $this->user->id;
        $folder->name = $name;
        $folder->save();
        
        // Redirect.
        
        \Common\AJAX::redirect('index.php?action=folders');
    }
    
    // Edit form.
    
    public function edit_form()
    {
        // Check user input.
        
        $folder_id = \Common\Request::get('folder_id');
        $folder = \Models\Folder::get($folder_id);
        if (!$folder || $folder->account_id !== $this->user->id) \Common\AJAX::error('Folder not found, or access denied.');
        
        // Display edit form.
        
        $view = new \Common\View('folders_edit');
        $view->title = 'Rename Folder';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->folder = $folder;
        $view->render();    
    }
    
    // Edit post.
    
    public function edit_post()
    {
        // Check user input.
        
        $folder_id = \Common\Request::post('folder_id');
        $name = \Common\Request::post('name');
        $csrf_token = \Common\Request::post('csrf_token');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=30'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 30 characters.');
        }
        
        // Rename.
        
        $folder = \Models\Folder::get($folder_id);
        if (!$folder || $folder->account_id !== $this->user->id) \Common\AJAX::error('Folder not found, or access denied.');
        $folder->save(array('name' => $name));
        
        // Redirect.
        
        \Common\AJAX::redirect('index.php?action=folders');
    }
    
    // Folder actions.
    
    public function do_action()
    {
        // Grab user input.
        
        $selected_folders = isset($_POST['selected_folders']) ? $_POST['selected_folders'] : array();
        $csrf_token = \Common\Request::post('csrf_token');
        $button = \Common\Request::post('button');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check if at least 1 folder is selected.
        
        if (!$selected_folders && $button !== 'empty') \Common\AJAX::error('No folder selected.');
        
        // Compile a list of valid folders.
        
        $folders = array();
        foreach ($selected_folders as $folder_id)
        {
            $folder = \Models\Folder::get($folder_id);
            if ($folder && $folder->account_id == $this->user->id) $folders[] = $folder;
        }
        
        // Do various other things with the selected folders.
        
        switch ($button)
        {
            case 'delete':
                \Common\DB::begin_transaction();
                foreach ($folders as $folder)
                {
                    if (in_array($folder->name, \Config\Defaults::$folders))
                    {
                        \Common\AJAX::error('\'' . $folder->name . '\' is a system folder. It cannot be deleted.');
                    }
                    if ($folder->messages_all > 0)
                    {
                        $msg = 'To prevent accidentally deleting a folder, only empty folders can be deleted.' . "\n\n";
                        $msg .= 'If you really want to delete \'' . $folder->name . '\', please try again after you delete all messages from it. ';
                        $msg .= 'There are currently ' . $folder->messages_all . ' messages in that folder.';
                        \Common\AJAX::error($msg);
                    }
                    $folder->delete();
                }
                \Common\DB::commit();
                \Common\AJAX::redirect('index.php?action=folders');
                
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
    }
}
