<?php
/**
 * UUID.php
 * 
 * Erzeugen des Unique-User-Identifier, wenn der Seitenaufruf im Browser und nicht
 * in der CLI erfolgte.
 */
if(!defined("cli")) {
  $UUID = hash(
    'sha256',
    $_SERVER['REMOTE_ADDR']."|".$_SERVER['HTTP_USER_AGENT']."|".$_SERVER['HTTP_ACCEPT']."|".$_SERVER['HTTP_ACCEPT_LANGUAGE']."|".$_SERVER['HTTP_ACCEPT_ENCODING']
  );
}
?>
