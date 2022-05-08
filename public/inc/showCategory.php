<?php
/**
 * showCategory.php
 * 
 * Anzeige aller Einträge innerhalb einer gewählten Kategorie.
 */

/**
 * Laden der zusätzlichen CSS Datei für die Darstellung
 */
$additionalStyles[] = "category";

/**
 * Prüfen ob die übergebene Kategorie leer ist.
 */
if(!isset($_GET['category']) OR empty(trim($_GET['category']))) {
  http_response_code(404);
  $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>";
  $content.= "<div class='infobox'>Du musst eine Kategorie angeben.</div>";
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
    $row = mysqli_fetch_assoc($result);


    /**
     * OG-Metadaten
     */
    $thumbresult = mysqli_query($dbl, "SELECT `categoryItems`.`itemId`, `images`.`fileHash` FROM `categoryItems` JOIN `images` ON `categoryItems`.`itemId`=`images`.`itemId` AND `thumb`='1' WHERE `categoryId` = '".$row['id']."' ORDER BY RAND() LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($thumbresult) == 1) {
      $thumbrow = mysqli_fetch_assoc($thumbresult);
      $thumb = 'https://'.$_SERVER['HTTP_HOST']."/img/thumb-".$thumbrow['itemId']."-".$thumbrow['fileHash'].".png";
    } else {
      $thumb = 'https://'.$_SERVER['HTTP_HOST'].'/assets/images/favicon.png';
    }
    /**
     * Verändern der standardmäßig konfigurierten OG-Metadaten
     */
    $ogMeta['title'] = $ogConfig['name'].' | Kategorie: '.output($row['title']);
    $ogMeta['image'] = $thumb;
    $ogMeta['image:secure_url'] = $thumb;


    /**
     * Adminschnellnavigation
     */
    if((isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12 alignCenter'><span class='bold highlight'>Admin-Schnellzugriff:</span> <a href='/adminCategories/edit?id=".$row['id']."'><span class='fas icon'>&#xf044;</span>Editieren</a> - <a href='/adminCategories/itemSort?id=".$row['id']."'><span class='fas icon'>&#xf0dc;</span>Rezepte in Kategorie sortieren</a></div>".
      "</div>";
    }


    $title = "Kategorie: ".$row['title'];
    $content.= "<h1><span class='far icon'>&#xf07c;</span>Kategorie: ".output($row['title'])."</h1>";
    $content.= "<div class='spacer-m'></div>";


    /**
     * Inhalte der Kategorie anzeigen.
     */
    $result = mysqli_query($dbl, "SELECT `items`.`id`, `items`.`title`, `items`.`shortTitle`, `metaCost`.`title` AS `cost`, `metaDifficulty`.`title` AS `difficulty`, `wD`.`title` AS `workDuration`, `tD`.`title` AS `totalDuration`, (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `categoryItems`.`itemId`) AS `clicks`, (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `categoryItems`.`itemId` AND `images`.`thumb`=1) AS `fileHash` FROM `categoryItems` JOIN `items` ON `categoryItems`.`itemId` = `items`.`id` JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id` JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id` JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id` JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id` WHERE `categoryId`='".$row['id']."' ORDER BY `categoryItems`.`sortIndex` ASC, `items`.`title` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Dieser Kategorie wurden noch keine Rezepte zugewiesen</div>";
    } else {
      $items = array();
      while($row = mysqli_fetch_assoc($result)) {
        $items[] =
        "<div class='row item'>".
          "<div class='col-s-12 col-l-2'>".
            "<a href='/rezept/".output($row['shortTitle'])."'>".
              "<img src='/".($row['fileHash'] === NULL ? "assets/images/noThumb.png" : "img/thumb-".$row['id']."-".$row['fileHash'].".png")."'>".
            "</a>".
          "</div>".
          "<div class='col-s-12 col-l-10 itemInfo'>".
            "<div class='title'><a href='/rezept/".output($row['shortTitle'])."'>".output($row['title'])."</a></div>".
            "<div class='specs cursorHelp' title='Aufrufe'><span class='far icon'>&#xf25a;</span> ".number_format($row['clicks'], 0, ",", ".")."</div>".
            "<div class='specs cursorHelp' title='Schwierigkeitsgrad'><span class='far icon'>&#xf0eb;</span> ".$row['difficulty']."</div>".
            "<div class='specs cursorHelp' title='Kosten'><span class='fas icon'>&#xf153;</span> ".$row['cost']."</div>".
            "<br>".
            "<div class='specs cursorHelp' title='Arbeitszeit'><span class='fas icon'>&#xf252;</span> ".$row['workDuration']."</div>".
            "<div class='specs cursorHelp' title='Gesamtzeit'><span class='fas icon'>&#xf253;</span> ".$row['totalDuration']."</div>".
          "</div>".
        "</div>";
      }
      $content.= implode("<hr class='itemHr'>", $items);
    }
  } else {
    /**
     * Fehlermeldung, wenn die Kategorie nicht existiert.
     */
    http_response_code(404);
    $content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>";
    $content.= "<div class='infobox'>Die Kategorie <span class='italic'>".output($category)."</span> existiert nicht.</div>";
  }
}
?>
