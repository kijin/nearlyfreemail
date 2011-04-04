
<!-- Mail Screen Header -->

<div id="header">

    <h1><a href="index.php?action=inbox"><img src="./public/images/logo_32px.png" alt="NearlyFreeMail" /></a></h1>
    
    <div class="profile">
        <span class="name"><?php e($user->get_default_alias()->name); ?></span>
        <span class="email"><?php e($user->get_default_alias()->email); ?></span>
    </div>
    
    <form id="search" action="index.php" method="get" accept-charset="UTF-8">
        <input type="hidden" name="action" value="search" />
        <input type="text" name="keywords" value="" class="rounded" />
        <button type="submit" class="rounded">Search</button>
    </form>
    
</div>

<!-- Sidebar -->

<div id="sidebar">
<?php
    if (isset($menu))
    {
        switch ($menu)
        {
            case 'main': include 'sidebar_main.php'; break;
            case 'compose': include 'sidebar_compose.php'; break;
            case 'settings': include 'sidebar_settings.php'; break;
        }
    }
    else
    {
        include 'sidebar_main.php';
    }
?>
</div>

<!-- Content Area -->

<div id="content">

