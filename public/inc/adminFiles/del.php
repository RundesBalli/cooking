<?php
/**
 * adminFiles/add.php
 * 
 * Entfernen eines Bildes eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Dateiverwaltung - Bild löschen";
$content.= "<h1>Dateiverwaltung - Bild löschen</h1>";

/**
 * Prüfen ob eine ID übergeben wurde
 */
if(!empty($_GET['id'])) {
  /**
   * Es wurde eine ID übergeben, jetzt wird geprüft ob das Bild existiert.
   */
  $id = (int)defuse($_GET['id']);

  $result = mysqli_query($dbl, "SELECT `images`.`id`, `images`.`itemId`, `images`.`thumb`, `images`.`fileHash`, `items`.`title`, `items`.`shortTitle` FROM `images` JOIN `items` ON `items`.`id`=`images`.`itemId` WHERE `images`.`id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

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
     * Das Bild existiert. Anzeige grundlegender Infos.
     */
    $row = mysqli_fetch_assoc($result);
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><span class='highlight bold'>Rezept:</span> <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "</div>";
    $content.= "<div class='spacer-m'></div>";

    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Soll das Bild wirklich gelöscht werden?</div>".
      "</div>";

      /**
       * Anzeige des Bildes
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><img src='/img/".($row['thumb'] ? 'thumb' : 'img')."-".$row['itemId']."-".$row['fileHash'].".png'></div>".
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

      $content.= "<form action='/adminFiles/del?id=".output($id)."' method='post' autocomplete='off'>";

      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";
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
          array_map('unlink', glob($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."*-".$row['fileHash'].".png"));
          mysqli_query($dbl, "DELETE FROM `images` WHERE `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 4, '".$row['itemId']."', '".($row['thumb'] ? 'Thumbnail' : 'Bild')." gelöscht')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Bild erfolgreich gelöscht.</div>";
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
          "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Bild wurde nicht gelöscht.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminItems/show?id=".output($row['itemId'])."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
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