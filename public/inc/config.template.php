<?php
/**
 * config.php
 * 
 * Konfigurationsdatei
 */

/**
 * MySQL-Zugangsdaten
 * 
 * @var string $mysqlHost
 * @var string $mysqlUser
 * @var string $mysqlPass
 * @var string $mysqlDb
 */
$mysqlHost = "localhost";
$mysqlUser = "";
$mysqlPass = "";
$mysqlDb   = "";

/**
 * pr0-Auth
 * 
 * @var string $clientSecret
 * @var int    $clientId
 * @var string $authURL      Beispiel: https://pr0gramm.com/auth/test123
 */
$clientSecret = "";
$clientId = 0;
$authURL = "https://pr0gramm.com/auth/";

/**
 * Speicherort des apiCalls.
 * Download: https://github.com/RundesBalli/pr0gramm-apiCall
 * Wird - sofern erforderlich - eingebunden.
 * 
 * Beispiel: /home/user/apiCall/apiCall.php
 * 
 * @var string
 */
$apiCall = "";

/**
 * Datenbankverbindung
*/
$dbl = mysqli_connect($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDb) OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, "utf8") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Zeitzoneneinstellung
 */
date_default_timezone_set("Europe/Berlin");

/**
 * Nicht ändern
 */
$uploaddir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR;
?>
