<?php
/**
 * adminFeaturedItems/add.php
 * 
 * Hinzufügen eines auf der Startseite vorgestellten Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezeptvorstellungen anlegen";
$content.= "<h1><span class='fas icon'>&#xf005;</span>Rezeptvorstellungen anlegen</h1>";

/**
 * Prüfen ob bereits ein Formular abgesendet wurde.
 */
if(isset($_POST['submit'])) {
  /**
   * Eintragen der Rezeptvorstellung.
   */
  $error = 0;
  /**
   * Sitzungstoken
   */
  if($_POST['token'] != $sessionHash) {
    http_response_code(403);
    $error = 1;
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
  }
  if(!empty($_POST['itemId'])) {
    $itemId = (int)defuse($_POST['itemId']);
  } else {
    $error = 1;
    $content.= "<div class='warnbox'>Ungültiges Rezept.</div>";
  }
  if($error == 0) {
    $result = mysqli_query($dbl, "SELECT * FROM `featured` WHERE `itemId`='".$itemId."'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 0) {
      /**
       * Rezeptfeature existiert bereits. Kann erneuert und "wieder nach vorne gezogen" werden.
       */
      mysqli_query($dbl, "UPDATE `featured` SET `timestamp`=NOW() WHERE `itemId`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 3, ".$itemId.", 'Rezeptvorstellung erneuert')") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='successbox'>Rezeptvorstellung erfolgreich erneuert.</div>";
    } else {
      if(mysqli_query($dbl, "INSERT INTO `featured` (`itemId`) VALUES ('".$itemId."')")) {
        /**
         * Erfolgreich angelegt.
         */
        mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 2, ".$itemId.", 'Rezeptvorstellung angelegt')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Rezeptvorstellung erfolgreich angelegt.</div>";
      } else {
        if(mysqli_errno($dbl) == 1452) {
          /**
           * Das Rezept existiert nicht.
           */
          $content.= "<div class='warnbox'>Das Rezept existiert nicht.</div>";
        } else {
          $content.= "<div class='warnbox'>Unbekannter Fehler (".mysqli_errno($dbl)."): ".mysqli_error($dbl)."</div>";
        }
      }
    }
  } else {
    $content.= "<div class='warnbox'>Konnte Rezeptvorstellung nicht anlegen.</div>";
  }
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminFeaturedItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
  $content.= "<div class='spacer-m'></div>";
} else {
  /**
   * Anzeige des Formulars.
   */
  $content.= "<form action='/adminFeaturedItems/add' method='post' autocomplete='off'>";

  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";

  /**
   * Tabellenüberschrift
   */
  $content.= "<div class='row highlight bold bordered'>".
  "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
  "<div class='col-s-12 col-l-4'>Feld</div>".
  "<div class='col-s-12 col-l-5'>Ergänzungen</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";
  /**
   * Selektion aller Rezepte
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  $options = array();
  if(!empty($_GET['id'])) {
    $id = (int)defuse($_GET['id']);
  } else {
    $id = NULL;
  }
  /**
   * Erzeugen der Rezeptliste
   */
  while($row = mysqli_fetch_array($result)) {
    $options[] = "<option value='".$row['id']."'".($id == $row['id'] ? " selected" : NULL).">".output($row['title'])."</option>";
  }
  /**
   * Selectfeld
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Rezept</div>".
  "<div class='col-s-12 col-l-4'><select name='itemId' tabindex='1' autofocus>"."<option value='' ".(($id === NULL OR $id == 0) ? "selected " : NULL)."disabled hidden>Bitte wählen</option>".implode("", $options)."</select></div>".
  "<div class='col-s-12 col-l-5'>Um ein Rezept \"wieder nach vorne zu holen\" genügt es, es erneut vorzuschlagen. Der alte Eintrag wird gelöscht.</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";
  /**
   * Absenden
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Rezeptvorstellung anlegen</div>".
  "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='2'></div>".
  "<div class='col-s-12 col-l-5'></div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";
  $content.= "</form>";
}
?>
