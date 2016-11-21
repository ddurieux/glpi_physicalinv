<?php

chdir(dirname($_SERVER["SCRIPT_FILENAME"]));

include ("../../../inc/includes.php");

// Init debug variable
$_SESSION['glpi_use_mode'] = Session::DEBUG_MODE;
$_SESSION['glpilanguage']  = "en_GB";

Session::loadLanguage();

// Only show errors
$CFG_GLPI["debug_sql"]        = $CFG_GLPI["debug_vars"] = 0;
$CFG_GLPI["use_log_in_files"] = 1;
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$DB = new DB();
if (!$DB->connected) {
   die("No DB connection\n");
}

/*---------------------------------------------------------------------*/

$plugin = new Plugin();

// To prevent problem of execution time
ini_set("max_execution_time", "0");
ini_set("memory_limit", "-1");
ini_set("session.use_cookies","0");

$plugin->getFromDBbyDir("physicalinv");
print("Installing Plugin...\n");
$plugin->install($plugin->fields['id']);
print("Install Done\n");
print("Activating Plugin...\n");
$plugin->activate($plugin->fields['id']);
print("Activation Done\n");
print("Loading Plugin...\n");
$plugin->load("physicalinv");
print("Load Done...\n");
