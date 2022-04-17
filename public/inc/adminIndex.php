<?php
/**
 * adminIndex.php
 * 
 * Übersichtsseite für Administratoren
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Titel und Überschrift
 */
$title = "Übersicht";
$content.= "<h1>Übersicht</h1>";

/**
 * Allgemeine Infos und Links
 */
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'>Eingeloggt als: <span class='warn bold'>".$username."</span></div>".
"</div>";

/**
 * Statistiken
 */
$content.= "<h2><span class='fas icon'>&#xf0cb;</span>Zahlen, Daten, Fakten</h2>";
$content.= "<div class='row highlight bold'>".
"<div class='col-s-12 col-l-3'>Bezeichnung</div>".
"<div class='col-s-12 col-l-9'>Wert</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

/**
 * Abrufen und Ausgeben des "stats" SQL-Views.
 */
$result = mysqli_query($dbl, "SELECT * FROM `stats`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Anzahl Kategorien</div>".
"<div class='col-s-12 col-l-9'>".$row['catCount']."</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Anzahl Rezepte</div>".
"<div class='col-s-12 col-l-9'>".$row['itemCount']."</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Anzahl Klicks</div>".
"<div class='col-s-12 col-l-9'>".$row['clickCount']."</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Klicks heute</div>".
"<div class='col-s-12 col-l-9'>".$row['clicksToday']."</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Abrufen uns Ausgeben des "mostClicked" SQL Views zur Anzeige der meist geklickten Rezepte.
 */
$content.= "<h2><span class='far icon'>&#xf25a;</span>Am meisten geklickte Rezepte</h2>";
$result = mysqli_query($dbl, "SELECT * FROM `mostClicked`") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn noch keine Klicks vorhanden, dann kurze Info.
   */
  $content.= "<div class='infobox'>Noch keine Klicks.</div>";
} else {
  /**
   * Wenn Klicks vorhanden sind, dann erfolgt die Auflistung.
   */
  $top = 0;
  $content.= "<div class='row highlight bold'>".
  "<div class='col-s-12 col-l-3'>Platz / Klicks</div>".
  "<div class='col-s-12 col-l-9'>Rezept</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $top++;
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-12 col-l-3'><span class='highlight'>#".$top."</span> (".number_format($row['c'], 0, ",", ".")." Klicks)</div>".
    "<div class='col-s-12 col-l-9'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
    "</div>";
  }
}
$content.= "<div class='spacer-m'></div>";

/**
 * Wenn leere Kategorien existieren, dann werden sie hier aufgeführt.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE NOT EXISTS (SELECT * FROM `categoryItems` WHERE `categories`.`id`=`categoryItems`.`categoryId`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>";
  $content.= "<h2 class='warn'><span class='far icon'>&#xf07c;</span>Leere Kategorien</h2>";
  $content.= "<div class='row highlight bold'>".
  "<div class='col-s-12 col-l-12'>Titel</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-12 col-l-12'>".output($row['title'])."</div>".
    "</div>";
  }
}

/**
 * Wenn nicht zugewiesene Rezepte existieren, dann werden sie hier aufgeführt.
 */
$result = mysqli_query($dbl, "SELECT `id`, `shortTitle`, `title` FROM `items` WHERE NOT EXISTS (SELECT * FROM `categoryItems` WHERE `items`.`id`=`categoryItems`.`itemId`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>";
  $content.= "<h2 class='warn'><span class='fas icon'>&#xf543;</span>Nicht in Kategorien eingeteilte Rezepte</h2>";
  $content.= "<div class='row highlight bold'>".
  "<div class='col-s-12 col-l-6'>Titel</div>".
  "<div class='col-s-12 col-l-6'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-12 col-l-6'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-s-12 col-l-6'><a href='/adminItems/edit?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Bearbeiten</a> - <a href='/adminItemAssignments/show?itemId=".$row['id']."' class='nowrap'><span class='far icon'>&#xf07c;</span>Zuweisen</a></div>".
    "</div>";
  }
}
?>
