<?php

namespace Config;

// Application constants.

const SESSION_NAME = 'nearlyfreemail';
const STORAGE_DIR = 'nearlyfreemail';
const INCOMING_KEY_LENGTH = 20;

// Default settings for new accounts.

class Defaults
{
    static $content_display_font = 'sans-serif';
    static $messages_per_page = 10;
    static $show_recent_contacts = 5;
    static $spam_threshold = 5;
    static $timezone = 'UTC';
    static $folders = array('Inbox', 'Archives', 'Drafts', 'Sent', 'Spam', 'Trash');
}
