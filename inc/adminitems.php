<?php
/**
 * adminitems.php
 * 
 * Seite um Rezepte anzuzeigen, anzulegen, zu bearbeiten, zu löschen und zuzuweisen.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

if(!isset($_GET['action'])) {
  /**
   * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezepte.
   */
  header("Location: /adminitems/list");
  die();
} elseif($_GET['action'] == 'list') {
  /**
   * Auflisten aller Rezepte.
   */
  $title = "Rezepte anzeigen";
  $content.= "<h1>Rezepte anzeigen</h1>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminitems/add'>Anlegen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-m'></div>".PHP_EOL;
  $result = mysqli_query($dbl, "SELECT `items`.*, IFNULL((SELECT round(avg(`votes`.`stars`),2) FROM `votes` WHERE `votes`.`itemid`=`items`.`id` GROUP BY `votes`.`itemid`), 0) AS `stars` FROM `items` ORDER BY `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn keine Rezepte existieren.
     */
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Noch keine Rezepte angelegt.</div>".PHP_EOL.
    "</div>".PHP_EOL;
  } else {
    /**
     * Anzeige vorhandener Rezepte.
     */
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-5 col-l-6 col-xl-6'>Titel</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-3 col-l-2 col-xl-2'>Sterne</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Kategorien</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    while($row = mysqli_fetch_array($result)) {
      $innerresult = mysqli_query($dbl, "SELECT `categories`.`title`, `categories`.`shortTitle` FROM `category_items` LEFT JOIN `categories` ON `category_items`.`category_id`=`categories`.`id` WHERE `category_items`.`item_id`='".$row['id']."'") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($innerresult) == 0) {
        $categories = "keine";
      } else {
        $categories = array();
        while($innerrow = mysqli_fetch_array($innerresult)) {
          $categories[] = "<a href='/kategorie/".output($innerrow['shortTitle'])."' target='_blank'>".output($innerrow['title'])."</a>";
        }
        $categories = implode("<br>", $categories);
      }
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-5 col-l-6 col-xl-6'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-3 col-l-2 col-xl-2'>".stars($row['stars'])."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$categories."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminitems/edit/".$row['id']."' class='nowrap'>Editieren</a><br>".PHP_EOL."<a href='/adminitems/del/".$row['id']."' class='nowrap'>Löschen</a><br>".PHP_EOL."<a href='/adminitems/assign/".$row['id']."' class='nowrap'>Kategorien</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Hinzufügen eines Rezepts.
   */
  $title = "Rezept hinzufügen";
  $content.= "<h1>Rezept hinzufügen</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'del') {
  /**
   * Löschen eines Rezepts.
   */
  $title = "Rezept löschen";
  $content.= "<h1>Rezept löschen</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'edit') {
  /**
   * Bearbeiten eines Rezepts.
   */
  $title = "Rezept bearbeiten";
  $content.= "<h1>Rezept bearbeiten</h1>".PHP_EOL;
  
} elseif($_GET['action'] == 'assign') {
  /**
   * Zuweisen eines Rezepts in eine Kategorie.
   */
  $title = "Rezept einer Kategorie hinzufügen";
  $content.= "<h1>Rezept einer Kategorie hinzufügen</h1>".PHP_EOL;
  
} else {
  /**
   * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
   */
  header("Location: /adminitems/list");
  die();
}
?>
