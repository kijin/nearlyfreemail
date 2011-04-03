<?php

namespace Controllers;

class Setting extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        \Common\AJAX::error('This feature has not been implemented yet.');
    }
}
