<?php

namespace Models;

class Install
{
    // Any error messages are stored here.
    
    protected static $_last_error = '';
    
    // Installed?
    
    public static function is_installed()
    {
        try
        {
            $query = \Common\DB::query('SELECT s_value FROM settings WHERE s_key = ?', 'installed_version');
            $version = $query->fetchColumn();
            if ($version != VERSION) self::upgrade($version);
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
        $schema = file_get_contents(BASEDIR . '/program/schemata/schema.sql');
        $schema = explode(';', $schema);
        try
        {
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
    
    // Upgrade the database to the current version.
    
    public static function upgrade($from_version)
    {
        if (!preg_match('/^\\d+\\.\\d+/', $from_version, $matches))
        {
            self::$_last_error = 'Invalid version: ' . $from_version;
            return false;
        }
        
        $filename = BASEDIR . '/program/schemata/upgrade.from.' . $matches[0] . '.sql';
        if (!file_exists($filename))
        {
            self::$_last_error = 'Cannot find upgrade path from ' . $matches[0];
            return false;
        }
        
        $schema = file_get_contents($filename);
        $schema = explode(';', $schema);
        try
        {
            foreach ($schema as $stmt)
            {
                $stmt = trim($stmt);
                if (!empty($stmt)) \Common\DB::query($stmt);
            }
            \Common\DB::query('UPDATE settings SET s_value = ? WHERE s_key = ?', VERSION, 'installed_version');
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
            return true;
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
