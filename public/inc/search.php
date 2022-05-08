<?php
/**
 * search.php
 * 
 * Rezeptsuche
 */

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

/**
 * Laden der zusätzlichen CSS Datei für die Darstellung
 */
$additionalStyles[] = "category";

/**
 * Titel und Überschrift
 */
$title = "Suche";
$content.= "<h1><span class='fas icon'>&#xf002;</span>Suche</h1>";

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
      $content.= "<div class='infobox'>Die Angabe der Kosten ist ungültig.</div>";
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
      $content.= "<div class='infobox'>Die Angabe der Schwierigkeit ist ungültig.</div>";
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
      $content.= "<div class='infobox'>Die Angabe der Arbeitszeit ist ungültig.</div>";
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
      $content.= "<div class='infobox'>Die Angabe der Gesamtzeit ist ungültig.</div>";
    } elseif(!empty($workDuration) AND $totalDuration < $workDuration) {
      $content.= "<div class='infobox'>Die Angabe der Gesamtzeit darf die Angabe der Arbeitszeit nicht unterschreiten.</div>";
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
      $content.= "<div class='infobox'>Die Zutat ist nicht suchbar oder existiert nicht.</div>";
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
$content.= "<form action='/search' method='post' autocomplete='off'>";

/**
 * Kosten
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Kosten</div>".
"<div class='col-s-12 col-l-4'><select name='cost' tabindex='1'>"."<option value='' selected>-- egal --</option>";
$result = mysqli_query($dbl, "SELECT * FROM `metaCost` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['cost']) && !empty($_POST['cost']) AND $row['id'] == $_POST['cost']) ? " selected" : NULL).">".output($row['title'])."</option>";
}
$content.= "</select></div>".
"<div class='col-s-12 col-l-5'></div>".
"</div>";

/**
 * Schwierigkeit
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Schwierigkeit</div>".
"<div class='col-s-12 col-l-4'><select name='difficulty' tabindex='2'>"."<option value='' selected>-- egal --</option>";
$result = mysqli_query($dbl, "SELECT * FROM `metaDifficulty` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['difficulty']) && !empty($_POST['difficulty']) AND $row['id'] == $_POST['difficulty']) ? " selected" : NULL).">".output($row['title'])."</option>";
}
$content.= "</select></div>".
"<div class='col-s-12 col-l-5'></div>".
"</div>";

/**
 * Arbeitszeit
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Arbeitszeit</div>".
"<div class='col-s-12 col-l-4'><select name='workDuration' tabindex='3'>"."<option value='' selected>-- egal --</option>";
$result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['workDuration']) && !empty($_POST['workDuration']) AND $row['id'] == $_POST['workDuration']) ? " selected" : NULL).">".output($row['title'])."</option>";
}
$content.= "</select></div>".
"<div class='col-s-12 col-l-5'></div>".
"</div>";

/**
 * Gesamtzeit
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Gesamtzeit</div>".
"<div class='col-s-12 col-l-4'><select name='totalDuration' tabindex='4'>"."<option value='' selected>-- egal --</option>";
$result = mysqli_query($dbl, "SELECT * FROM `metaDuration` ORDER BY `id` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['totalDuration']) && !empty($_POST['totalDuration']) AND $row['id'] == $_POST['totalDuration']) ? " selected" : NULL).">".output($row['title'])."</option>";
}
$content.= "</select></div>".
"<div class='col-s-12 col-l-5'>Hiermit ist die Gesamtzeit des Kochvorgangs gemeint (incl. das Warten auf den Backofen, etc.).</div>".
"</div>";

/**
 * Zutat
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Zutat</div>".
"<div class='col-s-12 col-l-4'><select name='ingredient' tabindex='5'>"."<option value='' selected>-- egal --</option>";
$result = mysqli_query($dbl, "SELECT * FROM `metaIngredients` WHERE `searchable`='1' ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $content.= "<option value='".$row['id']."'".((isset($_POST['ingredient']) && !empty($_POST['ingredient']) AND $row['id'] == $_POST['ingredient']) ? " selected" : NULL).">".output($row['title'])."</option>";
}
$content.= "</select></div>".
"<div class='col-s-12 col-l-5'>Diese Zutat muss im Rezept enthalten sein.</div>".
"</div>";

/**
 * Absenden
 */
$content.= "<div class='row hover bordered'>".
"<div class='col-s-12 col-l-3'>Suchen</div>".
"<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Suchen' tabindex='6'></div>".
"<div class='col-s-12 col-l-5'></div>".
"</div>";
$content.= "</form>";
$content.= "<div class='spacer-m'></div>";

/**
 * Wenn ein Suchquery erzeugt wurde, dann wird hier das Ergebnis angezeigt.
 */
if(!empty($searchQuery)) {
  $content.= "<h2><span class='fas icon'>&#xf1e5;</span>Suchergebnisse</h2>";
  $result = mysqli_query($dbl, $searchQuery) OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    $content.= "<div class='infobox'>Kein Rezept fällt in das eingetragene Suchmuster.</div>";
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
    $content.= implode("<hr class='itemhr'>", $items);
  }
  $content.= "<div class='spacer-m'></div>";
}
?>
