<?php
// initiation file for the web app
// includes core files and contains various settings

// error settings
error_reporting(-1);

// cookie stuff (settings)
session_start();

// language settings
setlocale(LC_ALL, 'nl_BE.UTF-8');

// database settings
define("DB_HOST", "localhost");
define("DB_NAME", "name");
define("DB_USER", "user");
define("DB_PASS", "pass");

// gmail settings
define("GMAIL_ADDR", "user@gmail.com");
define("GMAIL_PASS", "pass");

// ip / url settings
define("WEBSITE_URL", "//localhost");

// general website settings
define("WEBSITE_TITLE", "mijnscore");

// include the core files
require_once 'core/Database.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
