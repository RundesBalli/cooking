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
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `items`.`text`, `items`.`persons`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `wD`.`title` AS `workDuration`, `tD`.`title` AS `totalDuration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `items`.`id`) AS `clicks`, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `votes`, IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `voteCount` FROM `items` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id` JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id` WHERE `shortTitle`='".$item."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    /**
     * Klick zählen oder aktualisieren
     */
    mysqli_query($dbl, "UPDATE `clicks` SET `ts`=CURRENT_TIMESTAMP WHERE `hash`='".$UUI."' AND `ts` > DATE_SUB(NOW(), INTERVAL 30 HOUR) LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_affected_rows($dbl) != 1) {
      mysqli_query($dbl, "INSERT INTO `clicks` (`itemId`, `hash`) VALUES ('".$row['id']."', '".$UUI."')") OR DIE(MYSQLI_ERROR($dbl));
    }

    /**
     * OG-Metadaten
     */
    $thumbresult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$row['id']."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbresult) == 1) {
      $thumbrow = mysqli_fetch_array($thumbresult);
      $thumb = 'https://'.$_SERVER['HTTP_HOST']."/img/thumb-".$row['id']."-".$thumbrow['fileHash'].".png";
    } else {
      $thumb = 'https://'.$_SERVER['HTTP_HOST'].'/src/og_favicon.png';
    }
    $ogMeta = array(
      'title'            => 'pr0.cooking | Rezept: '.output($row['title']),
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
      $content.= "<div class='row no-print'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'><span class='bold warn'>Admin-Schnellzugriff:</span> <a href='/adminItems/edit/".$row['id']."'><span class='fas icon'>&#xf044;</span>Editieren</a> - <a href='/adminItems/assign/".$row['id']."'><span class='far icon'>&#xf07c;</span>Kategorien</a> - <a href='/adminFiles/list/".$row['id']."'><span class='fas icon'>&#xf302;</span>Bilder</a> - <a href='/adminIngredients/assign/".$row['id']."'><span class='fas icon'>&#xf4d8;</span>Zutaten</a> - <a href='/adminFeaturedItems/add/".$row['id']."'><span class='fas icon'>&#xf005;</span>Featuren</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }

    /**
     * Titel anzeigen
     */
    $title = output($row['title']);
    $content.= "<h1 class='center'><span class='fas icon'>&#xf543;</span>Rezept: ".output($row['title'])."</h1>".PHP_EOL;

    /**
     * Bilder Selektieren
     */
    $imgresult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$row['id']."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $images = array();
    while($imgrow = mysqli_fetch_array($imgresult)) {
      $images[] = array('fileHash' => $imgrow['fileHash'], 'description' => $imgrow['description']);
    }

    /**
     * ShareButtons
     */
    $shareText = urlencode("Ich habe ein leckeres Rezept für ".$row['title']." gefunden!\n"."Schau mal hier: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $content.= "<div id='shareButtons' class='no-print'><a href='tg://msg?text=".$shareText."' target='_blank' rel='noopener'><span class='fab icon'>&#xf3fe;</span></a><a href='whatsapp://send?text=".$shareText."' target='_blank' rel='noopener'><span class='fab icon'>&#xf232;</span></a><a href=\"#\" class=\"copy-link-btn\"><span class='far icon'>&#xf0c5;</span></a></div>".PHP_EOL;

    /**
     * Bilder, Eckdaten & Zutaten ausgeben
     */
    $content.= "<div class='row recipe center'>".PHP_EOL.

    /**
     * Eckdaten
     */
    "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6 ingredients center print-fullWidth'>".PHP_EOL.
    "<h2 class='center'><span class='fas icon'>&#xf0ce;</span>Eckdaten</h2>".PHP_EOL.
    "<ul>".PHP_EOL.
    "<li>".stars($row['votes'], $row['voteCount'])." - ".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/vote/".$row['shortTitle']."'>Abstimmen</a>" : "zum Abstimmen <a href='/login'>Einloggen</a>")."<br>".$row['votes']." von 5 Sternen (".number_format($row['voteCount'], 0, ",", ".")." Stimmen)</li>".PHP_EOL.
    "<li><span class='far icon'>&#xf005;</span>".(((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) ? "<a href='/fav/".$row['shortTitle']."'>Favorisieren</a>" : "zum Favorisieren <a href='/login'>Einloggen</a>")."</li>".PHP_EOL.
    "<li><span class='far icon'>&#xf25a;</span>".number_format($row['clicks'], 0, ",", ".")." Klicks</li>".PHP_EOL.
    "<li><span class='far icon'>&#xf0eb;</span>Schwierigkeit: ".$row['difficulty']."</li>".PHP_EOL.
    "<li><span class='fas icon'>&#xf252;</span>Arbeitszeit: ".$row['workDuration']."</li>".PHP_EOL.
    "<li><span class='fas icon'>&#xf253;</span>Gesamtzeit: ".$row['totalDuration']."</li>".PHP_EOL.
    "<li><span class='fas icon'>&#xf153;</span>Kosten: ".$row['cost']."</li>".PHP_EOL.
    "</ul>".PHP_EOL.
    "<div class='spacer-s'></div>".PHP_EOL;

    /**
     * Zutaten
     */
    $customPersons = FALSE;
    if($row['persons'] > 0) {
      /**
       * Personenrechner für Zutaten
       */
      if(!empty($_GET['persons'])) {
        $persons = (int)$_GET['persons'];
        if($persons < 1 OR $persons > 100) {
          $persons = $row['persons'];
        } else {
          $customPersons = TRUE;
        }
      } else {
        $persons = $row['persons'];
      }
    } else {
      $persons = 0;
    }
    $content.= "<h2 class='center'><span class='fas icon'>&#xf4d8;</span>Zutaten".($persons > 0 ? " für ".output($persons)." Person".($persons > 1 ? "en" : NULL) : NULL)."</h2>".PHP_EOL;
    $innerresult = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `metaUnits`.`short`, `metaUnits`.`spacer`, `itemIngredients`.`quantity` FROM `itemIngredients` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` LEFT OUTER JOIN `metaUnits` ON `metaUnits`.`id` = `itemIngredients`.`unitId` WHERE `itemIngredients`.`itemId`='".$row['id']."' ORDER BY `ingredientTitle` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($innerresult) == 0) {
      $content.= "<div class='warnbox'>Es wurden noch keine Zutaten hinzugefügt.</div>".PHP_EOL;
    } else {
      while($innerrow = mysqli_fetch_array($innerresult)) {
        if($customPersons == TRUE) {
          $quantity = $innerrow['quantity']/$row['persons']*$persons;
        } else {
          $quantity = $innerrow['quantity'];
        }
        $ingredients[] = ($quantity > 0 ? fractionizer($quantity, 2).($innerrow['spacer'] == 1 ? " " : NULL)."<span class='help' title='".output($innerrow['unitTitle'])."'>".output($innerrow['short'])."</span> - " : NULL).output($innerrow['ingredientTitle']);
      }
      $content.= "<ul>".PHP_EOL;
      $content.= "<li>".implode("</li>".PHP_EOL."<li>", $ingredients)."</li>".PHP_EOL;
      $content.= "</ul>".PHP_EOL;
      if($persons > 0) {
        $content.= "<form method='get'>".PHP_EOL;
        $content.= "<div class='row no-print'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 center'>Zutaten auf <input type='number' min='1' max='100' value='".output($persons)."' size='3' name='persons' style='width: auto;'> Person(en) <input type='submit' value='umrechnen' style='width: auto;'></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      }
    }
    $content.= "</div>".PHP_EOL;//der ingredients center div

    /**
     * Bilder
     */
    $count = count($images);
    if($count > 0) {
      /**
       * Wenn wenigstens ein Bild vorhanden => Slideshow
       */
      $slideshow = "<div id='slideshowContainer'>".PHP_EOL;
      foreach($images as $key => $val) {
        $internalId = $key + 1;
        $slideshow.= "<div class='mySlides fade'>".PHP_EOL.
        "<div class='numbertext'>".$internalId." / ".$count."</div>".PHP_EOL.
        "<img src='/img/img-".$row['id']."-".$val['fileHash'].".png' alt='Bild'>".PHP_EOL.
        (!empty($val['description']) ? "<div class='imagetext'>".output($val['description'])."</div>".PHP_EOL : NULL).
        "</div>".PHP_EOL;
      }
      /**
       * Wenn mehr als ein Bild vorhanden ist, muss gescrollt werden können.
       */
      if($count > 1) {
        $slideshow.= "<a id='prev'>&#10094;</a>".PHP_EOL;
        $slideshow.= "<a id='next'>&#10095;</a>".PHP_EOL;
      }
      $slideshow.= "</div>".PHP_EOL;

      $content.= "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6 no-print'>".$slideshow."</div>".PHP_EOL;
    } else {
      /**
       * kein Bild => Standard-Thumbnail
       */
      $content.= "<div class='col-x-12 col-s-12 col-m-12 col-l-6 col-xl-6'><img src='/img/noImg.png' alt='kein Bild vorhanden'></div>".PHP_EOL;
    }
    $content.= "</div>".PHP_EOL;

    $content.= "<div class='spacer-m'></div>".PHP_EOL;

    /**
     * Text
     */
    $content.= "<h2><span class='fas icon'>&#xf03a;</span>Zubereitung</h2>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 preparation'>".Slimdown::render($row['text'])."</div>".PHP_EOL.
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
