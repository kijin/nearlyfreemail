<?php

namespace Models;

class Rule extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'rules';
    public $id;
    public $account_id;
    public $priority;
    public $rule;
    public $action;
}
