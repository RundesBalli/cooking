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
 * Datenbankverbindung
*/
$dbl = mysqli_connect($mysqlHost, $mysqlUser, $mysqlPass, $mysqlDb) OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, "utf8") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Zeitzoneneinstellung
 */
date_default_timezone_set("Europe/Berlin");

/**
 * Speicherort der Bilder und Thumbnails
 */
$uploadDir = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."img";

if(!defined("cli")) {
  /**
   * Erzeugen des Unique-User-Identifier
   */
  $UUI = hash('sha256', $_SERVER['REMOTE_ADDR']."|".$_SERVER['HTTP_USER_AGENT']."|".$_SERVER['HTTP_ACCEPT']."|".$_SERVER['HTTP_ACCEPT_LANGUAGE']."|".$_SERVER['HTTP_ACCEPT_ENCODING']);
}
?>
