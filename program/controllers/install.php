<?php

namespace Controllers;

class Install extends Base
{
    // Display the install form.
    
    public function install_form()
    {
        // Only this method ever gets called until NFM is installed, so we need to do a little routing here.
        
        if (\Common\Request::info('method') === 'POST' && count($_POST))
        {
            return $this->install_post();
        }
        
        $view = new \Common\View('install');
        $view->title = 'Installation';
        $view->render();
    }
    
    // Install POST.
    
    public function install_post()
    {
        // Check the user input.
        
        $email = \Common\Request::post('email');
        $name = \Common\Request::post('name');
        $pass1 = \Common\Request::post('pass1');
        $pass2 = \Common\Request::post('pass2');
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=60'))
        {
            \Common\AJAX::error('Please enter your name. It can be between 1 and 60 characters.');
        }
        
        if ($pass1 !== $pass2)
        {
            \Common\AJAX::error('Please enter the same passphrase twice.');
        }
        
        if (empty($pass1))
        {
            \Common\AJAX::error('Please select a passphrase.');
        }
        
        // This will fail if the database is not writable.
        
        try
        {
            $schema = file_get_contents(BASEDIR . '/program/config/schema.sql');
            $schema = explode(';', $schema);
            foreach ($schema as $stmt)
            {
                if (!empty($stmt)) \Common\DB::query($stmt);
            }
            
            $incoming_key = \Common\Security::get_random(\Config\INCOMING_KEY_LENGTH);
            $account_controller = new Account();
            $account_id = $account->create($name, $email, $pass1, $incoming_key, 1);
            
            \Models\Setting::install();
            \Common\Session::login($account_id);
        }
        catch (\PDOException $e)
        {
            \Common\AJAX::error('ERROR: The database is not writable. Please check filesystem permissions.' . "\n\n" . $e->getMessage());
        }
        
        // Redirect to the welcome page.
        
        \Common\AJAX::redirect('index.php?action=welcome');
    }
    
    // Welcome screen with further instructions, displayed when installation is complete.
    
    public function install_welcome()
    {
        // The welcome screen contains sensitive information, so we require login.
        
        $user = $this->check_login();
        $alias = $user->get_default_alias();
        
        // We need to do some parsing to display easy-to-follow instructions.
        
        $view = new \Common\View('welcome');
        $view->title = 'Welcome to NearlyFreeMail';
        $view->user = $user;
        $view->email_local = substr($alias->email, 0, strrpos($alias->email, '@'));
        $view->email_domain = strtolower(substr($alias->email, strrpos($alias->email, '@') + 1));
        $view->render();
    }
}
