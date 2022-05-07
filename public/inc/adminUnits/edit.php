<?php
/**
 * adminUnits/edit.php
 * 
 * Bearbeiten einer Einheit
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Einheit bearbeiten";
$content.= "<h1><span class='fas icon'>&#xf044;</span>Einheit bearbeiten</h1>";

/**
 * Prüfen ob eine ID übergeben wurde
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Maßeinheit existiert.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Maßeinheit nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Maßeinheit mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Wenn die Maßeinheit existiert, dann prüfe ob das Formular übergeben wurde.
     * Wir gehen davon aus, dass alles okay ist, demzufolge muss das Formular nicht mehr angezeigt werden.
     * Im Fehlerfall wird das Formular nochmals angezeigt.
     */
    $row = mysqli_fetch_assoc($result);
    $form = 0;
    if(isset($_POST['submit'])) {
      /**
       * Auswertung. Falls alles ok dann $form auf 0 lassen, sonst 1. Bei 0 am Ende wird der Query ausgeführt.
       */

      /**
       * Sitzungstoken
       */
      if($_POST['token'] != output($sessionHash)) {
        http_response_code(403);
        $form = 1;
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      }

      /**
       * Bezeichnung
       */
      if(preg_match('/^.{2,50}$/', $_POST['title'], $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Bezeichnung der Maßeinheit ist ungültig. Sie muss zwischen 2 und 50 Zeichen lang sein.</div>";
      }

      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `metaUnits` SET `title`='".$formTitle."' WHERE `id`='".$id."' LIMIT 1")) {
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 3, 'Einheit geändert: von `".defuse($row['title'])."` in `".$formTitle."`')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Maßeinheit erfolgreich geändert.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminUnits/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits eine Maßeinheit mit dieser Bezeichnung oder dieser Kurzform.</div>";
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>";
          }
        }
      }
    } else {
      /**
       * Erstaufruf = Formular wird angezeigt und befüllt.
       */
      $form = 1;
    }
    /**
     * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
     */
    if($form == 1) {
      $content.= "<form action='/adminUnits/edit?id=".output($id)."' method='post' autocomplete='off'>";
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";

      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
      "<div class='col-s-12 col-l-4'>Feld</div>".
      "<div class='col-s-12 col-l-5'>Ergänzungen</div>".
      "</div>";

      /**
       * Bezeichnung
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Bezeichnung der Maßeinheit' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>2 bis 50 Zeichen</div>".
      "</div>";

      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kategorie anlegen</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";
      $content.= "</form>";
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
