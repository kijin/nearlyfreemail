<?php

namespace Controllers;

class Account extends Base
{
    // Create a new account.
    
    public function create($name, $email, $passphrase, $incoming_key, $is_admin)
    {
        // Begin a transaction.
        
        \Common\DB::begin_transaction();
        
        // Create the account.
        
        $account = new \Models\Account();
        $account->default_alias = 0;
        $account->password = '';
        $account->created_time = time();
        $account->is_admin = $is_admin;
        $account->save();
        
        // Create the default alias.
        
        $alias = new \Models\Alias();
        $alias->account_id = $account->id;
        $alias->name = $name;
        $alias->email = $email;
        $alias->incoming_key = $incoming_key;
        $alias->signature = '';
        $alias->created_time = $account->created_time;
        $alias->notes = '';
        $alias->save();
        
        // Add some additional properties to the account.
        
        $account->change_passphrase($passphrase);
        $account->save(array('default_alias' => $alias->id));
        
        // Create the default settings.
        
        $account->set_setting('content_display_font', \Config\Defaults::$content_display_font);
        $account->set_setting('messages_per_page', \Config\Defaults::$messages_per_page);
        $account->set_setting('show_sidebar_contacts', \Config\Defaults::$show_sidebar_contacts);
        $account->set_setting('show_compose_contacts', \Config\Defaults::$show_compose_contacts);
        $account->set_setting('spam_threshold', \Config\Defaults::$spam_threshold);
        $account->set_setting('timezone', \Config\Defaults::$timezone);
        
        // Create the default folders.
        
        foreach (\Config\Defaults::$folders as $name)
        {
            $folder = new \Models\Folder;
            $folder->account_id = $account->id;
            $folder->name = $name;
            $folder->save();
        }
        
        // Commit...
        
        \Common\DB::commit();
        
        // This method returns the ID of the newly created account.
        
        return $account->id;
    }
    
    // Display the login form.
    
    public function login_form()
    {
        // Note: This method may be called from the default route, or the check_login() method.
        
        $view = new \Common\View('login');
        $view->title = 'Login';
        $view->render();
    }
    
    // Login POST.
    
    public function login_post()
    {
        // Check user input.
        
        $email = \Common\Request::post('email');
        $pass = \Common\Request::post('pass');
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (empty($pass))
        {
            \Common\AJAX::error('Please enter your passphrase.');
        }
        
        // Check the login credentials.
        
        $ok = true;
        $alias = \Models\Alias::get_if_email($email);
        if (!$alias)
        {
            \Common\AJAX::error('Incorrect e-mail address or passphrase.');
        }
        
        $alias = $alias[0];
        $account = $alias->get_account();
        if ($account->get_default_alias()->id != $alias->id)  // Only allow the default alias.
        {
            \Common\AJAX::error('Incorrect e-mail address or passphrase.');
        }
        if (!$account->check_passphrase($pass))
        {
            \Common\AJAX::error('Incorrect e-mail address or passphrase.');
        }
        
        // Everything looks legit. Escort this user to his/her inbox.
        
        \Common\Session::login($account->id);
        \Common\AJAX::redirect(\Common\Router::get_url('/mail'));
    }
    
    // Logout.
    
    public function logout()
    {
        // Check the logout token to prevent CSRF.
        
        $token = \Common\Request::get('token');
        if (!$token || $token !== \Common\Session::get_logout_token())
        {
            \Common\AJAX::error('CSRF');
        }
        
        // Log this user out, and redirect to the login screen.
        
        \Common\Session::logout();
        \Common\AJAX::redirect(\Common\Router::get_url('/'));
    }
    
    // Show the "Manage Accounts" control panel.
    
    public function show()
    {
        // Only for admins.
        
        $this->check_login();
        $this->check_admin();
        
        // Display the list of accounts.
        
        $view = new \Common\View('accounts');
        $view->title = 'Manage Accounts';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->accounts = \Models\Account::select('ORDER BY id');
        $view->render();
    }
    
    // Add a new account.
    
    public function add()
    {
        // Only for admins.
        
        $this->check_login();
        $this->check_admin();
        
        // Get user input.
        
        $name = \Common\Request::post('name');
        $email = \Common\Request::post('email');
        $pass1 = \Common\Request::post('pass1');
        $pass2 = \Common\Request::post('pass2');
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
        
        if ($pass1 !== $pass2)
        {
            \Common\AJAX::error('Please enter the same passphrase twice.');
        }
        
        if (empty($pass1))
        {
            \Common\AJAX::error('Please select a passphrase.');
        }
        
        // Add.
        
        $incoming_key = \Common\Security::get_random(\Config\INCOMING_KEY_LENGTH);
        $this->create($name, $email, $pass1, $incoming_key, 0);
        
        // Redirect.
        
        \Common\AJAX::redirect(\Common\Router::get_url('/settings/accounts'));
    }
}
