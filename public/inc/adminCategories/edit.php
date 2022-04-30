<?php
/**
 * adminCategories/edit.php
 * 
 * Editieren einer Kategorie.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategorie editieren";
$content.= "<h1><span class='fas icon'>&#xf044;</span>Kategorie editieren</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Kategorie existiert.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
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
     * Wenn die Kategorie existiert, dann prüfe ob das Formular übergeben wurde.
     * Wir gehen davon aus, dass alles okay ist, demzufolge muss das Formular nicht mehr angezeigt werden.
     * Im Fehlerfall wird das Formular nochmals angezeigt.
     */
    $form = 0;
    if(isset($_POST['submit'])) {
      /**
       * Auswertung. Falls alles ok dann $form auf 0 lassen, sonst 1. Bei 0 am Ende wird der Query ausgeführt.
       */

      /**
       * Sitzungstoken
       */
      if($_POST['token'] != $sessionHash) {
        http_response_code(403);
        $form = 1;
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      }

      /**
       * Titel
       */
      if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Name der Kategorie ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>";
      }

      /**
       * Kurztitel
       */
      if(preg_match('/^[0-9a-z-_]{5,64}$/', $_POST['shortTitle'], $match) === 1) {
        $shortTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Kurztitel ist ungültig. Er muss zwischen 5 und 64 Zeichen lang sein und darf nur aus <code>0-9a-z-_</code> bestehen.</div>";
      }

      /**
       * Beschreibung
       */
      if(!empty($_POST['description']) AND preg_match('/^.{1,50}$/', $_POST['description'], $match) === 1) {
        $description = defuse($_POST['description']);
      } else {
        $description = NULL;
      }

      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `categories` SET `title`='".$formTitle."', `shortTitle`='".$shortTitle."', `description`=".($description === NULL ? "NULL" : "'".$description."'")." WHERE `id`='".$id."' LIMIT 1")) {
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `categoryId`, `text`) VALUES ('".$userId."', 3, ".$id.", 'Kategorie bearbeitet')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Kategorie erfolgreich geändert.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "<div class='col-s-12 col-l-12'><a href='/kategorie/".output($shortTitle)."'><span class='far icon'>&#xf07c;</span>Zur Kategorie</a></div>".
          "</div>";
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits ein Eintrag mit diesem Kurztitel.</div>";
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
      $row = mysqli_fetch_array($result);
    }
    /**
     * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
     */
    if($form == 1) {
      $content.= "<form action='/adminCategories/edit?id=".output($id)."' method='post' autocomplete='off'>";

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
       * Titel
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Name der Kategorie</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Name der Kategorie' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>Angezeigter Name in der Navigation<br>5 bis 100 Zeichen</div>".
      "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
      "</div>";

      /**
       * Kurztitel
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kurztitel für URL</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($row['shortTitle']) ? output($row['shortTitle']) : (isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".
      "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
      "</div>";

      /**
       * Kurzbeschreibung
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kurzbeschreibung</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='description' placeholder='Kurzbeschreibung' tabindex='3' value='".(isset($row['description']) ? output($row['description']) : (isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* _optional_\n* wird in den Kacheln auf der Startseite angezeigt")."</div>".
      "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
      "</div>";

      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kategorie anlegen</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".
      "<div class='col-s-12 col-l-5'><span class='highlight'>Info:</span> Die Kategorie wird sofort angezeigt.</div>".
      "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
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
  "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
