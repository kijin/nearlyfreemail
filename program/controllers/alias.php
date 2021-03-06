<?php

namespace Controllers;

class Alias extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        $view = new \Common\View('aliases');
        $view->title = 'Aliases';
        $view->menu = 'settings';
        $view->current_menu = 'aliases';
        $view->user = $this->user;
        $view->aliases = $this->user->get_aliases();
        $view->default_alias = $this->user->get_default_alias();
        $view->render();
    }
    
    // Add a new alias.
    
    public function add()
    {
        // Get user input.
        
        $name = \Common\Request::post('name');
        $email = \Common\Request::post('email');
        $csrf_token = \Common\Request::post('csrf_token');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=60'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 60 characters.');
        }
        
        // Add.
        
        $alias = new \Models\Alias();
        $alias->account_id = $this->user->id;
        $alias->name = $name;
        $alias->email = $email;
        $alias->incoming_key = \Common\Security::get_random(\Config\INCOMING_KEY_LENGTH);
        $alias->signature = '';
        $alias->created_time = time();
        $alias->notes = '';
        $alias->save();
        
        // Redirect.
        
        \Common\AJAX::redirect(\Common\Router::get_url('/settings/aliases'));
    }
    
    // Edit form.
    
    public function edit_form($alias_id)
    {
        // Check user input.
        
        $alias = \Models\Alias::get($alias_id);
        if (!$alias || $alias->account_id !== $this->user->id) \Common\AJAX::error('Alias not found, or access denied.');
        
        // Display edit form.
        
        $view = new \Common\View('aliases_edit');
        $view->title = 'Edit Alias';
        $view->menu = 'settings';
        $view->current_menu = 'aliases';
        $view->user = $this->user;
        $view->alias = $alias;
        $view->default_alias = $this->user->get_default_alias();
        $view->render();
    }
    
    // Edit post.
    
    public function edit_post()
    {
        // Check user input.
        
        $alias_id = \Common\Request::post('alias_id', 'int');
        $name = \Common\Request::post('name');
        $email = \Common\Request::post('email');
        $make_default = \Common\Request::post('make_default');
        $signature = \Common\Request::post('signature');
        $notification_email = \Common\Request::post('notification_email');
        $csrf_token = \Common\Request::post('csrf_token');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=60'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 60 characters.');
        }
        
        if ($notification_email && !\Common\Security::validate($notification_email, 'email'))
        {
            \Common\AJAX::error('The notification e-mail is not valid.');
        }
        
        if ($notification_email == $email)
        {
            \Common\AJAX::error('The notification e-mail cannot be the same as your own e-mail address.');
        }
        
        // Edit.
        
        $alias = \Models\Alias::get($alias_id);
        if (!$alias || $alias->account_id !== $this->user->id) \Common\AJAX::error('Alias not found, or access denied.');
        $alias->name = $name;
        $alias->email = $email;
        $alias->signature = $signature;
        $alias->set_note('notification_email', $notification_email);
        $alias->save();
        
        // Make default?
        
        if ($make_default === 'yes')
        {
            $this->user->save(array('default_alias' => $alias->id));
        }
        
        // Redirect.
        
        \Common\AJAX::redirect(\Common\Router::get_url('/settings/aliases'));
    }
    
    // Display setup instructions.
    
    public function instructions($alias_id)
    {
        // Check user input.
        
        $alias = \Models\Alias::get($alias_id);
        if (!$this->user->is_admin && (!$alias || $alias->account_id !== $this->user->id))  // Allow admin to see other users' aliases.
        {
            \Common\AJAX::error('Alias not found, or access denied.');
        }
        
        // Display edit form.
        
        $view = new \Common\View('aliases_howto');
        $view->title = 'Setup Instructions for "' . $alias->name . '" <' . $alias->email . '>';
        $view->menu = 'settings';
        $view->current_menu = 'aliases';
        $view->user = $this->user;
        $view->alias = $alias;
        $view->email_local = substr($alias->email, 0, strrpos($alias->email, '@'));
        $view->email_domain = strtolower(substr($alias->email, strrpos($alias->email, '@') + 1));
        $view->render();
    }
    
    // Alias actions.
    
    public function do_action()
    {
        // Grab user input.
        
        $selected_aliases = isset($_POST['selected_aliases']) ? $_POST['selected_aliases'] : array();
        $csrf_token = \Common\Request::post('csrf_token');
        $button = \Common\Request::post('button');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check if at least 1 alias is selected.
        
        if (!$selected_aliases && $button !== 'empty') \Common\AJAX::error('No alias selected.');
        
        // Compile a list of valid aliases. (Prevent deleting the default alias.)
        
        $aliases = array();
        $default_alias = $this->user->get_default_alias();
        foreach ($selected_aliases as $alias_id)
        {
            $alias = \Models\Alias::get($alias_id);
            if ($alias && $alias->account_id == $this->user->id && $alias->id != $default_alias->id) $aliases[] = $alias;
        }
        
        // Do various other things with the selected aliases.
        
        switch ($button)
        {
            case 'delete':
                \Common\DB::begin_transaction();
                foreach ($aliases as $alias) $alias->delete();
                \Common\DB::commit();
                \Common\AJAX::redirect(\Common\Router::get_url('/settings/aliases'));
                
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
    }
}
