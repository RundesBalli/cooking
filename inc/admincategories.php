<?php
/**
 * admincategories.php
 * 
 * Seite um Kategorien anzulegen, zu bearbeiten, zu löschen, und um ihnen Rezepte zuzuweisen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Kategorien.
   */
  header("Location: /admincategories/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflistung aller Kategorien mit Anzahl der darin befindlichen Rezepte.
   */
  $title = "Kategorien anzeigen";
  $content.= "<h1>Kategorien anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories'>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  /**
   * Danke an @Insax für den Query.
   * 
   * Alle Kategorien anzeigen. Beim ersten Subquery werden alle Kategorien ohne Rezepte einbezogen, beim zweiten alle mit Rezepten.
   */
  $query = "
  SELECT `id`, `title`, `shortTitle`, `itemcount` FROM (
    SELECT `sortindex`, `categories`.`id`, `categories`.`title`, `categories`.`shortTitle`, 0 AS `itemcount` FROM `categories` WHERE EXISTS(SELECT * FROM category_items WHERE categories.id != category_items.category_id)
      UNION
    SELECT `categories`.`sortindex`, `categories`.`id`, `categories`.`title`, `categories`.`shortTitle`, COUNT(`category_items`.`id`) AS `itemcount` FROM `categories`
      LEFT JOIN `category_items` ON `category_items`.`category_id`=`categories`.`id`
  ) AS results
  ORDER BY `sortindex` ASC, `title` ASC;";
  $result = mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Kategorien existieren.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Noch keine Kategorien angelegt.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Kategorien.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Titel</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Anzahl zugewiesener Rezepte</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>".$row['title']."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$row['itemcount']." Rezept".($row['itemcount'] == 1 ? "" : "e")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/admincategories/edit/".$row['id']."' class='nowrap'>Bearbeiten</a><br><a href='/admincategories/assign/".$row['id']."' class='nowrap'>Rezepte zuweisen</a><br><a href='/admincategories/del/".$row['id']."' class='nowrap'>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  $title = "Kategorie anlegen";
} elseif($_GET['action'] == 'del') {
  $title = "Kategorie löschen";
} elseif($_GET['action'] == 'assign') {
  $title = "Rezepte zuweisen";
} ## elseif...

?>
