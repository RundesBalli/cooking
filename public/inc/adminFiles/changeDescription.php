<?php
/**
 * adminFiles/changeDescription.php
 * 
 * Ändern der Beschreibung eines Bildes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Dateiverwaltung - Bildbeschreibung ändern";
$content.= "<h1>Dateiverwaltung - Bildbeschreibung ändern</h1>";

/**
 * Prüfen ob eine ID übergeben wurde
 */
if(!empty($_GET['id'])) {
  /**
   * Es wurde eine ID übergeben, jetzt wird geprüft ob das Bild existiert.
   */
  $id = (int)defuse($_GET['id']);

  $result = mysqli_query($dbl, "SELECT `images`.`id`, `images`.`itemId`, `images`.`thumb`, `images`.`description`, `images`.`fileHash`, `items`.`title`, `items`.`shortTitle` FROM `images` JOIN `items` ON `items`.`id`=`images`.`itemId` WHERE `images`.`id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  if(mysqli_num_rows($result) == 0) {
    /**
     * Das Bild existiert nicht, daher wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Bild mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Das Bild existiert. Prüfung ob es ein Thumbnail ist oder ein Bild.
     */
    $row = mysqli_fetch_assoc($result);
    if($row['thumb'] == 1) {
      /**
       * Ein Thumbnail hat keine Beschreibung
       */
      http_response_code(404);
      $content.= "<div class='warnbox'>Ein Thumbnail hat keine Beschreibung.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      /**
       * Es handelt sich um ein Bild, daher kann die Beschreibung geändert werden.
       */
      if(!isset($_POST['submit'])) {
        /**
         * Formular wurde noch nicht gesendet. Anzeige des Bildes.
         */
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><img src='/img/img-".$row['itemId']."-".$row['fileHash'].".png'></div>".
        "</div>";

        /**
         * Anzeige des Formulars
         */
        $content.= "<form action='/adminFiles/changeDescription?id=".output($id)."' method='post' autocomplete='off'>";

        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";

        /**
         * Text
         */
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-4'><input type='text' name='description' placeholder='Bildbeschreibung' value='".(!empty($row['description']) ? output($row['description']) : NULL)."' tabindex='1' autofocus></div>".
        "<div class='col-s-12 col-l-8'>".Slimdown::render("* Wird am unteren Bildrand eingeblendet\n* Kein Markdown möglich")."</div>".
        "</div>";

        /**
         * Ändern
         */
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Ändern' tabindex='2'></div>".
        "<div class='col-s-12 col-l-8'></div>".
        "</div>";
        $content.= "</form>";
        $content.= "<div class='spacer-m'></div>";
      } else {
        /**
         * Formular wurde abgesendet. Tokenprüfung
         */
        if($_POST['token'] == $sessionHash) {
          /**
           * Bildbeschreibung kann geändert werden.
           */
          if(empty($_POST['description'])) {
            $content.= "<div class='infobox'>Text ist leer!</div>";
          }
          mysqli_query($dbl, "UPDATE `images` SET `description`=".(empty($_POST['description']) ? "NULL" : "'".defuse($_POST['description'])."'")." WHERE `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 3, '".$row['itemId']."', 'Bildbeschreibung geändert')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Bildbeschreibung erfolgreich bearbeitet.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".
          "</div>";
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminFiles/changeDescription?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Nochmal versuchen</a></div>".
          "</div>";
        }
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
