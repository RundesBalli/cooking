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
  $title = "Rezepte anzeigen";
  $content.= "<h1>Rezepte anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminitems/add'>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
} elseif($_GET['action'] == 'add') {
  /**
   * Hinzufügen eines Rezepts.
   */
  $title = "Rezept hinzufügen";
  $content.= "<h1>Rezept hinzufügen</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen eines Rezepts.
   */
  $title = "Rezept löschen";
  $content.= "<h1>Rezept löschen</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'edit') {
  /**
   * Bearbeiten eines Rezepts.
   */
  $title = "Rezept bearbeiten";
  $content.= "<h1>Rezept bearbeiten</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'assign') {
  /**
   * Zuweisen eines Rezepts in eine Kategorie.
   */
  $title = "Rezept einer Kategorie hinzufügen";
  $content.= "<h1>Rezept einer Kategorie hinzufügen</h1>".PHP_EOL;
  
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminitems/list");
  die();
}
?>
