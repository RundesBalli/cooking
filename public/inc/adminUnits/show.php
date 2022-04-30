<?php
/**
 * adminUnits/show.php
 * 
 * Anzeige aller Einheiten
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Einheiten anzeigen";
$content.= "<h1><span class='fas icon'>&#xf543;</span>Einheiten anzeigen</h1>";

/**
 * Links
 */
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminUnits/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Selektieren aller Maßeinheiten.
 */
$result = mysqli_query($dbl, "SELECT * FROM `metaUnits` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn keine Maßeinheiten existieren.
   */
  $content.= "<div class='infobox'>Noch keine Maßeinheiten angelegt.</div>";
} else {
  /**
   * Anzeige vorhandener Maßeinheiten.
   */
  $content.= "<div class='row highlight bold bordered'>".
  "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
  "<div class='col-s-6 col-l-2'>Kurzform</div>".
  "<div class='col-s-6 col-l-2'>Trennung</div>".
  "<div class='col-s-12 col-l-5'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-12 col-l-3'>".output($row['title'])."</div>".
    "<div class='col-s-6 col-l-2'>".output($row['short'])."</div>".
    "<div class='col-s-6 col-l-2'>".($row['spacer'] == 1 ? "Ja" : "Nein")."</div>".
    "<div class='col-s-12 col-l-5'><a href='/adminUnits/edit?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a> - "."<a href='/adminUnits/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
    "</div>";
  }
}
$content.= "<div class='spacer-m'></div>";
?>
