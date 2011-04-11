<?php

namespace Controllers;

class Setting extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        $view = new \Common\View('settings');
        $view->title = 'Account Settings';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->render();
    }
    
    // Save settings.
    
    public function save()
    {
        // Get user input.
        
        $email = \Common\Request::post('email');
        $name = \Common\Request::post('name');
        $pass = \Common\Request::post('pass');
        $newpass1 = \Common\Request::post('newpass1');
        $newpass2 = \Common\Request::post('newpass2');
        $signature = \Common\Request::post('signature');
        $messages_per_page = \Common\Request::post('messages_per_page');
        $show_recent_contacts = \Common\Request::post('show_recent_contacts');
        $content_display_font = \Common\Request::post('content_display_font');
        $spam_threshold = \Common\Request::post('spam_threshold', 'float');
        $timezone = \Common\Request::post('timezone');
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
            \Common\AJAX::error('Please enter your name. It can be between 1 and 60 characters.');
        }
        
        if ($pass === '' && $newpass1 !== '')
        {
            \Common\AJAX::error('Please enter your CURRENT PASSPHRASE if you\'d like to change your password.');
        }
        
        if ($pass !== '' && $newpass1 !== '')
        {
            if (!$this->user->check_passphrase($pass))
            {
                \Common\AJAX::error('Please enter your current passphrase correctly.');
            }
            unset($phpass);
        }
        
        if ($newpass1 !== $newpass2)
        {
            \Common\AJAX::error('Please enter the same new passphrase twice.');
        }
        
        if (!\Common\Security::validate($messages_per_page, 'int,min=5,max=50'))
        {
            \Common\AJAX::error('Messages per page should be between 5 and 50.');
        }
        
        if (!\Common\Security::validate($show_recent_contacts, 'int,min=0,max=20'))
        {
            \Common\AJAX::error('Number of recent contacts should be between 0 and 20.');
        }
        
        if (!in_array($content_display_font, array('serif', 'sans-serif', 'monospace')))
        {
            \Common\AJAX::error('Please select a valid display font for message content.');
        }
        
        if ($spam_threshold <= 0.5 || $spam_threshold > 10)
        {
            \Common\AJAX::error('Please select a valid spam filtering type.');
        }
        
        if (!in_array($timezone, timezone_identifiers_list()))
        {
            \Common\AJAX::error('Timezone \'' . $timezone . '\' is not valid.');
        }
        
        // Save settings.
        
        \Common\DB::begin_transaction();
        
        $alias = $this->user->get_default_alias();
        if ($name !== $alias->name || $email !== $alias->email)
        {
            $alias->save(array('name' => $name, 'email' => $email));
        }
        if ($signature !== $alias->signature)
        {
            $alias->save(array('signature' => $signature));
        }
        
        if ($newpass1 !== '')
        {
            $this->user->change_passphrase($newpass1);
        }
        
        $this->user->set_setting('content_display_font', $content_display_font);
        $this->user->set_setting('messages_per_page', (int)$messages_per_page);
        $this->user->set_setting('show_recent_contacts', (int)$show_recent_contacts);
        $this->user->set_setting('spam_threshold', (float)$spam_threshold);
        $this->user->set_setting('timezone', $timezone);
        
        \Common\DB::commit();
        
        // Redirect to the same page.
        
        \Common\AJAX::redirect(\Common\Router::get_url('/settings'));
    }
}
