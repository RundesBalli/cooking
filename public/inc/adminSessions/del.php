<?php
/**
 * adminSessions/del.php
 * 
 * Aktion zum Beenden von Sitzungen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Sitzungen beenden";
$content.= "<h1><span class='fas icon'>&#xf057;</span>Sitzung beenden</h1>";

/**
 * Auslesen der ID
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Wenn noch kein Formular abgesendet wurde, wird die Bestätigung erbeten.
   */
  if(!isset($_POST['submit'])) {
    /**
     * Prüfung ob es die Sitzung gibt.
     */
    $result = mysqli_query($dbl, "SELECT `id` FROM `sessions` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * CSRF Bestätigung
       */
      $content.= "<div class='infobox'>Beenden bitte bestätigen.</div>";
      $content.= "<form action='/adminSessions/del?id=".$id."' method='post'>";
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";
      /**
       * Auswahl
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Möchtest du die Sitzung beenden?</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Ja'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
      "</div>";
      $content.= "</form>";
    } else {
      http_response_code(404);
      $content.= "<div class='warnbox'>Es existiert keine Sitzung mit der ID.</div>";
    }
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminSessions/show'><span class='fas icon'>&#xf0a8;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Bestätigung erfolgt.
     */
    if($_POST['token'] == $sessionHash) {
      /**
       * Token passt, Sitzung beenden.
       */
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_affected_rows($dbl) == 0) {
        http_response_code(404);
        $content.= "<div class='warnbox'>Es existiert keine Sitzung mit der ID.</div>";
      } else {
        $content.= "<div class='successbox'>Die Sitzung wurde beendet.</div>";
        mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 1, 'Sitzung manuell beendet')") OR DIE(MYSQLI_ERROR($dbl));
      }
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminSessions/show'><span class='fas icon'>&#xf0a8;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      /**
       * Ungültiges Sitzungstoken
       */
      http_response_code(403);
      $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminSessions/show'><span class='fas icon'>&#xf0a8;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    }
  }
} else {
  /**
   * Keine ID übergeben.
   */
  header("Location: /adminSessions/show");
  die();
}
?>
