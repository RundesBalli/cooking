<?php
/**
 * adminIngredients/show.php
 * 
 * Anzeige aller Zutaten
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Zutaten anzeigen";
$content.= "<h1><span class='fas icon'>&#xf543;</span>Zutaten anzeigen</h1>";

/**
 * Links
 */
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminIngredients/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".
"</div>";

/**
 * Selektieren aller Zutaten.
 */
$result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn keine Zutaten existieren.
   */
  $content.= "<div class='infobox'>Noch keine Zutaten angelegt.</div>";
} else {
  /**
   * Anzeige vorhandener Zutaten.
   */
  $content.= "<div class='row highlight bold bordered'>".
  "<div class='col-s-7 col-l-3'>Bezeichnung</div>".
  "<div class='col-s-5 col-l-2'>Suchbar</div>".
  "<div class='col-s-12 col-l-7'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover bordered'>".
    "<div class='col-s-7 col-l-3'>".output($row['title'])."</div>".
    "<div class='col-s-5 col-l-2'>".($row['searchable'] == 1 ? "Ja" : "Nein")."</div>".
    "<div class='col-s-12 col-l-7'><a href='/adminIngredients/edit?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a> - "."<a href='/adminIngredients/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
    "</div>";
  }
}
$content.= "<div class='spacer-m'></div>";
?>
