<?php
/**
 * adminindex.php
 * 
 * Übersichtsseite für Administratoren
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

/**
 * Titel und Überschrift
 */
$title = "Index";
$content.= "<h1>Index</h1>".PHP_EOL;

/**
 * Allgemeine Infos und Links
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='warn bold'>".$username."</span> - (<a href='/adminlogout'>Ausloggen</a>) - <a href='/adminmarkdowninfo'>Markdown Info</a></div>".PHP_EOL.
"</div>".PHP_EOL;

/**
 * Statistiken
 */
$content.= "<h1>Zahlen, Daten, Fakten</h1>".PHP_EOL;
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Bezeichnung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>Wert</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Abrufen und Ausgeben des "stats" SQL-Views.
 */
$result = mysqli_query($dbl, "SELECT * FROM `stats`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Kategorien</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['cat_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Rezepte</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['item_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Klicks</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['cat_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Klicks heute</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['clicks_today']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Abrufen uns Ausgeben des "most_clicked" SQL Views zur Anzeige der meist geklickten Rezepte.
 */
$content.= "<h1>Am meisten geklickte Rezepte</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `most_clicked`") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn noch keine Klicks vorhanden, dann kurze Info.
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Noch keine Klicks.</div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Wenn Klicks vorhanden sind, dann erfolgt die Auflistung.
   */
  $top = 0;
  $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Platz / Klicks</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>Rezept</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $top++;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'><span class='highlight'>#".$top."</span> (".$row['c']." Klicks)</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."</a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Abrufen uns Ausgeben des "best_voted" SQL Views zur Anzeige der am besten bewerteten Rezepte.
 */
$content.= "<h1>Am besten bewertete Rezepte</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `best_voted`") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn noch keine Bewertungen vorhanden, dann kurze Info.
   */
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Noch keine Bewertungen.</div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Wenn Bewertungen vorhanden sind, dann erfolgt die Auflistung.
   */
  $top = 0;
  $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Platz / Sterne</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-2 col-xl-2'>Sterne</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-7 col-l-8 col-xl-8'>Rezept</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $top++;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><span class='highlight'>#".$top."</span> (".$row['a']." Sterne)</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-3 col-l-2 col-xl-2'>".stars($row['a'])."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-7 col-l-8 col-xl-8'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."</a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}

/**
 * Wenn leere Kategorien existieren, dann werden sie hier aufgeführt.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE NOT EXISTS (SELECT * FROM `category_items` WHERE `categories`.`id`=`category_items`.`category_id`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $content.= "<h1 class='warn'>Leere Kategorien</h1>".PHP_EOL;
  $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Titel</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>".output($row['title'])."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}

/**
 * Wenn nicht zugewiesene Rezepte existieren, dann werden sie hier aufgeführt.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE NOT EXISTS (SELECT * FROM `category_items` WHERE `items`.`id`=`category_items`.`item_id`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $content.= "<h1 class='warn'>Nicht in Kategorien eingeteilte Rezepte</h1>".PHP_EOL;
  $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>Titel</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Aktionen</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".output($row['title'])."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'><a href='/adminitems/edit/".$row['id']."' class='nowrap'>Bearbeiten</a><br><a href='/adminitems/assign/".$row['id']."' class='nowrap'>Zuweisen</a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
