<?php
/**
 * adminItemAssignments/show.php
 * 
 * Anzeige der Kategoriezuweisungen eines Rezeptes.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Kategoriezuweisungen anzeigen";
$content.= "<h1><span class='far icon'>&#xf07c;</span>Kategoriezuweisungen anzeigen</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['itemId'])) {
  $itemId = (int)defuse($_GET['itemId']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle` FROM `items` WHERE `id`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($itemId)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Grundlegende Infos anzeigen
     */
    $row = mysqli_fetch_array($result);
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><span class='highlight bold'>Rezept:</span> <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
    $content.= "<div class='spacer-m'></div>";

    /**
     * Bestehende Zuweisungen anzeigen
     */
    $content.= "<h2>Bestehende Zuweisungen</h2>";
    $result = mysqli_query($dbl, "SELECT `categoryItems`.`id`, `categoryItems`.`categoryId`, `categories`.`title` FROM `categoryItems` LEFT JOIN `categories` ON `categoryItems`.`categoryId`=`categories`.`id` WHERE `categoryItems`.`itemId`='".$itemId."'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Dieses Rezept wurde noch keiner Kategorie zugewiesen.</div>";
    } else {
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-8 col-l-8'>Kategorie</div>".
      "<div class='col-s-4 col-l-4'>Aktionen</div>".
      "</div>";
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-8 col-l-8'>".$row['title']."</div>".
        "<div class='col-s-4 col-l-4'><a href='/adminItemAssignments/del?itemId=".output($itemId)."&categoryId=".$row['categoryId']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a> - "."<a href='/adminCategories/itemSort?id=".$row['categoryId']."' class='nowrap'><span class='fas icon'>&#xf0dc;</span>in dieser Kategorie sortieren</a></div>".
        "</div>";
      }
    }

    /**
     * Neue Zuweisungen erstellen
     */
    $content.= "<div class='spacer-m'></div>";
    $content.= "<h2>Neu zuweisen</h2>";
    $content.= "<div class='row highlight bold bordered'>".
    "<div class='col-s-8 col-l-8'>Kategorie</div>".
    "<div class='col-s-4 col-l-4'>Aktionen</div>".
    "</div>";
    $result = mysqli_query($dbl, "SELECT `categories`.`id`, `categories`.`title`, `categories`.`shortTitle`, (SELECT COUNT(`id`) FROM `categoryItems` WHERE `categoryItems`.`categoryId`=`categories`.`id` AND `categoryItems`.`itemId`='".$itemId."') AS `isset` FROM `categories` ORDER BY `categories`.`sortIndex` ASC, `categories`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-8 col-l-8'><a href='/kategorie/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
      "<div class='col-s-4 col-l-4'>".($row['isset'] == 1 ? "bereits zugewiesen" : "<a href='/adminItemAssignments/add?itemId=".output($itemId)."&categoryId=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf067;</span>Hinzufügen</a>")."</div>".
      "</div>";
    }
  }
} else {
  /**
   * Es wurde keine ID übergeben
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Es wurde keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
