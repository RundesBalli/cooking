<?php
/**
 * adminSessions/show.php
 * 
 * Seite um aktive Sitzungen anzuzeigen und ggf. zu beenden.
 * Weiterhin besteht die Möglichkeit sein eigenes Passwort zu ändern.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Sitzungen anzeigen";
$content.= "<h1><span class='fas icon'>&#xf51c;</span>Sitzungen anzeigen</h1>";

/**
 * Alle aktiven Sitzungen selektieren und ausgeben.
 */
$result = mysqli_query($dbl, "SELECT `sessions`.`id`, `sessions`.`hash`, UNIX_TIMESTAMP(`sessions`.`lastActivity`) AS `lastActivity`, UNIX_TIMESTAMP(DATE_ADD(`sessions`.`lastActivity`, INTERVAL 6 WEEK)) AS `validuntil`, `accounts`.`username` AS `username` FROM `sessions` LEFT JOIN `accounts` ON `sessions`.`accountId` = `accounts`.`id` ORDER BY `sessions`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
$content.= "<div class='row highlight bold bordered'>".
"<div class='col-s-12 col-l-3'>User</div>".
"<div class='col-s-12 col-l-3'>letzte Aktivität</div>".
"<div class='col-s-12 col-l-3'>gültig bis</div>".
"<div class='col-s-12 col-l-3'>Beenden</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<div class='row hover bordered'>";
  $content.= "<div class='col-s-12 col-l-3'>".output($row['username'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i:s", $row['lastActivity'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i:s", $row['validuntil'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".($sessionHash == $row['hash'] ? "eigene Sitzung" : "<a href='/adminSessions/del?id=".$row['id']."'>Beenden</a>")."</div>";
  $content.= "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>";
  $content.= "</div>";
}

/**
 * Formular zum Passwort ändern
 */
$content.= "<div class='spacer-m'></div>";
$content.= "<h2><span class='fas icon'>&#xf084;</span>Eigenes Passwort ändern</h2>";

$content.= "<form action='/adminPassword/change' method='post'>";
/**
 * Sitzungstoken
 */
$content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>neues Passwort</div>".
"<div class='col-s-12 col-l-4'><input type='password' name='password' tabindex='1'></div>".
"<div class='col-s-12 col-l-5'>Muss mindestens aus 20 Zeichen bestehen.<br><a href='https://rundesballi.com/pwgen' target='_blank'>Passwortgenerator</a></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Passwort ändern</div>".
"<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='ändern' tabindex='2'></div>".
"<div class='col-s-12 col-l-5'><span class='highlight'>Info:</span> Bei Erfolg werden alle eigenen offenen Sitzungen geschlossen.</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";
$content.= "</form>";
?>
