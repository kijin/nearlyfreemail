
### Introduction ###

NearlyFreeMail is a **Web-based e-mail client** (webmail)
customized for [NearlyFreeSpeech.NET](https://www.nearlyfreespeech.net/) (NFSN) users.
It uses NFSN's [e-mail-to-URL forwarding service](https://www.nearlyfreespeech.net/services/email) for incoming messages,
and the open-source [SwiftMailer](http://swiftmailer.org/) library for outgoing messages.

The goal is to allow NFSN users to send and receive e-mail with their own domain names,
without relying on third-party e-mail service providers such as Google Apps.
For some people, an **all-NFSN solution** like this could have significant privacy benefits.
For others, it may add to the geek factor of their NFSN experience.

NearlyFreeMail is free software. It is distributed under the GNU General Public License (GPL), version 3 or later.
Third-party components included with this distribution may be covered by other open-source licenses.


### Important Disclaimer ###

NearlyFreeMail is experimental software. **Do not rely on this software for critical correspondence.**
NearlyFreeMail depends on implementation details of NFSN's e-mail-to-URL gateway, which may change or disappear altogether.
Bugs in this software and/or network glitches could also cause your messages to get sucked into a black hole without a trace.

**NearlyFreeMail is neither endorsed nor supported by NearlyFreeSpeech.NET.**
The author is not affiliated with NFSN in any way, other than being just another happy customer.


### Features ###

Right now, you can:

- Send and receive plain-text messages with your own domain name (HTML messages are converted to plain text)
- Send and receive attachments up to ~7MB (larger messages may be rejected by NFSN)
- Organize your messages into folders
- Manage your address book
- Reply, reply all, and forward (with appropriate `In-Reply-To:` and `References:` headers)
- Add singnatures to outgoing messages
- Auto-save your drafts (requires JavaScript)
- Perform basic spam filtering (depends on spam score assigned by NFSN)

More features may be added if there is enough demand and if the author has time.
Updates, if any, will be published on the author's GitHub page.


### Requirements ###

To install and use NearlyFreeMail, you need:

- An account with NearlyFreeSpeech.NET
- A domain name, with name servers pointing to NearlyFreeSpeech.NET
- Enough money to purchase e-mail forwarding service which is quite expensive
- A site, with the server type **PHP 5.3** (This is very important!)

Currently, a lot of things are hard-coded in a way that would make it inconvenient to use NearlyFreeMail
anywhere except in NFSN's pay-as-you-go shared hosting environment.
For example, all messages and attachments are stored in the "protected" directory in a single SQLite database;
and some features of included third-party libraries are disabled due to incompatibilities with NFSN.


### Installation ###

Log in to your site with SSH. Issue the following commands, in that order:

    cd /home/public
    git clone git://github.com/kijin/nearlyfreemail.git
    chgrp web /home/public/nearlyfreemail/index.php
    echo "deny from all" > /home/public/nearlyfreemail/.git/.htaccess
    mkdir /home/protected/nearlyfreemail
    touch /home/protected/nearlyfreemail/db.sqlite
    chgrp -R web /home/protected/nearlyfreemail

Afterwards, visit http://yoursite.nfshost.com/nearlyfreemail in your browser.
Follow the instructions to create your first all-NFSN e-mail account.

You can move NearlyFreeMail to the document root of your site, or to any other directory,
simply by moving all the files and directories to the desired location.
Just don't forget the dotfiles: `.htaccess` and `.git`.

The "nearlyfreemail" directory in your protected folder should not be messed with unless you know what you're doing.
It contains information about all of your e-mail accounts, contacts, and messages.


### Updating ###

To keep your copy of NearlyFreeMail up to date, log in to your SSH account again,
go to the directory where you have installed NearlyFreeMail, and issue the following command:

    git pull

That's it. No tarballs to decompress and upload, no files to chmod or rename!
Do this from time to time, so that you may receive new features and critical security fixes.


### Resetting ###

If something goes horribly wrong while you are test-driving NearlyFreeMail and you want to start again from scratch,
there is no need to re-download NearlyFreeMail. (None of the application files are modified at runtime.)
Just run the following command. **This will erase all your e-mail accounts, contacts, and messages!**

    truncate -s 0 /home/protected/nearlyfreemail/db.sqlite

The next time you try to access NearlyFreeMail, you will be greeted with a new installation screen.

