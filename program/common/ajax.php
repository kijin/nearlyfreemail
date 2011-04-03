<?php

namespace Common;

class AJAX
{
    // Send some text or HTML content back to the client.
    
    public static function content($content)
    {
        if (Request::info('ajax'))
        {
            Response::send_json(array(
                'status' => 'CONTENT',
                'content' => $content,
            ));
        }
        else
        {
            Response::send_page($content, 'text/plain', 0);
        }
    }
    
    // Redirect the client to another location.
    
    public static function redirect($location)
    {
        if (Request::info('ajax'))
        {
            Response::send_json(array(
                'status' => 'REDIRECT',
                'location' => $location,
            ));
        }
        else
        {
            Response::redirect($location);
        }
    }
    
    // Notify the client of an error.
    
    public static function error($message)
    {
        if (Request::info('ajax'))
        {
            Response::send_json(array(
                'status' => 'ERROR',
                'message' => $message,
            ));
        }
        else
        {
            $view = new \Common\View('error');
            $view->title = 'Error';
            $view->message = $message;
            $view->render();
        }
    }
}