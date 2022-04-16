<?php
/**
 * adminCategories/show.php
 * 
 * Auflistung aller Kategorien mit Anzahl der darin befindlichen Rezepte.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Kategorien anzeigen";
$content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorien anzeigen</h1>";
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminCategories/add'><span class='fas icon'>&#xf067;</span>Anlegen</a> - <a href='/adminCategories/catsort'><span class='fas icon'>&#xf0dc;</span>Kategorien sortieren</a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Selektieren aller Kategorien und Zählung der zugewiesenen Rezepte. Danke an @Insax für den Query.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle`, (SELECT COUNT(`id`) FROM `categoryItems` WHERE `categoryItems`.`categoryId` = `categories`.`id`) AS `itemCount` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn keine Kategorien existieren.
   */
  $content.= "<div class='infobox'>Noch keine Kategorien angelegt.</div>";
} else {
  /**
   * Anzeige vorhandener Kategorien.
   */
  $content.= "<div class='row highlight bold bordered'>".
  "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Titel</div>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>zugewiesene Rezepte</div>".
  "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><a href='/kategorie/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$row['itemCount']." Rezept".($row['itemCount'] == 1 ? "" : "e")."</div>".
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminCategories/edit?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a><br>"."<a href='/adminCategories/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a><br>"."<a href='/adminCategories/itemSort?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf0dc;</span>Rezepte in Kategorie sortieren</a></div>".
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".
    "</div>";
  }
}
?>
