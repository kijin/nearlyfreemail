<?php

namespace Models;

class Account extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'accounts';
    public $id;
    public $default_alias;
    public $password;
    public $created_time;
    public $is_admin;
    
    // Settings cache. This prevents extra queries to the DB.
    
    protected $_settings = false;
    
    // Get all aliases.
    
    public function get_aliases()
    {
        return Alias::find_by_account_id($this->id, 'name+');
    }
    
    // Get the default alias.
    
    public function get_default_alias()
    {
        static $alias = null;
        if (!$alias) $alias = Alias::get($this->default_alias);
        return $alias;
    }
    
    // Set the default alias.
    
    public function set_default_alias($alias_id)
    {
        $this->save(array('default_alias' => $alias_id));
    }
    
    // Get a setting.
    
    public function get_setting($key)
    {
        if ($this->_settings === false) $this->_settings = Setting::get_settings($this->id);
        if (array_key_exists($key, $this->_settings))
        {
            return $this->_settings[$key];
        }
        elseif (property_exists('\\Config\\Defaults', $key))
        {
            $value = \Config\Defaults::${$key};
            Setting::add_setting($this->id, $key, $value);
            return $value;
        }
        else
        {
            return null;
        }
    }
    
    // Set a setting.
    
    public function set_setting($key, $value)
    {
        if ($this->_settings === false) $this->_settings = Setting::get_settings($this->id);
        if (array_key_exists($key, $this->_settings))
        {
            if (is_null($value))
            {
                Setting::delete_setting($this->id, $key);
                unset($this->_settings[$key]);
            }
            else
            {
                Setting::change_setting($this->id, $key, $value);
                $this->_settings[$key] = $value;
            }
        }
        else
        {
            Setting::add_setting($this->id, $key, $value);
            $this->_settings[$key] = $value;
        }
    }
    
    // Check passphrase.
    
    public function check_passphrase($passphrase)
    {
        load_third_party('phpass');
        $phpass = new \PasswordHash(8, false);
        if ($phpass->CheckPassword(hash('sha512', $passphrase), $this->password)) return true;
        if ($phpass->CheckPassword($passphrase, $this->password)) return true;
        return false;
    }
    
    // Change passphrase.
    
    public function change_passphrase($passphrase)
    {
        load_third_party('phpass');
        $phpass = new \PasswordHash(8, false);
        $hash = $phpass->HashPassword(hash('sha512', $passphrase));
        unset($phpass);
        $this->save(array('password' => $hash));
    }
}
