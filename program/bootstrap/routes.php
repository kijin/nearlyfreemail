<?php

// The function to call if NearlyFreeMail is not installed yet.

$install = function()
{
    $controller = new \Controllers\Install();
    $controller->install_form();
};

// The function to call if index.php is called without any arguments.

$default = function()
{
    $user = \Models\Account::get(\Common\Session::get_login_id());
    $user and \Common\AJAX::redirect(\Common\Router::get_url('/mail'));
    $controller = new \Controllers\Account();
    $controller->login_form();
};

// Other routes.

$routes = array(
    
    'GET  /' => $default,
    
    'POST /([0-9a-f]{12,})' => '\\Controllers\\Incoming->receive',
    
    'GET  /account/welcome' => '\\Controllers\\Install->install_welcome',
    'GET  /account/login'   => '\\Controllers\\Account->login_form',
    'POST /account/login'   => '\\Controllers\\Account->login_post',
    'POST /account/logout'  => '\\Controllers\\Account->logout',
    
    'GET  /mail'             => '\\Controllers\\Mailbox->inbox',
    'GET  /mail/list/(any)'  => '\\Controllers\\Mailbox->show',
    'POST /mail/list/action' => '\\Controllers\\Mailbox->do_action',
    'GET  /mail/search'      => '\\Controllers\\Mailbox->search',
    
    'GET  /mail/read/(int)'                     => '\\Controllers\\Message->read',
    'POST /mail/read/(int)/encoding'            => '\\Controllers\\Message->change_encoding',
    'POST /mail/read/action/(int)'              => '\\Controllers\\Message->do_action',
    'GET  /mail/attachment/(int)/(int)/([^/]+)' => '\\Controllers\\Message->download_attachment',
    'GET  /mail/source/(int)\\.eml'             => '\\Controllers\\Message->download_source',
    
    'GET  /mail/compose'    => '\\Controllers\\Compose->create',
    'POST /mail/compose'    => '\\Controllers\\Compose->save',
    'GET  /mail/edit/(int)' => '\\Controllers\\Compose->edit',
    
    'GET  /settings'                     => '\\Controllers\\Setting->show',
    'POST /settings/account'             => '\\Controllers\\Setting->save',
    'GET  /settings/aliases'             => '\\Controllers\\Alias->show',
    'GET  /settings/contacts'            => '\\Controllers\\Contact->show',
    'POST /settings/contacts/add'        => '\\Controllers\\Contact->add',
    'GET  /settings/contacts/edit/(int)' => '\\Controllers\\Contact->edit_form',
    'POST /settings/contacts/edit'       => '\\Controllers\\Contact->edit_post',
    'POST /settings/contacts/action'     => '\\Controllers\\Contact->do_action',
    'GET  /settings/folders'             => '\\Controllers\\Folder->show',
    'POST /settings/folders/add'         => '\\Controllers\\Folder->add',
    'GET  /settings/folders/edit/(int)'  => '\\Controllers\\Folder->edit_form',
    'POST /settings/folders/edit'        => '\\Controllers\\Folder->edit_post',
    'POST /settings/folders/action'      => '\\Controllers\\Folder->do_action',
    'GET  /settings/rules'               => '\\Controllers\\Rule->show',
    
);
