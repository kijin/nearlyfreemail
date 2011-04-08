<?php include 'common_header.php'; ?>

<!-- Welcome Dialog -->

<div id="dialog">

    <h1><img src="<?php u('/public/images/logo_32px.png'); ?>" alt="nearlyfreemail" /></h1>
    <h2>Welcome</h2>
    
    <div class="rounded box">
    
        <p>Welcome, <?php e($user->get_default_alias()->name); ?>! Your e-mail account has been created.</p>
        <p class="margin_up">There are two other things that you need to do before you will be able to send and receive e-mails using NearlyFreeMail. Please keep reading...</p>
        
        <h3>A. Set up e-mail forwarding with NearlyFreeSpeech.NET</h3>
        <ol>
            <li>Go to the <a href="https://members.nearlyfreespeech.net/domains">Domains</a> tab of your NearlyFreeSpeech.NET account.</li>
            <li>Find "<?php e($email_domain); ?>", and click the "Add" button in the "E-mail" column.</li>
            <li>You will be told that e-mail forwarding costs money. Continue.</li>
            <li>Click "Add a Forwarding Address" in the "Actions" menu.</li>
            <li>In the "Email Address" box, enter:<br /><p class="quote"><?php e($email_local); ?></p></li>
            <li>In the "Forwards To" box, enter:<br /><p class="quote"><?php e($user->get_default_alias()->get_incoming_url()); ?></p></li>
            <li>Click "Add E-mail Forward".</li>
        </ol>
        
        <h3>B. Set up an SPF record for your domain</h3>
        <ol>
            <li>Go to the <a href="https://members.nearlyfreespeech.net/domains">Domains</a> tab of your NearlyFreeSpeech.NET account.</li>
            <li>Find "<?php e($email_domain); ?>", and click the "Manage" button in the "DNS" column.</li>
            <li>If you have "SPF E-mail protection" enabled for this domain, disable it.</li>
            <li>Click "Add a DNS Resource Record" in the "Actions" menu.</li>
            <li>Leave the "Name" box blank, and select "TXT" for the "Type" box.</li>
            <li>In the "Data" box, enter:<br /><p class="quote">v=spf1 include:sites.nearlyfreespeech.net ~all</p></li>
            <li>Click "Add Record".</li>
        </ol>
        
        <h3>You're done!</h3>
        <p>Please be patient. It may take several hours for other e-mail service providers to notice the changes you just made.
           If your messages don't seem to get through, get some rest and try again tomorrow.</p>
        <p class="margin_up"><a href="<?php u('/mail'); ?>">Click here</a> to open your inbox when you're ready.</a></p>
        
    </div>
    
</div>

<?php include 'disclaimer.php'; ?>
<?php include 'common_footer.php'; ?>
