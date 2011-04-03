<?php include 'common_header.php'; ?>

<!-- Login Dialog -->

<div id="dialog">

    <h1><img src="./public/images/logo_32px.png" alt="nearlyfreemail" /></h1>
    <h2>Login</h2>
    
    <div class="rounded box">
    
        <p>Please make sure that your Internet connection is secure. If in doubt, use an SSH tunnel with your NearlyFreeSpeech.NET account.</p>
        
        <form id="login" action="index.php" method="post" accept-charset="UTF-8" onsubmit="return ajax(this)">
        
            <p class="margin"><label for="email">E-mail Address</label></p>
            <p><input id="email" name="email" type="text" class="focus" /></p>
            
            <p class="margin"><label for="pass">Passphrase</label></p>
            <p><input id="pass" name="pass" type="password" /></p>
            
            <p class="margin right">
                <input type="hidden" name="action" value="login" />
                <button type="submit" class="rounded">Login</button>
            </p>
            
        </form>
        
    </div>
    
</div>

<?php include 'disclaimer.php'; ?>
<?php include 'common_footer.php'; ?>
