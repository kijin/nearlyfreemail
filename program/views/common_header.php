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
    <script type="text/javascript" src="<?php u('public/thirdparty/jquery-1.6.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php u('public/common.js'); ?>?v=<?php e(VERSION); ?>"></script>
    <script type="text/javascript" src="<?php u('public/user/user.js'); ?>?v=<?php e(VERSION); ?>"></script>
</head>

<body class="<?php if (isset($BODY_CLASS)) e($BODY_CLASS); ?>">

<?php if (\Common\Request::info('old_browser')): ?>
<div id="old_browser">
    Warning: You are using an outdated web browser. This page may not display as intended.
    &nbsp;<a href="#" onclick="jQuery('#old_browser').fadeOut()">Close</a>
</div>
<?php endif; ?>