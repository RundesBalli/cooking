<?php
/**
 * adminFeaturedItems/show.php
 * 
 * Anzeige der auf der Startseite vorgestellten Rezepte
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Rezeptvorstellungen anzeigen";
$content.= "<h1><span class='fas icon'>&#xf005;</span>Rezeptvorstellungen anzeigen</h1>";

/**
 * Menü
 */
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminFeaturedItems/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Anzeige der vorgestellten Rezepte
 */
$result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `featured`.`timestamp` FROM `featured` JOIN `items` ON `featured`.`itemId` = `items`.`id` ORDER BY `featured`.`id` DESC LIMIT 4") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn keine Rezeptvorstellungen existieren.
   */
  $content.= "<div class='infobox'>Derzeit keine Rezepte vorgestellt.</div>";
} else {
  /**
   * Anzeige vorhandener Rezepte.
   */
  $content.= "<div class='row highlight bold bordered'>".
  "<div class='col-s-12 col-l-4'>Titel</div>".
  "<div class='col-s-6 col-l-3'>Angelegt am</div>".
  "<div class='col-s-6 col-l-3'>Angezeigt bis</div>".
  "<div class='col-s-12 col-l-2'>Aktionen</div>".
  "<div class='col-s-0 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-12 col-l-4'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-s-6 col-l-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".
    "<div class='col-s-6 col-l-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp'])+86400*14)."</div>".
    "<div class='col-s-12 col-l-2'><a href='/adminFeaturedItems/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
    "<div class='col-s-0 col-l-0'><div class='spacer-s'></div></div>".
    "</div>";
  }
}
?>
