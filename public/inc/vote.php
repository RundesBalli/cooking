<?php
/**
 * vote.php
 * 
 * Bewerten eines Rezepts.
 */

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
    
    $title = $row['title']." - Bewerten";
    $content.= "<h1><span class='fas icon'>&#xf543;</span>Rezept bewerten: ".$row['title']."</h1>".PHP_EOL;

    /**
     * Wenn das Formular nicht abgeschickt wurde, dann zeig es an.
     */
    if(!isset($_POST['submit'])) {
      /**
       * Formular wird angezeigt
       */
      $content.= "<form action='/vote/".$row['shortTitle']."' method='post'>".PHP_EOL;
      /**
       * UUI als CSRF Token
       */
      $content.= "<input type='hidden' name='token' value='".$UUI."'>".PHP_EOL;
      /**
       * Auswahl
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Abstimmen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'><select name='vote'><option value='0' selected disabled hidden>Bitte auswählen</option><option value='1'>1 Stern - schlecht</option><option value='2'>2 Sterne</option><option value='3'>3 Sterne</option><option value='4'>4 Sterne</option><option value='5'>5 Sterne - sehr gut</option></select></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-3'><input type='submit' name='submit' value='Abstimmen'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-4 col-xl-4'></div>".PHP_EOL.
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
      if($_POST['token'] != $UUI) {
        $content.= "<div class='warnbox'>Der Vote ist ungültig.</div>".PHP_EOL;
      } else {
        $vote = (int)defuse($_POST['vote']);
        if($vote < 1 OR $vote > 5) {
          $content.= "<div class='warnbox'>Der Vote ist ungültig.</div>".PHP_EOL;
        } else {
          $clickresult = mysqli_query($dbl, "SELECT * FROM `clicks` WHERE `hash`='".$UUI."' AND `ts` > DATE_SUB(NOW(), INTERVAL 30 HOUR) LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_num_rows($clickresult) == 1) {
            mysqli_query($dbl, "UPDATE `votes` SET `ts`=CURRENT_TIMESTAMP, `stars`='".$vote."' WHERE `itemid`='".$row['id']."' AND `hash`='".$UUI."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
            if(mysqli_affected_rows($dbl) != 1) {
              mysqli_query($dbl, "INSERT INTO `votes` (`itemid`, `hash`, `stars`) VALUES ('".$row['id']."', '".$UUI."', '".$vote."')") OR DIE(MYSQLI_ERROR($dbl));
              $content.= "<div class='successbox'>Dein Vote wurde eingetragen.</div>".PHP_EOL;
            } else {
              $content.= "<div class='successbox'>Dein Vote wurde aktualisiert.</div>".PHP_EOL;
            }
          } else {
            $content.= "<div class='warnbox'>Der Vote ist ungültig.</div>".PHP_EOL;
          }
        }
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
