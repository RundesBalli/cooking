<?php
/**
 * adminSessions.php
 * 
 * Seite um aktive Sitzungen anzuzeigen und ggf. zu beenden.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

$title = "Sitzungen anzeigen";
$content.= "<h1>Sitzungen anzeigen</h1>".PHP_EOL;

/**
 * Löschfunktion
 */
if((isset($_GET['action']) AND $_GET['action'] == 'del') AND (isset($_GET['id']) AND !empty($_GET['id']))) {
  $id = (int)defuse($_GET['id']);
  if(!isset($_POST['submit'])) {
    /**
     * CSRF Bestätigung
     */
    $content.= "<div class='infobox'>Beenden bitte bestätigen.</div>".PHP_EOL;
    $content.= "<form action='/adminSessions/del/".$id."' method='post'>".PHP_EOL;
    /**
     * Sitzungstoken
     */
    $content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
    /**
     * Auswahl
     */
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Möchtest du die Sitzung beenden?</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Ja'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
  } else {
    if($_POST['token'] == $adminSessionHash) {
      /**
       * Token passt, Sitzung beenden.
       */
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_affected_rows($dbl) == 0) {
        http_response_code(404);
        $content.= "<div class='warnbox'>Es existiert keine Sitzung mit der ID.</div>".PHP_EOL;
      } else {
        $content.= "<div class='successbox'>Die Sitzung wurde beendet.</div>".PHP_EOL;
      }
    } else {
      /**
       * Ungültiges Sitzungstoken
       */
      http_response_code(403);
      $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
    }
  }
}

/**
 * Alle aktiven Sitzungen selecten und ausgeben.
 */
$result = mysqli_query($dbl, "SELECT `sessions`.`id`, `sessions`.`hash`, UNIX_TIMESTAMP(`sessions`.`lastactivity`) AS `lastactivity`, UNIX_TIMESTAMP(DATE_ADD(`sessions`.`lastactivity`, INTERVAL 6 WEEK)) AS `validuntil`, `accounts`.`username` AS `username` FROM `sessions` LEFT JOIN `accounts` ON `sessions`.`userid` = `accounts`.`id` ORDER BY `sessions`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>User</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>letzte Aktivität</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>gültig bis</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>Beenden</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover bordered'>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".output($row['username'])."</div>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", $row['lastactivity'])."</div>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".date("d.m.Y, H:i:s", $row['validuntil'])."</div>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".($adminSessionHash == $row['hash'] ? "eigene Sitzung" : "<a href='/adminSessions/del/".$row['id']."'>Beenden</a>")."</div>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL;
  $content.= "</div>".PHP_EOL;
}

/**
 * Eigenes Passwort ändern
 */
$content.= "<div class='spacer-m'></div>".PHP_EOL;
$content.= "<h1>Eigenes Passwort ändern</h1>".PHP_EOL;

/**
 * Änderung des Passworts
 */
if(isset($_POST['password'])) {
  if($_POST['token'] == $adminSessionHash) {
    if(strlen($_POST['password']) >= 20) {
      $salt = hash('sha256', random_bytes(4096));
      $password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);
      mysqli_query($dbl, "UPDATE `accounts` SET `password`='".defuse($password)."', `salt`='".$salt."' WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      /**
       * Löschen der Sitzung.
       */
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `hash`='".$adminSessionHash."'") OR DIE(MYSQLI_ERROR($dbl));
      /**
       * Entfernen des Cookies und Umleitung zur Loginseite.
       */
      setcookie('cookingAdmin', NULL, 0);
      header("Location: /adminLogin");
      die();
    } else {
      $content.= "<div class='warnbox'>Das Passwort muss mindestens 20 Stellen lang sein.</div>".PHP_EOL;
    }
  } else {
    /**
     * Ungültiges Sitzungstoken
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>".PHP_EOL;
  }
}

/**
 * Formular zum Passwort ändern
 */
$content.= "<form action='/adminSessions' method='post'>".PHP_EOL;
/**
 * Sitzungstoken
 */
$content.= "<input type='hidden' name='token' value='".$adminSessionHash."'>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>neues Passwort</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='password' name='password' tabindex='1'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Muss mindestens aus 20 Zeichen bestehen.<br><a href='https://rundesballi.com/pwgen' target='_blank'>Passwortgenerator</a></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Passwort ändern</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='ändern' tabindex='2'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'><span class='highlight'>Info:</span> Bei Erfolg wird die Sitzung geschlossen.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "</form>".PHP_EOL;
?>
