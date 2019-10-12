<?php
/**
 * adminitems.php
 * 
 * Seite um Rezepte anzuzeigen, anzulegen, zu bearbeiten, zu löschen und zuzuweisen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezepte.
   */
  header("Location: /adminitems/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflisten aller Rezepte.
   */
} elseif($_GET['action'] == 'add') {
  /**
   * Hinzufügen eines Rezepts.
   */
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen eines Rezepts.
   */
} elseif($_GET['action'] == 'edit') {
  /**
   * Bearbeiten eines Rezepts.
   */
} elseif($_GET['action'] == 'assign') {
  /**
   * Zuweisen eines Rezepts in eine Kategorie.
   */
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminitems/list");
  die();
}
?>
