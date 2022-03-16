<?php
/**
 * addAccount.php
 * 
 * Datei zum Anlegen eines Administratoraccounts.
 * 
 * @param string $argv[1] Benutzername
 */

/**
 * Einbinden des Konfigurations- und Funktionsloaders
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."_cliLoader.php");

/**
 * Prüfen ob das Script in der Konsole läuft.
 */
if(php_sapi_name() != 'cli') {
  die("Das Script darf nur in der Konsole aufgerufen werden.\n\n");
}

/**
 * Auslesen und verarbeiten des Nutzernamens.
 */
if(isset($argv[1]) AND preg_match('/^[0-9a-zA-Z]{3,32}$/', defuse($argv[1]), $match) === 1) {
  $username = $match[0];
} else {
  die("Der Name ist ungültig. Er muss zwischen 3 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).\n\nBeispielaufruf:\nphp ".$argv[0]." Hans\nErstellt einen Nutzer \"Hans\".\n\n");
}

/**
 * Erzeugen eines zufälligen Passworts.
 */
$passwordClear = hash('md5', random_bytes(4096));
$salt = hash('sha256', random_bytes(4096));
$password = password_hash($passwordClear.$salt, PASSWORD_DEFAULT);

/**
 * Eintragen des neuen Nutzers.
 */
if(mysqli_query($dbl, "INSERT INTO `accounts` (`username`, `password`, `salt`) VALUES ('".$username."', '".$password."', '".$salt."')")) {
  mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".mysqli_insert_id($dbl)."', 1, 'Account angelegt (CLI)')") OR DIE(MYSQLI_ERROR($dbl));
  die("Account erfolgreich angelegt.\n\nZugangsdaten:\nUser: ".$username."\nPass: ".$passwordClear."\n\n");
} elseif(mysqli_errno($dbl) == 1062) {
  die("Es existiert bereits ein Account mit diesem Namen.\n\n");
} else {
  die("Unbekannter Fehler: ".mysqli_error($dbl)."\n\n");
}
?>
