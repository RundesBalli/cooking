<?php
/**
 * adminIngredients/add.php
 * 
 * Hinzufügen einer Zutat
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Zutat hinzufügen";
$content.= "<h1><span class='fas icon'>&#xf067;</span>Zutat hinzufügen</h1>";

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
  if($_POST['token'] != $sessionHash) {
    $form = 1;
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
  }

  /**
   * Bezeichnung
   */
  if(preg_match('/^.{2,150}$/', $_POST['title'], $match) === 1) {
    $formTitle = defuse($match[0]);
  } else {
    $form = 1;
    $content.= "<div class='warnbox'>Die Bezeichnung der Zutat ist ungültig. Sie muss zwischen 2 und 150 Zeichen lang sein.</div>";
  }

  /**
   * Plural Bezeichnung
   */
  if(preg_match('/^.{2,150}$/', $_POST['titlePlural'], $match) === 1) {
    $formTitlePlural = defuse($match[0]);
  } else {
    $form = 1;
    $content.= "<div class='warnbox'>Die Plural Bezeichnung der Zutat ist ungültig. Sie muss zwischen 2 und 150 Zeichen lang sein.</div>";
  }

  /**
   * Suchbar-Flag
   */
  if(!empty($_POST['searchable']) AND $_POST['searchable'] == 1) {
    $searchable = 1;
  } else {
    $searchable = 0;
  }

  /**
   * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
   */
  if($form == 0) {
    if(mysqli_query($dbl, "INSERT INTO `metaIngredients` (`title`, `titlePlural`, `searchable`) VALUES ('".$formTitle."', '".$formTitlePlural."', '".$searchable."')")) {
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 2, 'Zutat angelegt: `".$formTitle."`/`".$formTitlePlural."`')") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='successbox'>Zutat erfolgreich angelegt.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminIngredients/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "<div class='col-s-12 col-l-12'><a href='/adminIngredients/add'><span class='fas icon'>&#xf067;</span>eine weitere Zutat anlegen</a></div>".
      "</div>";
    } else {
      $form = 1;
      if(mysqli_errno($dbl) == 1062) {
        $content.= "<div class='warnbox'>Es existiert bereits eine Zutat mit dieser Bezeichnung.</div>";
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
  $content.= "<form action='/adminIngredients/add' method='post' autocomplete='off'>";
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
  "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Bezeichnung der Zutat' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>2 bis 150 Zeichen</div>".
  "</div>";

  /**
   * Plural Bezeichnung
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Plural Bezeichnung</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='titlePlural' placeholder='Plural Bezeichnung der Zutat' tabindex='2' autofocus value='".(isset($_POST['titlePlural']) && !empty($_POST['titlePlural']) ? output($_POST['titlePlural']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("2 bis 150 Zeichen\nWird angezeigt wenn Maßeinheit ungleich 1 ist.")."</div>".
  "</div>";

  /**
   * Suchbar
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Suchbar?</div>".
  "<div class='col-s-12 col-l-4'><select name='searchable' tabindex='3'><option value='' selected hidden disabled>Bitte auswählen</option><option value='1'>Ja</option><option value='0'>Nein</option></select></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("Zutat suchbar machen.\nEin Beispiel für `nein` wäre \"Salz\".")."</div>".
  "</div>";

  /**
   * Absenden
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Zutat anlegen</div>".
  "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".
  "<div class='col-s-12 col-l-5'></div>".
  "</div>";
  $content.= "</form>";
}
?>
