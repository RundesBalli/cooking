<?php
/**
 * _cliLoader.php
 * 
 * Konfigurations- und Funktionsloader für CLI Scripts
 */

/**
 * Grundlegende Webseitenkonfiguration
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."_config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."timezone.php");

/**
 * Datenbank und Datenbankfunktionen
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."sql.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."defuse.php");
?>
