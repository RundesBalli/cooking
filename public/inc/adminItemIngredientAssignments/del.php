<?php
/**
 * adminItemIngredientAssignments/del.php
 * 
 * Entfernen einer Rezeptzutat
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Zutat löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Zutat löschen</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Rezeptzutat existiert.
   */
  $result = mysqli_query($dbl, "SELECT `itemIngredients`.*, `items`.`title`, `items`.`shortTitle`, `metaIngredients`.`title` AS `ingredientTitle` FROM `itemIngredients` JOIN `items` ON `items`.`id`=`itemIngredients`.`itemId` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` WHERE `itemIngredients`.`id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Rezeptzutat nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Rezeptzutat mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Rezeptzutat existiert
     */
    $row = mysqli_fetch_assoc($result);

    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Soll die Rezeptzutat <span class='highlight italic'>".output($row['ingredientTitle'])."</span> im Rezept <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a> wirklich gelöscht werden?</div>".
      "</div>";

      /**
       * Es wird ein "verwirrendes" Select-Feld gebaut, damit die "ja"-Option jedes mal woanders steht und man bewusster löscht.
       */
      $options = array(1 => "Ja, wirklich löschen", 2 => "nein, nicht löschen", 3 => "nope", 4 => "auf keinen Fall", 5 => "nö", 6 => "hab es mir anders überlegt");
      $options1 = array();
      foreach($options as $key => $val) {
        $options1[] = "<option value='".$key."'>".$val."</option>";
      }
      shuffle($options1);

      $content.= "<form action='/adminItemIngredientAssignments/del?id=".output($id)."' method='post' autocomplete='off'>";

      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-4'><select name='selection'>"."<option value='' selected disabled hidden>Bitte wählen</option>".implode("", $options1)."</select></div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Handeln'></div>".
      "<div class='col-s-0 col-l-4'></div>".
      "</div>";
      $content.= "</form>";
      $content.= "<div class='spacer-m'></div>";
    } else {
      /**
       * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
       */
      if(!empty($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Token Überprüfung
         */
        if($_POST['token'] == $sessionHash) {
          /**
           * Kann gelöscht werden
           */
          mysqli_query($dbl, "DELETE FROM `itemIngredients` WHERE `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 4, '".$row['itemId']."', 'Zutat gelöscht: `".defuse($row['ingredientTitle'])."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Rezeptzutat erfolgreich gelöscht.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminItemIngredientAssignments/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".
          "</div>";
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminItemIngredientAssignments/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Rezeptzutat wurde nicht gelöscht.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminItemIngredientAssignments/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      }
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
