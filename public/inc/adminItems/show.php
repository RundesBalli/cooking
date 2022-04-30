<?php
/**
 * adminItems/show.php
 * 
 * Anzeige aller Rezepte
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Rezepte anzeigen";
$content.= "<h1><span class='fas icon'>&#xf543;</span>Rezepte anzeigen</h1>";

/**
 * Menü
 */
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminItems/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Selektion aller Rezepte
 */
$result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, IFNULL((SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `clicks`.`itemId`=`items`.`id`), 0) AS `clicks` FROM `items` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Wenn keine Rezepte existieren.
   */
  $content.= "<div class='infobox'>Noch keine Rezepte angelegt.</div>";
} else {
  /**
   * Anzeige vorhandener Rezepte.
   */
  $content.= "<div class='row highlight bold bordered mobileSmaller'>".
  "<div class='col-s-8 col-l-4'>Titel</div>".
  "<div class='col-s-4 col-l-1'>Klicks</div>".
  "<div class='col-s-12 col-l-2'>Kategorien</div>".
  "<div class='col-s-12 col-l-5'>Aktionen</div>".
  "</div>";
  while($row = mysqli_fetch_array($result)) {
    $innerresult = mysqli_query($dbl, "SELECT `categories`.`title`, `categories`.`shortTitle` FROM `categoryItems` LEFT JOIN `categories` ON `categoryItems`.`categoryId`=`categories`.`id` WHERE `categoryItems`.`itemId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($innerresult) == 0) {
      $categories = "keine";
    } else {
      $categories = array();
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $categories[] = "<a href='/kategorie/".output($innerrow['shortTitle'])."' target='_blank'>".output($innerrow['title'])."<span class='fas iconright'>&#xf35d;</span></a>";
      }
      $categories = implode("<br>", $categories);
    }
    $content.= "<div class='row hover bordered mobileSmaller'>".
    "<div class='col-s-8 col-l-4'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-s-4 col-l-1'>".number_format($row['clicks'], 0, ",", ".")."</div>".
    "<div class='col-s-12 col-l-2'>".$categories."</div>".
    "<div class='col-s-12 col-l-5'><a href='/adminItems/edit?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a> - "."<a href='/adminItems/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a> - "."<a href='/adminCategoryItemAssignments/show?itemId=".$row['id']."' class='nowrap'><span class='far icon'>&#xf07c;</span>Kategorien</a> - "."<a href='/adminFiles/show?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf302;</span>Bilder</a> - "."<a href='/adminIngredients/assign?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf4d8;</span>Zutaten</a></div>".
    "</div>";
  }
}
?>
