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
        $view->render();
    }
}
