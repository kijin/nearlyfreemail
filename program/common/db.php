<?php

namespace Common;

class DB
{
    // Some static properties.
    
    protected function __construct() { }
    protected static $_dbh = null;
    protected static $_in_transaction = false;
    protected static $_nested_transaction_sequence = 1;
    protected static $_nested_transaction_memory = array();
    
    // Initialize the database connection, and check if tables exist.
    
    public static function initialize($filename_or_pdo)
    {
        if ($filename_or_pdo instanceof \PDO)  // Used for dependency injection.
        {
            self::$_dbh = $filename_or_pdo;
        }
        else  // Default is to open an SQLite database.
        {
            self::$_dbh = new \PDO('sqlite:' . $filename_or_pdo);
        }
        self::$_dbh->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        self::$_dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    // Prepare.
    
    public static function prepare($querystring)
    {
        return self::$_dbh->prepare($querystring);
    }
    
    // Query.
    
    public static function query($querystring /* optional parameters */ )
    {
        $params = func_get_args(); array_shift($params);
        if (count($params) == 1 && is_array($params[0])) $params = $params[0];
        
        if (count($params))
        {
            $statement = self::$_dbh->prepare($querystring);
            $statement->execute($params);
        }
        else
        {
            $statement = self::$_dbh->query($querystring);
        }
        
        return $statement;
    }
    
    // Get direct access to the PDO object.
    
    public static function get_pdo()
    {
        return self::$_dbh;
    }
    
    // Get the last insert ID.
    
    public static function get_last_insert_id()
    {
        return self::$_dbh->lastInsertId();
    }
    
    // Normal transactions.
    
    public static function begin_transaction()
    {
        $status = self::$_dbh->beginTransaction();
        self::$_in_transaction = true;
        return $status;
    }
    
    public static function commit()
    {
        $status = self::$_dbh->commit();
        self::$_in_transaction = false;
        return $status;
    }
    
    public static function rollback()
    {
        $status = self::$_dbh->rollBack();
        self::$_in_transaction = false;
        return $status;
    }
    
    // Fake nested transactions.
    
    public static function try_begin_transaction()
    {
        $transid = self::$_nested_transaction_sequence++;
        if (self::$_in_transaction)
        {
            self::$_nested_transaction_memory[$transid] = false;
        }
        else
        {
            self::begin_transaction();
            self::$_nested_transaction_memory[$transid] = true;
        }
        return $transid;
    }
    
    public static function try_commit($transid)
    {
        if (self::$_nested_transaction_memory[$transid])
        {
            $status = self::commit();
        }
        else
        {
            $status = false;
        }
        return $status;
    }
}
