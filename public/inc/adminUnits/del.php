<?php
/**
 * adminUnits/del.php
 * 
 * Löschen einer Einheit
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Einheit Löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Einheit Löschen</h1>";

/**
 * Prüfen ob eine ID übergeben wurde
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Einheit existiert.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Einheit nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Einheit mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Wenn die Einheit existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_assoc($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Soll die Einheit <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Dies funktioniert nur, wenn sie in keinem Rezept verwendet wird.</div>".
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

      $content.= "<form action='/adminUnits/del?id=".$id."' method='post' autocomplete='off'>";

      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";

      /**
       * Selektion
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-4'><select name='selection'>"."<option value='' selected disabled hidden>Bitte wählen</option>".implode("", $options1)."</select></div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Handeln'></div>".
      "<div class='col-s-0 col-l-4'></div>".
      "</div>";
      $content.= "</form>";
    } else {
      /**
       * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
       */
      if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Im Select wurde "ja" ausgewählt, jetzt wird das Sitzungstoken geprüft.
         */
        if($_POST['token'] == $sessionHash) {
          $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $row = mysqli_fetch_assoc($result);
          if(mysqli_query($dbl, "DELETE FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1")) {
            mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 4, 'Einheit gelöscht: `".defuse($row['title'])."`')") OR DIE(MYSQLI_ERROR($dbl));
            $content.= "<div class='successbox'>Einheit erfolgreich gelöscht.</div>";
          } elseif(mysqli_errno($dbl) == 1451) {
            $content.= "<div class='warnbox'>Die Einheit ist noch in Rezepten zugewiesen. Es müssen zuerst alle Zuweisungen entfernt werden, bevor die Einheit gelöscht werden kann.</div>";
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>";
          }
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Zutat unverändert.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      }
    }
  }
} else {
  /**
   * Es wurde keine ID übergeben.
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Es wurde keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
