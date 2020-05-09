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
    "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-10 col-l-10 col-xl-10'>".output($row['title'])."</div>".PHP_EOL.
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
    "<div class='col-x-9 col-s-9 col-m-7 col-l-7 col-xl-7'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>Kurzform</div>".PHP_EOL.
    "<div class='col-x-1 col-s-1 col-m-1 col-l-1 col-xl-1'>Trennung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-9 col-s-9 col-m-7 col-l-7 col-xl-7'>".output($row['title'])."</div>".PHP_EOL.
      "<div class='col-x-2 col-s-2 col-m-2 col-l-2 col-xl-2'>".output($row['short'])."</div>".PHP_EOL.
      "<div class='col-x-1 col-s-1 col-m-1 col-l-1 col-xl-1'>".($row['spacer'] == 1 ? "Ja" : "Nein")."</div>".PHP_EOL.
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
     * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
     */
    if($form == 0) {
      if(mysqli_query($dbl, "INSERT INTO `metaIngredients` (`title`) VALUES ('".$formTitle."')")) {
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
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutat anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='2'></div>".PHP_EOL.
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
      if($form == 0) {
        /**
         * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
         */
        if(mysqli_query($dbl, "UPDATE `metaIngredients` SET `title`='".$formTitle."' WHERE `id`='".$id."' LIMIT 1")) {
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
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='2'></div>".PHP_EOL.
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
          if(mysqli_query($dbl, "DELETE FROM `metaIngredients` WHERE `id`='".$id."' LIMIT 1")) {
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
        $content.= "<div class='successbox'>Zutat erfolgreich angelegt.</div>".PHP_EOL;
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
          if(mysqli_query($dbl, "DELETE FROM `metaUnits` WHERE `id`='".$id."' LIMIT 1")) {
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
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminIngredients/list");
  die();
}
?>
