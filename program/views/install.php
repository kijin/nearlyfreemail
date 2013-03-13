<?php $BODY_CLASS='dialog'; ?>
<?php include 'common_header.php'; ?>

<!-- Installation Dialog -->

<div id="dialog">

    <h1><img src="<?php u('/public/images/logo_32px.png'); ?>" alt="nearlyfreemail" /></h1>
    <h2>Installation</h2>
    
    <div class="rounded box">
    
        <p>Welcome to NearlyFreeMail!</p>
        <p>Please complete the installation by creating your first e-mail account.</p>
        
        <?php if (!is_https()): ?>
            <p class="ssl_warning margin_up">Warning: It seems that your connection is insecure.<br />It is strongly recommended that you set up SSL before you proceed.</p>
        <?php endif; ?>
        
        <form id="install" class="ajax_capable" action="index.php" method="post" accept-charset="UTF-8">
        
            <p class="margin">
                <label for="email">E-mail</label>
                <span class="explain">The e-mail address you want, including the domain name. (e.g. bruce@wayne.com)</span>
            </p>
            <p><input id="email" name="email" type="text" class="focus" /></p>
            
            <p class="margin">
                <label for="name">Name</label>
                <span class="explain">This is the name that you want your recipients to see. (e.g. Batman)</span>
            </p>
            <p><input id="name" name="name" type="text" /></p>
            
            <p class="margin">
                <label for="pass1">Passphrase</label>
                <span class="explain">Please use a strong passphrase to keep the bad guys out.</span>
            </p>
            <p><input id="pass1" name="pass1" type="password" /></p>
            
            <p class="margin">
                <label for="pass2">Passphrase, Again</label>
                <span class="explain">Just to make sure you didn't make any typos the first time.</span>
            </p>
            <p><input id="pass2" name="pass2" type="password" /></p>
            
            <p class="margin right">
                <button type="submit" class="rounded">Install</button>
            </p>
            
        </form>
        
    </div>
    
</div>

<?php include 'common_footer.php'; ?>
