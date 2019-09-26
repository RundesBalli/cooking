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

$title = "Index";

$content.= "<h1>Index</h1>".PHP_EOL;
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='warn bold'>".$username."</span> - (<a href='/adminlogout'>Ausloggen</a>)</div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<h1>Zahlen, Daten, Fakten</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `stats`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Kategorien</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['cat_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Rezepte</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['item_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Anzahl Klicks</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['cat_count']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row hover'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Klicks heute</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['clicks_today']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='spacer-m'></div>";

$content.= "<h1>Am meisten geklickte Rezepte</h1>";
$result = mysqli_query($dbl, "SELECT * FROM `most_clicked`") OR DIE(MYSQLI_ERROR($dbl));
$top = 0;
while($row = mysqli_fetch_array($result)) {
  $top++;
  $content.= "<div class='row hover'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>#".$top." (".$row['c']." Klicks)</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'><a href='/rezept/".$row['shortTitle']."' target='_blank'>".$row['title']."</a></div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
}

$content.= "<div class='spacer-m'></div>";

$content.= "<h1>Am besten bewertete Rezepte</h1>";
$result = mysqli_query($dbl, "SELECT * FROM `best_voted`") OR DIE(MYSQLI_ERROR($dbl));
$top = 0;
while($row = mysqli_fetch_array($result)) {
  $top++;
  $content.= "<div class='row hover'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>#".$top." (".$row['a']." Sterne)</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-3 col-l-2 col-xl-2'>".stars($row['a'])."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-7 col-l-8 col-xl-8'><a href='/rezept/".$row['shortTitle']."' target='_blank'>".$row['title']."</a></div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
}


?>
