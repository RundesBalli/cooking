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
 * Auflistung aller Kategorien mit Kurzbeschreibung.
 */
$content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorien</h1>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  $content.= "<div class='infobox'>Es existieren noch keine Kategorien.</div>".PHP_EOL;
} else {
  while($row = mysqli_fetch_array($result)) {
    $content.= "<div class='row hover'>".PHP_EOL;
    $content.= "<div class='col-x-12 col-s-12 col-m-5 col-l-4 col-xl-3'><a href='/kategorie/".$row['shortTitle']."'>".$row['title']."</a></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-7 col-l-8 col-xl-9'>".($row['shortDescription'] == NULL ? "<span class='italic'>Keine Beschreibung vorhanden</span>" : SlimdownOneline::render($row['shortDescription']))."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL;
    $content.= "</div>".PHP_EOL;
  }
}
?>
