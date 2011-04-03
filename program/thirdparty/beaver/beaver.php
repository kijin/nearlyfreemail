<?php

/**
 * -----------------------------------------------------------------------------
 *  B E A V E R   :   Super lightweight object-to-database mapper for PHP 5.3 +
 * -----------------------------------------------------------------------------
 * 
 * @package    Beaver
 * @author     Kijin Sung <kijin.sung@gmail.com>
 * @copyright  (c) 2010-2011, Kijin Sung <kijin.sung@gmail.com>
 * @license    LGPL v3 <http://www.gnu.org/copyleft/lesser.html>
 * @link       http://github.com/kijin/beaver
 * @version    0.1.2
 * 
 * -----------------------------------------------------------------------------
 * 
 * Copyright (c) 2010-2011, Kijin Sung <kijin.sung@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * ----------------------------------------------------------------------------
 */

namespace Beaver;

// The base class. Extend this class for each object type you need.

class Base
{
    // The following properties should not be changed manually.
    
    protected static $_db = null;
    protected static $_db_is_pgsql = false;
    protected static $_cache = null;
    protected $_is_unsaved_object = false;
    
    // The following properties may be overridden by children.
    
    protected static $_table = null;
    protected static $_pk = 'id';
    
    // Call this method to inject a PDO object (or equivalent) to the ORM.
    
    public static function set_database($db)
    {
        self::$_db = $db;
        if ($db instanceof \PDO && $db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'pgsql')
        {
            self::$_db_is_pgsql = true;
        }
    }
    
    // Call this method to inject a Memcached object (or equivalent) to the ORM.
    
    public static function set_cache($cache)
    {
        self::$_cache = $cache;
    }
    
    // Constructor. The optional argument is set to false if it's called by get() or find().
    
    public function __construct($auto = true)
    {
        $this->_is_unsaved_object = (bool)$auto;
    }
    
    // Save any changes to this object.
    
    public function save($data = array())
    {
        // Make a list of fields and values to save.
        
        if (count($data))
        {
            $fields = array_keys($data);
            $values = array_values($data);
        }
        else
        {
            $fields = array();
            $values = array();
            foreach (get_object_vars($this) as $field => $value)
            {
                if ($field[0] === '_') continue;
                $fields[] = $field;
                $values[] = $value;
            }
        }
        
        // If inserting a new row.
        
        if ($this->_is_unsaved_object)
        {
            $query = 'INSERT INTO ' . static::$_table . ' (';
            $query .= implode(', ', $fields) . ') VALUES (';
            $query .= implode(', ', array_fill(0, count($values), '?'));
            $query .= ')';
            
            if (self::$_db_is_pgsql)
            {
                $query .= ' RETURNING ' . static::$_pk;
                $ps = self::$_db->prepare($query);
                $ps->execute($values);
                $this->{static::$_pk} = $ps->fetchColumn();
            }
            else
            {
                $ps = self::$_db->prepare($query);
                $ps->execute($values);
                $this->{static::$_pk} = self::$_db->lastInsertId();
            }
            
            $this->_is_unsaved_object = false;
        }
        
        // If updating an existing row.
        
        else
        {
            $query = 'UPDATE ' . static::$_table . ' SET ';
            $query .= implode(', ', array_map(function($str) { return $str . ' = ?'; }, $fields));
            $query .= ' WHERE ' . static::$_pk . ' = ?';
            $values[] = $this->{static::$_pk};
            
            $ps = self::$_db->prepare($query);
            $ps->execute($values);
            
            if (count($data))
            {
                foreach ($data as $field => $value)
                {
                    $this->$field = $value;
                }
            }
        }
    }
    
    // Delete method.
    
    public function delete()
    {
        $ps = self::$_db->prepare('DELETE FROM ' . static::$_table . ' WHERE ' . static::$_pk . ' = ?');
        $ps->execute(array($this->{static::$_pk}));
        $this->_is_unsaved_object = true;
    }
    
    // Get method.
    
