<?php
/**
 * adminItemAssignments/add.php
 * 
 * Anlegen einer Kategoriezuweisung eines Rezeptes.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategoriezuweisungen anlegen";
$content.= "<h1><span class='fas icon'>&#xf067;</span>Kategoriezuweisung anlegen</h1>";

/**
 * Prüfung ob eine itemId und eine categoryId übergeben wurde.
 */
if(!empty($_GET['itemId']) AND !empty($_GET['categoryId'])) {
  $itemId = (int)defuse($_GET['itemId']);
  $categoryId = (int)defuse($_GET['categoryId']);

  $itemResult = mysqli_query($dbl, "SELECT `items`.`title` FROM `items` WHERE `items`.`id`=".$itemId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  $categoryResult = mysqli_query($dbl, "SELECT `categories`.`title` FROM `categories` WHERE `categories`.`id`=".$categoryId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  if(mysqli_num_rows($itemResult) == 1 AND mysqli_num_rows($categoryResult) == 1) {
    $itemRow = mysqli_fetch_assoc($itemResult);
    $categoryRow = mysqli_fetch_assoc($categoryResult);

    if(!isset($_POST['submit'])) {
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Möchtest du das Rezept <span class='highlight italic'>".output($itemRow['title'])."</span> zur Kategorie <span class='highlight italic'>".output($categoryRow['title'])."</span> hinzufügen?</div>".
      "</div>";
      /**
       * CSRF Bestätigung
       */
      $content.= "<form action='/adminItemAssignments/add?itemId=".output($itemId)."&categoryId=".output($categoryId)."' method='post'>";
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Hinzufügen?</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Ja'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";
      $content.= "</form>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      if($_POST['token'] == $sessionHash) {
        /**
         * Token gültig.
         */
        if(mysqli_query($dbl, "INSERT INTO `categoryItems` (`categoryId`, `itemId`) VALUES ('".$categoryId."', '".$itemId."')")) {
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `categoryId`, `text`) VALUES ('".$userId."', 6, ".$itemId.", ".$categoryId.", 'Rezept in Kategorie zugewiesen')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Zuweisung erfolgreich angelegt.</div>";
        } else {
          if(mysqli_errno($dbl) == 1062) {
            $content.= "<div class='warnbox'>Es existiert bereits eine solche Zuweisung.</div>";
          } elseif(mysqli_errno($dbl) == 1452) {
            $content.= "<div class='warnbox'>Diese Kombination kann nicht angelegt werden.</div>";
          } else {
            $content.= "<div class='warnbox'>Unbekannter Fehler (".mysqli_errno($dbl)."): ".mysqli_error($dbl)."</div>";
          }
        }
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      }
    }
  } else {
    http_response_code(400);
    $content.= "<div class='warnbox'>Es müssen ein gültiges Rezept und eine gültige Kategorie übergeben werden.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  }
} else {
  http_response_code(400);
  $content.= "<div class='warnbox'>Es müssen eine <code>itemId</code> und eine <code>categoryId</code> übergeben werden.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
