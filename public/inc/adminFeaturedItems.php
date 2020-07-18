<?php
/**
 * adminFeaturedItems.php
 * 
 * Seite um Rezepte auf der Startseite vorzustellen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezeptvorstellungen.
   */
  header("Location: /adminFeaturedItems/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflisten aller Rezeptvorstellungen.
   */
  $title = "Rezeptvorstellungen anzeigen";
  $content.= "<h1><span class='fas icon'>&#xf005;</span>Rezeptvorstellungen anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminFeaturedItems/add'><span class='fas icon'>&#xf067;</span>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `featured`.`ts` FROM `featured` JOIN `items` ON `featured`.`itemId` = `items`.`id` ORDER BY `featured`.`id` DESC LIMIT 4") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Rezeptvorstellungen existieren.
     */
    $content.= "<div class='infobox'>Derzeit keine Rezepte vorgestellt.</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Rezepte.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'>Titel</div>".PHP_EOL.
    "<div class='col-x-12 col-s-6 col-m-6 col-l-3 col-xl-3'>Angelegt am</div>".PHP_EOL.
    "<div class='col-x-12 col-s-6 col-m-6 col-l-3 col-xl-3'>Angezeigt bis</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-0 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-6 col-m-6 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['ts']))."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-6 col-m-6 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['ts'])+86400*7)."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-2 col-xl-2'><a href='/adminFeaturedItems/del/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-0 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Hinzufügen einer Rezeptvorstellung.
   */
  $title = "Rezeptvorstellungen anlegen";
  $content.= "<h1><span class='fas icon'>&#xf005;</span>Rezeptvorstellungen anlegen</h1>".PHP_EOL;

  /**
   * Prüfen ob bereits ein Formular abgesendet wurde.
   */
  if(isset($_POST['submit'])) {
    /**
     * Eintragen der Rezeptvorstellung.
     */
    $error = 0;
    /**
     * Sitzungstoken
     */
    if($_POST['token'] != $adminSessionHash) {
      http_response_code(403);
      $error = 1;
      $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
    }
    if(!empty($_POST['itemId'])) {
      $itemId = (int)defuse($_POST['itemId']);
    } else {
      $error = 1;
      $content.= "<div class='warnbox'>Ungültiges Rezept.</div>".PHP_EOL;
    }
    if($error == 0) {
      mysqli_query($dbl, "DELETE FROM `featured` WHERE `itemId`='".$itemId."'") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_query($dbl, "INSERT INTO `featured` (`itemId`) VALUES ('".$itemId."')")) {
        adminLog($adminUserId, 2, $itemId, NULL, "Rezeptvorstellung angelegt.");
        $content.= "<div class='successbox'>Rezeptvorstellung erfolgreich angelegt.</div>".PHP_EOL;
      } else {
        if(mysqli_errno($dbl) == 1452) {
          $content.= "<div class='warnbox'>Diese Rezeptvorstellung kann nicht angelegt werden.</div>".PHP_EOL;
        } else {
          $content.= "<div class='warnbox'>Unbekannter Fehler (".mysqli_errno($dbl)."): ".mysqli_error($dbl)."</div>".PHP_EOL;
        }
      }
    } else {
      $content.= "<div class='warnbox'>Konnte Rezeptvorstellung nicht anlegen.</div>".PHP_EOL;
    }
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminFeaturedItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
  } else {
    /**
     * Anzeige des Formulars.
     */
    $content.= "<form action='/adminFeaturedItems/add' method='post' autocomplete='off'>".PHP_EOL;
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
     * Rezept
     */
    $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $options = array();
    if(!empty($_GET['id'])) {
      $id = (int)defuse($_GET['id']);
    } else {
      $id = NULL;
    }
    while($row = mysqli_fetch_array($result)) {
      $options[] = "<option value='".$row['id']."'".($id == $row['id'] ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
    }
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Rezept</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='itemId' tabindex='1' autofocus>".PHP_EOL."<option value='' ".(($id === NULL OR $id == 0) ? "selected " : NULL)."disabled hidden>Bitte wählen</option>".PHP_EOL.implode("", $options)."</select></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Um ein Rezept \"wieder nach vorne zu holen\" genügt es, es erneut vorzuschlagen. Der alte Eintrag wird gelöscht.</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Absenden
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Rezeptvorstellung anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen' tabindex='2'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen einer Rezeptvorstellung.
   */
  $title = "Rezeptvorstellung löschen";
  $content.= "<h1><span class='fas icon'>&#xf2ed;</span>Rezeptvorstellung löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Rezeptvorstellung existiert.
   */
  $result = mysqli_query($dbl, "SELECT `featured`.`id`, `featured`.`itemId`, `items`.`title` FROM `featured` JOIN `items` ON `featured`.`itemId` = `items`.`id` WHERE `featured`.`id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Rezeptvorstellung nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Rezeptvorstellung mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminFeaturedItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Wenn die Rezeptvorstellung existiert, dann wird abgefragt ob wirklich gelöscht werden soll.
     */
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll die Rezeptvorstellung <span class='italic highlight'>".output($row['title'])."</span> wirklich gelöscht werden?</div>".PHP_EOL.
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
      $content.= "<form action='/adminFeaturedItems/del/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
          mysqli_query($dbl, "DELETE FROM `featured` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          adminLog($adminUserId, 4, NULL, NULL, "Rezeptvorstellung gelöscht");
          /**
           * Zusätzlich legt der MySQL Trigger einen weiteren Logeintrag an.
           */
          $content.= "<div class='successbox'>Rezeptvorstellung erfolgreich gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminFeaturedItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>Rezeptvorstellung unverändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminFeaturedItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminFeaturedItems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminFeaturedItems/list");
  die();
}
?>
