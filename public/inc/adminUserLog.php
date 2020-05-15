<?php
/**
 * adminUserLog.php
 * 
 * Seite um Logeinträge der User zu betrachten.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('adminCookie.php');

$title = "UserLog";
$content.= "<h1><span class='fas icon'>&#xf70e;</span>UserLog</h1>".PHP_EOL;

/**
 * Tabellenüberschrift
 */
$content.= "<div class='row highlight bold bordered' style='border-left: 6px solid #888888;'>".PHP_EOL.
"<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>ID</div>".PHP_EOL.
"<div class='col-x-4 col-s-4 col-m-5 col-l-5 col-xl-2'><span class='fas icon'>&#xf007;</span>Username</div>".PHP_EOL.
"<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-3'><span class='far icon'>&#xf017;</span>Zeitpunkt</div>".PHP_EOL.
"<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-2'><span class='fas icon'>&#xf543;</span>Rezept</div>".PHP_EOL.
"<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-4'><span class='fas icon'>&#xf1dd;</span>Text</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

/**
 * Suchparameter
 */
$where = "";
if(!empty($_GET['older'])) {
  $older = (int)defuse($_GET['older']);
  if($older > 1) {
    $where = "WHERE `userLog`.`id` < '".$older."' ";
  }
}

/**
 * Loganzeige
 */
$result = mysqli_query($dbl, "SELECT `userLog`.`id`, `users`.`username`, `userLog`.`timestamp`, `logLevel`.`color`, `logLevel`.`title` AS `logLevelTitle`, `userLog`.`itemId`, `items`.`title` AS `itemTitle`, `items`.`shortTitle` AS `itemShortTitle`, `userLog`.`text` FROM `userLog` LEFT OUTER JOIN `users` ON `users`.`id`=`userLog`.`userId` JOIN `logLevel` ON `userLog`.`logLevel`=`logLevel`.`id` LEFT OUTER JOIN `items` ON `items`.`id`=`userLog`.`itemId` ".$where."ORDER BY `userLog`.`id` DESC LIMIT 100") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover bordered' style='border-left: 6px solid #".$row['color'].";' title='".$row['logLevelTitle']."'>".PHP_EOL.
  "<div class='col-x-2 col-s-2 col-m-1 col-l-1 col-xl-1'>".$row['id']."</div>".PHP_EOL.
  "<div class='col-x-4 col-s-4 col-m-5 col-l-5 col-xl-2'>".output($row['username'])."</div>".PHP_EOL.
  "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-3'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".PHP_EOL.
  "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-2'>".($row['itemId'] !== NULL ? "<a href='/rezept/".output($row['itemShortTitle'])."' target='_blank'>".output($row['itemTitle'])."<span class='fas iconright'>&#xf35d;</span></a>" : NULL)."</div>".PHP_EOL.
  "<div class='col-x-6 col-s-6 col-m-6 col-l-6 col-xl-4'>".Slimdown::render($row['text'])."</div>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
  "</div>".PHP_EOL;

  $logIds[] = $row['id'];
}
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `userLog` WHERE `id`<'".min($logIds)."') AS `older`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 text-right'>".($row['older'] != 0 ? "<a href='/adminLog?older=".min($logIds)."'>Älter »</a>" : NULL)."</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Loglevel
 */
$content.= "<h1>Loglevel</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `logLevel` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover' style='color: #".$row['color'].";'>".$row['title']."</div>".PHP_EOL.
  "</div>".PHP_EOL;
}
$content.= "<div class='spacer-m'></div>".PHP_EOL;

?>
