<?php

namespace Controllers;

class Base
{
    // If this flag is set to TRUE, all calls to this class will require login.
    
    protected $require_login = false;
    protected $user = null;
    
    public function __construct()
    {
        if ($this->require_login) $this->check_login();
    }
    
    // Check if the user is logged in, and display the login screen if not.
    
    protected function check_login()
    {
        $id = \Common\Session::get_login_id();
        if (!$id)
        {
            if (\Common\Request::info('ajax'))
            {
                \Common\AJAX::error('You are not logged in. Please log in again.');
            }
            else
            {
                $controller = new Account();
                $controller->login_form(); exit;
            }
        }
        $this->user = \Models\Account::get($id);
        date_default_timezone_set($this->user->get_setting('timezone'));
        return $this->user;
    }
    
    // Check if the user is an administrator, and display an error if not.
    
    protected function check_admin()
    {
        $admin = $this->user && $this->user->is_admin;
        if (!$admin)
        {
            \Common\AJAX::error('This feature is only available to administrators.');
        }
    }
}