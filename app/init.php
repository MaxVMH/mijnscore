<?php
// initiation file for the web app
// includes core files and contains various settings

// error settings
error_reporting(E_ALL);
ini_set("display_errors", 1);

// cookie stuff (settings)
session_start();

// language settings
setlocale(LC_ALL, 'nl_NL');

// database settings
define("DB_HOST", "localhost");
define("DB_NAME", "name");
define("DB_USER", "user");
define("DB_PASS", "pass");

// ip / url settings
define("WEBSITE_URL", "//localhost");

// general website settings
define("WEBSITE_TITLE", "mijnscore");

// include the core files
require_once 'core/Database.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
