<?php

namespace Common;

class View
{
    // Some instance variables.
    
    protected static $_dir = null;
    protected $_filename = null;
    protected $_vars = array();
    
    // Set the directory for template files.
    
    public static function set_dir($dir)
    {
        self::$_dir = rtrim($dir, '/');
    }
    
    // Constructor.
    
    public function __construct($name)
    {
        // Check if the view file exists.
        
        $this->_filename = self::$_dir . '/' . $name . '.php';
        if (!file_exists($this->_filename)) throw new ViewException("View '{$name}' does not exist.");
    }
    
    // Generic getter method.
    
    public function __get($name)
    {
        if (!isset($this->_vars[$name])) throw new ViewException("Undefined property: '{$name}'");
        return $this->_vars[$name];
    }
    
    // Generic setter method.
    
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }
    
    // Render method.
    
    public function render($content_type = 'text/html')
    {
        // If returning, render the view in the output buffer.
        
        if ($content_type === false)
        {
            ob_start();
            extract($this->_vars);
            include $this->_filename;
            echo "\n";
            return ob_get_clean();
        }
        
        // Set some headers. Sorry, these values are hard-coded into the library.
        
        header('Content-Type: ' . $content_type . '; charset=UTF-8');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
        header('Pragma: no-cache');
        
        // Render the view and exit.
        
        extract($this->_vars);
        include $this->_filename;
        echo "\n";
        exit;
    }
    
    // Return the rendered content if cast to string.
    
    public function __toString()
    {
        return $this->render(false);
    }
}

class ViewException extends Exception { }
