<?php

// Third-party library loader.

function load_third_party($library)
{
    switch ($library)
    {
        case 'beaver':
            include_once BASEDIR . '/program/thirdparty/beaver/beaver.php';
            break;
        
        case 'mimeparser':
            include_once BASEDIR . '/program/thirdparty/mimeparser/rfc822_addresses.php';
            break;
        
        case 'phpass':
            include_once BASEDIR . '/program/thirdparty/phpass/PasswordHash.php';
            break;
        
        case 'swiftmailer':
            include_once BASEDIR . '/program/thirdparty/swiftmailer/lib/swift_required.php';
            break;
    }
}
