<?php
/**
 * adminCategories/add.php
 * 
 * Anlegen einer Kategorie.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategorie anlegen";
$content.= "<h1><span class='fas icon'>&#xf067;</span>Kategorie anlegen</h1>";

/**
 * Falls das Formular übergeben wurde, gehen wir davon aus, dass alles okay ist, demzufolge muss das Formular nicht mehr angezeigt werden.
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
  if(!empty($_POST['token']) AND $_POST['token'] != $sessionHash) {
    $form = 1;
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
  }

  /**
   * Titel
   */
  if(!empty($_POST['title']) AND preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
    $formTitle = defuse($match[0]);
  } else {
    $form = 1;
    $content.= "<div class='warnbox'>Der Name der Kategorie ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>".PHP_EOL;
  }

  /**
   * Kurztitel
   */
  if(!empty($_POST['shortTitle']) AND preg_match('/^[0-9a-z-_]{5,64}$/', strtolower($_POST['shortTitle']), $match) === 1) {
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

  /**
   * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
   */
  if($form == 0) {
    if(mysqli_query($dbl, "INSERT INTO `categories` (`title`, `shortTitle`, `description`) VALUES ('".$formTitle."', '".$shortTitle."', ".($description === NULL ? "NULL" : "'".$description."'").")")) {
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `categoryId`, `text`) VALUES ('".$userId."', 2, ".mysqli_insert_id($dbl).", 'Kategorie angelegt')") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='successbox'>Kategorie erfolgreich angelegt.</div>";
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
   * Erstaufruf = Formular wird angezeigt.
   */
  $form = 1;
}

/**
 * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
 */
if($form == 1) {
  $content.= "<form action='/adminCategories/add' method='post' autocomplete='off'>";

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
  "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Name der Kategorie' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>Angezeigter Name in der Navigation<br>5 bis 100 Zeichen</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";

  /**
   * Kurztitel
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Kurztitel für URL</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";

  /**
   * Langbeschreibung
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Kurzbeschreibung</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='description' placeholder='Kurzbeschreibung' tabindex='3' value='".(isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* _optional_\n* kein Markdown möglich\n* wird in den Kacheln auf der Startseite angezeigt")."</div>".
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
?>
