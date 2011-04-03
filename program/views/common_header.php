<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta charset="UTF-8" />
    <title><?php e($title); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="./public/thirdparty/reset-2.0.css" />
    <link rel="stylesheet" type="text/css" media="all" href="./public/common-<?php e(VERSION); ?>.css" />
    <!--[if IE 8]><link rel="stylesheet" type="text/css" media="all" href="./public/ie8sucks-<?php e(VERSION); ?>.css" /><![endif]-->
    <!--[if IE 9]><link rel="stylesheet" type="text/css" media="all" href="./public/ie9sucks-<?php e(VERSION); ?>.css" /><![endif]-->
    <link rel="stylesheet" type="text/css" media="all" href="./public/user/user.css" />
    <script type="text/javascript" src="./public/thirdparty/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="./public/common-<?php e(VERSION); ?>.js"></script>
    <script type="text/javascript" src="./public/user/user.js"></script>
</head>

<body class="<?php if (isset($BODY_CLASS)) e($BODY_CLASS); ?>">

<?php if (\Common\Request::info('old_browser')): ?>
<div id="old_browser">
    Warning: You are using an outdated web browser. This page may not display as intended.
    &nbsp;<a href="#" onclick="jQuery('#old_browser').fadeOut()">Close</a>
</div>
<?php endif; ?>