<?php
/**
 * adminItems/edit.php
 * 
 * Bearbeiten eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezept bearbeiten";
$content.= "<h1><span class='fas icon'>&#xf044;</span>Rezept bearbeiten</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $rezeptresult = mysqli_query($dbl, "SELECT * FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($rezeptresult) == 0) {
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
     * Wenn das Rezept existiert, dann prüfe ob das Formular übergeben wurde.
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
      if(!empty(trim($_POST['token'])) AND trim($_POST['token']) != $sessionHash) {
        http_response_code(403);
        $form = 1;
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      }

      /**
       * Titel
       */
      if(!empty(trim($_POST['title'])) AND preg_match('/^.{5,100}$/', trim($_POST['title']), $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Name des Rezepts ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>";
      }

      /**
       * Kurztitel
       */
      if(!empty(trim($_POST['shortTitle'])) AND preg_match('/^[0-9a-z-_]{5,64}$/', strtolower(trim($_POST['shortTitle'])), $match) === 1) {
        $shortTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Kurztitel ist ungültig. Er muss zwischen 5 und 64 Zeichen lang sein und darf nur aus <code>0-9a-z-_</code> bestehen.</div>";
      }

      /**
       * Text
       */
      if(!empty(trim($_POST['text']))) {
        $text = defuse($_POST['text']);
      } else {
        $content.= "<div class='infobox'>Der Text ist leer. Rezept wird ohne Inhalt angelegt.</div>";
        $text = NULL;
      }

      /**
       * Personenanzahl
       */
      if(!empty(trim($_POST['persons'])) OR $_POST['persons'] == "0") {
        $persons = (int)defuse($_POST['persons']);
        if($persons < 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>";
        } elseif($persons > 10) {
          $content.= "<div class='infobox'>Für mehr als 10 Personen? Bist du dir sicher?</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>";
      }

      /**
       * Kosten
       */
      if(!empty(trim($_POST['cost']))) {
        $cost = (int)defuse($_POST['cost']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaCost` WHERE `id`='".$cost."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>";
      }

      /**
       * Schwierigkeit
       */
      if(!empty(trim($_POST['difficulty']))) {
        $difficulty = (int)defuse($_POST['difficulty']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDifficulty` WHERE `id`='".$difficulty."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>";
      }

      /**
       * Arbeitszeit
       */
      if(!empty(trim($_POST['workDuration']))) {
        $workDuration = (int)defuse($_POST['workDuration']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$workDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>";
      }

      /**
       * Gesamtzeit
       */
      if(!empty(trim($_POST['totalDuration']))) {
        $totalDuration = (int)defuse($_POST['totalDuration']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$totalDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>";
        } elseif($totalDuration < $workDuration) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit darf die Angabe der Arbeitszeit nicht unterschreiten.</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>";
      }

      /**
       * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
       */
      if($form == 0) {
        if(mysqli_query($dbl, "UPDATE `items` SET `title`='".$formTitle."', `shortTitle`='".$shortTitle."', `text`=".($text === NULL ? "NULL" : "'".$text."'").", `persons`='".$persons."', `cost`='".$cost."', `difficulty`='".$difficulty."', `workDuration`='".$workDuration."', `totalDuration`='".$totalDuration."' WHERE `id`='".$id."' LIMIT 1")) {
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 3, ".$id.", 'Rezept geändert')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Rezept erfolgreich geändert.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "<div class='col-s-12 col-l-12'><a href='/adminIngredients/assign?id=".output($id)."'><span class='fas icon'>&#xf4d8;</span>Zutatenpflege</a></div>".
          "<div class='col-s-12 col-l-12'><a href='/rezept/".output($shortTitle)."'><span class='fas icon'>&#xf543;</span>Zum Rezept</a></div>".
          "</div>";
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits ein Rezept mit diesem Kurztitel.</div>";
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
      $row = mysqli_fetch_array($rezeptresult);
    }
    /**
     * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
     */
    if($form == 1) {
      $content.= "<form action='/adminItems/edit?id=".output($id)."' method='post' autocomplete='off'>";
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
      "</div>";

      /**
       * Titel
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Name des Rezepts</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Name des Rezepts' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* Angezeigter Name in der Kategorie\n* 5 bis 100 Zeichen")."</div>".
      "</div>";

      /**
       * Kurztitel
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kurztitel für URL</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($row['shortTitle']) ? output($row['shortTitle']) : (isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("`/rezept/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".
      "</div>";

      /**
       * Text
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Text</div>".
      "<div class='col-s-12 col-l-4'><textarea name='text' placeholder='Mehrzeiliger Text' tabindex='3'>".(isset($row['text']) ? output($row['text']) : (isset($_POST['text']) && !empty($_POST['text']) ? output($_POST['text']) : NULL))."</textarea></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* Das hier ist das eigentliche Rezept. Der Haupttext.")."</div>".
      "</div>";

      /**
       * Zutatenliste
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Zutatenliste</div>".
      "<div class='col-s-12 col-l-4'><a href='/adminIngredients/assign?id=".$id."' target='_blank'>Zutaten verändern<span class='fas iconright'>&#xf35d;</span></a></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";

      /**
       * Personenanzahl
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Personenanzahl</div>".
      "<div class='col-s-12 col-l-4'><input type='number' name='persons' placeholder='z.B. 4' tabindex='4' min='0' value='".(isset($row['persons']) ? output($row['persons']) : (isset($_POST['persons']) && (!empty($_POST['persons']) OR $_POST['persons'] == "0") ? output($_POST['persons']) : NULL))."'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* Möglich sind alle positiven Zahlen\n* bei allgemeinen Rezepten (z.B. Gewürzmischungen) können \"0\" Personen angegeben werden, dann wird die Personenanzahl ausgeblendet.\n* bei über 10 Personen wird eine Info angezeigt, das Rezept wird aber angelegt.")."</div>".
      "</div>";

      /**
       * Kosten
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Kosten</div>".
      "<div class='col-s-12 col-l-4'><select name='cost' tabindex='5'>"."<option value='' selected disabled hidden>Bitte wählen</option>";
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['cost']) ? ($row['cost'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['cost']) && !empty($_POST['cost']) AND $innerrow['id'] == $_POST['cost']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>";
      }
      $content.= "</select></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";

      /**
       * Schwierigkeit
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Schwierigkeit</div>".
      "<div class='col-s-12 col-l-4'><select name='difficulty' tabindex='6'>"."<option value='' selected disabled hidden>Bitte wählen</option>";
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['difficulty']) ? ($row['difficulty'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $innerrow['id'] == $_POST['difficulty']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>";
      }
      $content.= "</select></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";

      /**
       * Arbeitszeit
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Arbeitszeit</div>".
      "<div class='col-s-12 col-l-4'><select name='workDuration' tabindex='7'>"."<option value='' selected disabled hidden>Bitte wählen</option>";
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['workDuration']) ? ($row['workDuration'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $innerrow['id'] == $_POST['workDuration']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>";
      }
      $content.= "</select></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";

      /**
       * Gesamtzeit
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Gesamtzeit</div>".
      "<div class='col-s-12 col-l-4'><select name='totalDuration' tabindex='8'>"."<option value='' selected disabled hidden>Bitte wählen</option>";
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['totalDuration']) ? ($row['totalDuration'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $innerrow['id'] == $_POST['totalDuration']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>";
      }
      $content.= "</select></div>".
      "<div class='col-s-12 col-l-5'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".
      "</div>";

      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Rezept ändern</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Ändern' tabindex='9'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";
      $content.= "</form>";
    }
  }
} else {
  http_response_code(400);
  $content.= "<div class='warnbox'>Keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
