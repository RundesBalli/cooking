<?php
/**
 * config.php
 * 
 * Konfigurationsdatei
 */

/**
 * MySQL-Zugang
 */
$mysql_host = "localhost";
$mysql_user = "";
$mysql_pass = "";
$mysql_db = "";

/**
 * Datenbankverbindung
*/
$dbl = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db) OR DIE(MYSQLI_ERROR($dbl));
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
