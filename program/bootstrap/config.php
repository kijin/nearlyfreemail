<?php

namespace Config;

// Application constants.

const SESSION_NAME = 'nearlyfreemail';
const STORAGE_DIR = 'nearlyfreemail';
const INCOMING_KEY_LENGTH = 20;

// Default settings for new accounts.

class Defaults
{
    // These are configurable per account.
    
    static $content_display_font = 'sans-serif';
    static $messages_per_page = 10;
    static $show_sidebar_contacts = 5;
    static $show_compose_contacts = 10;
    static $spam_threshold = 4;
    static $timezone = 'UTC';
    
    // These are fixed.
    
    static $folders = array('Inbox', 'Archives', 'Drafts', 'Sent', 'Spam', 'Trash');
}
