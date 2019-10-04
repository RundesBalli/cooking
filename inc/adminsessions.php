<?php
/**
 * adminsessions.php
 * 
 * Seite um aktive Sitzungen anzuzeigen und ggf. zu beenden.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

$title = "Sitzungen anzeigen";
$content.= "<h1>Sitzungen anzeigen</h1>".PHP_EOL;

/**
 * Löschfunktion
 */
if((isset($_GET['action']) AND $_GET['action'] == 'del') AND (isset($_GET['id']) AND !empty($_GET['id']))) {
  $id = (int)defuse($_GET['id']);
  mysqli_query($dbl, "DELETE FROM `sessions` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_affected_rows($dbl) == 0) {
    http_response_code(404);
    $content.= "<div class='warnbox'>Es existiert keine Sitzung mit der ID.</div>".PHP_EOL;
  } else {
    $content.= "<div class='successbox'>Die Sitzung wurde beendet.</div>".PHP_EOL;
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
  $content.= "<div class='col-x-12 col-s-12 col-m-3 col-l-3 col-xl-3'>".($sessionhash == $row['hash'] ? "eigene Sitzung" : "<a href='/adminsessions/del/".$row['id']."'>Beenden</a>")."</div>".PHP_EOL;
  $content.= "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL;
  $content.= "</div>".PHP_EOL;
}
?>
