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
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/admincategories/add'>Anlegen</a></div>".PHP_EOL.
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
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/admincategories/edit/".$row['id']."' class='nowrap'>Bearbeiten</a><br>".PHP_EOL."<a href='/admincategories/del/".$row['id']."' class='nowrap'>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  }
} elseif($_GET['action'] == 'add') {
  /**
   * Kategorie anlegen.
   */
  $title = "Kategorie anlegen";
  $content.= "<h1>Kategorie anlegen</h1>".PHP_EOL;
  /**
   * Falls das Formular übergeben wurde, gehen wir davon aus, dass alles okay ist, demzufolge muss das Formular nicht mehr angezeigt werden.
   * Im Fehlerfall wird das Formular nochmals angezeigt.
   */
  $form = 0;
  if(isset($_POST['submit'])) {
    /**
     * AUSWERTUNG, und falls alles ok dann FORM auf 1 lassen, sonst 0
     */
  } else {
    /**
     * Erstaufruf = Formular wird angezeigt.
     */
    $form = 1;
  }
  if($form == 1) {
    $content.= "<form action='/admincategories/add' method='post'>".PHP_EOL;
    $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Feld</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Ergänzungen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Name der Kategorie</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='title' placeholder='Name der Kategorie' value='".(isset($_POST['title']) ? $_POST['title'] : NULL)."'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Angezeigter Name in der Navigation</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurztitel für URL</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortTitle' placeholder='/kategorie/xxx'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("`/kategorie/foo-bar`\n* muss einzigartig sein\n* max. 64 Zeichen\n* keine Leerzeichen\n* keine Umlaute\n* keine Sonderzeichen\n* nur Kleinbuchstaben oder Zahlen\n* zur Worttrennung `-` oder `_` benutzen\n`0-9a-z-_`")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Sortierindex</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='number' name='sortIndex' placeholder='z.B. 10' min='0'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* aufsteigend sortiert, 1 = oben, 100 = unten\n* Möglich sind alle positiven Zahlen und 0")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Beschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><textarea name='description' placeholder='Mehrzeilige Beschreibung'></textarea></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* [Markdown für mehrzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird beim Aufruf der Kategorie angezeigt")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kurzeschreibung</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='text' name='shortDescription' placeholder='Kurzbeschreibung'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* [Markdown für einzeilige Textfelder](/adminmarkdowninfo)* möglich\n* wird auf der Startseite angezeigt")."</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='row hover bordered'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Kategorie anlegen</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Anlegen'></div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'><span class='highlight'>Info:</span> Die Kategorie wird sofort angezeigt.</div>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "</form>".PHP_EOL;
  }
} elseif($_GET['action'] == 'del') {
  $title = "Kategorie löschen";
  $content.= "<h1>Kategorie löschen</h1>".PHP_EOL;
} else {
  header("Location: /admincategories/list");
  die();
}

?>
