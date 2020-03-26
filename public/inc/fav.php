<?php
/**
 * fav.php
 * 
 * Favoriten anlegen oder entfernen
 */

/**
 * Sessionüberprüfung
 */
require_once('cookieCheck.php');

/**
 * Prüfen ob das übergebene Rezept leer ist.
 */
if(!isset($_GET['item']) OR empty(trim($_GET['item']))) {
  http_response_code(404);
  $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Du musst ein Rezept angeben.</div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Übergebene Kategorie für den Query vorbereiten.
   */
  $item = defuse($_GET['item']);

  /**
   * Rezept abfragen
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle` FROM `items` WHERE `shortTitle`='".$item."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    
    $title = $row['title']." - Favorisieren";
    $content.= "<h1><span class='fas icon'>&#xf005;</span>Rezept Favorisieren: ".$row['title']."</h1>".PHP_EOL;

    /**
     * Prüfung, ob bereits favorisiert wurde
     */
    $innerresult = mysqli_query($dbl, "SELECT * FROM `favs` WHERE `itemId`='".$row['id']."' AND `userId`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($innerresult) == 1) {
      /**
       * Wenn das Formular nicht abgeschickt wurde, dann zeig es an.
       */
      if(!isset($_POST['submit'])) {
        $content.= "<div class='infobox'>Du hast das Rezept bereits favorisiert.</div>".PHP_EOL;
        /**
         * Formular wird angezeigt
         */
        $content.= "<form action='/fav/".$row['shortTitle']."' method='post'>".PHP_EOL;
        /**
         * UUI als CSRF Token
         */
        $content.= "<input type='hidden' name='token' value='".$UUI."'>".PHP_EOL;
        /**
         * Bestätigung
         */
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Möchtest du das Rezept aus deinen Favoriten entfernen?</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'><input type='submit' name='submit' value='Ja'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-6 col-l-7 col-xl-7'></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".$row['shortTitle']."'><span class='fas icon'>&#xf359;</span>Zurück zum Rezept</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      } else {
        /**
         * Formular wurde abgesendet.
         */
        /**
         * CSRF-Token Prüfung
         */
        if($_POST['token'] != $UUI) {
          $content.= "<div class='warnbox'>Es ist ein Fehler aufgetreten.</div>".PHP_EOL;
        } else {
          /**
           * Löschung
           */
          mysqli_query($dbl, "DELETE FROM `favs` WHERE `itemId`='".$row['id']."' AND `userId`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            $content.= "<div class='successbox'>Rezept aus den Favoriten entfernt.</div>".PHP_EOL;
          } else {
            $content.= "<div class='warnbox'>Es ist ein Fehler aufgetreten.</div>".PHP_EOL;
          }
        }
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".$row['shortTitle']."'><span class='fas icon'>&#xf359;</span>Zurück zum Rezept</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    } else {
      /**
       * Noch nicht favorisiert, leg Favorit an.
       */
      mysqli_query($dbl, "INSERT INTO `favs` (`itemId`, `userId`) VALUES ('".$row['id']."', '".$userId."')") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_affected_rows($dbl) == 1) {
        $content.= "<div class='successbox'>Rezept in die Favoriten hinzugefügt.</div>".PHP_EOL;
      } else {
        $content.= "<div class='warnbox'>Es ist ein Fehler aufgetreten.</div>".PHP_EOL;
      }
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/rezept/".$row['shortTitle']."'><span class='fas icon'>&#xf359;</span>Zurück zum Rezept</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } else {
    /**
     * Fehlermeldung, wenn das Rezept nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Das Rezept <span class='italic'>".output($item)."</span> existiert nicht.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
