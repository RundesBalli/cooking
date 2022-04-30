<?php
/**
 * adminFiles/sort.php
 * 
 * Sortieren aller Bilder eines Rezeptes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Dateiverwaltung - Bilder sortieren";
$content.= "<h1>Dateiverwaltung - Bilder sortieren</h1>";

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
    "</div>";
    $content.= "<div class='spacer-m'></div>";

    /**
     * Abfragen ob es Bilder gibt und falls ja wie viele.
     */
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $imageCount = mysqli_num_rows($result);
    if($imageCount == 0) {
      /**
       * Keine Bilder vorhanden.
       */
      $content.= "<div class='warnbox'>Es existieren keine Bilder für dieses Rezept.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".
      "</div>";
    } elseif($imageCount == 1) {
      /**
       * Nur ein Bild vorhanden. Eine Sortierung würde keinen Sinn machen.
       */
      $content.= "<div class='infobox'>Es gibt nur ein Bild für dieses Rezept. Eine Sortierung macht keinen Sinn.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".
      "</div>";
    } elseif($imageCount >= 2) {
      /**
       * Zwei oder mehr Bilder vorhanden. Sortierung kann stattfinden.
       */
      if(!isset($_POST['submit'])) {
        /**
         * Wenn kein Formular übergeben wurde, dann zeig es an.
         */
        $content.= "<form action='/adminFiles/sort?id=".output($id)."' method='post' autocomplete='off'>";

        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";

        /**
         * Tabellenüberschrift
         */
        $content.= "<div class='row highlight bold bordered'>".
        "<div class='col-s-4 col-l-2'>Sortierindex</div>".
        "<div class='col-s-8 col-l-4'>Bild</div>".
        "<div class='col-s-12 col-l-6'>Bildbeschreibung</div>".
        "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
        "</div>";

        /**
         * Durchgehen der einzelnen Zuweisungen.
         */
        $tabindex = 0;
        while($row = mysqli_fetch_array($result)) {
          $tabindex++;
          $content.= "<div class='row hover bordered'>".
          "<div class='col-s-4 col-l-2'><input type='number' name='sortIndex[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'></div>".
          "<div class='col-s-8 col-l-4'><a href='/img/img-".$row['itemId']."-".$row['fileHash'].".png' target='_blank'>/img/img-".$row['itemId']."-".$row['fileHash'].".png<span class='fas iconright'>&#xf35d;</span></a></div>".
          "<div class='col-s-12 col-l-6'>".($row['description'] != NULL ? output($row['description']) : "<span class='italic'>NULL</span>")."</div>".
          "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
          "</div>";
        }
        $tabindex++;

        /**
         * Absenden
         */
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-4 col-l-2'><input type='submit' name='submit' value='ändern' tabindex='".$tabindex."'></div>".
        "</div>";
        $content.= "</form>";
      } else {
        /**
         * Formularauswertung
         */
        if($_POST['token'] == $sessionHash) {
          if(isset($_POST['sortIndex']) AND is_array($_POST['sortIndex'])) {
            asort($_POST['sortIndex']);
            $index = 0;
            $query = "UPDATE `images` SET `sortIndex` = CASE ";
            foreach($_POST['sortIndex'] as $key => $val) {
              $key = (int)defuse($key);
              $index+= 10;
              $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
            }
            $query.= "ELSE '9999999' END WHERE `itemId`='".$id."'";
            mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
            mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 5, '".$id."', 'Bilder sortiert')") OR DIE(MYSQLI_ERROR($dbl));
            $content.= "<div class='successbox'>Sortierung geändert.</div>";
            $content.= "<div class='row'>".
            "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
            "</div>";
          } else {
            $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>";
            $content.= "<div class='row'>".
            "<div class='col-s-12 col-l-12'><a href='/adminFiles/sort?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
            "</div>";
          }
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminFiles/sort?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
          "</div>";
        }
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
