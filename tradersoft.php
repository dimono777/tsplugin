<?php
echo 'Hello there!!!!';
/*
Plugin Name: TraderSoft
Plugin URI: https://www.tradersoft.com
Description: The plugin allows you to connect your website to TraderSoft trading platform
Release date: 2021-07-12
Version: 0.3.65
Author: Tradersoft
Author URI: https://www.tradersoft.com
License: GPL2
GitHub Plugin URI: dimono777/tsplugin
GitHub Plugin URI: https://github.com/dimono777/tsplugin
Primary Branch: main
 
    Copyright 2017 Tradersoft
 
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
ob_start();

define('TS_DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('TS_PLUGIN_BASENAME', plugin_basename(__FILE__));

if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string) {
        return strtolower($string);
    }
}

require_once( TS_DOCROOT . 'inc/autoloader.php' );

/*
 * Include functions
 */
require_once TS_DOCROOT . 'model/functions.php';

/**
 * Init TSInit
 */

require_once TS_DOCROOT . 'tsinit.php';
TSInit::getInstance();
?>
