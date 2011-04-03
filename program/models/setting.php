<?php

namespace Models;

class Setting
{
    // Installed?
    
    public static function is_installed()
    {
        try
        {
            $query = \Common\DB::query('SELECT s_value FROM settings WHERE s_key = ?', 'installed_version');
            return true;
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }
    
    // Install!
    
    public static function install()
    {
        \Common\DB::query('INSERT INTO settings (s_key, s_value) VALUES (?, ?)', 'installed_version', VERSION);
    }
    
    // Get all settings for an account.
    
    public static function get_settings($account_id)
    {
        $query = \Common\DB::query('SELECT s_key, s_value FROM settings WHERE account_id = ?', $account_id);
        $return = array();
        while ($row = $query->fetch(\PDO::FETCH_ASSOC))
        {
            $return[$row['s_key']] = $row['s_value'];
        }
        return $return;
    }
    
    // Add a new setting.
    
    public static function add_setting($account_id, $key, $value)
    {
        \Common\DB::query('INSERT INTO settings (account_id, s_key, s_value) VALUES (?, ?, ?)', $account_id, $key, $value);
    }
    
    // Change an existing setting.
    
    public static function change_setting($account_id, $key, $new_value)
    {
        \Common\DB::query('UPDATE settings SET s_value = ? WHERE account_id = ? AND s_key = ?', $new_value, $account_id, $key);
    }
    
    // Delete a setting.
    
    public static function delete_setting($account_id, $key)
    {
        \Common\DB::query('DELETE FROM settings WHERE account_id = ? AND s_key = ?', $account_id, $key);
    }
}
