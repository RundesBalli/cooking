<?php
/**
 * adminFeaturedItems/del.php
 * 
 * Entfernen eines auf der Startseite vorgestellten Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezeptvorstellung löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Rezeptvorstellung löschen</h1>";

/**
 * Prüfen ob die Rezeptvorstellung existiert.
 */
$id = (int)defuse($_GET['id']);
$result = mysqli_query($dbl, "SELECT `featured`.`id`, `featured`.`itemId`, `items`.`title` FROM `featured` JOIN `items` ON `featured`.`itemId` = `items`.`id` WHERE `featured`.`id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Falls die Rezeptvorstellung nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
   */
  http_response_code(404);
  $content.= "<div class='warnbox'>Die Rezeptvorstellung mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminFeaturedItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
} else {
  /**
   * Wenn die Rezeptvorstellung existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
   */
  $row = mysqli_fetch_assoc($result);
  if(!isset($_POST['submit'])) {
    /**
     * Formular wurde noch nicht gesendet.
     */
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'>Soll die Rezeptvorstellung <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden?</div>".
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

    /**
     * Formular
     */
    $content.= "<form action='/adminFeaturedItems/del?id=".output($id)."' method='post' autocomplete='off'>";

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
      if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Im Select wurde "ja" ausgewählt
         */
        mysqli_query($dbl, "DELETE FROM `featured` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 4, ".$row['itemId'].", 'Rezeptvorstellung gelöscht')") OR DIE(MYSQLI_ERROR($dbl));
        /**
         * Zusätzlich legt der MySQL Trigger einen weiteren Logeintrag an.
         */
        $content.= "<div class='successbox'>Rezeptvorstellung erfolgreich gelöscht.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminFeaturedItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Rezeptvorstellung unverändert.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminFeaturedItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      }
    } else {
      /**
       * Ungültiges Sitzungstoken
       */
      http_response_code(403);
      $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFeaturedItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    }
  }
}
?>
