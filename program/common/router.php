<?php

namespace Common;

class Router
{
    // Shortcut definitions.
    
    protected static $_shortcuts = array('(alpha)', '(alnum)', '(num)', '(int)', '(hex)', '(any)');
    protected static $_regexes = array('([a-zA-Z]+)', '([a-zA-Z0-9]+)', '([0-9]+)', '([1-9][0-9]*)', '([a-fA-F0-9]+)', '([a-zA-Z0-9._-]+)');
    
    // The actual dispatcher.
    
    public static function dispatch($routes)
    {
        // Fetch some information about the current request.
        
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $url = substr($url, 0, (($pos = strpos($url, '?')) !== false) ? $pos : strlen($url));
        $url = substr($url, strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')));
        
        // Try to match routes to the current request.
        
        foreach ($routes as $def => $callback)
        {
            // Parse the route definition.
            
            if ($def[0] === '/')
            {
                $def_method = null;
                $def_host = null;
                $def_url = $def;
            }
            else
            {
                $first_slash = strpos($def, '/');
                if (!$first_slash) throw new RouterException('Invalid route: ' . $def);
                $prefixes = explode(' ', substr($def, 0, $first_slash));
                $def_method = (isset($prefixes[0]) && !empty($prefixes[0])) ? $prefixes[0] : null;
                $def_host = (isset($prefixes[1]) && !empty($prefixes[1])) ? $prefixes[1] : null;
                $def_url = substr($def, $first_slash);
            }
            
            // Try to match the request method, hostname, and the URI.
            
            if (!is_null($def_method) && $def_method !== $method) continue;
            if (!is_null($def_host) && $def_host !== $host) continue;
            if (!preg_match('#^' . str_replace(self::$_shortcuts, self::$_regexes, $def_url) . '$#', $url, $args)) continue;
            array_shift($args);
            
            // Parse the callback.
            
            if (is_string($callback) && ($arrow = strpos($callback, '->')) !== false)  // Instance method.
            {
                list($class_name, $method) = explode('->', $callback);
                return call_user_func_array(array(new $class_name, $method), $args);
            }
            elseif (is_callable($callback))  // Static method or closure.
            {
                return call_user_func_array($callback, $args);
            }
            elseif (is_string($callback) && $callback[0] === '@')  // String literal.
            {
                return substr($callback, 1);
            }
            else  // Everything else is invalid.
            {
                return false;
            }
        }
        
        // If we're here, it means we couldn't find a matching route.
        
        return false;
    }
    
    // URL constructor.
    
    public static function get_url( /* args */ )
    {
        static $base = false;
        if (!$base) $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $args = func_get_args();
        return str_replace('//', '/', $base . '/' . implode('/', $args));
    }
}

class RouterException extends Exception { }
