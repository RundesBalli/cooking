<?php
/**
 * start.php
 * 
 * Startseite mit kurzer Begrüßung und Erläuterung.
 */
$title = "Startseite";
$content.= "<h1><span class='fas icon'>&#xf805;</span>pr0.cooking</h1>".PHP_EOL;
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Willkommen auf pr0.cooking!</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>pr0.cooking ist eine Rezeptsammlung für leckere Rezepte.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Feedback bitte an <a href='https://pr0gramm.com/inbox/messages/Nezos' target='_blank' rel='noopener'>Nezos</a>.</div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Kategorienauflistung mit Kacheln
 */
$content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorien</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  $content.= "<div class='infobox'>Es existieren noch keine Kategorien.</div>".PHP_EOL;
} else {
  $content.= "<div id='categoryContainer'>".PHP_EOL;
  while($row = mysqli_fetch_array($result)) {
    $thumbresult = mysqli_query($dbl, "SELECT `categoryItems`.`itemId`, `images`.`fileHash` FROM `categoryItems` JOIN `images` ON `categoryItems`.`itemId`=`images`.`itemId` AND `thumb`='1' WHERE `categoryId` = '".$row['id']."' ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbresult) == 1) {
      $thumbrow = mysqli_fetch_array($thumbresult);
      $thumb = "/img/thumb-".$thumbrow['itemId']."-".$thumbrow['fileHash'].".png";
    } else {
      $thumb = "/src/og_favicon.png";
    }
    $content.= "<a href='/kategorie/".$row['shortTitle']."' style='background-image: linear-gradient(0deg, rgba(22,22,24, 0.7), rgba(22,22,24, 0.7)), url(\"".$thumb."\");'><div class=\"category-wrap\">".output($row['title']).(!empty($row['shortDescription']) ? "<span>".SlimdownOneline::render($row['shortDescription'])."</span>" : NULL)."</div></a>".PHP_EOL;
  }
  $content.= "</div>".PHP_EOL;
}
?>
