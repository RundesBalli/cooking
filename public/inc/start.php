<?php
/**
 * start.php
 * 
 * Startseite
 */
$content.= "<h1 class='alignCenter'><span class='fas icon'>&#xf805;</span>".$ogConfig['sitename']."</h1>";

/**
 * Laden der zusätzlichen CSS Datei für die Kacheln
 */
$additionalStyles[] = "tiles";

/**
 * Vorgestellte Rezepte
 */
$result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `featured`.`itemId` AND `images`.`thumb`=1) AS `fileHash` FROM `featured` JOIN `items` ON `featured`.`itemId` = `items`.`id` ORDER BY `featured`.`id` DESC LIMIT 4") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) > 0) {
  $content.= "<h2 class='alignCenter'><span class='fas icon'>&#xf005;</span>Vorgestellte Rezepte</h2>";
  $content.= "<div class='tileContainer'>";
  while($row = mysqli_fetch_array($result)) {
    if($row['fileHash'] !== NULL) {
      $thumb = "/img/thumb-".$row['id']."-".$row['fileHash'].".png";
    } else {
      $thumb = "/assets/images/favicon.png";
    }
    $content.= "<a href='/rezept/".$row['shortTitle']."' style='background-image: linear-gradient(0deg, rgba(20, 20, 20, 0.7), rgba(20, 20, 20, 0.7)), url(\"".$thumb."\");'><div class=\"tile-wrap\">".output($row['title'])."</div></a>";
  }
  $content.= "</div>";
  $content.= "<div class='spacer-l'></div>";
}

/**
 * Kategorienauflistung mit Kacheln
 */
$content.= "<h2 class='alignCenter'><span class='far icon'>&#xf07c;</span>Kategorien</h2>";
$result = mysqli_query($dbl, "SELECT * FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  $content.= "<div class='infobox'>Es existieren noch keine Kategorien.</div>";
} else {
  $content.= "<div class='tileContainer'>";
  while($row = mysqli_fetch_array($result)) {
    $thumbresult = mysqli_query($dbl, "SELECT `categoryItems`.`itemId`, `images`.`fileHash` FROM `categoryItems` JOIN `images` ON `categoryItems`.`itemId`=`images`.`itemId` AND `thumb`='1' WHERE `categoryId` = '".$row['id']."' ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbresult) == 1) {
      $thumbrow = mysqli_fetch_array($thumbresult);
      $thumb = "/img/thumb-".$thumbrow['itemId']."-".$thumbrow['fileHash'].".png";
    } else {
      $thumb = "/assets/images/favicon.png";
    }
    $content.= "<a href='/kategorie/".$row['shortTitle']."' style='background-image: linear-gradient(0deg, rgba(20, 20, 20, 0.7), rgba(20, 20, 20, 0.7)), url(\"".$thumb."\");'><div class=\"tile-wrap\">".output($row['title']).(!empty($row['shortDescription']) ? "<span>".SlimdownOneline::render($row['shortDescription'])."</span>" : NULL)."</div></a>";
  }
  $content.= "</div>";
}
?>
