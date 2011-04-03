
Introduction
------------

NearlyFreeMail is a **Web-based e-mail client** (webmail) written in PHP.
It uses NearlyFreeSpeech.NET's e-mail-to-URL forwarding service for incoming
messages, and `mail()` for outgoing messages. (Actually, it uses the open-source
SwiftMailer library for sending, but that's just `mail()` on steroids.)

The goal is to allow NearlyFreeSpeech.NET (a.k.a. NFSN) users to send and
receive e-mail without relying on third-party e-mail service providers such as
Google Apps. For some people, an **all-NFSN solution** like this could have
significant privacy benefits. For others, it may add to the geek factor of their
NFSN experience.

NearlyFreeMail is free software. It is distributed under the GNU General Public
License (GPL), version 3 or later. Third-party components included with this
distribution may be covered by other open-source licenses.


Disclaimer
----------

NearlyFreeMail is experimental software. **Do not rely on this software for
important correspondence.** NearlyFreeMail depends on implementation details of
NFSN's e-mail-to-URL gateway, which may change or disappear altogether without
notice. Bugs in this software and/or network glitches could also cause your
messages to get sucked into a black hole without a trace.

**NearlyFreeMail is neither endorsed nor supported by NearlyFreeSpeech.NET.**
The author is not affiliated with NearlyFreeSpeech.NET in any way, other than
being just another happy customer.


Requirements
------------

To install and use NearlyFreeMail, you need:

- An account with NearlyFreeSpeech.NET
- A domain name, with name servers pointing to NearlyFreeSpeech.NET
- Enough money to purchase e-mail forwarding service which is quite expensive
- A site, with the server type **PHP 5.3** (This is very important!)


Installation
------------

Log in to your site with SSH. Issue the following commands, in that order:

    cd /home/public
    git clone git://github.com/kijin/nearlyfreemail.git
    echo "deny from all" > /home/public/nearlyfreemail/.git/.htaccess
    mkdir /home/protected/nearlyfreemail
    touch /home/protected/nearlyfreemail/db.sqlite
    chgrp -R web /home/protected/nearlyfreemail

Afterwards, visit http://yoursite.nfshost.com/nearlyfreemail in your browser.
Follow the instructions to create your first all-NFSN e-mail account.

You can move NearlyFreeMail to the document root of your site, or to any other
directory, simply by moving all the files and directories to the desired
location. Just don't forget the dotfiles: `.htaccess` and `.git`.

The "nearlyfreemail" directory in your protected folder should not be messed
with unless you absolutely know what you're doing. It contains information
about all of your e-mail accounts, contacts, and messages.


Updating
--------

To receive bug fixes and critical security updates (which may or may not be
forthcoming, since NearlyFreeMail is experimental software), log in to your
SSH account again, go to the directory (e.g. "nearlyfreemail") where you have
installed NearlyFreeMail, and issue the following command:

    git pull

That's it. No tarballs to decompress and upload, no files to chmod or rename!
