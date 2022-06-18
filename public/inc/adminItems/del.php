<?php
/**
 * adminItems/del.php
 * 
 * Löschen eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezept löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Rezept löschen</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
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
     * Wenn das Rezept existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_assoc($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Soll das Rezept <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Alle Bilder, Aufrufe und Kategoriezuweisungen werden dabei ebenfalls gelöscht.</div>".
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
      $content.= "<form action='/adminItems/del?id=".output($id)."' method='post' autocomplete='off'>";
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
    } else {
      /**
       * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
       */
      if($_POST['token'] == $sessionHash) {
        if(!empty($_POST['selection']) AND $_POST['selection'] == 1) {
          /**
           * Im Select wurde "ja" ausgewählt
           */
          $itemTitle = $row['title'];
          $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."'") OR DIE(MYSQLI_ERROR($dbl));
          while($row = mysqli_fetch_assoc($result)) {
            array_map('unlink', glob($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."*-".$row['fileHash'].".png"));
          }
          mysqli_query($dbl, "DELETE FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 4, 'Rezept gelöscht: `".defuse($itemTitle)."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Rezept erfolgreich gelöscht.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>Rezept unverändert.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
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
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
