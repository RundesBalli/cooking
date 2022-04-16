<?php
/**
 * adminLog.php
 * 
 * Seite um Logeinträge einzusehen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

$title = "Log";
$content.= "<h1><span class='fas icon'>&#xf70e;</span>Log</h1>";

/**
 * Tabellenüberschrift
 */
$content.= "<div class='row highlight bold smaller' style='border-left: 6px solid #888888;'>".
"<div class='col-s-2 col-l-1'>ID</div>".
"<div class='col-s-4 col-l-1'><span class='fas icon'>&#xf007;</span>Username</div>".
"<div class='col-s-6 col-l-2'><span class='far icon'>&#xf017;</span>Zeitpunkt</div>".
"<div class='col-s-6 col-l-2'>Daten</div>".
"<div class='col-s-6 col-l-6'><span class='fas icon'>&#xf1dd;</span>Text</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

/**
 * Suchparameter
 */
$where = "";
if(!empty($_GET['older'])) {
  $older = (int)defuse($_GET['older']);
  if($older > 1) {
    $where = "WHERE `log`.`id` < '".$older."' ";
  }
}

/**
 * Loganzeige
 */
$result = mysqli_query($dbl, "SELECT `log`.`id`, `accounts`.`username`, `log`.`timestamp`, `logLevel`.`color`, `logLevel`.`title` AS `logLevelTitle`, `log`.`itemId`, `items`.`title` AS `itemTitle`, `items`.`shortTitle` AS `itemShortTitle`, `log`.`categoryId`, `categories`.`title` AS `categoryTitle`, `categories`.`shortTitle` AS `categoryShortTitle`, `log`.`text` FROM `log` LEFT OUTER JOIN `accounts` ON `accounts`.`id`=`log`.`accountId` JOIN `logLevel` ON `log`.`logLevel`=`logLevel`.`id` LEFT OUTER JOIN `items` ON `items`.`id`=`log`.`itemId` LEFT OUTER JOIN `categories` ON `categories`.`id`=`log`.`categoryId` ".$where."ORDER BY `log`.`id` DESC LIMIT 100") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row hover bordered smaller' style='border-left: 6px solid #".$row['color'].";' title='".$row['logLevelTitle']."'>".
  "<div class='col-s-2 col-l-1'>".$row['id']."</div>".
  "<div class='col-s-4 col-l-1'>".($row['username'] === NULL ? "-" : ($row['username'] == $username ? "<span class='highlight'>".output($row['username'])."</span>" : output($row['username'])))."</div>".
  "<div class='col-s-6 col-l-2'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".
  "<div class='col-s-6 col-l-2'>".((!isset($row['itemId']) AND !isset($row['categoryId'])) ? "-" : NULL).($row['itemId'] !== NULL ? "<span class='fas icon'>&#xf543;</span><a href='/rezept/".output($row['itemShortTitle'])."' target='_blank'>".output($row['itemTitle'])."<span class='fas iconright'>&#xf35d;</span></a>" : NULL).((isset($row['itemId']) AND isset($row['categoryId'])) ? "<br>" : NULL).($row['categoryId'] !== NULL ? "<span class='far icon'>&#xf07c;</span><a href='/kategorie/".output($row['categoryShortTitle'])."' target='_blank'>".output($row['categoryTitle'])."<span class='fas iconright'>&#xf35d;</span></a>" : NULL)."</div>".
  "<div class='col-s-6 col-l-6'>".Slimdown::render($row['text'])."</div>".
  "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
  "</div>";

  $logIds[] = $row['id'];
}
$result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `log` WHERE `id`<'".min($logIds)."') AS `older`") OR DIE(MYSQLI_ERROR($dbl));
$row = mysqli_fetch_array($result);
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12 text-right'>".($row['older'] != 0 ? "<a href='/adminLog?older=".min($logIds)."'>Älter »</a>" : NULL)."</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Loglevel
 */
$content.= "<h2>Loglevel</h2>";
$result = mysqli_query($dbl, "SELECT * FROM `logLevel` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12 hover' style='border-left: 6px solid #".$row['color'].";' title='".$row['title']."''>".$row['title']."</div>".
  "</div>";
}
$content.= "<div class='spacer-m'></div>";

?>
