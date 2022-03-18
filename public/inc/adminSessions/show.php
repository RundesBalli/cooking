<?php
/**
 * adminSessions.php
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
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover bordered'>";
  $content.= "<div class='col-s-12 col-l-3'>".output($row['username'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i:s", $row['lastActivity'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i:s", $row['validuntil'])."</div>";
  $content.= "<div class='col-s-12 col-l-3'>".($sessionHash == $row['hash'] ? "eigene Sitzung" : "<a href='/adminSessions/del?id=".$row['id']."'>Beenden</a>")."</div>";
  $content.= "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>";
  $content.= "</div>";
}
?>
