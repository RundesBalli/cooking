<?php
/**
 * showCategory.php
 * 
 * Anzeige aller Einträge innerhalb einer gewählten Kategorie.
 */

/**
 * Prüfen ob die übergebene Kategorie leer ist.
 */
if(!isset($_GET['category']) OR empty(trim($_GET['category']))) {
  http_response_code(404);
  $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>".PHP_EOL;
  $content.= "<div class='infobox'>Du musst eine Kategorie angeben.</div>".PHP_EOL;
} else {
  /**
   * Übergebene Kategorie für den Query vorbereiten.
   */
  $category = defuse($_GET['category']);

  /**
   * Kategorie abfragen
   */
  $result = mysqli_query($dbl, "SELECT * FROM `categories` WHERE `shortTitle`='".$category."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);

    /**
     * OG-Metadaten
     */
    $thumbresult = mysqli_query($dbl, "SELECT `categoryItems`.`itemId`, `images`.`fileHash` FROM `categoryItems` JOIN `images` ON `categoryItems`.`itemId`=`images`.`itemId` AND `thumb`='1' WHERE `categoryId` = '".$row['id']."' ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbresult) == 1) {
      $thumbrow = mysqli_fetch_array($thumbresult);
      $thumb = 'https://'.$_SERVER['HTTP_HOST']."/img/thumb-".$thumbrow['itemId']."-".$thumbrow['fileHash'].".png";
    } else {
      $thumb = 'https://'.$_SERVER['HTTP_HOST'].'/src/og_favicon.png';
    }
    $ogMeta = array(
      'title'            => 'pr0.cooking | Kategorie: '.output($row['title']),
      'type'             => 'article',
      'url'              => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
      'image'            => $thumb,
      'image:secure_url' => $thumb,
      'image:width'      => '300',
      'image:height'     => '300',
      'image:alt'        => 'pr0.cooking',
      'description'      => 'Rezeptsammlung von Fettsäcken für Fettsäcke',
      'locale'           => 'de_DE',
      'site_name'        => 'pr0.cooking'
    );

    /**
     * Adminschnellnavigation
     */
    if((isset($_COOKIE['cookingAdmin']) AND !empty($_COOKIE['cookingAdmin'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cookingAdmin']), $match) === 1) {
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><span class='bold warn'>Admin-Schnellzugriff:</span> <a href='/adminCategories/edit/".$row['id']."'><span class='fas icon'>&#xf044;</span>Editieren</a> - <a href='/adminCategories/sort/".$row['id']."'><span class='fas icon'>&#xf0dc;</span>Rezepte Sortieren</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    $title = $row['title'];
    $content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorie: ".output($row['title'])."</h1>".PHP_EOL;
    /**
     * Kategorienbeschreibung anzeigen, sofern vorhanden.
     */
    if($row['description'] !== NULL) {
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 borderleft'><span class='fas icon'>&#xf086;</span>".PHP_EOL.
      Slimdown::render($row['description'])."</div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='spacer-l'></div>".PHP_EOL;
    }

    /**
     * Inhalte der Kategorie anzeigen.
     */
    $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `wD`.`title` AS `workDuration`, `tD`.`title` AS `totalDuration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `categoryItems`.`itemId`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `categoryItems`.`itemId`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `categoryItems`.`itemId`), 0) AS `voteCount`, (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `categoryItems`.`itemId` AND `images`.`thumb`=1) AS `fileHash` FROM `categoryItems` JOIN `items` ON `categoryItems`.`itemId` = `items`.`id` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id` JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id` WHERE `categoryId`='".$row['id']."' ORDER BY `categoryItems`.`sortIndex` ASC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Dieser Kategorie wurden noch keine Rezepte zugewiesen</div>".PHP_EOL;
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
            "<div class='title'><a href='/rezept/".output($row['shortTitle'])."'>".output($row['title'])."</a></div>".PHP_EOL.
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
  } else {
    /**
     * Fehlermeldung, wenn die Kategorie nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='infobox'>Die Kategorie <span class='italic'>".output($category)."</span> existiert nicht.</div>".PHP_EOL;
  }
}
?>
