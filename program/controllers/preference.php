<?php

namespace Controllers;

class Preference extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the preferences page.
    
    public function show()
    {
        $view = new \Common\View('preferences');
        $view->title = 'Preferences';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->render();
    }
    
    // Save preferences.
    
    public function save()
    {
        // Get user input.
        
        $pass = \Common\Request::post('pass');
        $newpass1 = \Common\Request::post('newpass1');
        $newpass2 = \Common\Request::post('newpass2');
        $messages_per_page = \Common\Request::post('messages_per_page');
        $show_sidebar_contacts = \Common\Request::post('show_sidebar_contacts');
        $show_compose_contacts = \Common\Request::post('show_compose_contacts');
        $content_display_font = \Common\Request::post('content_display_font');
        $spam_threshold = \Common\Request::post('spam_threshold', 'float');
        $timezone = \Common\Request::post('timezone');
        $csrf_token = \Common\Request::post('csrf_token');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
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
        
        if (!\Common\Security::validate($show_sidebar_contacts, 'int,min=0,max=20'))
        {
            \Common\AJAX::error('Number of displayed contacts should be between 0 and 20.');
        }
        
        if (!\Common\Security::validate($show_compose_contacts, 'int,min=0,max=20'))
        {
            \Common\AJAX::error('Number of displayed contacts should be between 0 and 20.');
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
        
        if ($newpass1 !== '')
        {
            $this->user->change_passphrase($newpass1);
        }
        
        $this->user->set_setting('content_display_font', $content_display_font);
        $this->user->set_setting('messages_per_page', (int)$messages_per_page);
        $this->user->set_setting('show_sidebar_contacts', (int)$show_sidebar_contacts);
        $this->user->set_setting('show_compose_contacts', (int)$show_compose_contacts);
        $this->user->set_setting('spam_threshold', (float)$spam_threshold);
        $this->user->set_setting('timezone', $timezone);
        
        \Common\DB::commit();
        
        // Redirect to the same page.
        
        \Common\AJAX::redirect(\Common\Router::get_url('/settings'));
    }
}
