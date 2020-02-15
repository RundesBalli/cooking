<?php
/**
 * showitem.php
 * 
 * Anzeige eines Rezepts.
 */

/**
 * Prüfen ob das übergebene Rezept leer ist.
 */
if(!isset($_GET['item']) OR empty(trim($_GET['item']))) {
  http_response_code(404);
  $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Du musst ein Rezept angeben.</div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Übergebene Kategorie für den Query vorbereiten.
   */
  $item = defuse($_GET['item']);

  /**
   * Rezept abfragen
   */
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `items`.`text`, `items`.`ingredients`, `items`.`persons`, `meta_cost`.`title` AS `cost`, `meta_difficulty`.`title` AS `difficulty`, `meta_duration`.`title` AS `duration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemid` = `items`.`id`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemid` = `items`.`id`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemid` = `items`.`id`), 0) AS `voteCount` FROM `items` JOIN `meta_cost` ON `items`.`cost` = `meta_cost`.`id` JOIN `meta_difficulty` ON `items`.`difficulty` = `meta_difficulty`.`id`JOIN `meta_duration` ON `items`.`duration` = `meta_duration`.`id` WHERE `shortTitle`='".$item."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    
    /**
     * Klick zählen oder aktualisieren
     */
    mysqli_query($dbl, "UPDATE `clicks` SET `ts`=CURRENT_TIMESTAMP WHERE `hash`='".$UUI."' AND `ts` > DATE_SUB(NOW(), INTERVAL 30 HOUR) LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_affected_rows($dbl) != 1) {
      mysqli_query($dbl, "INSERT INTO `clicks` (`itemid`, `hash`) VALUES ('".$row['id']."', '".$UUI."')") OR DIE(MYSQLI_ERROR($dbl));
    }

    $title = $row['title'];
    $content.= "<h1 class='center'><span class='fas icon'>&#xf543;</span>Rezept: ".$row['title']."</h1>".PHP_EOL;
    /**
     * Bilder Selektieren
     */
    $imgresult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$row['id']."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $images = array();
    while($imgrow = mysqli_fetch_array($imgresult)) {
      $images[] = $imgrow['filehash'];
    }
    /**
     * Bilder, Eckdaten & Zutaten ausgeben
     */
    $count = count($images);
    if($count > 1) {
      /**
       * Bei mehreren Bildern
       */
      $content.= "<div class='row recipe center'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6 ingredients center'>".PHP_EOL.
      "<h2 class='center'><span class='fas icon'>&#xf0ce;</span>Eckdaten</h2>".PHP_EOL.
      "<ul>".PHP_EOL.
      "<li>".stars($row['votes'], $row['voteCount'])." - <a href='/vote/".$row['shortTitle']."'>Abstimmen</a><br>".$row['votes']." von 5 Sternen (".number_format($row['voteCount'], 0, ",", ".")." Stimmen)</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf25a;</span>".number_format($row['clicks'], 0, ",", ".")." Klicks</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf0eb;</span>Schwierigkeit: ".$row['difficulty']."</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf254;</span>Dauer: ".$row['duration']."</li>".PHP_EOL.
      "<li><span class='fas icon'>&#xf153;</span>Kosten: ".$row['cost']."</li>".PHP_EOL.
      "</ul>".PHP_EOL.
      "<div class='spacer-s'></div>".PHP_EOL;
      if(!empty($row['ingredients'])) {
        $content.= "<h2 class='center'><span class='fas icon'>&#xf4d8;</span>Zutaten".($row['persons'] > 0 ? " für ".$row['persons']." Personen" : NULL)."</h2>".PHP_EOL.Slimdown::render($row['ingredients']).PHP_EOL.
        "<div class='spacer-s'></div>".PHP_EOL;
      }
      $content.= "</div>".PHP_EOL;

      $slideshow = "<div id='slideshowContainer'>".PHP_EOL;
      foreach($images as $key => $val) {
        $internalId = $key + 1;
        $slideshow.= "<div class='mySlides fade'>".PHP_EOL.
        "<div class='numbertext'>".$internalId." / ".$count."</div>".PHP_EOL.
        "<a href='/img/img-".$row['id']."-full-".$val.".png' target='_blank'><img src='/img/img-".$row['id']."-small-".$val.".png' alt='Bild'></a>".PHP_EOL.
        "</div>".PHP_EOL;
      }
      $slideshow.= "<a id='prev'>&#10094;</a>".PHP_EOL;
      $slideshow.= "<a id='next'>&#10095;</a>".PHP_EOL;
      $slideshow.= "</div>".PHP_EOL;

      $content.= "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6'>".$slideshow."</div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Bei einem oder keinem Bild
       */
      $content.= "<div class='row recipe center'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6 ingredients center'>".PHP_EOL.
      "<h2 class='center'><span class='fas icon'>&#xf0ce;</span>Eckdaten</h2>".PHP_EOL.
      "<ul>".PHP_EOL.
      "<li>".stars($row['votes'], $row['voteCount'])." - <a href='/vote/".$row['shortTitle']."'>Abstimmen</a><br>".$row['votes']." von 5 Sternen (".number_format($row['voteCount'], 0, ",", ".")." Stimmen)</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf25a;</span>".number_format($row['clicks'], 0, ",", ".")." Klicks</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf0eb;</span>Schwierigkeit: ".$row['difficulty']."</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf254;</span>Dauer: ".$row['duration']."</li>".PHP_EOL.
      "<li><span class='fas icon'>&#xf153;</span>Kosten: ".$row['cost']."</li>".PHP_EOL.
      "</ul>".PHP_EOL.
      "<div class='spacer-s'></div>".PHP_EOL;
      if(!empty($row['ingredients'])) {
        $content.= "<h2 class='center'><span class='fas icon'>&#xf4d8;</span>Zutaten".($row['persons'] > 0 ? " für ".$row['persons']." Personen" : NULL)."</h2>".PHP_EOL.Slimdown::render($row['ingredients']).PHP_EOL.
        "<div class='spacer-s'></div>".PHP_EOL;
      }
      $content.= "</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6'>".(count($images) == 1 ? "<a href='/img/img-".$row['id']."-full-".$images[0].".png' target='_blank'><img src='/img/img-".$row['id']."-small-".$images[0].".png' alt='Bild'></a>" : "<img src='/img/noimg.png' alt='kein Bild vorhanden'>")."</div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    /**
     * Text
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>".Slimdown::render($row['text'])."</div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Fehlermeldung, wenn das Rezept nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Das Rezept <span class='italic'>".output($item)."</span> existiert nicht.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
}
?>
