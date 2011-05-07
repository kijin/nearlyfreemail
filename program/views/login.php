<?php include 'common_header.php'; ?>

<!-- Login Dialog -->

<div id="dialog">

    <h1><img src="<?php u('/public/images/logo_32px.png'); ?>" alt="nearlyfreemail" /></h1>
    <h2>Login</h2>
    
    <div class="rounded box">
    
        <p>Please make sure that your Internet connection is secure!</p>
        
        <form id="login" class="ajax_capable" action="<?php u('/account/login'); ?>" method="post" accept-charset="UTF-8">
        
            <p class="margin"><label for="email">E-mail Address</label></p>
            <p><input id="email" name="email" type="text" class="focus" /></p>
            
            <p class="margin"><label for="pass">Passphrase</label></p>
            <p><input id="pass" name="pass" type="password" /></p>
            
            <p class="margin right"><button type="submit" class="rounded">Login</button></p>
            
        </form>
        
    </div>
    
</div>

<?php include 'disclaimer.php'; ?>
<?php include 'common_footer.php'; ?>
