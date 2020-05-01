<?php
/**
 * favs.php
 * 
 * Favoritenseite zur Anzeige der eigenen Favoriten
 */

/**
 * Titel
 */
$title = "Favoriten";
$content.= "<h1><span class='fas icon'>&#xf005;</span>Favoriten</h1>".PHP_EOL;

/**
 * Sessionüberprüfung
 */
require_once('cookieCheck.php');

/**
 * Selektion der Favoriten und Ausgabe ebendieser.
 */
$result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `wD`.`title` AS `workDuration`, `tD`.`title` AS `totalDuration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `favs`.`itemId`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `favs`.`itemId`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `favs`.`itemId`), 0) AS `voteCount`, (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `favs`.`itemId` AND `images`.`thumb`=1) AS `fileHash` FROM `favs` JOIN `items` ON `favs`.`itemId` = `items`.`id` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id` JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id` WHERE `userId`='".$userId."' ORDER BY `favs`.`id` DESC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  $content.= "<div class='infobox'>Du hast keine Favoriten.</div>".PHP_EOL;
} else {
  $items = array();
  while($row = mysqli_fetch_array($result)) {
    $items[] =
    "<div class='row item'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-3'>".PHP_EOL.
        "<a href='/rezept/".output($row['shortTitle'])."'>".PHP_EOL.
          "<img src='/img/".($row['fileHash'] === NULL ? "noThumb.png" : "thumb-".$row['id']."-".$row['fileHash'].".png")."'>".PHP_EOL.
        "</a>".PHP_EOL.
      "</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-8 col-xl-9 iteminfo'>".PHP_EOL.
        "<div class='title'><a href='/rezept/".output($row['shortTitle'])."'>".$row['title']."</a></div>".PHP_EOL.
        "<div class='stars'>".stars($row['votes'], $row['voteCount'])."</div>".PHP_EOL.
        "<div class='specs'><span class='far icon pointer' title='Klicks'>&#xf25a;</span> ".number_format($row['clicks'], 0, ",", ".")."</div>".PHP_EOL.
        "<div class='specs'><span class='far icon pointer' title='Schwierigkeitsgrad'>&#xf0eb;</span> ".$row['difficulty']."</div>".PHP_EOL.
        "<div class='specs'><span class='fas icon pointer' title='Kosten'>&#xf153;</span> ".$row['cost']."</div>".PHP_EOL.
        "<br>".PHP_EOL.
        "<div class='specs'><span class='fas icon pointer' title='Arbeitszeit'>&#xf252;</span> ".$row['workDuration']."</div>".PHP_EOL.
        "<div class='specs'><span class='fas icon pointer' title='Gesamtzeit'>&#xf253;</span> ".$row['totalDuration']."</div>".PHP_EOL.
      "</div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
  $content.= implode("<hr class='itemhr'>".PHP_EOL, $items);
}
?>
