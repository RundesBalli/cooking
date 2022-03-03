<?php
/**
 * _loader.php
 * 
 * Konfigurations- und Funktionsloader
 */

/**
 * Grundlegende Webseitenkonfiguration
 */
require_once("./_config.php");
require_once("./timezone.php");

/**
 * Einbinden der OG Metadaten
 */
require_once("./ogMeta.php");

/**
 * Datenbank und Datenbankfunktionen
 */
require_once("./sql.php");
require_once("./defuse.php");

/**
 * Einbinden grundlegender Webseitenfunktionen
 */
require_once("./fractionizer.php");
require_once("./output.php");
require_once("./UUID.php");

/**
 * Slimdown Markdownparser
 */
require_once("./slimdown.php");
?>
