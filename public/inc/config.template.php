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

 */
$mysql_host = "localhost";
$mysql_user = "";
$mysql_pass = "";
$mysql_db = "";

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
