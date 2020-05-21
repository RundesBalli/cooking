<?php
/**
 * adminIngredients.php
 * 
 * Seite um Zutaten und Maßeinheiten anzuzeigen, anzulegen, zu bearbeiten oder zu löschen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Zutaten und Maßeinheiten.
   */
  header("Location: /adminIngredients/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflisten aller Zutaten.
   */
  $title = "Zutaten und Maßeinheiten anzeigen";
  $content.= "<h1><span class='fas icon'>&#xf4d8;</span>Zutaten</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminIngredients/addI'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  /**
   * Selektieren aller Zutaten.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Zutaten existieren.
     */
    $content.= "<div class='infobox'>Noch keine Zutaten angelegt.</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Zutaten.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-7 col-s-7 col-m-8 col-l-8 col-xl-8'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-3 col-s-3 col-m-2 col-l-2 col-xl-2'>Suchbar</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-7 col-s-7 col-m-8 col-l-8 col-xl-8'>".output($row['title'])."</div>".PHP_EOL.
      "<div class='col-x-3 col-s-3 col-m-2 col-l-2 col-xl-2'>".($row['searchable'] == 1 ? "Ja" : "Nein")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminIngredients/editI/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a><br>".PHP_EOL."<a href='/adminIngredients/delI/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
  $content.= "<div class='spacer-m'></div>".PHP_EOL;

  /**
   * Auflisten aller Maßeinheiten.
   */
  $content.= "<h1><span class='fas icon'>&#xf24e;</span>Maßeinheiten</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminIngredients/addU'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  /**
   * Selektieren aller Maßeinheiten.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Maßeinheiten existieren.
     */
    $content.= "<div class='infobox'>Noch keine Maßeinheiten angelegt.</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Maßeinheiten.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-7 col-s-7 col-m-5 col-l-5 col-xl-5'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-3 col-s-3 col-m-3 col-l-3 col-xl-3'>Kurzform</div>".PHP_EOL.
    "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Trennung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-7 col-s-7 col-m-5 col-l-5 col-xl-5'>".output($row['title'])."</div>".PHP_EOL.
      "<div class='col-x-3 col-s-3 col-m-3 col-l-3 col-xl-3'>".output($row['short'])."</div>".PHP_EOL.
      "<div class='col-x-1 col-s-1 col-m-2 col-l-2 col-xl-2'>".($row['spacer'] == 1 ? "Ja" : "Nein")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminIngredients/editU/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf044;</span>Editieren</a><br>".PHP_EOL."<a href='/adminIngredients/delU/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
} elseif($_GET['action'] == 'addI') {
  /**
   * Zutat anlegen.
   */
  $title = "Zutat anlegen";
  $content.= "<h1><span class='fas icon'>&#xf4d8;</span>Zutat anlegen</h1>".PHP_EOL;
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
     * Bezeichnung
     */
    if(preg_match('/^.{2,150}$/', $_POST['title'], $match) === 1) {
      $formTitle = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Bezeichnung der Zutat ist ungültig. Sie muss zwischen 2 und 150 Zeichen lang sein.</div>".PHP_EOL;
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
      if(mysqli_query($dbl, "INSERT INTO `metaIngredients` (`title`, `searchable`) VALUES ('".$formTitle."', '".$searchable."')")) {
        adminLog($adminUserId, 2, NULL, NULL, "Zutat angelegt: `".$formTitle."`");
        $content.= "<div class='successbox'>Zutat erfolgreich angelegt.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/addI'><span class='fas icon'>&#xf067;</span>eine weitere Zutat anlegen</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        $form = 1;
        if(mysqli_errno($dbl) == 1062) {
          $content.= "<div class='warnbox'>Es existiert bereits eine Zutat mit dieser Bezeichnung.</div>".PHP_EOL;
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
    $content.= "<form action='/adminIngredients/addI' method='post' autocomplete='off'>".PHP_EOL;
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
     * Bezeichnung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Bezeichnung der Zutat' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>2 bis 150 Zeichen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Suchbar
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Suchbar?</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='searchable' tabindex='2'><option value='' selected hidden disabled>Bitte auswählen</option><option value='1'>Ja</option><option value='0'>Nein</option></select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("Zutat suchbar machen.\nEin Beispiel für `nein` wäre \"Salz\".")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutat anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='3'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'editI') {
  /**
   * Zutat editieren.
   */
  $title = "Zutat editieren";
  $content.= "<h1><span class='fas icon'>&#xf4d8;</span>Zutat editieren</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Zutat existiert.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Zutat nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Zutat mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Zutat existiert, dann prüfe ob das Formular übergeben wurde.
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
       * Bezeichnung
       */
      if(preg_match('/^.{2,150}$/', $_POST['title'], $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Bezeichnung der Zutat ist ungültig. Sie muss zwischen 2 und 150 Zeichen lang sein.</div>".PHP_EOL;
      }
      /**
       * Suchbar-Flag
       */
      if(!empty($_POST['searchable']) AND $_POST['searchable'] == 1) {
        $searchable = 1;
      } else {
        $searchable = 0;
      }
      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `metaIngredients` SET `title`='".$formTitle."', `searchable`='".$searchable."' WHERE `id`='".$id."' LIMIT 1")) {
          adminLog($adminUserId, 3, NULL, NULL, "Zutat geändert: `".$formTitle."`");
          $content.= "<div class='successbox'>Zutat erfolgreich geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits eine Zutat mit dieser Bezeichnung.</div>".PHP_EOL;
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
      $content.= "<form action='/adminIngredients/editI/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
       * Bezeichnung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Bezeichnung der Zutat' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>2 bis 150 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Suchbar
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Suchbar?</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='searchable' tabindex='2'><option value='1'".(isset($row['searchable']) ? ($row['searchable'] == 1 ? " selected" : NULL) : (isset($_POST['searchable']) && $_POST['searchable'] == 1 ? " selected" : NULL)).">Ja</option><option value='0'".(isset($row['searchable']) ? ($row['searchable'] == 0 ? " selected" : NULL) : (isset($_POST['searchable']) && $_POST['searchable'] == 0 ? " selected" : NULL)).">Nein</option></select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("Zutat suchbar machen.\nEin Beispiel für `nein` wäre \"Salz\".")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='3'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'delI') {
  /**
   * Zutat löschen.
   */
  $title = "Zutat löschen";
  $content.= "<h1><span class='fas icon'>&#xf2ed;</span>Zutat löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Zutat existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaIngredients` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Zutat nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Zutat mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Zutat existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Zutat <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Dies funktioniert nur, wenn sie in keinem Rezept verwendet wird.</div>".PHP_EOL.
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
      $content.= "<form action='/adminIngredients/delI/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
          $result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $row = mysqli_fetch_array($result);
          if(mysqli_query($dbl, "DELETE FROM `metaIngredients` WHERE `id`='".$id."' LIMIT 1")) {
            adminLog($adminUserId, 4, NULL, NULL, "Zutat gelöscht: `".$row['title']."`");
            $content.= "<div class='successbox'>Zutat erfolgreich gelöscht.</div>".PHP_EOL;
          } elseif(mysqli_errno($dbl) == 1451) {
            $content.= "<div class='warnbox'>Die Zutat ist noch in Rezepten zugewiesen. Es müssen zuerst alle Zuweisungen entfernt werden, bevor die Zutat gelöscht werden kann.</div>".PHP_EOL;
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>".PHP_EOL;
          }
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Zutat unverändert.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'addU') {
  /**
   * Maßeinheit anlegen.
   */
  $title = "Maßeinheit anlegen";
  $content.= "<h1><span class='fas icon'>&#xf24e;</span>Maßeinheit anlegen</h1>".PHP_EOL;
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
     * Bezeichnung
     */
    if(preg_match('/^.{2,50}$/', $_POST['title'], $match) === 1) {
      $formTitle = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Bezeichnung der Maßeinheit ist ungültig. Sie muss zwischen 2 und 50 Zeichen lang sein.</div>".PHP_EOL;
    }
    /**
     * Kurzform
     */
    if(preg_match('/^.{1,10}$/', $_POST['short'], $match) === 1) {
      $short = defuse($match[0]);
    } else {
      $form = 1;
      $content.= "<div class='warnbox'>Die Kurzform der Maßeinheit ist ungültig. Sie muss zwischen 1 und 10 Zeichen lang sein.</div>".PHP_EOL;
    }
    /**
     * Spacer zwischen Titel und Einheit
     */
    if($_POST['spacer'] == 1) {
      $spacer = 1;
    } else {
      $spacer = 0;
    }
    /**
     * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
     */
    if($form == 0) {
      if(mysqli_query($dbl, "INSERT INTO `metaUnits` (`title`, `short`, `spacer`) VALUES ('".$formTitle."', '".$short."', '".$spacer."')")) {
        adminLog($adminUserId, 2, NULL, NULL, "Maßeinheit angelegt: `".$formTitle."`");
        $content.= "<div class='successbox'>Maßeinheit erfolgreich angelegt.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/addU'><span class='fas icon'>&#xf067;</span>eine weitere Maßeinheit anlegen</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        $form = 1;
        if(mysqli_errno($dbl) == 1062) {
          $content.= "<div class='warnbox'>Es existiert bereits eine Maßeinheit mit dieser Bezeichnung oder dieser Kurzform.</div>".PHP_EOL;
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
    $content.= "<form action='/adminIngredients/addU' method='post' autocomplete='off'>".PHP_EOL;
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
     * Bezeichnung
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Bezeichnung der Maßeinheit' tabindex='1' autofocus value='".(isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>2 bis 50 Zeichen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Kurzform
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzform</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='short' placeholder='Kurzform der Maßeinheit' tabindex='2' autofocus value='".(isset($_POST['short']) && !empty($_POST['short']) ? output($_POST['short']) : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>1 bis 10 Zeichen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Trennung zwischen Menge und Kurzform
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Trennung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='spacer' tabindex='3'><option value='' ".(!isset($_POST['spacer']) OR ($_POST['spacer'] != "1" && $_POST['spacer'] != "0") ? "selected " : NULL)."disabled hidden>Bitte wählen</option><option value='1'".(isset($_POST['spacer']) && $_POST['spacer'] == "1" ? " selected" : NULL).">Ja</option><option value='0'".(isset($_POST['spacer']) && $_POST['spacer'] == "0" ? " selected" : NULL).">Nein</option></select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("Trennung zwischen Menge und Kurzform\n* Ja: `100 g`\n* Nein: `100g`")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Maßeinheit anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'editU') {
  /**
   * Maßeinheit editieren.
   */
  $title = "Maßeinheit editieren";
  $content.= "<h1><span class='fas icon'>&#xf4d8;</span>Maßeinheit editieren</h1>".PHP_EOL;
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
    $content.= "<div class='warnbox'>Die Maßeinheit mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUnits/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Maßeinheit existiert, dann prüfe ob das Formular übergeben wurde.
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
       * Bezeichnung
       */
      if(preg_match('/^.{2,50}$/', $_POST['title'], $match) === 1) {
        $formTitle = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Bezeichnung der Maßeinheit ist ungültig. Sie muss zwischen 2 und 50 Zeichen lang sein.</div>".PHP_EOL;
      }
      /**
       * Kurzform
       */
      if(preg_match('/^.{1,10}$/', $_POST['short'], $match) === 1) {
        $short = defuse($match[0]);
      } else {
        $form = 1;
        $content.= "<div class='warnbox'>Die Kurzform der Maßeinheit ist ungültig. Sie muss zwischen 1 und 10 Zeichen lang sein.</div>".PHP_EOL;
      }
      /**
       * Spacer zwischen Titel und Einheit
       */
      if($_POST['spacer'] == 1) {
        $spacer = 1;
      } else {
        $spacer = 0;
      }
      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `metaUnits` SET `title`='".$formTitle."', `short`='".$short."', `spacer`='".$spacer."' WHERE `id`='".$id."' LIMIT 1")) {
          adminLog($adminUserId, 3, NULL, NULL, "Maßeinheit geändert: `".$formTitle."`");
          $content.= "<div class='successbox'>Maßeinheit erfolgreich geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $form = 1;
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits eine Maßeinheit mit dieser Bezeichnung oder dieser Kurzform.</div>".PHP_EOL;
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
      $content.= "<form action='/adminIngredients/editU/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
       * Bezeichnung
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Bezeichnung der Maßeinheit' tabindex='1' autofocus value='".(isset($row['title']) ? output($row['title']) : (isset($_POST['title']) && !empty($_POST['title']) ? output($_POST['title']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>2 bis 50 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Kurzform
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzform</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='short' placeholder='Kurzform der Maßeinheit' tabindex='2' autofocus value='".(isset($row['short']) ? output($row['short']) : (isset($_POST['short']) && !empty($_POST['short']) ? output($_POST['short']) : NULL))."'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>1 bis 10 Zeichen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Trennung zwischen Menge und Kurzform
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Trennung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='spacer' tabindex='3'><option value='1'".(isset($row['spacer']) && $row['spacer'] == 1 ? " selected" : (isset($_POST['spacer']) && $_POST['spacer'] == "1" ? " selected" : NULL)).">Ja</option><option value='0'".(isset($row['spacer']) && $row['spacer'] == 0 ? " selected" : (isset($_POST['spacer']) && $_POST['spacer'] == "0" ? " selected" : NULL)).">Nein</option></select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("Trennung zwischen Menge und Kurzform\n* Ja: `100 g`\n* Nein: `100g`")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'delU') {
  /**
   * Maßeinheit löschen.
   */
  $title = "Maßeinheit löschen";
  $content.= "<h1><span class='fas icon'>&#xf2ed;</span>Maßeinheit löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Maßeinheit existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Maßeinheit nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Maßeinheit mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Maßeinheit existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Maßeinheit <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden? Dies funktioniert nur, wenn sie in keinem Rezept verwendet wird.</div>".PHP_EOL.
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
      $content.= "<form action='/adminIngredients/delU/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
          $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $row = mysqli_fetch_array($result);
          if(mysqli_query($dbl, "DELETE FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1")) {
            adminLog($adminUserId, 4, NULL, NULL, "Maßeinheit gelöscht: `".$row['title']."`");
            $content.= "<div class='successbox'>Maßeinheit erfolgreich gelöscht.</div>".PHP_EOL;
          } elseif(mysqli_errno($dbl) == 1451) {
            $content.= "<div class='warnbox'>Die Maßeinheit ist noch in Rezepten zugewiesen. Es müssen zuerst alle Zuweisungen entfernt werden, bevor die Maßeinheit gelöscht werden kann.</div>".PHP_EOL;
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler: ".mysqli_error($dbl)."</div>".PHP_EOL;
          }
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Im Select wurde etwas anderes als "ja" ausgewählt.
         */
        $content.= "<div class='infobox'>Zutat unverändert.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminIngredients/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} elseif($_GET['action'] == 'assign') {
  /**
   * Einem Rezept Zutaten zuweisen.
   */
  $title = "Zutaten bearbeiten";
  $content.= "<h1><span class='far icon'>&#xf07c;</span>Zutaten bearbeiten</h1>".PHP_EOL;
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
     * Neue Zuweisungen anlegen
     */
    $content.= "<h2>Neue Zuweisungen anlegen</h2>".PHP_EOL;
    $form = 1;
    /**
     * Selektieren der Zutaten
     */
    $result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $form = 0;
      $content.= "<div class='warnbox'>Es müssen zuerst Zutaten angelegt werden.</div>".PHP_EOL;
    } else {
      $ingredients = array();
      while($row = mysqli_fetch_array($result)) {
        $ingredients[] = "<option value='".$row['id']."'>".output($row['title'])."</option>";
      }
      $ingredients = "<select name='ingredient' tabindex='1' autofocus><option value='' selected disabled hidden>Bitte wählen</option>".implode("", $ingredients)."</select>";
    }
    /**
     * Selektieren der Einheiten
     */
    $result = mysqli_query($dbl, "SELECT * FROM `metaUnits` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $form = 0;
      $content.= "<div class='warnbox'>Es müssen zuerst Maßeinheiten angelegt werden.</div>".PHP_EOL;
    } else {
      $units = array();
      while($row = mysqli_fetch_array($result)) {
        $units[] = "<option value='".$row['id']."'>".output($row['title'])." (".output($row['short']).")</option>";
      }
      $units = "<select name='unit' tabindex='3'><option value='' selected disabled hidden>Bitte wählen</option>".implode("", $units)."</select>";
    }
    /**
     * Formular anzeigen, wenn Daten vorhanden
     */
    if($form == 1) {
      $content.= "<form action='/adminIngredients/assign/".output($id)."' method='post' autocomplete='off'>".PHP_EOL;
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
       * Zutat
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutat</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".$ingredients."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Menge
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Menge</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='quantity' tabindex='2' placeholder='Menge'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Kommazahlen möglich.<br>Wenn die Menge 0 ist, wird die Zutat einfach so angezeigt.<br>0x ABC = ABC</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Einheit
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Einheit</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".$units."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Wird ignoriert, wenn Menge = 0</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutat zuweisen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='4'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "</form>".PHP_EOL;
    }
    /**
     * Nach Absenden des Formulars zur Anlage einer Zuweisung
     */
    if(isset($_POST['submit'])) {
      $add = 1;
      /**
       * Sitzungstoken
       */
      if($_POST['token'] != $adminSessionHash) {
        $add = 0;
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
      }
      /**
       * Zutat
       */
      if(!empty($_POST['ingredient'])) {
        $ingredient = (int)defuse($_POST['ingredient']);
        $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaIngredients` WHERE `id`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $add = 0;
          $content.= "<div class='warnbox'>Die Zutat existiert nicht.</div>".PHP_EOL;
        } else {
          $row = mysqli_fetch_array($result);
          $ingredientTitle = $row['title'];
        }
      } else {
        $add = 0;
        $content.= "<div class='warnbox'>Die Zutat ist ungültig.</div>".PHP_EOL;
      }
      /**
       * Menge & Maßeinheit
       */
      if(empty($_POST['quantity']) OR floatval(str_replace(",", ".", $_POST['quantity'])) <= 0) {
        $quantity = NULL;
        $unit = NULL;
      } else {
        $quantity = (float)str_replace(",", ".", defuse($_POST['quantity']));
        /**
         * Wenn eine Positive Menge übergeben wurde, dann ist die Maßeinheit relevant.
         */
        if(!empty($_POST['unit'])) {
          $unit = (int)defuse($_POST['unit']);
          $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `metaUnits` WHERE `id`='".$unit."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_num_rows($result) == 0) {
            $add = 0;
            $content.= "<div class='warnbox'>Die Maßeinheit existiert nicht.</div>".PHP_EOL;
          } else {
            $row = mysqli_fetch_array($result);
            $unitTitle = $row['title'];
          }
        } else {
          $add = 0;
          $content.= "<div class='warnbox'>Die Maßeinheit ist ungültig.</div>".PHP_EOL;
        }
      }
      if($add == 1) {
        /**
         * Alle Daten sind ok und können angelegt oder aktualisiert werden.
         */
        /**
         * Prüfung, ob die Kombination itemId/ingredientId schon existiert.
         */
        $result = mysqli_query($dbl, "SELECT `id` FROM `itemIngredients` WHERE `itemId`='".$id."' AND `ingredientId`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 1) {
          /**
           * Kombination existiert. Datensatz wird aktualisiert.
           */
          mysqli_query($dbl, "UPDATE `itemIngredients` SET `unitId`=".($unit === NULL ? NULL : "'".$unit."'").", `quantity`=".($quantity === NULL ? "NULL" : "'".$quantity."'")." WHERE `itemId`='".$id."' AND `ingredientId`='".$ingredient."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            /**
             * Aktualisierung erfolgreich.
             */
            adminLog($adminUserId, 8, $id, NULL, "Zuweisung aktualisiert: qty: ".($quantity === NULL ? "NULL" : "`".$quantity."`")."; unit: ".($unit === NULL ? "NULL" : "`".$unitTitle."`")."; ing: `".$ingredientTitle."`");
            $content.= "<div class='successbox'>Datensatz aktualisiert.</div>".PHP_EOL;
          } else {
            /**
             * Aktualisierung schlug fehl.
             */
            $content.= "<div class='warnbox'>Datensatz konnte nicht aktualisiert werden.</div>".PHP_EOL;
          }
        } else {
          /**
           * Kombination existiert nicht. Datensatz wird angelegt.
           */
          mysqli_query($dbl, "INSERT INTO `itemIngredients` (`itemId`, `ingredientId`, `unitId`, `quantity`) VALUES ('".$id."', '".$ingredient."', ".($unit === NULL ? "NULL" : "'".$unit."'").", ".($quantity === NULL ? "NULL" : "'".$quantity."'").")") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            /**
             * Eintrag erfolgreich.
             */
            adminLog($adminUserId, 8, $id, NULL, "Zuweisung angelegt: qty: ".($quantity === NULL ? "NULL" : "`".$quantity."`")."; unit: ".($unit === NULL ? "NULL" : "`".$unitTitle."`")."; ing: `".$ingredientTitle."`");
            $content.= "<div class='successbox'>Zuweisung angelegt.</div>".PHP_EOL;
          } else {
            /**
             * Eintrag schlug fehl.
             */
            $content.= "<div class='warnbox'>Zuweisung konnte nicht angelegt werden.</div>".PHP_EOL;
          }
        }
      }
    }
    $content.= "<div class='spacer-l'></div>".PHP_EOL;

    /**
     * Zugewiesene Zutaten anzeigen
     */
    $content.= "<h2>Bestehende Zuweisungen</h2>".PHP_EOL;
    if(!empty($_GET['del'])) {
      /**
       * Zuweisungen entfernen
       */
      $del = (int)defuse($_GET['del']);
      if(!isset($_POST['delsubmit'])) {
        /**
         * Formular noch nicht abgesendet.
         */
        $content.= "<div class='infobox'>Soll die Zuweisung wirklich gelöscht werden?</div>".PHP_EOL;
        /**
         * Es wird ein "verwirrendes" Select-Feld gebaut, damit die "ja"-Option jedes mal woanders steht und man bewusster löscht.
         */
        $options = array(1 => "Ja, wirklich löschen", 2 => "nein, nicht löschen", 3 => "nope", 4 => "auf keinen Fall", 5 => "nö", 6 => "hab es mir anders überlegt");
        $options1 = array();
        foreach($options as $key => $val) {
          $options1[] = "<option value='".$key."'>".$val."</option>".PHP_EOL;
        }
        shuffle($options1);
        $content.= "<form action='/adminIngredients/assign/".output($id)."/del/".output($del)."' method='post' autocomplete='off'>".PHP_EOL;
        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><select name='selection'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL.implode("", $options1)."</select></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><input type='submit' name='delsubmit' value='Löschen'></div>".PHP_EOL.
        "<div class='col-x-0 col-s-0 col-m-0 col-l-4 col-xl-4'></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      } else {
        /**
         * Formular abgesendet.
         */
        if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
          /**
           * Token Überprüfung
           */
          if($_POST['token'] == $adminSessionHash) {
            /**
             * Kann gelöscht werden
             */
            $result = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `itemIngredients`.`quantity` FROM `itemIngredients` JOIN `metaIngredients` ON `itemIngredients`.`ingredientId`=`metaIngredients`.`id` LEFT OUTER JOIN `metaUnits` ON `itemIngredients`.`unitId`=`metaUnits`.`id` WHERE `itemIngredients`.`id`='".$del."' AND `itemIngredients`.`itemId`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
            $row = mysqli_fetch_array($result);
            mysqli_query($dbl, "DELETE FROM `itemIngredients` WHERE `id`='".$del."' AND `itemId`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
            if(mysqli_affected_rows($dbl) == 1) {
              adminLog($adminUserId, 8, $id, NULL, "Zuweisung gelöscht: qty: ".($row['quantity'] === NULL ? "NULL" : "`".$row['quantity']."`")."; unit: ".($row['unitTitle'] === NULL ? "NULL" : "`".$row['unitTitle']."`")."; ing: `".$row['ingredientTitle']."`");
              $content.= "<div class='successbox'>Zuweisung erfolgreich gelöscht.</div>".PHP_EOL;
            } else {
              $content.= "<div class='warnbox'>Löschung schlug fehl.</div>".PHP_EOL;
            }
          } else {
            /**
             * Ungültiges Sitzungstoken
             */
            http_response_code(403);
            $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
          }
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>Zuweisung wurde nicht gelöscht.</div>".PHP_EOL;
        }
      }
    }
    $result = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `metaUnits`.`short`, `metaUnits`.`spacer`, `itemIngredients`.* FROM `itemIngredients` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` LEFT OUTER JOIN `metaUnits` ON `metaUnits`.`id` = `itemIngredients`.`unitId` WHERE `itemIngredients`.`itemId`='$id' ORDER BY `ingredientTitle` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Es wurden noch keine Zuweisungen angelegt.</div>".PHP_EOL;
    } else {
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Zum Ändern einfach die selbe Zutat nochmal anlegen. Wird dann aktualisiert.</div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-7 col-s-7 col-m-5 col-l-5 col-xl-5'>Zutat</div>".PHP_EOL.
      "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Menge</div>".PHP_EOL.
      "<div class='col-x-3 col-s-3 col-m-3 col-l-3 col-xl-3'>Einheit</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-7 col-s-7 col-m-5 col-l-5 col-xl-5'>".output($row['ingredientTitle'])."</div>".PHP_EOL.
        "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>".($row['quantity'] > 0 ? fractionizer($row['quantity'], 2) : "<span class='italic'>NULL</span>")."</div>".PHP_EOL.
        "<div class='col-x-3 col-s-3 col-m-3 col-l-3 col-xl-3'>".($row['unitTitle'] == NULL ? "<span class='italic'>NULL</span>" : output($row['unitTitle']))."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminIngredients/assign/".$id."/del/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
  }
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminIngredients/list");
  die();
}
?>
