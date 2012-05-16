<?php

namespace Models;

class Contact extends \Beaver\Base
{
    // ORM properties.
    
    protected static $_table = 'contacts';
    public $id;
    public $account_id;
    public $name;
    public $email;
    public $notes;
    public $last_used;
    
    // Get recently used contacts.
    
    public static function get_recent($account_id, $count)
    {
        return self::select('WHERE account_id = ? ORDER BY last_used DESC LIMIT ' . (int)$count, array($account_id));
    }
    
    // Update the last-used timestamp of a set of contacts.
    
    public static function update_last_used_timestamp($emails)
    {
        $placeholders = implode(', ', array_fill(0, count($emails), '?'));
        array_unshift($emails, time());
        return \Common\DB::query('UPDATE contacts SET last_used = ? WHERE email IN (' . $placeholders . ')', $emails);
    }
    
    // A method to parse name & e-mail pairs.
    
    public static function extract($input)
    {
        // Use the MIME parser library to separate the addresses.
        
        load_third_party('mimeparser');
        $parser = new \RFC822_Addresses_Class();
        $parser->ParseAddressList($input, $parsed);
        $return = array();
        
        // Format into an array of objects.
        
        foreach ($parsed as $address)
        {
            $obj = new Contact();
            $obj->name = isset($address['name']) ? $address['name'] : '';
            $obj->email = isset($address['address']) ? $address['address'] : '';
            $return[] = $obj;
        }
        return $return;
    }
    
    // A method to parse name & e-mail pairs, and make HTML links out of them.
    
    public static function extract_and_link($input, $html)
    {
        $addresses = self::extract($input);
        $count = count($addresses);
        $return = '';
        for ($i = 0; $i < $count; $i++)
        {
            $return .= str_replace('ADDRESS', htmlspecialchars($addresses[$i]->get_profile(), ENT_COMPAT, 'UTF-8', true), $html);
            if ($i < $count - 1) $return .= ', ';
        }
        return $return;
    }
    
    // Get a profile text for this contact.
    
    public function get_profile()
    {
        if ($this->name === '') return $this->email;
        
        $need_quote = false;
        foreach (array(',', ';', ':', '.', '@', '\\', '(', ')', '<', '>', '[', ']') as $char)
        {
            if (strpos($this->name, $char) !== false) $need_quote = true;
        }
        
        if ($need_quote)
        {
            return '"' . str_replace(array('\\', '"'), array('\\\\', '\\"'), $this->name) . '" <' . $this->email . '>';
        }
        else
        {
            return $this->name . ' <' . $this->email . '>';
        }
    }
    
    public function __toString()
    {
        return $this->get_profile();
    }
}
