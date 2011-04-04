<?php

// The function to call if NearlyFreeMail is not installed yet.

$install = function()
{
    $controller = new \Controllers\Install();
    $controller->install_form();
};

// The function to call when an incoming message is detected.

$incoming = function($key)
{
    $controller = new \Controllers\Incoming();
    $controller->receive($key);
};

// The function to call if index.php is called without any arguments.

$default = function()
{
    $user = \Models\Account::get(\Common\Session::get_login_id());
    $user and \Common\Response::redirect('index.php?action=inbox');
    $controller = new \Controllers\Account();
    $controller->login_form();
};

// Other routes.

$routes = array(

    'GET.welcome' => 'Account.install_welcome',
    'GET.login'   => 'Account.login_form',
    'POST.login'  => 'Account.login_post',
    'POST.logout' => 'Account.logout',
    
    'GET.inbox'              => 'Mailbox.inbox',
    'GET.list'               => 'Mailbox.show',
    'GET.search'             => 'Mailbox.search',
    'POST.mailbox_do_action' => 'Mailbox.do_action',
    
    'GET.read'                    => 'Message.read',
    'GET.message_change_encoding' => 'Message.change_encoding',
    'POST.message_do_action'      => 'Message.do_action',
    'GET.download_attachment'     => 'Message.download_attachment',
    'GET.download_source'         => 'Message.download_source',
    
    'GET.compose'  => 'Compose.create',
    'POST.compose' => 'Compose.save',
    'GET.edit'     => 'Compose.edit',
    
    'GET.folders'  => 'Folder.show',
    'GET.contacts' => 'Contact.show',
    'GET.settings' => 'Setting.show',
    'POST.settings' => 'Setting.save',
);
