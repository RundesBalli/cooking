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
  $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Du musst eine Kategorie angeben.</div>".PHP_EOL.
  "</div>".PHP_EOL;
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
    $title = $row['title'];
    $content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorie: ".$row['title']."</h1>".PHP_EOL;
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
    $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `metaDuration`.`title` AS `duration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `categoryItems`.`itemId`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `categoryItems`.`itemId`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `categoryItems`.`itemId`), 0) AS `voteCount`, (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `categoryItems`.`itemId` AND `images`.`thumb`=1) AS `fileHash` FROM `categoryItems` JOIN `items` ON `categoryItems`.`itemId` = `items`.`id` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` ON `items`.`duration` = `metaDuration`.`id` WHERE `categoryId`='".$row['id']."' ORDER BY `categoryItems`.`sortIndex` ASC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Dieser Kategorie wurden noch keine Rezepte zugewiesen</div>".PHP_EOL;
    } else {
      $content.= "<div class='row'>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='col-x-12 col-s-12 col-m-6 col-l-4 col-xl-3 item'>".PHP_EOL.
        "<a href='/rezept/".output($row['shortTitle'])."'>".PHP_EOL.
        "<img src='/img/".($row['fileHash'] === NULL ? "noThumb.png" : "thumb-".$row['id']."-".$row['fileHash'].".png")."'>".PHP_EOL.
        "<div><span class='title'>".$row['title']."</span></div>".PHP_EOL.
        "</a>".PHP_EOL.
        "<div class='stars'>".stars($row['votes'], $row['voteCount'])."</div>".PHP_EOL.
        "<div class='specs'><span class='far icon'>&#xf25a;</span>&nbsp;".number_format($row['clicks'], 0, ",", ".")."</div>".PHP_EOL.
        "<div class='specs'><span class='far icon'>&#xf0eb;</span> ".$row['difficulty']."</div>".PHP_EOL.
        "<div class='specs'><span class='far icon'>&#xf254;</span> ".$row['duration']."</div>".PHP_EOL.
        "<div class='specs'><span class='fas icon'>&#xf153;</span> ".$row['cost']."</div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
      $content.= "</div>".PHP_EOL;
    }
  } else {
    /**
     * Fehlermeldung, wenn die Kategorie nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Die Kategorie <span class='italic'>".output($category)."</span> existiert nicht.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
