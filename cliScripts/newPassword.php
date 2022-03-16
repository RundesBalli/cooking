<?php
/**
 * newPassword.php
 * 
 * Datei zum Ändern eines Administratorpassworts in ein neues, zufälliges Passwort.
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
  die("Der Name ist ungültig. Er muss zwischen 3 und 32 Zeichen lang sein und darf keine Sonderzeichen enthalten (0-9a-zA-Z).\n\nBeispielaufruf:\nphp ".$argv[0]." Hans\nSetzt ein zufälliges Passwort beim Benutzer \"Hans\".\n\n");
}

/**
 * Erzeugen eines zufälligen Passworts.
 */
$passwordClear = hash('md5', random_bytes(4096));
$salt = hash('sha256', random_bytes(4096));
$password = password_hash($passwordClear.$salt, PASSWORD_DEFAULT);

/**
 * Updaten des bestehenden Nutzers.
 */
mysqli_query($dbl, "UPDATE `accounts` SET `password`='".$password."', `salt`='".$salt."' WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_affected_rows($dbl) == 1) {
  $result = mysqli_query($dbl, "SELECT `id` FROM `accounts` WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  $row = mysqli_fetch_assoc($result);
  mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$row['id']."', 1, 'Passwort geändert (CLI)')") OR DIE(MYSQLI_ERROR($dbl));
  die("Passwort erfolgreich geändert.\n\nNeue Zugangsdaten:\nUser: ".$username."\nPass: ".$passwordClear."\n\n");
} else {
  die("Dieser Account existiert nicht.\n\n");
}
?>
