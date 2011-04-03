<?php

namespace Common;

class Session
{
    public static function start($name)
    {
        session_name($name);
        session_start();
    }
    
    public static function refresh()
    {
        session_regenerate_id();
    }
    
    public static function login($id)
    {
        $_SESSION['login'] = $id;
        $_SESSION['logout_token'] = Security::get_random(32);
        session_regenerate_id();
    }
    
    public static function logout()
    {
        session_destroy();
    }
    
    public static function get_login_id()
    {
        return isset($_SESSION['login']) ? $_SESSION['login'] : null;
    }
    
    public static function get_logout_token()
    {
        return isset($_SESSION['logout_token']) ? $_SESSION['logout_token'] : '';
    }
    
    public static function add_token($token)
    {
        isset($_SESSION['tokens']) or $_SESSION['tokens'] = array();
        $_SESSION['tokens'][] = $token;
    }
    
    public static function check_token($token)
    {
        isset($_SESSION['tokens']) or $_SESSION['tokens'] = array();
        return in_array($token, $_SESSION['tokens']);
    }
    
    public static function clear_tokens()
    {
        $_SESSION['tokens'] = array();
    }
}
