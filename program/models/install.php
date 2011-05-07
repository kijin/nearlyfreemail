<?php

namespace Models;

class Install
{
    // Any error messages are stored here.
    
    protected $_last_error = '';
    
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
            self::$_last_error = $e->getMessage();
            return false;
        }
    }
    
    // Create tables.
    
    public static function create_tables()
    {
        try
        {
            $schema = file_get_contents(BASEDIR . '/program/bootstrap/schema.sql');
            $schema = explode(';', $schema);
            foreach ($schema as $stmt)
            {
                $stmt = trim($stmt);
                if (!empty($stmt)) \Common\DB::query($stmt);
            }
            return true;
        }
        catch (\PDOException $e)
        {
            self::$_last_error = $e->getMessage();
            return false;
        }
    }
    
    // Mark as Installed.
    
    public static function mark_as_installed()
    {
        try
        {
            \Common\DB::query('INSERT INTO settings (s_key, s_value) VALUES (?, ?)', 'installed_version', VERSION);
        }
        catch (\PDOException $e)
        {
            self::$_last_error = $e->getMessage();
            return false;
        }
    }
    
    // Get the last error message.
    
    public static function get_last_error()
    {
        return self::$_last_error;
    }
}
