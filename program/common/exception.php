<?php

namespace Common;

// The base exception class.

class Exception extends \Exception
{
    // Once you call this method, all PHP errors become exceptions.
    
    public static function replace_errors()
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline)
        {
            switch ($errno)
            {
                case E_ERROR:
                    throw new E_ERROR_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_WARNING:
                    throw new E_WARNING_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_NOTICE:
                    throw new E_NOTICE_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_DEPRECATED:
                    throw new E_DEPRECATED_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_USER_ERROR:
                    throw new E_USER_ERROR_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_USER_WARNING:
                    throw new E_USER_WARNING_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_USER_NOTICE:
                    throw new E_USER_NOTICE_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_USER_DEPRECATED:
                    throw new E_USER_DEPRECATED_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_RECOVERABLE_ERROR:
                    throw new E_RECOVERABLE_Exception($errstr, 0, $errno, $errfile, $errline);
                case E_STRICT:
                    throw new E_STRICT_Exception($errstr, 0, $errno, $errfile, $errline);
                default:
                    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        }, -1);
    }
}

// Error replacement exception classes.

class ErrorException extends \ErrorException { }
class E_ERROR_Exception extends ErrorException { }
class E_WARNING_Exception extends ErrorException { }
class E_NOTICE_Exception extends ErrorException { }
class E_DEPRECATED_Exception extends ErrorException { }
class E_USER_ERROR_Exception extends E_ERROR_Exception { }
class E_USER_WARNING_Exception extends E_WARNING_Exception { }
class E_USER_NOTICE_Exception extends E_NOTICE_Exception { }
class E_USER_DEPRECATED_Exception extends E_DEPRECATED_Exception { }
class E_RECOVERABLE_Exception extends ErrorException { }
class E_STRICT_Exception extends ErrorException { }
