<?php include 'common_header.php'; ?>

<!-- Settings View Title -->

<h3><?php e($title); ?></h3>

<!-- Setup Instructions -->

<div id="howto">

    <p>These steps must be completed in order for your new e-mail address to work properly.</p>
    
    <h3>A. Set up e-mail forwarding with NearlyFreeSpeech.NET</h3>
    <ol>
        <li>Go to the <a href="https://members.nearlyfreespeech.net/domains">Domains</a> tab of your NearlyFreeSpeech.NET account.</li>
        <li>Find "<?php e($email_domain); ?>", and click the "Add" or "Manage" button in the "E-mail" column.</li>
        <li>If this is the first time for this domain, you will be told that e-mail forwarding costs money. Continue.</li>
        <li>Click "Add a Forwarding Address" in the "Actions" menu.</li>
        <li>In the "Email Address" box, enter:<br /><p class="quote"><?php e($email_local); ?></p></li>
        <li>In the "Forwards To" box, enter:<br /><p class="quote"><?php e($alias->get_incoming_url()); ?></p></li>
        <li>Click "Add E-mail Forward".</li>
    </ol>
    
    <h3>B. Set up an SPF record for your domain (if you haven't already done so)</h3>
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
    <p>It may take several hours for other e-mail service providers to notice the changes you just made.<br />
       If your messages don't seem to get through, get some rest and try again tomorrow.<br /><br /><br /></p>
    
</div>

<!-- Standard Footers -->

<?php include 'common_footer.php'; ?>