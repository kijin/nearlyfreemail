
Introduction
------------

NearlyFreeMail is a **Web-based e-mail client** (webmail) designed for [NearlyFreeSpeech.NET](https://www.nearlyfreespeech.net/) (NFSN) members.
It uses NFSN's [e-mail-to-URL forwarding service](https://www.nearlyfreespeech.net/services/email) for incoming messages,
and the open-source [SwiftMailer](http://swiftmailer.org/) library (i.e. `mail()` on steroids) for outgoing messages.

The goal is to allow NFSN members to send and receive e-mail with their own domain names,
without relying on third-party e-mail service providers such as Google Apps.
For some people, an **all-NFSN solution** like this could have significant privacy benefits.
For others, it may add to the geek factor of their NFSN experience.
It might not cost less than other options, though -- it's only "nearly" free, after all.

NearlyFreeMail is free software. It is distributed under the GNU General Public License (GPL), version 3 or later.
Third-party components included with this distribution may be covered by other open-source licenses.

Screenshots can be found [here](http://imgur.com/a/7oUDK).


Important Disclaimer
--------------------

NearlyFreeMail is experimental software. **Do not rely on this software for critical correspondence.**
NearlyFreeMail depends on implementation details of NFSN's e-mail-to-URL gateway, which may change or disappear altogether.
Bugs in this software and/or network glitches could also cause your messages to get sucked into a black hole without a trace.

**NearlyFreeMail is neither endorsed nor supported by NearlyFreeSpeech.NET.**
The author is not affiliated with NFSN in any way, other than being just another happy customer.
Please do not contact NFSN about bugs in this software.


Features
--------

As of this writing, NearlyFreeMail supports:

- Multiple accounts, each with multiple aliases
- Sending and receiving plain-text messages with your own domain name _(Sorry, no HTML)_
- Aattachments up to ~7MB _(Larger messages may be rejected by NFSN)_
- Reply, reply all, and forward _(With appropriate `In-Reply-To:` and `References:` headers)_
- Basic spam filtering _(Based on spam scores assigned by NFSN's e-mail forwarder)_
- Draft auto-saving _(This is the only feature that requires JavaScript)_
- Unicode _(Incoming messages may be in any character set; outgoing messages are always UTF-8)_
- SQLite or MySQL for data storage
- Exporting folders in the mbox format _(Can be imported into Mozilla Thunderbird)_
- Signatures
- Folders
- Address book

More features may be added if there is demand and if the author has time.
Updates, if any, will be published on the author's GitHub page.

Please also read "Known Issues" below.


Requirements
------------

To install and use NearlyFreeMail, you need:

- An account with NearlyFreeSpeech.NET
- A domain name, with name servers pointing to NearlyFreeSpeech.NET
- Enough money to purchase e-mail forwarding service which is quite expensive
- A site, with the server type **PHP 5.3** (This is very important!)

Currently, a lot of things are hard-coded in a way that would make it inconvenient to use NearlyFreeMail
anywhere except in NFSN's pay-as-you-go shared hosting environment.
For example, all messages and attachments are stored in the "protected" directory in a single SQLite database
(this prevents annoying situations where you have to repossess files and subdirectories owned by "web");
and some features of included third-party libraries are disabled due to incompatibilities with NFSN's strict policies.

NearlyFreeMail follows web standards for the most part, except where browser-specific hack are required.
It should appear and function as intended in all modern desktop browsers,
such as Firefox 3.6 and higher, Internet Explorer 8 and higher, Chrome, Safari, and Opera.
It is known to break noticeably in Internet Explorer 7 and lower, and there are no plans to fix this.
Mobile browsers have not been tested.


Installation
------------

### Step 1

Log in to your site with SSH. Issue the following commands, in that order:

    cd /home/public
    git clone git://github.com/kijin/nearlyfreemail.git
    chgrp web /home/public/nearlyfreemail/index.php
    echo "deny from all" > /home/public/nearlyfreemail/.git/.htaccess
    mkdir /home/protected/nearlyfreemail
    touch /home/protected/nearlyfreemail/db.sqlite
    chgrp -R web /home/protected/nearlyfreemail

If you would like to use an SQLite database for data storage, skip to Step 3.
If you would like to use MySQL instead, proceed to Step 2.

### Step 2 (MySQL Only)

While logged in with SSH, issue the following command:

    cp /home/public/nearlyfreemail/program/bootstrap/mysql.php /home/protected/nearlyfreemail

Then, using your favorite text editor, edit `/home/protected/nearlyfreemail/mysql.php`
and fill in your MySQL connection information.

    $mysql['host'] = 'your_mysql_hostname.db';
    $mysql['port'] = 3306;
    $mysql['username'] = 'your_username';
    $mysql['password'] = 'your_password';
    $mysql['dbname'] = 'your_database_name';

### Step 3

Visit http://yoursite.nfshost.com/nearlyfreemail in your browser.
Follow the instructions to create your first all-NFSN e-mail account.
The welcome screen will tell you how to configure e-mail forwarding for your domain.

You may move NearlyFreeMail to the document root of your site, or to any other directory,
simply by moving all the files and directories to the desired location.
**You must go to your NFSN control panel and update the forwarding URL if you change the installed location of NearlyFreeMail.**
Also, don't forget the dotfiles: `.htaccess` and `.git`.

If you would like to help fix bugs, it might be a good idea to enable the **error log** in your site.
To assist debugging, NearlyFreeMail produces a generous amount of diagnostic messages when e-mail is received.
These are deposited into your error log, so that you can see which (if any) e-mail failed to deliver.


Updating
--------

To keep your copy of NearlyFreeMail up to date, log in to your SSH account again,
go to the directory where you have installed NearlyFreeMail, and issue the following command:

    git pull

That's it. No tarballs to decompress and upload, no files to chmod or rename!
Do this from time to time, so that you may receive new features and critical security fixes.


Security
--------

The author will try to keep NearlyFreeMail protected from known attack vectors on a best effort basis.
However, since the security of a web application depends heavily on its environment,
you are also responsible for protecting your own copy of NearlyFreeMail.
There are two things that you must guard particularly closely: your passphrase,
and the randomly generated URL that you will receive upon installation.
Whoever knows this URL can spam you to oblivion, so do not give it to anyone except NFSN.
Outgoing messages, on the other hand, are subject to
NFSN's [sending limits](https://members.nearlyfreespeech.net/support/faq?q=EmailBank#EmailBank).
This should be more than enough unless you frequently try to e-mail a lot of people.

As of April 2011, NFSN does not support HTTPS on member sites.
People can steal your passphrase (and more) if you use NearlyFreeMail (or any other non-HTTPS site)
with an insecure Internet connection, such as free Wi-Fi hotspots at libraries and coffee shops.
Many governments and ISPs also have the capacity to intercept anything not sent over HTTPS.
So if you care about security at all, **use a VPN or SSH tunnel whenever you use NearlyFreeMail**.
NFSN [seems to allow](https://members.nearlyfreespeech.net/support/faq?q=SSH#SSH)
using an SSH tunnel with your NFSN account to manage your own site -- but please don't abuse it --
and there are other places where you can rent a low-cost virtual server to proxy through.
Also, there are programs such as [FoxyProxy](https://addons.mozilla.org/en-US/firefox/addon/foxyproxy-standard/)
which can be configured to switch to a tunneled connection automatically when you visit your own site.

You might be tempted to add HTTP authentication ([basic](http://en.wikipedia.org/wiki/Basic_access_authentication) or
[digest](http://en.wikipedia.org/wiki/Digest_access_authentication)) to your site. This is a bad idea.
**If you use HTTP authentication on your site, NFSN will not be able to deliver any incoming messages.**
The best that you can do realistically to keep NearlyFreeMail hidden from random attackers
would be to stash it in a subdirectory with a name that is hard to guess.
(The recipients of your messages, however, will be able to infer the location by reading the headers automatically added by NFSN.)
Don't forget to update the forwarding URL if you move your installation of NearlyFreeMail.


Known Issues
------------

Incoming attachments with names that include non-ASCII characters will be renamed to body1, body2, etc.
This is because NFSN renames such files when forwarding them. A workaround will be developed in the future.

Autosave will save the list of recipients, subject, and content of your draft,
but it will not automatically save attachments that have yet to be uploaded.
If you want to save attachments as well, click the "Save Draft" button.
This shortcoming will be fixed in the future.

The message source is stored separately from the content and any attachments.
This leads NearlyFreeMail to consume up to twice as much storage as the actual size of the message.
("Up to" twice as much, but usually less, since the message source is compressed before storage.)

Deleting large messages will not automatically reduce the size of the SQLite database file.
In order to reclaim unused space, you must manually vacuum the database using the following commands:

    sqlite3 /home/protected/nearlyfreemail/db.sqlite
    vacuum;
    .quit

Do not do this too often, because vacuuming a large SQLite database may consume a lot of server resources.
