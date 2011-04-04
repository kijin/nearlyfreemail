<?php

namespace Controllers;

class Contact extends Base
{
    // All calls to this controller require login.
    
    protected $require_login = true;
    
    // Show the control panel.
    
    public function show()
    {
        $view = new \Common\View('contacts');
        $view->title = 'Contacts';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->contacts = \Models\Contact::find_by_account_id($this->user->id, 'name+');
        $view->render();
    }
    
    // Add a new contact.
    
    public function add()
    {
        // Get user input.
        
        $name = \Common\Request::post('name');
        $email = \Common\Request::post('email');
        $csrf_token = \Common\Request::post('csrf_token');
    
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=60'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 60 characters.');
        }
        
        // Add.
        
        $contact = new \Models\Contact();
        $contact->account_id = $this->user->id;
        $contact->name = $name;
        $contact->email = $email;
        $contact->notes = '';
        $contact->last_used = time();
        $contact->save();
        
        // Redirect.
        
        \Common\AJAX::redirect('index.php?action=contacts');
    }
    
    // Edit form.
    
    public function edit_form()
    {
        // Check user input.
        
        $contact_id = \Common\Request::get('contact_id');
        $contact = \Models\Contact::get($contact_id);
        if (!$contact || $contact->account_id !== $this->user->id) \Common\AJAX::error('Contact not found, or access denied.');
        
        // Display edit form.
        
        $view = new \Common\View('contacts_edit');
        $view->title = 'Edit Contact';
        $view->menu = 'settings';
        $view->user = $this->user;
        $view->contact = $contact;
        $view->render();    
    }
    
    // Edit post.
    
    public function edit_post()
    {
        // Check user input.
        
        $contact_id = \Common\Request::post('contact_id');
        $name = \Common\Request::post('name');
        $email = \Common\Request::post('email');
        $csrf_token = \Common\Request::post('csrf_token');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check user input.
        
        if (!\Common\Security::validate($email, 'email'))
        {
            \Common\AJAX::error('Please enter a valid e-mail address, including the domain name.');
        }
        
        if (!\Common\Security::validate($name, 'unicode,min=1,max=60'))
        {
            \Common\AJAX::error('Please enter a name between 1 and 60 characters.');
        }
        
        // Edit.
        
        $contact = \Models\Contact::get($contact_id);
        if (!$contact || $contact->account_id !== $this->user->id) \Common\AJAX::error('Contact not found, or access denied.');
        
        $contact->name = $name;
        $contact->email = $email;
        $contact->last_used = time();
        $contact->save();
        
        // Redirect.
        
        \Common\AJAX::redirect('index.php?action=contacts');
    }
    
    // Contact actions.
    
    public function do_action()
    {
        // Grab user input.
        
        $selected_contacts = isset($_POST['selected_contacts']) ? $_POST['selected_contacts'] : array();
        $csrf_token = \Common\Request::post('csrf_token');
        $button = \Common\Request::post('button');
        
        // Check the CSRF token.
        
        if (!\Common\Session::check_token($csrf_token)) \Common\AJAX::error('CSRF');
        
        // Check if at least 1 contact is selected.
        
        if (!$selected_contacts && $button !== 'empty') \Common\AJAX::error('No contact selected.');
        
        // Compile a list of valid contacts.
        
        $contacts = array();
        foreach ($selected_contacts as $contact_id)
        {
            $contact = \Models\Contact::get($contact_id);
            if ($contact && $contact->account_id == $this->user->id) $contacts[] = $contact;
        }
        
        // Do various other things with the selected contacts.
        
        switch ($button)
        {
            case 'send_message':
                $_SESSION['selected_contacts'] = implode(', ', $contacts);
                \Common\AJAX::redirect('index.php?action=compose&to=selected');
            
            case 'delete':
                \Common\DB::begin_transaction();
                foreach ($contacts as $contact) $contact->delete();
                \Common\DB::commit();
                \Common\AJAX::redirect('index.php?action=contacts');
                
            default:
                \Common\AJAX::error('Action not recognized. Are you using an old version of Internet Explorer?');
        }
    }
}
