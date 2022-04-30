<?php
/**
 * adminLogout.php
 * 
 * Seite zum Löschen der Sitzung und um den Cookie zu leeren.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

/**
 * Titel
 */
$title = "Logout";
$content.= "<h1><span class='fas icon'>&#xf2f5;</span>Logout</h1>";

if(!isset($_POST['submit'])) {
  /**
   * Formular wird angezeigt
   */
  $content.= "<form action='/adminLogout' method='post'>";
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";
  /**
   * Auswahl
   */
  $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Möchtest du dich ausloggen?</div>".
      "<div class='col-s-12 col-l-12'><input type='radio' id='killAll-1' name='killAll' value='1'><label for='killAll-1'>Alle Sitzungen, auf allen Geräten beenden</label><br><input type='radio' id='killAll-0' name='killAll' value='0' checked><label for='killAll-0'>Nur diese Sitzung beenden</label></div>".
      "<div class='col-s-12 col-l-3'><input type='submit' name='submit' value='Ja'></div>".
      "<div class='col-s-0 col-l-9'></div>".
      "</div>";
  $content.= "</form>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminIndex'><span class='fas icon'>&#xf0a8;</span>Zurück zur Übersicht</a></div>".
  "</div>";
} else {
  /**
   * Formular abgesendet
   */
  /**
   * Sitzungstoken
   */
  if($_POST['token'] != $sessionHash) {
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminIndex'><span class='fas icon'>&#xf0a8;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Löschen der Sitzung.
     */
    if($_POST['killAll'] == 1) {
      $where = "`accountId`=".$userId;
    } else {
      $where = "`hash`='".$sessionHash."'";
    }
    mysqli_query($dbl, "DELETE FROM `sessions` WHERE ".$where) OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 1, 'Logout, ".($_POST['killAll'] == 1 ? "alle Sitzungen" : "Einzelsitzung")."')") OR DIE(MYSQLI_ERROR($dbl));
    /**
     * Entfernen des Cookies und Umleitung zur Loginseite.
     */
    setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
    header("Location: /adminLogin");
    die();
  }
}
?>
