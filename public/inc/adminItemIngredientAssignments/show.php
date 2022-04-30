<?php
/**
 * adminItemIngredientAssignments/show.php
 * 
 * Anzeige der Zutatenliste eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Zutaten bearbeiten";
$content.= "<h1><span class='far icon'>&#xf07c;</span>Zutaten bearbeiten</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Grundlegende Infos anzeigen
     */
    $row = mysqli_fetch_assoc($result);
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
    $result = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `metaUnits`.`short`, `metaUnits`.`spacer`, `itemIngredients`.* FROM `itemIngredients` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` LEFT OUTER JOIN `metaUnits` ON `metaUnits`.`id` = `itemIngredients`.`unitId` WHERE `itemIngredients`.`itemId`='$id' ORDER BY `ingredientTitle` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Es wurden noch keine Zuweisungen angelegt.</div>";
    } else {
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><span class='highlight'>Info:</span> Zum Ändern einfach die selbe Zutat nochmal anlegen.</div>".
      "</div>";
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-7 col-l-4'>Zutat</div>".
      "<div class='col-s-2 col-l-2'>Menge</div>".
      "<div class='col-s-3 col-l-2'>Einheit</div>".
      "<div class='col-s-12 col-l-4'>Aktionen</div>".
      "</div>";
      while($row = mysqli_fetch_assoc($result)) {
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-7 col-l-4'>".output($row['ingredientTitle'])."</div>".
        "<div class='col-s-2 col-l-2'>".($row['quantity'] > 0 ? fractionizer($row['quantity'], 2) : "<span class='italic'>NULL</span>")."</div>".
        "<div class='col-s-3 col-l-2'>".($row['unitTitle'] == NULL ? "<span class='italic'>NULL</span>" : output($row['unitTitle']))."</div>".
        "<div class='col-s-12 col-l-4'><a href='/adminItemIngredientAssignments/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
        "</div>";
      }
    }
    $content.= "<div class='spacer-m'></div>";
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
