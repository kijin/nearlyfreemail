<?php

/**
 * -----------------------------------------------------------------------------
 *                 N  E  A  R  L  Y    F  R  E  E    M  A  I  L
 * -----------------------------------------------------------------------------
 * 
 * @package    NearlyFreeMail
 * @author     Kijin Sung <kijin.sung@gmail.com>
 * @copyright  (c) 2011, Kijin Sung <kijin.sung@gmail.com>
 * @license    GPL v3 <http://www.opensource.org/licenses/gpl-3.0.html>
 * @link       http://github.com/kijin/nearlyfreemail
 * @version    0.1
 * 
 * -----------------------------------------------------------------------------
 * 
 * Copyright (c) 2011, Kijin Sung <kijin.sung@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * -----------------------------------------------------------------------------
 */

date_default_timezone_set('UTC');
error_reporting(E_ALL | E_STRICT);
ini_set('log_errors', 1);
ini_set('magic_quotes_gpc', 'Off');
define('BASEDIR', __DIR__);
define('VERSION', '0.1');

// Load the configuration.

include BASEDIR . '/program/config/config.php';
include BASEDIR . '/program/config/routes.php';
include BASEDIR . '/program/thirdparty/loader.php';
load_third_party('beaver');

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
    echo "Please create /home/protected/{\Config\STORAGE_DIR} ";
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

// Start the session, and initialize the database and the Beaver ORM.

\Common\Session::start(\Config\SESSION_NAME);
\Common\DB::initialize(STORAGE_DBFILE);
\Common\DB::query('PRAGMA foreign_keys = ON');
\Beaver\Base::set_database(\Common\DB::get_pdo());

// If installation is not complete, run the installation now.

if (!\Models\Setting::is_installed()) $install();

// Dispatch all other requests to the appropriate controller/method.

\Common\Router::dispatch($routes);
\Common\Response::not_found();
