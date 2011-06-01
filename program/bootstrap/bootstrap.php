<?php

// Load some configuration files.

include BASEDIR . '/program/bootstrap/config.php';
include BASEDIR . '/program/bootstrap/functions.php';
include BASEDIR . '/program/bootstrap/routes.php';

// Find the protected directory.

if (preg_match('#^(/\\w+/\\w+)/public#', BASEDIR, $matches) &&  // NFSN Web.
    file_exists($matches[1] . '/protected') &&
    is_dir($matches[1] . '/protected'))
{
    define('STORAGE_DIR', $matches[1] . '/protected/' . \Config\STORAGE_DIR);
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

// Set up the default error message.

\Common\Response::not_found_set_default_callback(function()
{
    $view = new \Common\View('error');
    $view->title = '404 Not Found';
    $view->message = 'It seems you\'re looking for a page that doesn\'t exist.';
    $view->render();
});

// Start the session now.

\Common\Session::start(\Config\SESSION_NAME);

// Set the directory for template files.

\Common\View::set_dir(BASEDIR . '/program/views');

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

// Initialize the Beaver ORM.

load_third_party('beaver');
\Beaver\Base::set_database(\Common\DB::get_pdo());

// If installation is not complete, run the installer now.

if (!\Models\Install::is_installed()) $install();

// Dispatch all other requests to the appropriate controller/method.

\Common\Router::dispatch($routes);
\Common\Response::not_found();
