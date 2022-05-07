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
$content.= "<h1><span class='fas icon'>&#xf4d8;</span>Zutaten bearbeiten</h1>";

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
     * Neue Rezeptzutat anlegen
     */
    $content.= "<h2>Neue Rezeptzutat anlegen</h2>";
    $form = 1;

    /**
     * Selektieren der Zutaten
     */
    $result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $form = 0;
      $content.= "<div class='warnbox'>Es müssen zuerst Zutaten angelegt werden.</div>";
    } else {
      $ingredients = array();
      while($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = "<option value='".output($row['id'])."'>".output($row['title'])."</option>";
      }
      $ingredients = "<select name='ingredient' tabindex='1' autofocus><option value='' selected disabled hidden>Bitte wählen</option>".implode("", $ingredients)."</select>";
    }

    /**
     * Selektieren der Einheiten
     */
    $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $form = 0;
      $content.= "<div class='warnbox'>Es müssen zuerst Maßeinheiten angelegt werden.</div>";
    } else {
      $units = array();
      while($row = mysqli_fetch_assoc($result)) {
        $units[] = "<option value='".output($row['id'])."'>".output($row['title'])."</option>";
      }
      $units = "<select name='unit' tabindex='3'><option value='' selected disabled hidden>Bitte wählen</option>".implode("", $units)."</select>";
    }

    /**
     * Formular zur Anlage einer Rezeptzutat anzeigen, wenn Daten vorhanden.
     */
    if($form == 1) {
      $content.= "<form action='/adminItemIngredientAssignments/add' method='post' autocomplete='off'>";

      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";

      /**
       * ItemID
       */
      $content.= "<input type='hidden' name='itemId' value='".output($id)."'>";

      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
      "<div class='col-s-12 col-l-4'>Feld</div>".
      "<div class='col-s-12 col-l-5'>Ergänzungen</div>".
      "</div>";

      /**
       * Zutat
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Zutat</div>".
      "<div class='col-s-12 col-l-4'>".$ingredients."</div>".
      "<div class='col-s-12 col-l-5'><a href='/adminIngredients/add' target='_blank'><span class='fas icon'>&#xf4d8;</span>Zutat hinzufügen<span class='fas iconright'>&#xf35d;</span></a><br><a href='/adminItemIngredientAssignments/show?id=".output($id)."'><span class='fas icon'>&#xf021;</span>Neu laden</a></div>".
      "</div>";

      /**
       * Menge
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Menge</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='quantity' tabindex='2' placeholder='Menge'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* Kommazahlen möglich\n* Wenn die Menge = 0 ist, wird die Zutat einfach so angezeigt\n* 0xSalz = Salz")."</div>".
      "</div>";

      /**
       * Einheit
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Einheit</div>".
      "<div class='col-s-12 col-l-4'>".$units."</div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* Wird ignoriert, wenn Menge = 0")."<a href='/adminUnits/add' target='_blank'><span class='fas icon'>&#xf496;</span>Einheit hinzufügen<span class='fas iconright'>&#xf35d;</span></a><br><a href='/adminItemIngredientAssignments/show?id=".output($id)."'><span class='fas icon'>&#xf021;</span>Neu laden</a></div>".
      "</div>";

      /**
       * Art der Zutat
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Art der Zutat</div>".
      "<div class='col-s-12 col-l-4'><input type='radio' name='optional' value='0' id='optional-required' checked><label for='optional-required'>Erforderliche Zutat</label><br><input type='radio' name='optional' value='1' id='optional-optional'><label for='optional-optional'>Optionale Zutat</label></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* optionale Zutaten werden im Rezept separat aufgeführt\n* dies kann zum Beispiel ein bestimmtes Gewürz sein")."</div>".
      "</div>";

      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Zutat zuweisen</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";
      $content.= "</form>";
    }

    /**
     * Bestehende Zuweisungen anzeigen
     */
    $content.= "<h2>Bestehende Rezeptzutaten</h2>";
    $result = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `itemIngredients`.* FROM `itemIngredients` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` LEFT OUTER JOIN `metaUnits` ON `metaUnits`.`id` = `itemIngredients`.`unitId` WHERE `itemIngredients`.`itemId`='$id' ORDER BY `optional` ASC, `ingredientTitle` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Es wurden noch keine Rezeptzutaten hinzugefügt.</div>";
    } else {
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><span class='highlight'>Hinweis:</span> Zum Ändern einfach die selbe Zutat nochmal anlegen.</div>".
      "</div>";
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-7 col-l-3'>Zutat</div>".
      "<div class='col-s-2 col-l-2'>Menge</div>".
      "<div class='col-s-3 col-l-2'>Einheit</div>".
      "<div class='col-s-3 col-l-2'>Optional</div>".
      "<div class='col-s-12 col-l-3'>Aktionen</div>".
      "</div>";
      while($row = mysqli_fetch_assoc($result)) {
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-7 col-l-3'>".output($row['ingredientTitle'])."</div>".
        "<div class='col-s-2 col-l-2'>".($row['quantity'] > 0 ? fractionizer($row['quantity'], 2) : "<span class='italic'>NULL</span>")."</div>".
        "<div class='col-s-3 col-l-2'>".($row['unitTitle'] == NULL ? "<span class='italic'>NULL</span>" : output($row['unitTitle']))."</div>".
        "<div class='col-s-3 col-l-2'>".($row['optional'] == 1 ? "ja" : "nein")."</div>".
        "<div class='col-s-12 col-l-3'><a href='/adminItemIngredientAssignments/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
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
