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
        $view->user = $this->user;
        $view->render();
    }
}
