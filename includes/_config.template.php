<?php
/**
 * config.php
 * 
 * Konfigurationsdatei
 */

/**
 * MySQL-Zugangsdaten
 * 
 * @var string $mysqlHost    Host der MySQL-Verbindung
 * @var string $mysqlUser    Username für die MySQL-Verbindung
 * @var string $mysqlPass    Passwort für die MySQL-Verbindung
 * @var string $mysqlDb      Datenbank auf dem SQL-Server, in der gearbeitet werden soll.
 * @var string $mysqlCharset Charset der Verbindung. Standard: utf8
 */
$mysqlHost    = "localhost";
$mysqlUser    = "";
$mysqlPass    = "";
$mysqlDb      = "";
$mysqlCharset = "utf8";

/**
 * Zeitzoneneinstellung
 * 
 * @var string $defaultTimezone Gewünschte Zeitzone. Standard: Europe/Berlin
 */
$defaultTimezone = "Europe/Berlin";

/**
 * Standard OG Metadaten
 * 
 * @var string $ogConfig['name']        Titel der Seite
 * @var string $ogConfig['imgAlt']      Ersatztext für das angezeigte Bild
 * @var string $ogConfig['description'] Angezeigte Beschreibung / Text unter dem Bild
 * @var string $ogConfig['locale']      Spracheinstellung. Standard: de_DE
 * @var string $ogConfig['sitename']    Titel der Seite, wird auch als Präfix für das <title> Element genutzt
 * 
 * @see https://ogp.me/
 */
$ogConfig['name'] = "";
$ogConfig['imgAlt'] = "";
$ogConfig['description'] = "";
$ogConfig['locale'] = "de_DE";
$ogConfig['sitename'] = "";

/**
 * Speicherort der Bilder und Thumbnails
 */
$uploadDir = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."img";
?>
