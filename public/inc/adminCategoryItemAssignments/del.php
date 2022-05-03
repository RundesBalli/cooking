<?php
/**
 * adminCategoryItemAssignments/del.php
 * 
 * Löschen einer Kategoriezuweisung eines Rezeptes.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategoriezuweisungen löschen";
$content.= "<h1><span class='fas icon'>&#xf2ed;</span>Kategoriezuweisung löschen</h1>";

/**
 * Prüfung ob eine itemId und eine categoryId übergeben wurde.
 */
if(!empty($_GET['itemId']) AND !empty($_GET['categoryId'])) {
  $itemId = (int)defuse($_GET['itemId']);
  $categoryId = (int)defuse($_GET['categoryId']);

  /**
   * Abfragen der jeweiligen IDs und der Zuweisung.
   */
  $itemResult = mysqli_query($dbl, "SELECT `items`.`title` FROM `items` WHERE `items`.`id`=".$itemId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  $categoryResult = mysqli_query($dbl, "SELECT `categories`.`title` FROM `categories` WHERE `categories`.`id`=".$categoryId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  $assignmentResult = mysqli_query($dbl, "SELECT `id` FROM `categoryItems` WHERE `itemId`=".$itemId." AND `categoryId`=".$categoryId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  /**
   * Prüfung ob die IDs existieren.
   */
  if(mysqli_num_rows($itemResult) == 1 AND mysqli_num_rows($categoryResult) == 1) {
    $itemRow = mysqli_fetch_assoc($itemResult);
    $categoryRow = mysqli_fetch_assoc($categoryResult);

    /**
     * Prüfung ob die Zuweisung existiert.
     */
    if(mysqli_num_rows($assignmentResult) == 1) {
      if(!isset($_POST['submit'])) {
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'>Möchtest du die Rezeptzuweisung <span class='highlight italic'>".output($itemRow['title'])."</span> in der Kategorie <span class='highlight italic'>".output($categoryRow['title'])."</span> entfernen?</div>".
        "</div>";
        /**
         * CSRF Bestätigung
         */
        $content.= "<form action='/adminCategoryItemAssignments/del?itemId=".output($itemId)."&categoryId=".output($categoryId)."' method='post'>";

        /**
         * Sitzungstoken
         */
        $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";

        /**
         * Formular
         */
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-12 col-l-3'>Entfernen?</div>".
        "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Ja'></div>".
        "<div class='col-s-12 col-l-5'></div>".
        "</div>";
        $content.= "</form>";

        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminCategoryItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      } else {
        if($_POST['token'] == $sessionHash) {
          /**
           * Token gültig.
           */
          mysqli_query($dbl, "DELETE FROM `categoryItems` WHERE `categoryId`=".$categoryId." AND `itemId`=".$itemId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            $content.= "<div class='successbox'>Zuweisung erfolgreich gelöscht.</div>";
            mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `categoryId`, `text`) VALUES ('".$userId."', 6, ".$itemId.", ".$categoryId.", 'Kategoriezuweisung entfernt')") OR DIE(MYSQLI_ERROR($dbl));
          } else {
            $content.= "<div class='warnbox'>Diese Rezept / Kategorie Zuweisung existiert nicht.</div>";
          }
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategoryItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          /**
           * Ungültiges Sitzungstoken
           */
          http_response_code(403);
          $content.= "<div class='warnbox'>Ungültiges Token.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategoryItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        }
      }
    } else {
      /**
       * Übergebene Zuweisung existiert nicht.
       */
      http_response_code(404);
      $content.= "<div class='warnbox'>Die Rezeptzuweisung <span class='highlight italic'>".output($itemRow['title'])."</span> in der Kategorie <span class='highlight italic'>".output($categoryRow['title'])."</span> existiert nicht.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminCategoryItemAssignments/show?itemId=".output($itemId)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    }
  } else {
    /**
     * Übergebene IDs existieren nicht.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Es müssen ein gültiges Rezept und eine gültige Kategorie übergeben werden.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  }
} else {
  /**
   * Es wurden keine IDs übergeben.
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Es müssen eine <code>itemId</code> und eine <code>categoryId</code> übergeben werden.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
