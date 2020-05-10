<?php
/**
 * adminItems.php
 * 
 * Seite um Rezepte anzuzeigen, anzulegen, zu bearbeiten, zu löschen und zuzuweisen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezepte.
   */
  header("Location: /adminItems/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflisten aller Rezepte.
   */
  $title = "Rezepte anzeigen";
  $content.= "<h1><span class='fas icon'>&#xf543;</span>Rezepte anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminItems/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, IFNULL((SELECT ROUND(AVG(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId`=`items`.`id` GROUP BY `votes`.`itemId`), 0) AS `stars`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId`=`items`.`id`), 0) AS `voteCount`, IFNULL((SELECT COUNT(`clicks`.`id`) FROM `clicks` WHERE `clicks`.`itemId`=`items`.`id`), 0) AS `clicks` FROM `items` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Rezepte existieren.
     */
    $content.= "<div class='infobox'>Noch keine Rezepte angelegt.</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Rezepte.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-5 col-xl-5'>Titel</div>".PHP_EOL.
    "<div class='col-x-12 col-s-4 col-m-4 col-l-1 col-xl-1'>Klicks</div>".PHP_EOL.
    "<div class='col-x-12 col-s-8 col-m-8 col-l-2 col-xl-2'>Sterne</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'>Kategorien</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-0 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $innerresult = mysqli_query($dbl, "SELECT `categories`.`title`, `categories`.`shortTitle` FROM `categoryItems` LEFT JOIN `categories` ON `categoryItems`.`categoryId`=`categories`.`id` WHERE `categoryItems`.`itemId`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($innerresult) == 0) {
        $categories = "keine";
      } else {
        $categories = array();
        while($innerrow = mysqli_fetch_array($innerresult)) {
          $categories[] = "<a href='/kategorie/".output($innerrow['shortTitle'])."' target='_blank'>".output($innerrow['title'])."<span class='fas iconright'>&#xf35d;</span></a>";
        }
        $categories = implode("<br>", $categories);
      }
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-5 col-xl-5'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-4 col-m-4 col-l-1 col-xl-1'>".$row['clicks']."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-8 col-m-8 col-l-2 col-xl-2'>".stars($row['stars'], $row['voteCount'])."<br>".number_format($row['stars'], 2, ",", ".")." - ".number_format($row['voteCount'], 0, ",", ".")." Stimmen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'>".$categories."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'><a href='/adminItems/edit/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a><br>".PHP_EOL."<a href='/adminItems/del/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a><br>".PHP_EOL."<a href='/adminItems/assign/".$row['id']."' class='nowrap'><span class='far icon'>&#xf07c;</span>Kategorien</a><br>".PHP_EOL."<a href='/adminFiles/list/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf302;</span>Bilder</a><br>".PHP_EOL."<a href='/adminIngredients/assign/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf4d8;</span>Zutaten</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-0 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Hinzufügen eines Rezepts.
   */
  $title = "Rezept hinzufügen";
  $content.= "<h1><span class='fas icon'>&#xf067;</span>Rezept hinzufügen</h1>".PHP_EOL;
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
    if($_POST['token'] != $adminSessionHash) {
      http_response_code(403);
      $form = 1;
      $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
    }
    /**
     * Titel
     */
    if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
      $formTitle = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Der Name des Rezepts ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>".PHP_EOL;
    }
    /**
     * Kurztitel
     */
    if(preg_match('/^[0-9a-z-_]{5,64}$/', $_POST['shortTitle'], $match) === 1) {
      $shortTitle = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Der Kurztitel ist ungültig. Er muss zwischen 5 und 64 Zeichen lang sein und darf nur aus <code>0-9a-z-_</code> bestehen.</div>".PHP_EOL;
    }
    /**
     * Text
     */
    if(!empty(trim($_POST['text']))) {
      $text = defuse($_POST['text']);
    } else {
      $content.= "<div class='infobox'>Der Text ist leer. Rezept wird ohne Inhalt angelegt.</div>".PHP_EOL;
      $text = NULL;
    }
    /**
     * Personenanzahl
     */
    if(!empty($_POST['persons']) OR $_POST['persons'] == "0") {
      $persons = (int)defuse($_POST['persons']);
      if($persons < 0) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>".PHP_EOL;
      } elseif($persons > 10) {
        $content.= "<div class='infobox'>Für mehr als 10 Personen? Bist du dir sicher?</div>".PHP_EOL;
      }
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>".PHP_EOL;
    }
    /**
     * Kosten
     */
    if(!empty($_POST['cost'])) {
      $cost = (int)defuse($_POST['cost']);
      $result = mysqli_query($dbl, "SELECT `id` FROM `metaCost` WHERE `id`='".$cost."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>".PHP_EOL;
      }
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>".PHP_EOL;
    }
    /**
     * Schwierigkeit
     */
    if(!empty($_POST['difficulty'])) {
      $difficulty = (int)defuse($_POST['difficulty']);
      $result = mysqli_query($dbl, "SELECT `id` FROM `metaDifficulty` WHERE `id`='".$difficulty."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>".PHP_EOL;
      }
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>".PHP_EOL;
    }
    /**
     * Arbeitszeit
     */
    if(!empty($_POST['workDuration'])) {
      $workDuration = (int)defuse($_POST['workDuration']);
      $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$workDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>".PHP_EOL;
      }
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>".PHP_EOL;
    }
    /**
     * Gesamtzeit
     */
    if(!empty($_POST['totalDuration'])) {
      $totalDuration = (int)defuse($_POST['totalDuration']);
      $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$totalDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>".PHP_EOL;
      } elseif($totalDuration < $workDuration) {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit darf die Angabe der Arbeitszeit nicht unterschreiten.</div>".PHP_EOL;
      }
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>".PHP_EOL;
    }
    /**
     * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
     */
    if($form == 0) {
      if(mysqli_query($dbl, "INSERT INTO `items` (`title`, `shortTitle`, `text`, `persons`, `cost`, `difficulty`, `workDuration`, `totalDuration`) VALUES ('".$formTitle."', '".$shortTitle."', ".($text === NULL ? "NULL" : "'".$text."'").", '".$persons."', '".$cost."', '".$difficulty."', '".$workDuration."', '.$totalDuration.')")) {
        $lastId = mysqli_insert_id($dbl);
        adminLog($adminUserId, 2, $lastId, NULL, "Rezept angelegt");
        $content.= "<div class='successbox'>Rezept erfolgreich angelegt.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/assign/".$lastId."'><span class='fas icon'>&#xf4d8;</span>Zutatenpflege</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".output($shortTitle)."'><span class='fas icon'>&#xf543;</span>Zum Rezept</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        $form = 1;
        if(mysqli_errno($dbl) == 1062) {
          $content.= "<div class='warnbox'>Es existiert bereits ein Rezept mit diesem Kurztitel.</div>".PHP_EOL;
        } else {
          $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>".PHP_EOL;
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
    $content.= "<form action='/adminItems/add' method='post' autocomplete='off'>".PHP_EOL;
    /**
     * Sitzungstoken
     */
    $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
    /**
     * Tabellenüberschrift
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Feld</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Ergänzungen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Titel
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name des Rezepts</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name des Rezepts' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Kategorie<br>5 bis 100 Zeichen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kurztitel
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/rezept/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Text
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Text</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='text' placeholder='Mehrzeiliger Text' tabindex='3'>".(isset($_POST['text']) && !empty($_POST['text']) ? output($_POST['text']) : NULL)."</textarea></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* Das hier ist das eigentliche Rezept. Der Haupttext.")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Zutatenliste
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutatenliste</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Wird nach Anlegen des Rezepts hinzugefügt.</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Personenanzahl
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Personenanzahl</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='number' name='persons' placeholder='z.B. 4' tabindex='4' min='0' value='".(isset($_POST['persons']) && (!empty($_POST['persons']) OR $_POST['persons'] == "0") ? output($_POST['persons']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* Möglich sind alle positiven Zahlen\n* bei allgemeinen Rezepten (z.B. Gewürzmischungen) können \"0\" Personen angegeben werden, dann wird die Personenanzahl ausgeblendet.\n* bei über 10 Personen wird eine Info angezeigt, das Rezept wird aber angelegt.")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kosten
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kosten</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='cost' tabindex='5'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<option value='".$row['id']."'".((isset($_POST['cost']) && !empty($_POST['cost']) AND $row['id'] == $_POST['cost']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
    }
    $content.= "</select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Schwierigkeit
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Schwierigkeit</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='difficulty' tabindex='6'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<option value='".$row['id']."'".((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $row['id'] == $_POST['difficulty']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
    }
    $content.= "</select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Arbeitszeit
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Arbeitszeit</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='workDuration' tabindex='7'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<option value='".$row['id']."'".((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $row['id'] == $_POST['workDuration']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
    }
    $content.= "</select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Gesamtzeit
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Gesamtzeit</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='totalDuration' tabindex='8'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<option value='".$row['id']."'".((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $row['id'] == $_POST['totalDuration']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
    }
    $content.= "</select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Rezept anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='9'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen eines Rezepts.
   */
  $title = "Rezept löschen";
  $content.= "<h1><span class='fas icon'>&#xf2ed;</span>Rezept löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn das Rezept existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll das Rezept <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Alle Bilder, Votes, Klicks und Kategoriezuweisungen werden dabei ebenfalls gelöscht.</div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Es wird ein "verwirrendes" Select-Feld gebaut, damit die "ja"-Option jedes mal woanders steht und man bewusster löscht.
       */
      $options = array(1 => "Ja, wirklich löschen", 2 => "nein, nicht löschen", 3 => "nope", 4 => "auf keinen Fall", 5 => "nö", 6 => "hab es mir anders überlegt");
      $options1 = array();
      foreach($options as $key => $val) {
        $options1[] = "<option value='".$key."'>".$val."</option>".PHP_EOL;
      }
      shuffle($options1);
      $content.= "<form action='/adminItems/del/".$id."' method='post' autocomplete='off'>".PHP_EOL;
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><select name='selection'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL.implode("", $options1)."</select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><input type='submit' name='submit' value='Handeln'></div>".PHP_EOL.
      "<div class='col-x-0 col-s-0 col-m-0 col-l-4 col-xl-4'></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    } else {
      /**
       * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
       */
      if($_POST['token'] == $adminSessionHash) {
        if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
          /**
           * Im Select wurde "ja" ausgewählt
           */
          $itemTitle = $row['title'];
          $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."'") OR DIE(MYSQLI_ERROR($dbl));
          while($row = mysqli_fetch_array($result)) {
            array_map('unlink', glob($uploaddir."*-".$row['fileHash'].".png"));
          }
          mysqli_query($dbl, "DELETE FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          adminLog($adminUserId, 4, NULL, NULL, "Rezept gelöscht: `".$itemTitle."`");
          $content.= "<div class='successbox'>Rezept erfolgreich gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>Rezept unverändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'edit') {
  /**
   * Bearbeiten eines Rezepts.
   */
  $title = "Rezept bearbeiten";
  $content.= "<h1><span class='fas icon'>&#xf044;</span>Rezept bearbeiten</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
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
      if($_POST['token'] != $adminSessionHash) {
        http_response_code(403);
        $form = 1;
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
      }
      /**
       * Titel
       */
      if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Name des Rezepts ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>".PHP_EOL;
      }
      /**
       * Kurztitel
       */
      if(preg_match('/^[0-9a-z-_]{5,64}$/', $_POST['shortTitle'], $match) === 1) {
        $shortTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Kurztitel ist ungültig. Er muss zwischen 5 und 64 Zeichen lang sein und darf nur aus <code>0-9a-z-_</code> bestehen.</div>".PHP_EOL;
      }
      /**
       * Text
       */
      if(!empty(trim($_POST['text']))) {
        $text = defuse($_POST['text']);
      } else {
        $content.= "<div class='infobox'>Der Text ist leer. Rezept wird ohne Inhalt angelegt.</div>".PHP_EOL;
        $text = NULL;
      }
      /**
       * Personenanzahl
       */
      if(!empty($_POST['persons']) OR $_POST['persons'] == "0") {
        $persons = (int)defuse($_POST['persons']);
        if($persons < 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>".PHP_EOL;
        } elseif($persons > 10) {
          $content.= "<div class='infobox'>Für mehr als 10 Personen? Bist du dir sicher?</div>".PHP_EOL;
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Personenanzahl ist ungültig.</div>".PHP_EOL;
      }
      /**
       * Kosten
       */
      if(!empty($_POST['cost'])) {
        $cost = (int)defuse($_POST['cost']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaCost` WHERE `id`='".$cost."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>".PHP_EOL;
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Kosten ist ungültig.</div>".PHP_EOL;
      }
      /**
       * Schwierigkeit
       */
      if(!empty($_POST['difficulty'])) {
        $difficulty = (int)defuse($_POST['difficulty']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDifficulty` WHERE `id`='".$difficulty."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>".PHP_EOL;
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Schwierigkeit ist ungültig.</div>".PHP_EOL;
      }
      /**
       * Arbeitszeit
       */
      if(!empty($_POST['workDuration'])) {
        $workDuration = (int)defuse($_POST['workDuration']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$workDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>".PHP_EOL;
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Arbeitszeit ist ungültig.</div>".PHP_EOL;
      }
      /**
       * Gesamtzeit
       */
      if(!empty($_POST['totalDuration'])) {
        $totalDuration = (int)defuse($_POST['totalDuration']);
        $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$totalDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>".PHP_EOL;
        } elseif($totalDuration < $workDuration) {
          $form = 1;
          $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit darf die Angabe der Arbeitszeit nicht unterschreiten.</div>".PHP_EOL;
        }
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Angabe der Gesamtzeit ist ungültig.</div>".PHP_EOL;
      }
      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `items` SET `title`='".$formTitle."', `shortTitle`='".$shortTitle."', `text`=".($text === NULL ? "NULL" : "'".$text."'").", `persons`='".$persons."', `cost`='".$cost."', `difficulty`='".$difficulty."', `workDuration`='".$workDuration."', `totalDuration`='".$totalDuration."' WHERE `id`='".$id."' LIMIT 1")) {
          adminLog($adminUserId, 3, $id, NULL, "Rezept bearbeitet");
          $content.= "<div class='successbox'>Rezept erfolgreich geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/assign/".output($id)."'><span class='fas icon'>&#xf4d8;</span>Zutatenpflege</a></div>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".output($shortTitle)."'><span class='fas icon'>&#xf543;</span>Zum Rezept</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits ein Rezept mit diesem Kurztitel.</div>".PHP_EOL;
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>".PHP_EOL;
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
      $content.= "<form action='/adminItems/edit/".output($id)."' method='post' autocomplete='off'>".PHP_EOL;
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Feld</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Ergänzungen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Titel
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name des Rezepts</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name des Rezepts' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Kategorie<br>5 bis 100 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurztitel
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($row['shortTitle']) ? output($row['shortTitle']) : (isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/rezept/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Text
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Text</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='text' placeholder='Mehrzeiliger Text' tabindex='3'>".(isset($row['text']) ? output($row['text']) : (isset($_POST['text']) && !empty($_POST['text']) ? output($_POST['text']) : NULL))."</textarea></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* Das hier ist das eigentliche Rezept. Der Haupttext.")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Zutatenliste
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutatenliste</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><a href='/adminIngredients/assign/".$id."' target='_blank'>Zutaten verändern<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Personenanzahl
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Personenanzahl</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='number' name='persons' placeholder='z.B. 4' tabindex='4' min='0' value='".(isset($row['persons']) ? output($row['persons']) : (isset($_POST['persons']) && (!empty($_POST['persons']) OR $_POST['persons'] == "0") ? output($_POST['persons']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* Möglich sind alle positiven Zahlen\n* bei allgemeinen Rezepten (z.B. Gewürzmischungen) können \"0\" Personen angegeben werden, dann wird die Personenanzahl ausgeblendet.\n* bei über 10 Personen wird eine Info angezeigt, das Rezept wird aber angelegt.")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kosten
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kosten</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='cost' tabindex='5'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['cost']) ? ($row['cost'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['cost']) && !empty($_POST['cost']) AND $innerrow['id'] == $_POST['cost']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>".PHP_EOL;
      }
      $content.= "</select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Schwierigkeit
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Schwierigkeit</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='difficulty' tabindex='6'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['difficulty']) ? ($row['difficulty'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $innerrow['id'] == $_POST['difficulty']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>".PHP_EOL;
      }
      $content.= "</select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Arbeitszeit
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Arbeitszeit</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='workDuration' tabindex='7'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['workDuration']) ? ($row['workDuration'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $innerrow['id'] == $_POST['workDuration']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>".PHP_EOL;
      }
      $content.= "</select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Gesamtzeit
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Gesamtzeit</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='totalDuration' tabindex='8'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL;
      $innerresult = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
      while($innerrow = mysqli_fetch_array($innerresult)) {
        $content.= "<option value='".$innerrow['id']."'".(isset($row['totalDuration']) ? ($row['totalDuration'] == $innerrow['id'] ? " selected" : NULL) : ((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $innerrow['id'] == $_POST['totalDuration']) ? " selected" : NULL)).">".output($innerrow['title'])."</option>".PHP_EOL;
      }
      $content.= "</select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Rezept ändern</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Ändern' tabindex='9'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'assign') {
  /**
   * Zuweisen eines Rezepts in Kategorien.
   */
  $title = "Kategoriezuweisungen bearbeiten";
  $content.= "<h1><span class='far icon'>&#xf07c;</span>Kategoriezuweisungen bearbeiten</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob das Rezept existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Grundlegende Infos anzeigen
     */
    $row = mysqli_fetch_array($result);
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Rezept:</span> <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
    /**
     * Zuweisen von Kategorien
     */
    if(isset($_GET['add']) AND !empty($_GET['add'])) {
      $addId = (int)defuse($_GET['add']);
      if(!isset($_POST['submit'])) {
        /**
         * CSRF Bestätigung
         */
        $content.= "<h1>Hinzufügen bestätigen</h1>".PHP_EOL;
        $content.= "<div class='infobox'>Zuweisung bitte bestätigen.</div>".PHP_EOL;
        $content.= "<form action='/adminItems/assign/".output($id)."/add/".output($addId)."' method='post'>";
        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Hinzufügen?</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Ja'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      } else {
        if($_POST['token'] == $adminSessionHash) {
          /**
           * Token gültig.
           */
          if(mysqli_query($dbl, "INSERT INTO `categoryItems` (`categoryId`, `itemId`) VALUES ('".$addId."', '".$id."')")) {
            adminLog($adminUserId, 8, $id, $addId, "Kategorie zugewiesen");
            $content.= "<div class='successbox'>Zuweisung erfolgreich angelegt.</div>".PHP_EOL;
          } else {
            if(mysqli_errno($dbl) == 1062) {
              $content.= "<div class='warnbox'>Es existiert bereits eine solche Zuweisung.</div>".PHP_EOL;
            } elseif(mysqli_errno($dbl) == 1452) {
              $content.= "<div class='warnbox'>Diese Kombination kann nicht angelegt werden.</div>".PHP_EOL;
            } else {
              $content.= "<div class='warnbox'>Unbekannter Fehler (".mysqli_errno($dbl)."): ".mysqli_error($dbl)."</div>".PHP_EOL;
            }
          }
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        }
      }
      $content.= "<div class='spacer-m'></div>".PHP_EOL;
    }
    /**
     * Löschen von Zuweisungen
     */
    if(isset($_GET['del']) AND !empty($_GET['del'])) {
      $delId = (int)defuse($_GET['del']);
      if(!isset($_POST['submit'])) {
        /**
         * CSRF Bestätigung
         */
        $content.= "<h1>Löschen bestätigen</h1>".PHP_EOL;
        $content.= "<div class='infobox'>Löschung bitte bestätigen.</div>".PHP_EOL;
        $content.= "<form action='/adminItems/assign/".output($id)."/del/".output($delId)."' method='post'>";
        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Löschen?</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Ja'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      } else {
        if($_POST['token'] == $adminSessionHash) {
          /**
           * Token gültig.
           */
          $result = mysqli_query($dbl, "SELECT `categories`.`id` FROM `categoryItems` JOIN `categories` ON `categoryItems`.`categoryId`=`categories`.`id` WHERE `categoryItems`.`id`='".$delId."' AND `itemId`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "DELETE FROM `categoryItems` WHERE `id`='".$delId."' AND `itemId`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            $content.= "<div class='successbox'>Die Zuweisung wurde gelöscht.</div>".PHP_EOL;
            $row = mysqli_fetch_array($result);
            adminLog($adminUserId, 8, $id, $row['id'], "Kategoriezuweisung entfernt");
          } else {
            $content.= "<div class='warnbox'>Es existiert für dieses Rezept keine Kategoriezuweisung mit der ID <span class='italic'>".output($delId)."</span>.</div>".PHP_EOL;
          }
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        }
      }
      $content.= "<div class='spacer-m'></div>".PHP_EOL;
    }
    /**
     * Bestehende Zuweisungen anzeigen
     */
    $content.= "<h2>Bestehende Zuweisungen</h2>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT `categoryItems`.`id`, `categoryItems`.`categoryId`, `categories`.`title` FROM `categoryItems` LEFT JOIN `categories` ON `categoryItems`.`categoryId`=`categories`.`id` WHERE `categoryItems`.`itemId`='".$id."'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Dieses Rezept wurde noch keiner Kategorie zugewiesen.</div>".PHP_EOL;
    } else {
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-8 col-m-8 col-l-8 col-xl-8'>Kategorie</div>".PHP_EOL.
      "<div class='col-x-12 col-s-4 col-m-4 col-l-4 col-xl-4'>Aktionen</div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-8 col-m-8 col-l-8 col-xl-8'>".$row['title']."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-4 col-m-4 col-l-4 col-xl-4'><a href='/adminItems/assign/".output($id)."/del/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a><br>".PHP_EOL."<a href='/adminCategories/sort/".$row['categoryId']."' class='nowrap'><span class='fas icon'>&#xf0dc;</span>in dieser Kategorie sortieren</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
    /**
     * Neue Zuweisungen erstellen
     */
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
    $content.= "<h2>Neu zuweisen</h2>".PHP_EOL;
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-8 col-m-8 col-l-8 col-xl-8'>Kategorie</div>".PHP_EOL.
    "<div class='col-x-12 col-s-4 col-m-4 col-l-4 col-xl-4'>Aktionen</div>".PHP_EOL.
    "</div>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT `categories`.`id`, `categories`.`title`, `categories`.`shortTitle`, `categories`.`shortDescription`, (SELECT COUNT(`id`) FROM `categoryItems` WHERE `categoryItems`.`categoryId`=`categories`.`id` AND `categoryItems`.`itemId`='".$id."') AS `isset` FROM `categories` ORDER BY `categories`.`sortIndex` ASC, `categories`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-8 col-m-8 col-l-8 col-xl-8'>".output($row['title'])."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-4 col-m-4 col-l-4 col-xl-4'>".($row['isset'] == 1 ? "bereits zugewiesen" : "<a href='/adminItems/assign/".output($id)."/add/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf067;</span>Hinzufügen</a>")."</div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminItems/list");
  die();
}
?>
