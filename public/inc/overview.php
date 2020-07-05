<?php
/**
 * overview.php
 * 
 * Übersichtsseite
 */

/**
 * Titel
 */
$title = "Übersicht";
$content.= "<h1>Übersicht</h1>".PHP_EOL;

/**
 * Sessionüberprüfung
 */
require_once('cookieCheck.php');

/**
 * Selektion der Vote- und Favoriten-Anzahl und Ausgabe ebendieser.
 */
$result = mysqli_query($dbl, "SELECT (SELECT COUNT(*) FROM `votes` WHERE `userId`='".$userId."') AS `voteCount`, (SELECT COUNT(*) FROM `favs` WHERE `userId`='".$userId."') AS `favCount`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);

$content.= "<h2><span class='fas icon'>&#xf0cb;</span>Zahlen, Daten, Fakten</h2>".PHP_EOL;
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Bezeichnung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>Anzahl</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'><span class='fas icon'>&#xf772;</span>Votes</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['voteCount']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'><span class='fas icon'>&#xf005;</span><a href='/favs'>Favoriten</a></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-9 col-l-9 col-xl-9'>".$row['favCount']."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
?>
