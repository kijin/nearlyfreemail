<?php

namespace Common;

class Request
{
    // GET wrapper.
    
    public static function get($name, $filter = '')
    {
        if (!isset($_GET[$name])) return null;
        $value = get_magic_quotes_gpc() ? stripcslashes($_GET[$name]) : $_GET[$name];
        return $filter ? Security::filter($value, $filter) : $value;
    }
    
    // POST wrapper.
    
    public static function post($name, $filter = '')
    {
        if (!isset($_POST[$name])) return null;
        $value = get_magic_quotes_gpc() ? stripcslashes($_POST[$name]) : $_POST[$name];
        return $filter ? Security::filter($value, $filter) : $value;
    }
    
    // Fetch some information about the request.
    
    public static function info($type)
    {
        switch ($type)
        {
            case 'http_version':
                return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 0;
                
            case 'protocol':
                return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                
            case 'method':
                return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
                
            case 'domain':
            case 'host':
                return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
                
            case 'uri':
            case 'url':
                return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
                
            case 'time':
                return isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
                
            case 'ip':
                return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
                
            case 'user_agent':
                return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
                
            case 'referer':
                return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
                
            case 'ajax':
                return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false;
                
            case 'keepalive':
                if (isset($_SERVER['HTTP_CONNECTION']) && strtolower($_SERVER['HTTP_CONNECTION']) === 'keep-alive')
                {
                    return isset($_SERVER['HTTP_KEEP_ALIVE']) ? intval($_SERVER['HTTP_KEEP_ALIVE']) : true;
                }
                else
                {
                    return false;
                }
            
            case 'old_browser':
                if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
                return preg_match('/MSIE ([1-7])\\./', $_SERVER['HTTP_USER_AGENT'], $matches) ? $matches[1] : false;
        }
    }
}
