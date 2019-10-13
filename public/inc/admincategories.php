<?php
/**
 * admincategories.php
 * 
 * Seite um Kategorien anzuzeigen, anzulegen, zu bearbeiten und zu löschen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Kategorien.
   */
  header("Location: /admincategories/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflistung aller Kategorien mit Anzahl der darin befindlichen Rezepte.
   */
  $title = "Kategorien anzeigen";
  $content.= "<h1>Kategorien anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/admincategories/add'>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  /**
   * Alle Kategorien selektieren und die zugewiesenen Rezepte zählen. Danke an @Insax für den Query.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle`, (SELECT COUNT(`id`) FROM `category_items` WHERE `category_items`.`category_id` = `categories`.`id`) AS `itemcount` FROM `categories` ORDER BY `sortindex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Kategorien existieren.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Noch keine Kategorien angelegt.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Kategorien.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Titel</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>zugewiesene Rezepte</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>".output($row['title'])."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$row['itemcount']." Rezept".($row['itemcount'] == 1 ? "" : "e")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/admincategories/edit/".$row['id']."' class='nowrap'>Editieren</a><br>".PHP_EOL."<a href='/admincategories/del/".$row['id']."' class='nowrap'>Löschen</a><br>".PHP_EOL."<a href='/admincategories/sort/".$row['id']."' class='nowrap'>Rezepte sortieren</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Kategorie anlegen.
   */
  $title = "Kategorie anlegen";
  $content.= "<h1>Kategorie anlegen</h1>".PHP_EOL;
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
     * Titel
     */
    if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
      $form_title = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Der Name der Kategorie ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>".PHP_EOL;
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
     * Sortierindex
     */
    if(preg_match('/^\d{1,10}$/', $_POST['sortIndex'], $match) === 1) {
      $sortIndex = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Der Sortierindex ist ungültig. Möglich sind alle positiven Ganzzahlen.</div>".PHP_EOL;
    }
    /**
     * Langbeschreibung
     */
    if(!empty($_POST['description'])) {
      $description = defuse($_POST['description']);
    } else {
      $description = NULL;
    }
    /**
     * Kurzbeschreibung
     */
    if(!empty($_POST['shortDescription'])) {
      $shortDescription = defuse($_POST['shortDescription']);
    } else {
      $shortDescription = NULL;
    }
    /**
     * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
     */
    if($form == 0) {
      if(mysqli_query($dbl, "INSERT INTO `categories` (`title`, `shortTitle`, `sortIndex`, `description`, `shortDescription`) VALUES ('".$form_title."', '".$shortTitle."', '".$sortIndex."', ".($description === NULL ? "NULL" : "'".$description."'").", ".($shortDescription === NULL ? "NULL" : "'".$shortDescription."'").")")) {
        $content.= "<div class='successbox'>Kategorie erfolgreich angelegt.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        $form = 1;
        if(mysqli_errno($dbl) == 1062) {
          $content.= "<div class='warnbox'>Es existiert bereits ein Eintrag mit diesem Kurztitel.</div>".PHP_EOL;
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
    $content.= "<form action='/admincategories/add' method='post' autocomplete='off'>".PHP_EOL;
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
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name der Kategorie</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name der Kategorie' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Navigation<br>5 bis 100 Zeichen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kurztitel
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='/kategorie/xxx' tabindex='2' value='".(isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Sortierindex
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Sortierindex</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='number' name='sortIndex' placeholder='z.B. 10' tabindex='3' min='1' value='".(isset($_POST['sortIndex']) && !empty($_POST['sortIndex']) ? output($_POST['sortIndex']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* aufsteigend sortiert, 1 = oben, 100 = unten\n* Möglich sind alle positiven Zahlen")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Langbeschreibung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Beschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='description' placeholder='Mehrzeilige Beschreibung' tabindex='4'>".(isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL)."</textarea></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für mehrzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird beim Aufruf der Kategorie angezeigt")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kurzbeschreibung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzeschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortDescription' placeholder='Kurzbeschreibung' tabindex='5' value='".(isset($_POST['shortDescription']) && !empty($_POST['shortDescription']) ? output($_POST['shortDescription']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für einzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird auf der Startseite angezeigt")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'><span class='highlight'>Info:</span> Die Kategorie wird sofort angezeigt.</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'del') {
  /**
   * Kategorie löschen.
   */
  $title = "Kategorie löschen";
  $content.= "<h1>Kategorie löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Kategorie existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Kategorie nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Kategorie existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Kategorie <span class='italic highlight'>".$row['title']."</span> wirklich gelöscht werden? Alle Rezeptzuweisungen werden dabei ebenfalls gelöscht (die Rezepte bleiben aber erhalten).</div>".PHP_EOL.
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
      $content.= "<form action='/admincategories/del/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
      if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Im Select wurde "ja" ausgewählt
         */
        mysqli_query($dbl, "DELETE FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Kategorie erfolgreich gelöscht.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Kategorie unverändert.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'edit') {
  /**
   * Kategorie editieren.
   */
  $title = "Kategorie editieren";
  $content.= "<h1>Kategorie editieren</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
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
       * Titel
       */
      if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
        $form_title = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Name der Kategorie ist ungültig. Er muss zwischen 5 und 100 Zeichen lang sein.</div>".PHP_EOL;
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
       * Sortierindex
       */
      if(preg_match('/^\d{1,10}$/', $_POST['sortIndex'], $match) === 1) {
        $sortIndex = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Der Sortierindex ist ungültig. Möglich sind alle positiven Ganzzahlen.</div>".PHP_EOL;
      }
      /**
       * Langbeschreibung
       */
      if(!empty(trim($_POST['description']))) {
        $description = defuse($_POST['description']);
      } else {
        $description = NULL;
      }
      /**
       * Kurzbeschreibung
       */
      if(!empty(trim($_POST['shortDescription']))) {
        $shortDescription = defuse($_POST['shortDescription']);
      } else {
        $shortDescription = NULL;
      }
      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `categories` SET `title`='".$form_title."', `shortTitle`='".$shortTitle."', `sortIndex`='".$sortIndex."', `description`=".($description === NULL ? "NULL" : "'".$description."'").", `shortDescription`=".($shortDescription === NULL ? "NULL" : "'".$shortDescription."'")." WHERE `id`='".$id."' LIMIT 1")) {
          $content.= "<div class='successbox'>Kategorie erfolgreich geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits ein Eintrag mit diesem Kurztitel.</div>".PHP_EOL;
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
      $row = mysqli_fetch_array($result);
    }
    /**
     * Das Formular wird beim Erstaufruf und bei Fehleingaben angezeigt.
     */
    if($form == 1) {
      $content.= "<form action='/admincategories/edit/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name der Kategorie</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name der Kategorie' tabindex='1' value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Navigation<br>5 bis 100 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurztitel
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='/kategorie/xxx' tabindex='2' value='".(isset($row['shortTitle']) ? output($row['shortTitle']) : (isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Sortierindex
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Sortierindex</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='number' name='sortIndex' placeholder='z.B. 10' tabindex='3' min='1' value='".(isset($row['sortIndex']) ? output($row['sortIndex']) : (isset($_POST['sortIndex']) && !empty($_POST['sortIndex']) ? output($_POST['sortIndex']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* aufsteigend sortiert, 1 = oben, 100 = unten\n* Möglich sind alle positiven Zahlen")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Langbeschreibung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Beschreibung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='description' placeholder='Mehrzeilige Beschreibung' tabindex='4'>".(isset($row['description']) ? output($row['description']) : (isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL))."</textarea></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für mehrzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird beim Aufruf der Kategorie angezeigt")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurzbeschreibung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzeschreibung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortDescription' placeholder='Kurzbeschreibung' tabindex='5' value='".(isset($row['shortDescription']) ? output($row['shortDescription']) : (isset($_POST['shortDescription']) && !empty($_POST['shortDescription']) ? output($_POST['shortDescription']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für einzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird auf der Startseite angezeigt")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'><span class='highlight'>Info:</span> Die Kategorie wird sofort angezeigt.</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'sort') {
  /**
   * SORTIEREN
   */
} else {
  header("Location: /admincategories/list");
  die();
}
?>
