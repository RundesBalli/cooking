<?php
/**
 * adminFiles/show.php
 * 
 * Anzeige aller Bilder eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Dateiverwaltung - Bilder anzeigen";
$content.= "<h1>Dateiverwaltung - Bilder anzeigen</h1>";

/**
 * Prüfen ob eine ID übergeben wurde
 */
if(!empty($_GET['id'])) {
  /**
   * Es wurde eine ID übergeben, jetzt wird geprüft ob das Rezept existiert.
   */
  $id = (int)defuse($_GET['id']);

  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  if(mysqli_num_rows($result) == 0) {
    /**
     * Das Rezept existiert nicht, daher wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Das Rezept existiert. Anzeige grundlegender Infos.
     */
    $row = mysqli_fetch_assoc($result);
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><span class='highlight bold'>Rezept:</span> <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "<div class='col-s-12 col-l-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminFiles/add?id=".output($id)."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a> - <a href='/adminFiles/sort?id=".output($id)."'><span class='fas icon'>&#xf0dc;</span>Sortieren</a></div>".
    "</div>";
    $content.= "<div class='spacer-m'></div>";

    /**
     * Thumbnail
     */
    $content.= "<h2>Thumbnail</h2>";
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Kein Thumbnail vorhanden.
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keinen Thumbnail.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/add?id=".output($id)."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".
      "</div>";
    } elseif(mysqli_num_rows($result) == 1) {
      /**
       * Thumbnail vorhanden. Auflistung.
       */
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-12 col-l-8'>Dateiname</div>".
      "<div class='col-s-12 col-l-4'>Aktionen</div>".
      "</div>";
      $row = mysqli_fetch_array($result);
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-8'><a href='/img/thumb-".$row['itemId']."-".$row['fileHash'].".png' target='_blank'>/img/thumb-".$row['itemId']."-".$row['fileHash'].".png<span class='fas iconright'>&#xf35d;</span></a></div>".
      "<div class='col-s-12 col-l-4'><a href='/adminFiles/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
      "</div>";
    } else {
      /**
       * Mehrere Thumbnails vorhanden, was nicht sein darf. Löschung aller Thumbnails und Aufforderung zum erneuten Hochladen.
       */
      while($row = mysqli_fetch_array($result)) {
        unlink($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."thumb-".$row['itemId']."-".$row['fileHash'].".png");
      }
      mysqli_query($dbl, "DELETE FROM `images` WHERE `itemId`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='warnbox'>Das Rezept hat Fehler im Thumbnail. Er wurde gelöscht und muss neu hochgeladen werden.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/add?id=".output($id)."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".
      "</div>";
    }

    /**
     * Bilder
     */
    $content.= "<div class='spacer-m'></div>";
    $content.= "<h2>Bilder</h2>";
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Noch keine Bilder vorhanden
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keine Bilder.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/add?id=".output($id)."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".
      "</div>";
    } else {
      /**
       * Bilder vorhanden. Auflistung nach Sortierindex.
       */
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-12 col-l-5'>Dateiname</div>".
      "<div class='col-s-12 col-l-3'>Bildbeschreibung</div>".
      "<div class='col-s-12 col-l-2'>Sortierindex</div>".
      "<div class='col-s-12 col-l-2'>Aktionen</div>".
      "</div>";
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-12 col-l-5'><a href='/img/img-".$row['itemId']."-".$row['fileHash'].".png' target='_blank'>/img/img-".$row['itemId']."-".$row['fileHash'].".png<span class='fas iconright'>&#xf35d;</span></a></div>".
        "<div class='col-s-12 col-l-3'>".($row['description'] != NULL ? output($row['description']) : "<span class='italic'>NULL</span>")."</div>".
        "<div class='col-s-12 col-l-2'>".$row['sortIndex']."</div>".
        "<div class='col-s-12 col-l-2'><a href='/adminFiles/del?id=".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".
        "</div>";
      }
    }
  }
} else {
  /**
   * Es wurde keine ID übergeben.
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Es wurde keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
