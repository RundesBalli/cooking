<?php
/**
 * adminItems/add.php
 * 
 * Hinzufügen eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezept hinzufügen";
$content.= "<h1><span class='fas icon'>&#xf067;</span>Rezept hinzufügen</h1>";

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
   * Gastautor & Link zum Gastautor
   */
  if(!empty(trim($_POST['author'])) AND preg_match('/^.{1,64}$/', trim($_POST['author']), $match) === 1) {
    $author = defuse($match[0]);
    if(!empty(trim($_POST['authorURL']))) {
      if(preg_match('/^https?:\/\/.{5,192}$/', trim($_POST['authorURL']), $match) === 1) {
        if(filter_var($match[0], FILTER_VALIDATE_URL)) {
          $authorURL = defuse($match[0]);
        } else {
          $form = 1;
          $content.= "<div class='warnbox'>Der eingegebene Gastautor Link ist ungültig.</div>";
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der eingegebene Gastautor Link ist ungültig.</div>";
      }
    }
  } else {
    $author = NULL;
    $authorURL = NULL;
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
    if(mysqli_query($dbl, "INSERT INTO `items` (`title`, `shortTitle`, `author`, `authorURL`, `text`, `persons`, `cost`, `difficulty`, `workDuration`, `totalDuration`) VALUES ('".$formTitle."', '".$shortTitle."', ".($author === NULL ? "NULL" : "'".$author."'").", ".($authorURL === NULL ? "NULL" : "'".$authorURL."'").", ".($text === NULL ? "NULL" : "'".$text."'").", '".$persons."', '".$cost."', '".$difficulty."', '".$workDuration."', '.$totalDuration.')")) {
      $lastId = mysqli_insert_id($dbl);
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 2, ".$lastId.", 'Rezept angelegt')") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='successbox'>Rezept erfolgreich angelegt.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "<div class='col-s-12 col-l-12'><a href='/adminIngredients/assign?id=".$lastId."'><span class='fas icon'>&#xf4d8;</span>Zutatenpflege</a></div>".
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
   * Erstaufruf = Formular wird angezeigt.
   */
  $form = 1;
}

/**
 * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
 */
if($form == 1) {
  $content.= "<form action='/adminItems/add' method='post' autocomplete='off'>";
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
  "<div class='col-s-12 col-l-4'><input type='text' name='title' placeholder='Name des Rezepts' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* Angezeigter Name in der Kategorie\n* 5 bis 100 Zeichen")."</div>".
  "</div>";

  /**
   * Kurztitel
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Kurztitel für URL</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("`/rezept/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".
  "</div>";

  /**
   * Gastautor
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Gastautor</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='author' placeholder='Gastautor' tabindex='2' value='".(isset($_POST['author']) && !empty($_POST['author']) ? output($_POST['author']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* optional\nFalls es sich um einen Gastbeitrag handelt, kann hier der Name des Autors eingetragen werden.\n* max. 64 Zeichen")."</div>".
  "</div>";

  /**
   * Link zum Gastautor
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Link zum Gastautor</div>".
  "<div class='col-s-12 col-l-4'><input type='text' name='authorURL' placeholder='Link zum Gastautor' tabindex='2' value='".(isset($_POST['authorURL']) && !empty($_POST['authorURL']) ? output($_POST['authorURL']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* optional\n* Wenn ein Gastautor eingetragen wurde, kann hier seine Seite / sein Profil verlinkt werden.\n* max. 200 Zeichen")."</div>".
  "</div>";

  /**
   * Text
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Text</div>".
  "<div class='col-s-12 col-l-4'><textarea name='text' placeholder='Mehrzeiliger Text' tabindex='3'>".(isset($_POST['text']) && !empty($_POST['text']) ? output($_POST['text']) : NULL)."</textarea></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* Das hier ist das eigentliche Rezept. Der Haupttext.")."</div>".
  "</div>";

  /**
   * Zutatenliste
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Zutatenliste</div>".
  "<div class='col-s-12 col-l-4'>Wird nach Anlegen des Rezepts hinzugefügt.</div>".
  "<div class='col-s-12 col-l-5'></div>".
  "</div>";

  /**
   * Personenanzahl
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Personenanzahl</div>".
  "<div class='col-s-12 col-l-4'><input type='number' name='persons' placeholder='z.B. 4' tabindex='4' min='0' value='".(isset($_POST['persons']) && (!empty($_POST['persons']) OR $_POST['persons'] == "0") ? output($_POST['persons']) : NULL)."'></div>".
  "<div class='col-s-12 col-l-5'>".Slimdown::render("* Möglich sind alle positiven Zahlen\n* bei allgemeinen Rezepten (z.B. Gewürzmischungen) können \"0\" Personen angegeben werden, dann wird die Personenanzahl ausgeblendet.\n* bei über 10 Personen wird eine Info angezeigt, das Rezept wird aber angelegt.")."</div>".
  "</div>";

  /**
   * Kosten
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Kosten</div>".
  "<div class='col-s-12 col-l-4'><select name='cost' tabindex='5'>"."<option value='' selected disabled hidden>Bitte wählen</option>";
  $result = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
  while($row = mysqli_fetch_array($result)) {
    $content.= "<option value='".$row['id']."'".((isset($_POST['cost']) && !empty($_POST['cost']) AND $row['id'] == $_POST['cost']) ? " selected" : NULL).">".output($row['title'])."</option>";
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
  $result = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
  while($row = mysqli_fetch_array($result)) {
    $content.= "<option value='".$row['id']."'".((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $row['id'] == $_POST['difficulty']) ? " selected" : NULL).">".output($row['title'])."</option>";
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
  $result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
  while($row = mysqli_fetch_array($result)) {
    $content.= "<option value='".$row['id']."'".((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $row['id'] == $_POST['workDuration']) ? " selected" : NULL).">".output($row['title'])."</option>";
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
  $result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
  while($row = mysqli_fetch_array($result)) {
    $content.= "<option value='".$row['id']."'".((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $row['id'] == $_POST['totalDuration']) ? " selected" : NULL).">".output($row['title'])."</option>";
  }
  $content.= "</select></div>".
  "<div class='col-s-12 col-l-5'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".
  "</div>";

  /**
   * Absenden
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-3'>Rezept anlegen</div>".
  "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Anlegen' tabindex='9'></div>".
  "<div class='col-s-12 col-l-5'></div>".
  "</div>";
  $content.= "</form>";
}
?>
