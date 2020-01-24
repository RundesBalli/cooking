<?php
/**
 * showcategory.php
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
    $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `meta_cost`.`title` AS `cost`, `meta_difficulty`.`title` AS `difficulty`, `meta_duration`.`title` AS `duration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemid` = `category_items`.`item_id`) AS `clicks`, (SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemid` = `category_items`.`item_id`) AS `votes`, (SELECT `images`.`filehash` FROM `images` WHERE `images`.`itemid` = `category_items`.`item_id` AND `images`.`thumb`=1) AS `filehash` FROM `category_items` JOIN `items` ON `category_items`.`item_id` = `items`.`shortTitle` JOIN `meta_cost` ON `items`.`cost` = `meta_cost`.`id` JOIN `meta_difficulty` ON `items`.`difficulty` = `meta_difficulty`.`id` JOIN `meta_duration` ON `items`.`duration` = `meta_duration`.`id` WHERE `category_id`='".$row['id']."' ORDER BY `category_items`.`sortIndex` ASC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $content.= "<div class='row'>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='col-x-12 col-s-12 col-m-6 col-l-4 col-xl-3 item'>".PHP_EOL.
      "<a href='/rezept/".output($row['shortTitle'])."'>".PHP_EOL.
      "<img src='/img/".($row['filehash'] === NULL ? "nothumb.png" : "thumb-".$row['id']."-".$row['filehash'].".png")."'>".PHP_EOL.
      "<div><span class='title'>".$row['title']."</span></div>".PHP_EOL.
      "</a>".PHP_EOL.
      "<div class='stars'>".stars($row['votes'])."</div>".PHP_EOL.
      "<div class='specs'><span class='far icon'>&#xf25a;</span>&nbsp;".$row['clicks']."</div>".PHP_EOL.
      "<div class='specs'><span class='far icon'>&#xf0eb;</span> ".$row['difficulty']."</div>".PHP_EOL.
      "<div class='specs'><span class='far icon'>&#xf254;</span> ".$row['duration']."</div>".PHP_EOL.
      "<div class='specs'><span class='fas icon'>&#xf153;</span> ".$row['cost']."</div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    $content.= "</div>".PHP_EOL;
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
