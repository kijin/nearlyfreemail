<?php

// Load some configuration files.

include BASEDIR . '/program/bootstrap/config.php';
include BASEDIR . '/program/bootstrap/functions.php';
include BASEDIR . '/program/bootstrap/routes.php';

// Find the protected directory.

if (isset($_SERVER['NFSN_SITE_ROOT']))  // NFSN Web.
{
    define('STORAGE_DIR', $_SERVER['NFSN_SITE_ROOT'] . '/protected/' . \Config\STORAGE_DIR);
    define('STORAGE_DBFILE', STORAGE_DIR . '/db.sqlite');
}
elseif (file_exists('/home/protected') && is_dir('/home/protected'))  // CLI.
{
    define('STORAGE_DIR', '/home/protected/' . \Config\STORAGE_DIR);
    define('STORAGE_DBFILE', STORAGE_DIR . '/db.sqlite');
}
else
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Cannot find the protected directory.\n";
    echo "Are you sure you're using NearlyFreeSpeech.NET?\n";
    exit;
}

// Make sure the storage directory exists and is writable.

if (!file_exists(STORAGE_DIR) || !is_writable(STORAGE_DIR))
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "The protected directory doesn't exist, or it is not writable.\n";
    echo "Please create /home/protected/" . \Config\STORAGE_DIR . " ";
    echo "and chgrp it to 'web'.\n";
    exit;
}

// Set up the autoloader.

spl_autoload_register(function($class_name)
{
    $class_name = strtolower(str_replace('\\', '/', $class_name));
    $file_name = BASEDIR . '/program/' . $class_name . '.php';
    if (file_exists($file_name)) include $file_name;
});

// Start output buffering before we do anything else.

ob_start();

// Start the session now.

\Common\Session::start(\Config\SESSION_NAME);

// Set the directory for template files.

\Common\View::set_dir(BASEDIR . '/program/views');

// Set up the default 404 error message.

\Common\Response::not_found_set_default_callback(function()
{
    $view = new \Common\View('error');
    $view->title = '404 Not Found';
    $view->message = 'It seems you\'re looking for a page that doesn\'t exist.';
    $view->render();
});

// Set the default exception handler, and a temporary error handler for PDO.

set_exception_handler(function($exception)
{
    ob_clean();
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Uncaught exception: ' . get_class($exception) . "\n";
    echo $exception->getMessage() . "\n";
    echo $exception->getFile() . ' line ' . $exception->getLine() . "\n\n";
    error_log('Uncaught exception: ' . get_class($exception));
    error_log($exception->getMessage());
    error_log($exception->getFile() . ' line ' . $exception->getLine());
    error_log($exception->getTraceAsString());
    exit;
});

set_error_handler(function($errno, $errstr)
{
    ob_clean();
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Cannot connect to the database.\n";
    echo $errstr . "\n";
    exit;
}, E_WARNING);

// Initialize the database.

if (file_exists(STORAGE_DIR . '/mysql.php'))
{
    include STORAGE_DIR . '/mysql.php';
    extract($mysql, EXTR_SKIP);
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $opt = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
    $pdo = new PDO($dsn, $username, $password, $opt);
    \Common\DB::initialize($pdo);
}
else
{
    \Common\DB::initialize(STORAGE_DBFILE);
    \Common\DB::query('PRAGMA foreign_keys = ON');  // SQLite needs this.
}

// PDO now throws exceptions instead of warnings, so cancel the error handler.

restore_error_handler();

// Initialize the Beaver ORM.

load_third_party('beaver');
\Beaver\Base::set_database(\Common\DB::get_pdo());

// If installation is not complete, run the installer now.

if (!\Models\Install::is_installed()) $install();

// Dispatch all other requests to the appropriate controller/method.

\Common\Router::dispatch($routes);
\Common\Response::not_found();
