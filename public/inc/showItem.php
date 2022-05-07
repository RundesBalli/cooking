<?php
/**
 * showItem.php
 * 
 * Anzeige eines Rezepts.
 */

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder, die Rezept-Darstellung und für die Druckausgabe
 */
$additionalStyles[] = "input";
$additionalStyles[] = "item";
$additionalStyles[] = "print";

/**
 * Laden der zusätzlichen JS Dateien zum Teilen des Rezeptes und für die Slideshow
 */
$additionalScripts[] = "shareLink";
$additionalScripts[] = "slideshow";

/**
 * Prüfen ob das übergebene Rezept leer ist.
 */
if(!isset($_GET['item']) OR empty(trim($_GET['item']))) {
  http_response_code(404);
  $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>";
  $content.= "<div class='infobox'>Du musst ein Rezept angeben.</div>";
} else {
  /**
   * Übergebene Kategorie für den Query vorbereiten.
   */
  $item = defuse($_GET['item']);

  /**
   * Rezept abfragen
   */
  $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `items`.`text`, `items`.`persons`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `wD`.`title` AS `workDuration`, `tD`.`title` AS `totalDuration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `items`.`id`) AS `clicks` FROM `items` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id` JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id` WHERE `shortTitle`='".$item."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    /**
     * Klick zählen oder aktualisieren
     */
    mysqli_query($dbl, "UPDATE `clicks` SET `timestamp`=CURRENT_TIMESTAMP WHERE `uuid`='".$UUID."' AND `timestamp` > DATE_SUB(NOW(), INTERVAL 30 HOUR) LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_affected_rows($dbl) != 1) {
      mysqli_query($dbl, "INSERT INTO `clicks` (`itemId`, `uuid`) VALUES ('".$row['id']."', '".$UUID."')") OR DIE(MYSQLI_ERROR($dbl));
    }


    /**
     * OG-Metadaten
     */
    $thumbResult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$row['id']."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbResult) == 1) {
      $thumbRow = mysqli_fetch_assoc($thumbResult);
      $thumb = 'https://'.$_SERVER['HTTP_HOST']."/img/thumb-".$thumbRow['itemId']."-".$thumbRow['fileHash'].".png";
    } else {
      $thumb = 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/favicon.png';
    }
    /**
     * Verändern der standardmäßig konfigurierten OG-Metadaten
     */
    $ogMeta = array(
      'title'            => $ogConfig['name'].' | Rezept: '.output($row['title']),
      'image'            => $thumb,
      'image:secure_url' => $thumb
    );


    /**
     * Adminschnellnavigation
     */
    if((isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
      $content.= "<div class='row noPrint'>".
      "<div class='col-s-12 col-l-12 alignCenter'><span class='bold warn'>Admin-Schnellzugriff:</span> <a href='/adminItems/edit?id=".$row['id']."'><span class='fas icon'>&#xf044;</span>Editieren</a> - <a href='/adminCategoryItemAssignments/show?itemId=".$row['id']."'><span class='far icon'>&#xf07c;</span>Kategorien</a> - <a href='/adminFiles/show?id=".$row['id']."'><span class='fas icon'>&#xf302;</span>Bilder</a> - <a href='/adminItemIngredientAssignments/show?id=".$row['id']."'><span class='fas icon'>&#xf4d8;</span>Zutaten</a> - <a href='/adminFeaturedItems/add?id=".$row['id']."'><span class='fas icon'>&#xf005;</span>Featuren</a></div>".
      "</div>";
    }


    /**
     * Titel anzeigen
     */
    $title = output($row['title']);
    $content.= "<h1 class='alignCenter'><span class='fas icon'>&#xf543;</span>Rezept: ".output($row['title'])."</h1>";


    /**
     * ShareButtons
     */
    $shareText = urlencode("Ich habe ein leckeres Rezept für ".$row['title']." gefunden!\n"."Schau mal hier: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $content.= "<div id='shareButtons' class='noPrint'><a href='tg://msg?text=".$shareText."' target='_blank' rel='noopener'><span class='fab icon'>&#xf3fe;</span></a><a href='whatsapp://send?text=".$shareText."' target='_blank' rel='noopener'><span class='fab icon'>&#xf232;</span></a><a href=\"#\" class=\"copy-link-btn\"><span class='far icon'>&#xf0c5;</span></a></div>";


    /**
     * Bilder selektieren und Slideshow vorbereiten
     */
    $imageResult = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$row['id']."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($imageResult) > 0) {
      $images = array();
      while($imageRow = mysqli_fetch_assoc($imageResult)) {
        $images[] = array('fileHash' => $imageRow['fileHash'], 'description' => $imageRow['description']);
      }
      $count = count($images);
      if($count > 0) {
        /**
         * Wenn wenigstens ein Bild vorhanden => Slideshow
         */
        $slideshow = "<div id='slideshowContainer'>";
        foreach($images as $key => $val) {
          $slideshow.= "<div class='mySlides fade'>".
          "<div class='numberText'>".($key + 1)." / ".$count."</div>".
          "<img src='/img/img-".$row['id']."-".$val['fileHash'].".png' alt='Bild'>".
          (!empty($val['description']) ? "<div class='imageText'>".output($val['description'])."</div>" : NULL).
          "</div>";
        }
        /**
         * Wenn mehr als ein Bild vorhanden ist, muss gescrollt werden können.
         */
        if($count > 1) {
          $slideshow.= "<a id='prev'>&#10094;</a>";
          $slideshow.= "<a id='next'>&#10095;</a>";
        }
        $slideshow.= "</div>";
      }
    } else {
      /**
       * kein Bild => Standard-Thumbnail
       */
      $slideshow.= "<img src='/assets/images/noThumb.png' alt='kein Bild vorhanden'>";
    }


    /**
     * Eckdaten vorbereiten
     */
    $data = "".
    $data.= "<div class='row'>".
      "<div class='col-s-6 col-l-6 alignRight'><span class='far icon'>&#xf25a;</span>Klick".($row['clicks'] > 1 ? "s" : NULL)."</div>".
      "<div class='col-s-6 col-l-6'>".number_format($row['clicks'], 0, ",", ".")."</div>".
    "</div>";
    $data.= "<div class='row'>".
      "<div class='col-s-6 col-l-6 alignRight'><span class='far icon'>&#xf0eb;</span>Schwierigkeit</div>".
      "<div class='col-s-6 col-l-6'>".$row['difficulty']."</div>".
    "</div>";
    $data.= "<div class='row'>".
      "<div class='col-s-6 col-l-6 alignRight'><span class='fas icon'>&#xf252;</span>Arbeitszeit</div>".
      "<div class='col-s-6 col-l-6'>".$row['workDuration']."</div>".
    "</div>";
    $data.= "<div class='row'>".
      "<div class='col-s-6 col-l-6 alignRight'><span class='fas icon'>&#xf253;</span>Gesamtzeit</div>".
      "<div class='col-s-6 col-l-6'>".$row['totalDuration']."</div>".
    "</div>";
    $data.= "<div class='row'>".
      "<div class='col-s-6 col-l-6 alignRight'><span class='fas icon'>&#xf153;</span>Kosten</div>".
      "<div class='col-s-6 col-l-6'>".$row['cost']."</div>".
    "</div>";


    /**
     * Zutatenliste vorbereiten und ggf. Umrechnen
     */
    $ingredients = "";
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
    $innerResult = mysqli_query($dbl, "SELECT `metaIngredients`.`title` AS `ingredientTitle`, `metaUnits`.`title` AS `unitTitle`, `metaUnits`.`short`, `metaUnits`.`spacer`, `itemIngredients`.`quantity` FROM `itemIngredients` JOIN `metaIngredients` ON `metaIngredients`.`id` = `itemIngredients`.`ingredientId` LEFT OUTER JOIN `metaUnits` ON `metaUnits`.`id` = `itemIngredients`.`unitId` WHERE `itemIngredients`.`itemId`='".$row['id']."' ORDER BY `ingredientTitle` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($innerResult) == 0) {
      $ingredients.= "<div class='warnbox'>Es wurden noch keine Zutaten hinzugefügt.</div>";
    } else {
      while($innerRow = mysqli_fetch_assoc($innerResult)) {
        if($customPersons == TRUE) {
          $quantity = $innerRow['quantity']/$row['persons']*$persons;
        } else {
          $quantity = $innerRow['quantity'];
        }
        $ingredients.= "<div class='row'>".
          "<div class='col-s-6 col-l-6 alignRight'>".($quantity > 0 ? fractionizer($quantity, 2).($innerRow['spacer'] == 1 ? " " : NULL) : NULL).output($innerRow['unitTitle'])."</span></div>".
          "<div class='col-s-6 col-l-6'>".output($innerRow['ingredientTitle'])."</div>".
        "</div>";
      }
      if($persons > 0) {
        $ingredients.= "<div class='spacer-s'></div>";
        $ingredients.= "<form method='get'>";
        $ingredients.= "<div class='row noPrint'>".
        "<div class='col-s-12 col-l-12 alignCenter'>Zutaten auf <input type='number' min='1' max='100' value='".output($persons)."' size='3' name='persons' style='width: auto;'> Person(en) <input type='submit' value='umrechnen' style='width: auto;'></div>".
        "</div>";
        $ingredients.= "</form>";
      }
    }


    /**
     * Anzeige der aufbereiteten Daten
     */
    $content.= "<div class='row alignCenter'>".
      "<div class='col-s-12 col-l-12'>".$slideshow."</div>".
    "</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-6 printFullWidth'><h2 class='alignCenter'><span class='fas icon'>&#xf0ce;</span>Eckdaten</h2>".$data."</div>".
      "<div class='col-s-12 col-l-6 printFullWidth'><h2 class='alignCenter'><span class='fas icon'>&#xf4d8;</span>Zutaten".($persons > 0 ? " für ".output($persons)." Person".($persons > 1 ? "en" : NULL) : NULL)."</h2>".$ingredients."</div>".
    "</div>";

    /**
     * Text
     */
    $content.= "<div class='row alignCenter'>".
    "<div class='col-s-0 col-l-2'></div>".
    "<div class='col-s-12 col-l-8 printFullWidth'><h2><span class='fas icon'>&#xf03a;</span>Zubereitung</h2>".Slimdown::render($row['text'])."</div>".
    "<div class='col-s-0 col-l-2'></div>".
    "</div>";
    $content.= "<div class='spacer-l'></div>";
  } else {
    /**
     * Fehlermeldung, wenn das Rezept nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>";
    $content.= "<div class='infobox'>Das Rezept <span class='italic'>".output($item)."</span> existiert nicht.</div>";
  }
}
?>
