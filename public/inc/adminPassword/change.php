<?php
/**
 * adminPassword.php
 * 
 * Aktion zum ändern des eigenen Passwortes
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Eigenes Passwort ändern
 */
$content.= "<div class='spacer-m'></div>";
$content.= "<h1><span class='fas icon'>&#xf084;</span>Eigenes Passwort ändern</h1>";

/**
 * Änderung des Passworts
 */
if(isset($_POST['password'])) {
  if($_POST['token'] == $sessionHash) {
    if(strlen($_POST['password']) >= 20) {
      $salt = hash('sha256', random_bytes(4096));
      $password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);
      mysqli_query($dbl, "UPDATE `accounts` SET `password`='".defuse($password)."', `salt`='".$salt."' WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 1, 'Passwortänderung')") OR DIE(MYSQLI_ERROR($dbl));
      /**
       * Löschen aller Sitzungen des Users.
       */
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `accountId`='".$userId."'") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 1, 'Logout, Passwortänderung')") OR DIE(MYSQLI_ERROR($dbl));
      /**
       * Entfernen des Cookies und Umleitung zur Loginseite.
       */
      setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
      header("Location: /adminLogin");
      die();
    } else {
      $content.= "<div class='warnbox'>Das Passwort muss mindestens 20 Stellen lang sein.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminSessions/show'><span class='fas icon'>&#xf0a8;</span>Zurück zum Formular</a></div>".
      "</div>";
    }
  } else {
    /**
     * Ungültiges Sitzungstoken
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminSessions/show'><span class='fas icon'>&#xf0a8;</span>Zurück zum Formular</a></div>".
    "</div>";
  }
} else {
  header("Location: /adminSessions/show");
  die();
}
?>
