<?php

/**
 * -----------------------------------------------------------------------------
 *                 N  E  A  R  L  Y    F  R  E  E    M  A  I  L
 * -----------------------------------------------------------------------------
 * 
 * @package    NearlyFreeMail
 * @author     Kijin Sung <kijin@kijinsung.com>
 * @copyright  (c) 2011-2013, Kijin Sung <kijin@kijinsung.com>
 * @license    GPL v3 <http://www.opensource.org/licenses/gpl-3.0.html>
 * @link       http://github.com/kijin/nearlyfreemail
 * @version    0.2.15.1
 * 
 * -----------------------------------------------------------------------------
 * 
 * Copyright (c) 2011-2013, Kijin Sung <kijin@kijinsung.com>
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
error_reporting(-1);
ini_set('log_errors', 1);
ini_set('magic_quotes_gpc', 'Off');
define('BASEDIR', dirname(__FILE__));
define('VERSION', '0.2.15.1');

// Version check. This is more friendly than a parse error.

if (version_compare(PHP_VERSION, '5.3', '<'))
{
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "This script requires PHP 5.3 or higher.\n";
    echo "Please change your server type to PHP 5.3 or higher.\n";
    exit;
}

// The rest of the program makes extensive use of PHP 5.3 features.

include BASEDIR . '/program/bootstrap/bootstrap.php';
