<?php
/**
 * adminCategories/del.php
 * 
 * Löschen einer Kategorie.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategorie löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Kategorie löschen</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Kategorie existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Kategorie nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Wenn die Kategorie existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_assoc($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Soll die Kategorie <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Alle Rezeptzuweisungen werden dabei ebenfalls gelöscht (die Rezepte bleiben aber erhalten).</div>".
      "</div>";
      /**
       * Es wird ein "verwirrendes" Select-Feld gebaut, damit die "ja"-Option jedes mal woanders steht und man bewusster löscht.
       */
      $options = array(1 => "Ja, wirklich löschen", 2 => "nein, nicht löschen", 3 => "nope", 4 => "auf keinen Fall", 5 => "nö", 6 => "ich habe es mir anders überlegt");
      $options1 = array();
      foreach($options as $key => $val) {
        $options1[] = "<option value='".$key."'>".$val."</option>";
      }
      shuffle($options1);
      $content.= "<form action='/adminCategories/del?id=".output($id)."' method='post' autocomplete='off'>";
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
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      /**
       * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
       */
      if(!empty($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Im Select wurde "ja" ausgewählt, jetzt wird das Sitzungstoken geprüft.
         */
        if($_POST['token'] == $sessionHash) {
          $result = mysqli_query($dbl, "SELECT * FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $row = mysqli_fetch_assoc($result);
          mysqli_query($dbl, "DELETE FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 4, 'Kategorie gelöscht: `".defuse($row['title'])."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Kategorie erfolgreich gelöscht.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Kategorie unverändert.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
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
  "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
