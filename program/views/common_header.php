<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta charset="UTF-8" />
    <title><?php e($title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php u('favicon.ico'); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php u('public/thirdparty/reset-2.0.css'); ?>" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php u('public/common.css'); ?>?v=<?php e(VERSION); ?>" />
    <!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="<?php u('public/ie8sucks.css'); ?>?v=<?php e(VERSION); ?>" /><![endif]-->
    <!--[if IE 9]><link rel="stylesheet" type="text/css" media="all" href="<?php u('public/ie9sucks.css'); ?>?v=<?php e(VERSION); ?>" /><![endif]-->
    <link rel="stylesheet" type="text/css" media="all" href="<?php u('public/user/user.css'); ?>?v=<?php e(VERSION); ?>" />
    <script type="text/javascript" src="<?php u('public/thirdparty/jquery-1.6.4.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php u('public/common.js'); ?>?v=<?php e(VERSION); ?>"></script>
    <script type="text/javascript" src="<?php u('public/user/user.js'); ?>?v=<?php e(VERSION); ?>"></script>
</head>

<body class="<?php if (isset($BODY_CLASS)) e($BODY_CLASS); ?>">

<?php if (\Common\Request::info('old_browser')): /* Outdated Browser Warning */ ?>
<div id="old_browser">
    Warning: You are using an outdated web browser. This page may not display as intended.
    &nbsp;<a href="#" onclick="jQuery('#old_browser').fadeOut()">Close</a>
</div>
<?php endif; ?>

<?php if (!isset($BODY_CLASS)): /* Default Page Header */ ?>

<!-- Mail Screen Header -->

<div id="header">

    <h1><a href="<?php u('/'); ?>"><img src="<?php u('/public/images/logo_32px.png'); ?>" alt="NearlyFreeMail" /></a></h1>
    
    <div class="profile">
        <span class="name"><?php e($user->get_default_alias()->name); ?></span>
        <span class="email"><?php e($user->get_default_alias()->email); ?></span>
    </div>
    
    <form id="search" action="<?php u('/mail/search'); ?>" method="get" accept-charset="UTF-8">
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

<?php endif; ?>
