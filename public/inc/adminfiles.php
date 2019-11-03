<?php
/**
 * adminfiles.php
 * 
 * Seite um Rezepten Bilder und Thumbnails hinzuzufügen.
 * Es erfolgt der Direkteinstieg in das Rezept (über /adminitems).
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

/**
 * Entschärfen der übergebenen ID
 */
$id = (int)defuse($_GET['id']);

/**
 * Prüfen ob das Rezept existiert.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
   */
  $title = "Dateiverwaltung";
  $content.= "<h1>Dateiverwaltung</h1>".PHP_EOL;
  http_response_code(404);
  $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminitems/list'>Zurück zur Übersicht</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Falls das Rezept existiert, dann wird geprüft was gemacht werden soll.
   */
  $row = mysqli_fetch_array($result);
  if(!isset($_GET['action'])) {
    /**
     * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezepte.
     */
    header("Location: /adminfiles/list/".$id);
    die();
  } elseif($_GET['action'] == 'list') {
    /**
     * Dateien auflisten
     */
    $title = "Dateiverwaltung - Dateien anzeigen";
    $content.= "<h1>Dateiverwaltung - Dateien anzeigen</h1>".PHP_EOL;
    /**
     * Thumbnail
     */
    $content.= "<h2>Thumbnail</h2>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Kein Thumbnail vorhanden.
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keinen Thumbnail.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } elseif(mysqli_num_rows($result) == 1) {
      /**
       * Thumbnail vorhanden. Auflistung.
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Dateiname</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Aktionen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $row = mysqli_fetch_array($result);
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><a href='/img/".output($row['filename']).".png' target='_blank'>/img/".output($row['filename']).".png</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><a href='/adminfiles/del/".$row['id']."' class='nowrap'>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Mehrere Thumbnails vorhanden, was nicht sein darf. Löschung aller Thumbnails und Aufforderung zum erneuten Hochladen.
       */
      while($row = mysqli_fetch_array($result)) {
        unlink($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR.$row['filename'].".png");
      }
      mysqli_query($dbl, "DELETE FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='warnbox'>Das Rezept hat Fehler im Thumbnail. Er wurde gelöscht und muss neu hochgeladen werden.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    /**
     * Bilder
     */
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
    $content.= "<h2>Bilder</h2>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Noch keine Bilder vorhanden
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keine Bilder.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Bilder vorhanden. Auflistung nach Sortierindex.
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Dateiname</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Sortierindex</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><a href='/img/".output($row['filename']).".png' target='_blank'>/img/".output($row['filename']).".png</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".output($row['sortIndex'])."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminfiles/del/".$row['id']."' class='nowrap'>Löschen</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  } elseif($_GET['action'] == 'add') {

  } elseif($_GET['action'] == 'del') {
/**
 * htaccess: /adminfiles/itemid/del/imageid hat.
 * RewriteRule ^adminfiles\/([\d]+)\/del\/([\d]+)$ /index.php?p=adminfiles&action=del&id=$1&imageid=$2 [NC,L,QSA]
 */
  } elseif($_GET['action'] == 'sort') {

  } else {
    /**
     * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
     */
    header("Location: /adminfiles/list/".$id);
    die();
  }
}
?>
