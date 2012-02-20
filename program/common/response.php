<?php

namespace Common;

class Response
{
    // Send a page.
    
    public static function send_page($content, $content_type = 'text/html', $expires = false)
    {
        // Set the content type. Encoding is always UTF-8 if you use this library.
        
        header('Content-Type: ' . $content_type . '; charset=UTF-8');
        
        // Set the cache-control headers.
        
        if ($expires === 0)
        {
            header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
            header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
            header('Pragma: no-cache');
        }
        elseif ($expires > 0)
        {
            header('Cache-Control: max-age=' . (int)$expires);
            header('Expires: ' . date('D, d M Y H:i:s', time() + (int)$expires) . ' GMT');
        }
        
        // Print the content and exit.
        
        echo $content;
        exit;
    }
    
    // Send a file.
    
    public static function send_file($filename, $name = false)
    {
        // If name is empty, infer from the filename.
        
        if ($name === false) $name = basename($filename);
        
        // Stupid IE doesn't follow RFC2231, so urlencode the filename.
        
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
        {
            $name = rawurlencode($name);
        }
        
        // Check that the file exists.
        
        $handle = fopen($filename, 'rb');
        if ($handle === false) return false;
        
        // Clear all output buffers. We don't want to corrupt the file.
        
        while (ob_get_level()) ob_end_clean();
        
        // Send some headers.
        
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));
        
        // Read the file in 64KB chunks. No, we can't use readfile() on large files because PHP is stupid.
        
        $buffer = '';
        while (!feof($handle))
        {
            $buffer = fread($handle, 64 * 1024);
            echo $buffer;
            flush();
        }
        fclose($handle);
        exit;
    }
    
    // Send a JSON object.
    
    public static function send_json($object)
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
        header('Pragma: no-cache');
        echo json_encode($object);
        exit;
    }
    
    // Redirect.
    
    public static function redirect($location, $permanent = false)
    {
        $http_code = $permanent ? '301 Moved Permanently' : '302 Found';
        preg_replace('/([\\x00-\\x1f\\xff]+)/', '', $location);
        header('HTTP/1.0 ' . $http_code);
        header('Location: ' . $location);
        exit;
    }
    
    // 404 error.
    
    public static function not_found($message = false)
    {
        header('HTTP/1.0 404 Not Found');
        if ($message !== false)
        {
            echo $message;
        }
        elseif (self::$_not_found_default_message)
        {
            echo self::$_not_found_default_message;
        }
        elseif (self::$_not_found_default_callback)
        {
            call_user_func(self::$_not_found_default_callback);
        }
        else
        {
            echo "404 Not Found\n";
        }
        exit;
    }
    
    // Set the default message/callback for 404 errors.
    
    public static function not_found_set_default_message($message)
    {
        self::$_not_found_default_message = $message;
    }
    
    public static function not_found_set_default_callback($callback)
    {
        self::$_not_found_default_callback = $callback;
    }
    
    protected static $_not_found_default_message;
    protected static $_not_found_default_callback;
    
    // Send a HTTP status code.
    
    public static function send_http_status_code($code)
    {
        static $codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            444 => 'No Response',
            449 => 'Retry With',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
        );
        $message = array_key_exists($code, $codes) ? $codes[$code] : '';
        header('HTTP/1.0 ' . (int)$code . ' ' . $message);
    }
}