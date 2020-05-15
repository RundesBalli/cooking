<?php
/**
 * logout.php
 * 
 * Seite zum Löschen der Sitzung und um den Cookie zu leeren.
 */

/**
 * Sessionüberprüfung
 */
require_once('cookieCheck.php');

/**
 * Titel
 */
$title = "Logout";
$content.= "<h1><span class='fas icon'>&#xf2f5;</span>Logout</h1>".PHP_EOL;

if(!isset($_POST['submit'])) {
  /**
   * Formular wird angezeigt
   */
  $content.= "<form action='/logout' method='post'>".PHP_EOL;
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionHash."'>".PHP_EOL;
  /**
   * Auswahl
   */
  $content.= "<div class='row bordered'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Möchtest du dich ausloggen? (alle offenen Sitzungen)</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Ja'></div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'><a href='/overview'>Nein, zurück.</a></div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
} else {
  /**
   * Formular abgesendet
   */
  /**
   * Sitzungstoken
   */
  if($_POST['token'] != $sessionHash) {
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/overview'>Zurück zur Übersicht</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Löschen der Sitzung.
     */
    mysqli_query($dbl, "DELETE FROM `userSessions` WHERE `userId`='".$userId."'") OR DIE(MYSQLI_ERROR($dbl));
    userLog($userId, 1, NULL, "Logout");
    /**
     * Entfernen des Cookies und Umleitung zur Loginseite.
     */
    setcookie('cooking', NULL, 0);
    header("Location: /login");
    die();
  }
}
?>
