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
    $result = mysqli_query($dbl, "SELECT `items`.* FROM `category_items` JOIN `items` ON `category_items`.`item_id` = `items`.`shortTitle` WHERE `category_id`='".$row['id']."' ORDER BY `category_items`.`sortIndex` ASC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    while($row = mysqli_fetch_array($result)) {
      /**
       * 
       * 
       * 
       * 
       * 
       * 
       * 
       */
    }
  } else {
    /**
     * Fehlermeldung, wenn die Kategorie nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Die Kategorie <span class='italic'>".htmlentities($category, ENT_QUOTES)."</span> existiert nicht.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
