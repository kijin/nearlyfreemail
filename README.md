
### Introduction ###

NearlyFreeMail is a **Web-based e-mail client** (webmail)
customized for [NearlyFreeSpeech.NET](https://www.nearlyfreespeech.net/) (NFSN) members.
It uses NFSN's [e-mail-to-URL forwarding](https://www.nearlyfreespeech.net/services/email) service for incoming messages,
and the open-source [SwiftMailer](http://swiftmailer.org/) library for outgoing messages.

The goal is to allow NFSN members to send and receive e-mail with their own domain names,
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
Please do not contact NFSN about bugs in this software.


### Features ###

Right now, you can:

- Send and receive plain-text messages with your own domain name _(HTML messages are converted to plain text)_
- Send and receive attachments up to ~7MB _(Larger messages may be rejected by NFSN)_
- Reply, reply all, and forward _(With appropriate `In-Reply-To:` and `References:` headers)_
- Add signatures to outgoing messages
- Organize your messages into folders
- Manage your address book
- Auto-save your drafts _(This is the only feature that requires JavaScript)_
- Perform basic spam filtering _(Based on spam scores assigned by NFSN's e-mail forwarder)_
- Unicode support _(Incoming messages may be in any character set; outgoing messages are always UTF-8)_

More features may be added if there is enough demand and if the author has time.
Planned features include MySQL support, aliases/personalities, and message filters.
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
and some features of included third-party libraries are disabled due to incompatibilities with NFSN's strict policies.


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
**You must go to your NFSN control panel and update the forwarding URL if you move NearlyFreeMail.**
Also, don't forget the dotfiles: `.htaccess` and `.git`.

If you would like to help fix bugs, it might be a good idea to enable the **error log** in your site.
To help with debugging, NearlyFreeMail produces a generous amount of diagnostic messages when e-mail is received.
These are deposited into your error log, so that you can see which (if any) e-mail failed to deliver.


### Security ###

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
Don't forget to update the forwarding URL if you do so.


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

