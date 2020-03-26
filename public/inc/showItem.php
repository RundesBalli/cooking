<?php
/**
 * showItem.php
 * 
 * Anzeige eines Rezepts.
 */

/**
 * Prüfen ob das übergebene Rezept leer ist.
 */
if(!isset($_GET['item']) OR empty(trim($_GET['item']))) {
  http_response_code(404);
  $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>".PHP_EOL;
  $content.= "<div class='infobox'>Du musst ein Rezept angeben.</div>".PHP_EOL;
} else {
  /**
   * Übergebene Kategorie für den Query vorbereiten.
   */
  $item = defuse($_GET['item']);

  /**
   * Rezept abfragen
   */
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `items`.`text`, `items`.`ingredients`, `items`.`persons`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `metaDuration`.`title` AS `duration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `items`.`id`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `voteCount` FROM `items` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id`JOIN `metaDuration` ON `items`.`duration` = `metaDuration`.`id` WHERE `shortTitle`='".$item."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    
    /**
     * Klick zählen oder aktualisieren
     */
    mysqli_query($dbl, "UPDATE `clicks` SET `ts`=CURRENT_TIMESTAMP WHERE `hash`='".$UUI."' AND `ts` > DATE_SUB(NOW(), INTERVAL 30 HOUR) LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_affected_rows($dbl) != 1) {
      mysqli_query($dbl, "INSERT INTO `clicks` (`itemId`, `hash`) VALUES ('".$row['id']."', '".$UUI."')") OR DIE(MYSQLI_ERROR($dbl));
    }

    $title = $row['title'];
    $content.= "<h1 class='center'><span class='fas icon'>&#xf543;</span>Rezept: ".$row['title']."</h1>".PHP_EOL;
    /**
     * Bilder Selektieren
     */
    $imgresult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$row['id']."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $images = array();
    while($imgrow = mysqli_fetch_array($imgresult)) {
      $images[] = $imgrow['fileHash'];
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
      "<li>".stars($row['votes'], $row['voteCount'])." - ".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/vote/".$row['shortTitle']."'>Abstimmen</a>" : "zum Abstimmen <a href='/login'>Einloggen</a>")."<br>".$row['votes']." von 5 Sternen (".number_format($row['voteCount'], 0, ",", ".")." Stimmen)</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf005;</span>".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/fav/".$row['shortTitle']."'>Favorisieren</a>" : "zum Favorisieren <a href='/login'>Einloggen</a>")."</li>".PHP_EOL.
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
        "<img src='/img/img-".$row['id']."-".$val.".png' alt='Bild'>".PHP_EOL.
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
      "<li>".stars($row['votes'], $row['voteCount'])." - ".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/vote/".$row['shortTitle']."'>Abstimmen</a>" : "zum Abstimmen <a href='/login'>Einloggen</a>")."<br>".$row['votes']." von 5 Sternen (".number_format($row['voteCount'], 0, ",", ".")." Stimmen)</li>".PHP_EOL.
      "<li><span class='far icon'>&#xf005;</span>".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/fav/".$row['shortTitle']."'>Favorisieren</a>" : "zum Favorisieren <a href='/login'>Einloggen</a>")."</li>".PHP_EOL.
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
      "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6'>".(count($images) == 1 ? "<img src='/img/img-".$row['id']."-".$images[0].".png' alt='Bild'>" : "<img src='/img/noImg.png' alt='kein Bild vorhanden'>")."</div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    /**
     * Text
     */
    $content.= "<h2><span class='fas icon'>&#xf03a;</span>Zubereitung</h2>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>".Slimdown::render($row['text'])."</div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
  } else {
    /**
     * Fehlermeldung, wenn das Rezept nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>".PHP_EOL;
    $content.= "<div class='infobox'>Das Rezept <span class='italic'>".output($item)."</span> existiert nicht.</div>".PHP_EOL;
  }
}
?>
