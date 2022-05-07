<?php
/**
 * adminItemIngredientAssignments/add.php
 * 
 * Hinzufügen einer Zutat zu einem Rezept
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Zutat hinzufügen";
$content.= "<h1><span class='fas icon'>&#xf4d8;</span>Zutat hinzufügen</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_POST['itemId'])) {
  $id = (int)defuse($_POST['itemId']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `shortTitle` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
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
     * Wir gehen davon aus, dass die Eingaben korrekt sind. Falls nicht, wird der Vorgang abgebrochen.
     */
    $add = 1;
    $row = mysqli_fetch_assoc($result);
    $shortTitle = $row['shortTitle'];

    /**
     * Zutat
     */
    if(!empty($_POST['ingredient'])) {
      $ingredient = (int)defuse($_POST['ingredient']);
      $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaIngredients` WHERE `id`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $add = 0;
        $content.= "<div class='warnbox'>Die Zutat existiert nicht.</div>";
      } else {
        $row = mysqli_fetch_assoc($result);
        $ingredientTitle = $row['title'];
      }
    } else {
      $add = 0;
      $content.= "<div class='warnbox'>Die Zutat ist ungültig.</div>";
    }

    /**
     * Menge & Maßeinheit
     */
    if(empty($_POST['quantity']) OR floatval(str_replace(",", ".", $_POST['quantity'])) <= 0) {
      $quantity = NULL;
      $unit = NULL;
    } else {
      $quantity = (float)str_replace(",", ".", defuse($_POST['quantity']));
      /**
       * Wenn eine Positive Menge übergeben wurde, dann ist die Maßeinheit relevant.
       */
      if(!empty($_POST['unit'])) {
        $unit = (int)defuse($_POST['unit']);
        $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaUnits` WHERE `id`='".$unit."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $add = 0;
          $content.= "<div class='warnbox'>Die Maßeinheit existiert nicht.</div>";
        } else {
          $row = mysqli_fetch_assoc($result);
          $unitTitle = $row['title'];
        }
      } else {
        $add = 0;
        $content.= "<div class='warnbox'>Die Maßeinheit ist ungültig.</div>";
      }
    }

    /**
     * Optional
     */
    if(!empty($_POST['optional']) AND intval($_POST['optional']) == 1) {
      $optional = 1;
    } else {
      $optional = 0;
    }

    if($add == 1) {
      /**
       * Alle Daten sind ok und können angelegt oder aktualisiert werden.
       */
      /**
       * Prüfung, ob die Kombination itemId/ingredientId schon existiert.
       */
      $result = mysqli_query($dbl, "SELECT `id` FROM `itemIngredients` WHERE `itemId`='".$id."' AND `ingredientId`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 1) {
        /**
         * Kombination existiert. Zuweisung wird aktualisiert.
         */
        mysqli_query($dbl, "UPDATE `itemIngredients` SET `unitId`=".($unit === NULL ? NULL : "'".$unit."'").", `quantity`=".($quantity === NULL ? "NULL" : "'".$quantity."'").", `optional`=".$optional." WHERE `itemId`='".$id."' AND `ingredientId`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_affected_rows($dbl) == 1) {
          /**
           * Aktualisierung erfolgreich.
           */
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 6, '".$id."', 'Zuweisung aktualisiert: qty: ".($quantity === NULL ? "NULL" : "`".$quantity."`")."; unit: ".($unit === NULL ? "NULL" : "`".$unitTitle."`")."; ing: `".$ingredientTitle."`, opt: `".$optional."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Zuweisung aktualisiert.</div>";
        } else {
          /**
           * Aktualisierung schlug fehl.
           */
          $content.= "<div class='warnbox'>Zuweisung konnte nicht aktualisiert werden.</div>";
        }
      } else {
        /**
         * Kombination existiert nicht. Zuweisung wird angelegt.
         */
        mysqli_query($dbl, "INSERT INTO `itemIngredients` (`itemId`, `ingredientId`, `unitId`, `quantity`, `optional`) VALUES ('".$id."', '".$ingredient."', ".($unit === NULL ? "NULL" : "'".$unit."'").", ".($quantity === NULL ? "NULL" : "'".$quantity."'").", ".$optional.")") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_affected_rows($dbl) == 1) {
          /**
           * Eintrag erfolgreich.
           */
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 6, '".$id."', 'Zuweisung angelegt: qty: ".($quantity === NULL ? "NULL" : "`".$quantity."`")."; unit: ".($unit === NULL ? "NULL" : "`".$unitTitle."`")."; ing: `".$ingredientTitle."`, opt: `".$optional."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Zuweisung angelegt.</div>";
        } else {
          /**
           * Eintrag schlug fehl.
           */
          $content.= "<div class='warnbox'>Zuweisung konnte nicht angelegt werden.</div>";
        }
      }
    }
    /**
     * Navigationslinks für alle durchgeführten Vorgänge
     */
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "<div class='col-s-12 col-l-12'><a href='/adminItemIngredientAssignments/show?id=".output($id)."'><span class='fas icon'>&#xf4d8;</span>Zutatenpflege</a></div>".
    "<div class='col-s-12 col-l-12'><a href='/rezept/".output($shortTitle)."'><span class='fas icon'>&#xf543;</span>Zum Rezept</a></div>".
    "</div>";
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
