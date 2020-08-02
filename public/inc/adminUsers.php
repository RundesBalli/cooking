<?php
/**
 * adminUsers.php
 * 
 * Bearbeitung von Nutzerkonten
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');


if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller User.
   */
  header("Location: /adminUsers/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflistung aller User.
   */
  $title = "Userverwaltung";
  $content.= "<h1><span class='fas icon'>&#xf0c0;</span>Userverwaltung</h1>".PHP_EOL;
  
  /**
   * Userliste
   */
  $content.= "<h2><span class='fas icon'>&#xf2b9;</span>Userliste</h2>".PHP_EOL;
  $result = mysqli_query($dbl, "SELECT `users`.`id`, `users`.`username`, `users`.`lastSynced`, (SELECT COUNT(`id`) FROM `favs` WHERE `favs`.`userId` = `users`.`id`) AS `favs`, (SELECT COUNT(`id`) FROM `votes` WHERE `votes`.`userId` = `users`.`id`) AS `votes` FROM `users` ORDER BY `username` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Keine User vorhanden
     */
    $content.= "<div class='infobox'>Kein User vorhanden.</div>".PHP_EOL;
  } else {
    /**
     * Tabellenüberschrift
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Username</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>Favoriten</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>Votes</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".output($row['username'])."</div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>".$row['favs']."</div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-2 col-l-2 col-xl-2'>".$row['votes']."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><a href='/adminUsers/show/".$row['id']."'><span class='fas icon'>&#xf530;</span>Anzeigen</a><br><a href='/adminUsers/del/".$row['id']."'><span class='fas icon'>&#xf506;</span>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'show') {
  /**
   * Anzeigen
   */
  $title = "User anzeigen";
  $content.= "<h1><span class='fas icon'>&#xf530;</span>User anzeigen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  
  /**
   * Prüfen ob der User existiert
   */
  $result = mysqli_query($dbl, "SELECT `users`.`id`, `users`.`username`, `users`.`pr0grammUserId`, `users`.`lastSynced` FROM `users` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    /**
     * User existiert nicht
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Der User mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * User existiert
     */
    $row = mysqli_fetch_assoc($result);
    $content.= "<h2>User: ".output($row['username'])."</h2>".PHP_EOL;

    /**
     * Aktionen
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminUsers/del/".$row['id']."'><span class='fas icon'>&#xf506;</span>Löschen</a> - <a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='spacer-m'></div>".PHP_EOL;

    /**
     * Eckdaten
     */
    $content.= "<h3>Eckdaten</h3>".PHP_EOL;
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-3 col-l-3 col-xl-3'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-9 col-l-9 col-xl-9'>Wert</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    foreach($row as $key => $val) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-3 col-l-3 col-xl-3'><code>".$key."</code></div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-9 col-l-9 col-xl-9'><code>".$val."</code></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    $content.= "<div class='spacer-m'></div>".PHP_EOL;

    /**
     * Votes
     */
    $content.= "<h3>Votes</h3>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT `items`.`title`, `items`.`shortTitle`, `votes`.`stars` FROM `votes` JOIN `items` ON `items`.`id`=`votes`.`itemId` WHERE `votes`.`userId` = '".$id."' ORDER BY `votes`.`stars` DESC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Keine Votes vorhanden
       */
      $content.= "<div class='infobox'>Der User hat noch keine Votes abgegeben.</div>".PHP_EOL;
    } else {
      /**
       * Anzeige der Votes
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-6 col-l-6 col-xl-6'>Rezept</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-6 col-l-6 col-xl-6'>Sterne</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-6 col-l-6 col-xl-6'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-6 col-l-6 col-xl-6'>".stars($row['stars'])."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
    $content.= "<div class='spacer-m'></div>".PHP_EOL;

    /**
     * Favoriten
     */
    $content.= "<h3>Favoriten</h3>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT `items`.`title`, `items`.`shortTitle` FROM `favs` JOIN `items` ON `items`.`id`=`favs`.`itemId` WHERE `favs`.`userId` = '".$id."' ORDER BY `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Keine Favoriten vorhanden
       */
      $content.= "<div class='infobox'>Der User hat noch keine Favoriten angelegt.</div>".PHP_EOL;
    } else {
      /**
       * Anzeige der Favoriten
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Rezept</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
    $content.= "<div class='spacer-m'></div>".PHP_EOL;

    /**
     * Logeinträge
     */
    $content.= "<h3>Logeinträge</h3>".PHP_EOL;
    $content.= "<div class='row highlight bold bordered' style='border-left: 6px solid #888888;'>".PHP_EOL.
    "<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>ID</div>".PHP_EOL.
    "<div class='col-x-4 col-s-4 col-m-5 col-l-5 col-xl-2'><span class='fas icon'>&#xf007;</span>Username</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-3'><span class='far icon'>&#xf017;</span>Zeitpunkt</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-2'><span class='fas icon'>&#xf543;</span>Rezept</div>".PHP_EOL.
    "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-4'><span class='fas icon'>&#xf1dd;</span>Text</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT `userLog`.`id`, `users`.`username`, `userLog`.`timestamp`, `logLevel`.`color`, `logLevel`.`title` AS `logLevelTitle`, `userLog`.`itemId`, `items`.`title` AS `itemTitle`, `items`.`shortTitle` AS `itemShortTitle`, `userLog`.`text` FROM `userLog` LEFT OUTER JOIN `users` ON `users`.`id`=`userLog`.`userId` JOIN `logLevel` ON `userLog`.`logLevel`=`logLevel`.`id` LEFT OUTER JOIN `items` ON `items`.`id`=`userLog`.`itemId` WHERE `userId`='".$id."' ORDER BY `userLog`.`id` DESC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered' style='border-left: 6px solid #".$row['color'].";' title='".$row['logLevelTitle']."'>".PHP_EOL.
      "<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>".$row['id']."</div>".PHP_EOL.
      "<div class='col-x-4 col-s-4 col-m-5 col-l-5 col-xl-2'>".output($row['username'])."</div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-2'>".($row['itemId'] !== NULL ? "<a href='/rezept/".output($row['itemShortTitle'])."' target='_blank'>".output($row['itemTitle'])."<span class='fas iconright'>&#xf35d;</span></a>" : NULL)."</div>".PHP_EOL.
      "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-4'>".Slimdown::render($row['text'])."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
  }
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen
   */
  $title = "User löschen";
  $content.= "<h1><span class='fas icon'>&#xf506;</span>User löschen</h1>".PHP_EOL;
  $id = (int)defuse($_GET['id']);
  
  /**
   * Prüfen ob der User existiert
   */
  $result = mysqli_query($dbl, "SELECT `users`.`id`, `users`.`username`, `users`.`pr0grammUserId`, `users`.`lastSynced` FROM `users` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    /**
     * User existiert nicht
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Der User mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    $row = mysqli_fetch_array($result);
    if(!isset($_POST['submit'])) {
      /**
       * Formular wurde noch nicht gesendet.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll der User <span class='italic highlight'>".output($row['username'])."</span> wirklich gelöscht werden?</div>".PHP_EOL.
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
      $content.= "<form action='/adminUsers/del/".$id."' method='post' autocomplete='off'>".PHP_EOL;
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
          mysqli_query($dbl, "DELETE FROM `users` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          adminLog($adminUserId, 4, NULL, NULL, "User gelöscht: `".md5($row['username'])."` (hashed)");
          $content.= "<div class='successbox'>User erfolgreich gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>User unverändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminUsers/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  }
} else {
  header("Location: /adminUsers/list");
  die();
}
?>