    public static function get($id, $cache = false)
    {
        // Look up the cache.
        
        if ($cache && self::$_cache)
        {
            $cache_key = '_BEAVER::' . get_called_class() . ':' . (is_array($id) ? sha1(serialize($id)) : $id);
            $cache_result = self::$_cache->get($cache_key);
            if ($cache_result !== false) return $cache_result;
        }
        
        // If fetching a single object.
        
        if (!is_array($id))
        {
            $ps = self::$_db->prepare('SELECT * FROM ' . static::$_table . ' WHERE ' . static::$_pk . ' = ? LIMIT 1');
            $ps->execute(array($id));
            $object = $ps->fetchObject(get_called_class(), array(false));
            $result = $object ?: null;
        }
        
        // If fetching an array of objects.
        
        else
        {
            $query = 'SELECT * FROM ' . static::$_table . ' WHERE ' . static::$_pk . ' IN (';
            $query .= implode(', ', array_fill(0, count($id), '?')) . ')';
            $ps = self::$_db->prepare($query);
            $ps->execute($id);
            
            $result = array_combine($id, array_fill(0, count($id), null));  // Preserve order
            while ($object = $ps->fetchObject(get_called_class(), array(false)))
            {
                $result[$object->{static::$_pk}] = $object;
            }
        }
        
        // Store in cache.
        
        if ($cache && self::$_cache) self::$_cache->set($cache_key, $result, (int)$cache);
        return $result;
    }
    
    // Generic search method.
    
    public static function find($where, $params = array(), $cache = false)
    {
        // Look up the cache.
        
        if ($cache && self::$_cache)
        {
            $cache_key = '_BEAVER::' . get_called_class() . ':' . sha1($where . "\n" . serialize($id));
            $cache_result = self::$_cache->get($cache_key);
            if ($cache_result !== false) return $cache_result;
        }
        
        // Find some objects.
        
        $ps = self::$_db->prepare('SELECT * FROM ' . static::$_table . ' ' . $where);
        $ps->execute((array)$params);
        
        $result = array();
        while ($object = $ps->fetchObject(get_called_class(), array(false)))
        {
            $result[] = $object;
        }
        return $result;
        
        // Store in cache.
        
        if ($cache && self::$_cache) self::$_cache->set($cache_key, $result, (int)$cache);
        return $result;
    }
    
    // Various search methods.
    
    public static function __callStatic($name, $args)
    {
        // Check method name.
        
        $name = strtolower($name);
        if (strncmp($name, 'find_by_', 8))
        {
            throw new BadMethodCallException('Static method not found: ' . $name);
        }
        
        $field = substr($name, 8);
        if (!$field || !property_exists(get_called_class(), $field) || $field[0] === '_')
        {
            throw new BadMethodCallException('Property not found: ' . $field);
        }
        
        // Check arguments.
        
        if (!count($args)) throw new BadMethodCallException('Missing arguments');
        $value = $args[0];
        if (isset($args[1]))
        {
            $order_field = $args[1];
            if (strlen($order_field) && in_array($order_field[strlen($order_field) - 1], array('+', '-')))
            {
                $order_sign = ($order_field[strlen($order_field) - 1] === '+') ? 'ASC' : 'DESC';
                $order_field = substr($order_field, 0, strlen($order_field) - 1);
            }
            if (!$order_field || !property_exists(get_called_class(), $order_field) || $order_field[0] === '_')
            {
                throw new BadMethodCallException('Property not found: ' . $order_field);
            }
        }
        
        // Find all matching objects.
        
        $query = 'SELECT * FROM ' . static::$_table . ' WHERE ' . $field . ' = ?';
        if (isset($order_field))
        {
            $query .= ' ORDER BY ' . $order_field;
            if (isset($order_sign)) $query .= ' ' . $order_sign;
        }
        
        $ps = self::$_db->prepare($query);
        $ps->execute(array($value));
        
        $return = array();
        while ($object = $ps->fetchObject(get_called_class(), array(false)))
        {
            $return[] = $object;
        }
        return $return;
    }
}

// Exceptions.

class Exception extends \Exception { }
class BadMethodCallException extends \BadMethodCallException { }
