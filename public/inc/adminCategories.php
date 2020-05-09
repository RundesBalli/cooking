<?php
/**
 * adminCategories.php
 * 
 * Seite um Kategorien anzuzeigen, anzulegen, zu bearbeiten und zu löschen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Kategorien.
   */
  header("Location: /adminCategories/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflistung aller Kategorien mit Anzahl der darin befindlichen Rezepte.
   */
  $title = "Kategorien anzeigen";
  $content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorien anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminCategories/add'><span class='fas icon'>&#xf067;</span>Anlegen</a> - <a href='/adminCategories/catsort'><span class='fas icon'>&#xf0dc;</span>Kategorien sortieren</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  /**
   * Alle Kategorien selektieren und die zugewiesenen Rezepte zählen. Danke an @Insax für den Query.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle`, (SELECT COUNT(`id`) FROM `categoryItems` WHERE `categoryItems`.`categoryId` = `categories`.`id`) AS `itemcount` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Kategorien existieren.
     */
    $content.= "<div class='infobox'>Noch keine Kategorien angelegt.</div>".PHP_EOL;
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
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><a href='/kategorie/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$row['itemcount']." Rezept".($row['itemcount'] == 1 ? "" : "e")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminCategories/edit/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a><br>".PHP_EOL."<a href='/adminCategories/del/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a><br>".PHP_EOL."<a href='/adminCategories/sort/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf0dc;</span>Rezepte sortieren</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Kategorie anlegen.
   */
  $title = "Kategorie anlegen";
  $content.= "<h1><span class='fas icon'>&#xf067;</span>Kategorie anlegen</h1>".PHP_EOL;
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
      $form = 1;
      http_response_code(403);
      $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
    }
    /**
     * Titel
     */
    if(preg_match('/^.{5,100}$/', $_POST['title'], $match) === 1) {
      $formTitle = defuse($match[0]);
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
      if(mysqli_query($dbl, "INSERT INTO `categories` (`title`, `shortTitle`, `description`, `shortDescription`) VALUES ('".$formTitle."', '".$shortTitle."', ".($description === NULL ? "NULL" : "'".$description."'").", ".($shortDescription === NULL ? "NULL" : "'".$shortDescription."'").")")) {
        $content.= "<div class='successbox'>Kategorie erfolgreich angelegt.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/kategorie/".output($shortTitle)."'><span class='far icon'>&#xf07c;</span>Zur Kategorie</a></div>".PHP_EOL.
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
    $content.= "<form action='/adminCategories/add' method='post' autocomplete='off'>".PHP_EOL;
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
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Langbeschreibung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Beschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='description' placeholder='Mehrzeilige Beschreibung' tabindex='4'>".(isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL)."</textarea></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* wird beim Aufruf der Kategorie angezeigt")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kurzbeschreibung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzeschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortDescription' placeholder='Kurzbeschreibung' tabindex='5' value='".(isset($_POST['shortDescription']) && !empty($_POST['shortDescription']) ? output($_POST['shortDescription']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für einzeilige Textfelder](/adminMarkdownInfo)* möglich\n* wird auf der Startseite angezeigt")."</div>".PHP_EOL.
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
  $content.= "<h1><span class='fas icon'>&#xf2ed;</span>Kategorie löschen</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
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
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Kategorie <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Alle Rezeptzuweisungen werden dabei ebenfalls gelöscht (die Rezepte bleiben aber erhalten).</div>".PHP_EOL.
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
      $content.= "<form action='/adminCategories/del/".output($id)."' method='post' autocomplete='off'>".PHP_EOL;
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
      if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
        /**
         * Im Select wurde "ja" ausgewählt, jetzt wird das Sitzungstoken geprüft.
         */
        if($_POST['token'] == $adminSessionHash) {
          mysqli_query($dbl, "DELETE FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Kategorie erfolgreich gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Kategorie unverändert.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'edit') {
  /**
   * Kategorie editieren.
   */
  $title = "Kategorie editieren";
  $content.= "<h1><span class='fas icon'>&#xf044;</span>Kategorie editieren</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
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
        if(mysqli_query($dbl, "UPDATE `categories` SET `title`='".$formTitle."', `shortTitle`='".$shortTitle."', `description`=".($description === NULL ? "NULL" : "'".$description."'").", `shortDescription`=".($shortDescription === NULL ? "NULL" : "'".$shortDescription."'")." WHERE `id`='".$id."' LIMIT 1")) {
          $content.= "<div class='successbox'>Kategorie erfolgreich geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/kategorie/".output($shortTitle)."'><span class='far icon'>&#xf07c;</span>Zur Kategorie</a></div>".PHP_EOL.
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
      $content.= "<form action='/adminCategories/edit/".output($id)."' method='post' autocomplete='off'>".PHP_EOL;
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
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name der Kategorie</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name der Kategorie' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Navigation<br>5 bis 100 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurztitel
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='Kurztitel' tabindex='2' value='".(isset($row['shortTitle']) ? output($row['shortTitle']) : (isset($_POST['shortTitle']) && !empty($_POST['shortTitle']) ? output($_POST['shortTitle']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/kategorie/&lt;Kurztitel&gt;`\n* muss einzigartig sein\n* 5 bis 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Langbeschreibung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Beschreibung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='description' placeholder='Mehrzeilige Beschreibung' tabindex='4'>".(isset($row['description']) ? output($row['description']) : (isset($_POST['description']) && !empty($_POST['description']) ? output($_POST['description']) : NULL))."</textarea></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für mehrzeilige Textfelder](/adminMarkdownInfo)* möglich\n* wird beim Aufruf der Kategorie angezeigt")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurzbeschreibung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzeschreibung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortDescription' placeholder='Kurzbeschreibung' tabindex='5' value='".(isset($row['shortDescription']) ? output($row['shortDescription']) : (isset($_POST['shortDescription']) && !empty($_POST['shortDescription']) ? output($_POST['shortDescription']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* _optional_\n* [Markdown für einzeilige Textfelder](/adminMarkdownInfo)* möglich\n* wird auf der Startseite angezeigt")."</div>".PHP_EOL.
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
   * Elemente / Querverweise innerhalb der Kategorie sortieren.
   */
  $title = "Innerhalb der Kategorie sortieren";
  $content.= "<h1><span class='fas icon'>&#xf0dc;</span>Innerhalb der Kategorie sortieren</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    if(!isset($_POST['submit'])) {
      /**
       * Selektieren der zugewiesenen Rezepte.
       */
      $result = mysqli_query($dbl, "SELECT `categoryItems`.`id`, `categoryItems`.`sortIndex`, `items`.`title`, `items`.`shortTitle` FROM `categoryItems` LEFT JOIN `items` ON `categoryItems`.`itemId`=`items`.`id` WHERE `categoryItems`.`categoryId` = '".$id."' ORDER BY `categoryItems`.`sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        $content.= "<div class='warnbox'>Dieser Kategorie sind keine Rezepte zugewiesen.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } elseif(mysqli_num_rows($result) == 1) {
        $content.= "<div class='infobox'>Dieser Kategorie ist nur ein Rezept zugewiesen. Eine Sortierung macht keinen Sinn.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        /**
         * Wenn kein Formular übergeben wurde, dann zeig es an.
         */
        $content.= "<form action='/adminCategories/sort/".output($id)."' method='post' autocomplete='off'>".PHP_EOL;
        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
        /**
         * Tabellenüberschrift
         */
        $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
        "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'>Sortierindex</div>".PHP_EOL.
        "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'>Rezept</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
        /**
         * Durchgehen der einzelnen Zuweisungen.
         */
        $tabindex = 0;
        while($row = mysqli_fetch_array($result)) {
          $tabindex++;
          $content.= "<div class='row hover bordered'>".PHP_EOL.
          "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='number' name='ci[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'></div>".PHP_EOL.
          "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
        $tabindex++;
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='submit' name='submit' value='ändern' tabindex='".$tabindex."'></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      }
    } else {
      /**
       * Formularauswertung
       */
      if($_POST['token'] == $adminSessionHash) {
        if(isset($_POST['ci']) AND is_array($_POST['ci'])) {
          asort($_POST['ci']);
          $index = 0;
          $query = "UPDATE `categoryItems` SET `sortIndex` = CASE ";
          foreach($_POST['ci'] as $key => $val) {
            $key = (int)defuse($key);
            $index+= 10;
            $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
          }
          $query.= "ELSE '9999999' END WHERE `categoryId`='".$id."'";
          mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Sortierung geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/sort/".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/sort/".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'catsort') {
  /**
   * Alle Kategorien sortieren
   */
  $title = "Kategorien sortieren";
  $content.= "<h1><span class='fas icon'>&#xf0dc;</span>Kategorien sortieren</h1>".PHP_EOL;
  if(!isset($_POST['submit'])) {
    /**
     * Wenn das Formular noch nicht übergeben wurde, dann zeig es an.
     */
    $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle`, `sortIndex` FROM `categories` ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Wenn noch keine Kategorien angelegt wurden.
       */
      $content.= "<div class='infobox'>Noch keine Kategorien angelegt.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } elseif(mysqli_num_rows($result) == 1) {
      /**
       * Wenn erst eine Kategorie angelegt wurde.
       */
      $content.= "<div class='infobox'>Es wurde erst eine Kategorie angelegt. Ein Sortieren hätte keine Auswirkungen.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Formularanzeige
       */
      $content.= "<form action='/adminCategories/catsort' method='post' autocomplete='off'>".PHP_EOL;
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'>Sortierindex</div>".PHP_EOL.
      "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'>Kategorie</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Durchgehen der einzelnen Kategorien.
       */
      $tabindex = 0;
      while($row = mysqli_fetch_array($result)) {
        $tabindex++;
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='number' name='cat[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'></div>".PHP_EOL.
        "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'><a href='/kategorie/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
      $tabindex++;
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='submit' name='submit' value='ändern' tabindex='".$tabindex."'></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
  } else {
    /**
     * Formularauswertung
     */
    if($_POST['token'] == $adminSessionHash) {
      if(isset($_POST['cat']) AND is_array($_POST['cat'])) {
        asort($_POST['cat']);
        $index = 0;
        $query = "UPDATE `categories` SET `sortIndex` = CASE ";
        foreach($_POST['cat'] as $key => $val) {
          $key = (int)defuse($key);
          $index+= 10;
          $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
        }
        $query.= "ELSE '9999999' END";
        mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='successbox'>Sortierung geändert.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/catsort'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    } else {
      /**
       * Ungültiges Sitzungstoken
       */
      http_response_code(403);
      $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminCategories/catsort'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} else {
  header("Location: /adminCategories/list");
  die();
}
?>
