<?php
/**
 * search.php
 * 
 * Rezeptsuche
 */

/**
 * Titel und Überschrift
 */
$title = "Suche";
$content.= "<h1><span class='fas icon'>&#xf002;</span>Suche</h1>".PHP_EOL;

/**
 * Formularauswertung
 */
if(isset($_POST['submit'])) {
  /**
   * Falls keine gültigen Werte übergeben werden, wird nicht gesucht.
   */
  $search = 0;

  /**
   * Such-Array
   */
  $searchFlags = array();

  /**
   * Kosten
   */
  if(!empty($_POST['cost'])) {
    $cost = (int)defuse($_POST['cost']);
    $result = mysqli_query($dbl, "SELECT `id` FROM `metaCost` WHERE `id`='".$cost."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Die Angabe der Kosten ist ungültig.</div>".PHP_EOL;
    } else {
      $search = 1;
      $searchFlags[] = "`cost`='".$cost."'";
    }
  }

  /**
   * Schwierigkeit
   */
  if(!empty($_POST['difficulty'])) {
    $difficulty = (int)defuse($_POST['difficulty']);
    $result = mysqli_query($dbl, "SELECT `id` FROM `metaDifficulty` WHERE `id`='".$difficulty."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Die Angabe der Schwierigkeit ist ungültig.</div>".PHP_EOL;
    } else {
      $search = 1;
      $searchFlags[] = "`difficulty`='".$difficulty."'";
    }
  }

  /**
   * Arbeitszeit
   */
  if(!empty($_POST['workDuration'])) {
    $workDuration = (int)defuse($_POST['workDuration']);
    $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$workDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Die Angabe der Arbeitszeit ist ungültig.</div>".PHP_EOL;
    } else {
      $search = 1;
      $searchFlags[] = "`workDuration`='".$workDuration."'";
    }
  }

  /**
   * Gesamtzeit
   */
  if(!empty($_POST['totalDuration'])) {
    $totalDuration = (int)defuse($_POST['totalDuration']);
    $result = mysqli_query($dbl, "SELECT `id` FROM `metaDuration` WHERE `id`='".$totalDuration."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Die Angabe der Gesamtzeit ist ungültig.</div>".PHP_EOL;
    } elseif(!empty($workDuration) AND $totalDuration < $workDuration) {
      $content.= "<div class='infobox'>Die Angabe der Gesamtzeit darf die Angabe der Arbeitszeit nicht unterschreiten.</div>".PHP_EOL;
    } else {
      $search = 1;
      $searchFlags[] = "`totalDuration`='".$totalDuration."'";
    }
  }

  /**
   * Inhaltssuche
   */
  $join = NULL;
  if(!empty($_POST['ingredient'])) {
    $ingredient = (int)defuse($_POST['ingredient']);
    $result = mysqli_query($dbl, "SELECT `id` FROM `metaIngredients` WHERE `id`='".$ingredient."' AND `searchable`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Die Zutat ist nicht suchbar oder existiert nicht.</div>".PHP_EOL;
    } else {
      $search = 1;
      $join = "JOIN `itemIngredients` ON `items`.`id`=`itemIngredients`.`itemId` AND `itemIngredients`.`ingredientId`='".$ingredient."'";
    }
  }

  /**
   * Query generieren, sofern die Suche gültig war.
   */
  if($search == 1) {
    $searchQuery = "SELECT
    `items`.`id`,
    `items`.`title`,
    `items`.`shortTitle`,
    `metaCost`.`title` AS `cost`,
    `metaDifficulty`.`title` AS `difficulty`,
    `wD`.`title` AS `workDuration`,
    `tD`.`title` AS `totalDuration`,
    (SELECT COUNT(`id`) FROM `clicks` WHERE `clicks`.`itemId` = `items`.`id`) AS `clicks`,
    IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `votes`,
    IFNULL((SELECT COUNT(`votes`.`id`) FROM `votes` WHERE `votes`.`itemId` = `items`.`id`), 0) AS `voteCount`,
    (SELECT `images`.`fileHash` FROM `images` WHERE `images`.`itemId` = `items`.`id` AND `images`.`thumb`=1) AS `fileHash`
    
    FROM `items`
    
    JOIN `metaCost` ON `items`.`cost` = `metaCost`.`id`
    JOIN `metaDifficulty` ON `items`.`difficulty` = `metaDifficulty`.`id`
    JOIN `metaDuration` AS `wD` ON `items`.`workDuration` = `wD`.`id`
    JOIN `metaDuration` AS `tD` ON `items`.`totalDuration` = `tD`.`id`
    ".$join."
    
    ".(!empty($searchFlags) ? "WHERE ".implode(" AND ", $searchFlags) : NULL)."
    
    ORDER BY `clicks` DESC, `items`.`id` ASC
    LIMIT 25";
  }
}

/**
 * Formular
 */
$content.= "<form action='/search' method='post' autocomplete='off'>".PHP_EOL;
/**
 * Tabellenüberschrift
 */
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Feld</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Ergänzungen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Kosten
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kosten</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='cost' tabindex='1'>".PHP_EOL."<option value='' selected>-- egal --</option>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['cost']) && !empty($_POST['cost']) AND $row['id'] == $_POST['cost']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
}
$content.= "</select></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Schwierigkeit
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Schwierigkeit</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='difficulty' tabindex='2'>".PHP_EOL."<option value='' selected>-- egal --</option>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $row['id'] == $_POST['difficulty']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
}
$content.= "</select></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Arbeitszeit
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Arbeitszeit</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='workDuration' tabindex='3'>".PHP_EOL."<option value='' selected>-- egal --</option>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $row['id'] == $_POST['workDuration']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
}
$content.= "</select></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Gesamtzeit
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Gesamtzeit</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='totalDuration' tabindex='4'>".PHP_EOL."<option value='' selected>-- egal --</option>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $row['id'] == $_POST['totalDuration']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
}
$content.= "</select></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Zutat
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Zutat</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><select name='ingredient' tabindex='5'>".PHP_EOL."<option value='' selected>-- egal --</option>".PHP_EOL;
$result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` WHERE `searchable`='1' ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_array($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['ingredient']) && !empty($_POST['ingredient']) AND $row['id'] == $_POST['ingredient']) ? " selected" : NULL).">".output($row['title'])."</option>".PHP_EOL;
}
$content.= "</select></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Diese Zutat muss im Rezept enthalten sein.</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
/**
 * Absenden
 */
$content.= "<div class='row hover bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Suchen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Suchen' tabindex='6'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "</form>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Wenn ein Suchquery erzeugt wurde, dann wird hier das Ergebnis angezeigt.
 */
if(!empty($searchQuery)) {
  $content.= "<h1><span class='fas icon'>&#xf1e5;</span>Suchergebnisse</h1>".PHP_EOL;
  $result = mysqli_query($dbl, $searchQuery) OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    $content.= "<div class='infobox'>Kein Rezept fällt in das eingetragene Suchmuster.</div>".PHP_EOL;
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
          "<div class='title'><a href='/rezept/".output($row['shortTitle'])."'>".$row['title']."</a></div>".PHP_EOL.
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
}
?>
