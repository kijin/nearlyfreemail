<?php

namespace Models;

class Alias extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'aliases';
    public $id;
    public $account_id;
    public $name;
    public $email;
    public $incoming_key;
    public $signature;
    public $created_time;
    public $notes;
    
    // Get the incoming URL.
    
    public function get_incoming_url()
    {
        $base = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($base, '/') . '/' . $this->incoming_key;
    }
    
    // Get the account which owns this alias.
    
    public function get_account()
    {
        return Account::get($this->account_id);
    }
    
    // Get a profile string.
    
    public function get_profile()
    {
        $contact = new Contact();
        $contact->name = $this->name;
        $contact->email = $this->email;
        return $contact->get_profile();
    }
    
    // Get a note item.
    
    public function get_note($key)
    {
        if (!$this->notes) return null;
        $decoded = json_decode($this->notes, true);
        if (is_array($decoded) && array_key_exists($key, $decoded))
        {
            return $decoded[$key];
        }
        else
        {
            return null;
        }
    }
    
    // Set a note item.
    
    public function set_note($key, $value)
    {
        if ($this->notes)
        {
            $decoded = json_decode($this->notes, true);
        }
        else
        {
            $decoded = array();
        }
        $decoded[$key] = $value;
        $this->notes = json_encode($decoded);
    }
    
    // Is this the default alias for the account that owns it?
    
    public function is_default()
    {
        $account = Account::get($this->account_id);
        return ($this->id == $account->id);
    }
}
