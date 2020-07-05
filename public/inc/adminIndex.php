<?php
/**
 * adminIndex.php
 * 
 * Übersichtsseite für Administratoren
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

/**
 * Titel und Überschrift
 */
$title = "Index";
$content.= "<h1>Index</h1>".PHP_EOL;

/**
 * Allgemeine Infos und Links
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='warn bold'>".$username."</span> - (<a href='/adminLogout'><span class='fas icon'>&#xf2f5;</span>Ausloggen</a>) - <a href='/adminMarkdownInfo'><span class='fab icon'>&#xf60f;</span>Markdown Info</a></div>".PHP_EOL.
"</div>".PHP_EOL;

/**
 * Statistiken
 */
$content.= "<h2><span class='fas icon'>&#xf0cb;</span>Zahlen, Daten, Fakten</h2>".PHP_EOL;
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
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['catCount']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Rezepte</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['itemCount']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Klicks</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['clickCount']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Klicks heute</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['clicksToday']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Abrufen uns Ausgeben des "mostClicked" SQL Views zur Anzeige der meist geklickten Rezepte.
 */
$content.= "<h2><span class='far icon'>&#xf25a;</span>Am meisten geklickte Rezepte</h2>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `mostClicked`") OR DIE(MYSQLI_ERROR($dbl));
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
    "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Abrufen uns Ausgeben des "bestVoted" SQL Views zur Anzeige der am besten bewerteten Rezepte.
 */
$content.= "<h2><span class='fas icon'>&#xf5a2;</span>Am besten bewertete Rezepte</h2>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `bestVoted`") OR DIE(MYSQLI_ERROR($dbl));
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
    "<div class='col-x-12 col-s-12 col-m-7 col-l-8 col-xl-8'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}

/**
 * Wenn leere Kategorien existieren, dann werden sie hier aufgeführt.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE NOT EXISTS (SELECT * FROM `categoryItems` WHERE `categories`.`id`=`categoryItems`.`categoryId`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $content.= "<h2 class='warn'><span class='far icon'>&#xf07c;</span>Leere Kategorien</h2>".PHP_EOL;
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
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE NOT EXISTS (SELECT * FROM `categoryItems` WHERE `items`.`id`=`categoryItems`.`itemId`)") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $content.= "<h2 class='warn'><span class='fas icon'>&#xf543;</span>Nicht in Kategorien eingeteilte Rezepte</h2>".PHP_EOL;
  $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>Titel</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Aktionen</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".output($row['title'])."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'><a href='/adminItems/edit/".$row['id']."' class='nowrap'>Bearbeiten</a><br><a href='/adminItems/assign/".$row['id']."' class='nowrap'>Zuweisen</a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
