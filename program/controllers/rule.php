<?php

namespace Controllers;

class Rule extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        $view = new \Common\View('rules');
        $view->title = 'Rules';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->render();
    }
}
