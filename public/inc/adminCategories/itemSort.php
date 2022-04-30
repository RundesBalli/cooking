<?php
/**
 * adminCategories/itemSort.php
 * 
 * Zugewiesene Rezepte innerhalb der Kategorie sortieren.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Rezepte innerhalb der Kategorie sortieren";
$content.= "<h1><span class='fas icon'>&#xf0dc;</span>Rezepte innerhalb der Kategorie sortieren</h1>";

/**
 * Prüfung ob eine ID übergeben wurde.
 */
if(!empty($_GET['id'])) {
  $id = (int)defuse($_GET['id']);
  /**
   * Prüfen ob die Kategorie existiert.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title` FROM `categories` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Falls die Kategorie nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Die Kategorie mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Kategorie existiert.
     * Prüfung ob schon ein Formular übergeben wurde.
     */
    if(!isset($_POST['submit'])) {
      /**
       * Selektieren der zugewiesenen Rezepte.
       */
      $result = mysqli_query($dbl, "SELECT `categoryItems`.`id`, `categoryItems`.`sortIndex`, `items`.`title`, `items`.`shortTitle` FROM `categoryItems` LEFT JOIN `items` ON `categoryItems`.`itemId`=`items`.`id` WHERE `categoryItems`.`categoryId` = '".$id."' ORDER BY `categoryItems`.`sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
        /**
         * Keine Rezepte in dieser Kategorie
         */
        $content.= "<div class='warnbox'>Dieser Kategorie sind keine Rezepte zugewiesen.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      } elseif(mysqli_num_rows($result) == 1) {
        /**
         * Nur ein Rezept in der Kategorie
         */
        $content.= "<div class='infobox'>Dieser Kategorie ist nur ein Rezept zugewiesen. Eine Sortierung macht keinen Sinn.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
        "</div>";
      } else {
        /**
         * Wenn kein Formular übergeben wurde, dann zeig es an.
         */

        $content.= "<form action='/adminCategories/itemSort?id=".output($id)."' method='post' autocomplete='off'>";
        /**
         * Sitzungstoken
         */

        $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";
        /**
         * Tabellenüberschrift
         */

        $content.= "<div class='row highlight bold bordered'>".
        "<div class='col-s-4 col-l-2'>Sortierindex</div>".
        "<div class='col-s-8 col-l-10'>Rezept</div>".
        "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
        "</div>";

        /**
         * Durchgehen der einzelnen Zuweisungen.
         */
        $tabindex = 0;
        while($row = mysqli_fetch_array($result)) {
          $tabindex++;
          $content.= "<div class='row hover bordered'>".
          "<div class='col-s-4 col-l-2'><input type='number' name='ci[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'".($tabindex == 1 ? " autofocus" : NULL)."></div>".
          "<div class='col-s-8 col-l-10'><a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
          "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
          "</div>";
        }
        $tabindex++;
        $content.= "<div class='row hover bordered'>".
        "<div class='col-s-4 col-l-2'><input type='submit' name='submit' value='ändern' tabindex='".$tabindex."'></div>".
        "</div>";
        $content.= "</form>";
      }
    } else {
      /**
       * Formularauswertung
       */
      if($_POST['token'] == $sessionHash) {
        if(isset($_POST['ci']) AND is_array($_POST['ci'])) {
          /**
           * Array sortieren nach Wert
           */
          asort($_POST['ci']);
          $index = 0;
          $query = "UPDATE `categoryItems` SET `sortIndex` = CASE ";
          foreach($_POST['ci'] as $key => $val) {
            $key = (int)defuse($key);
            $index+= 10;
            $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
          }
          $query.= "ELSE '9999999' END WHERE `categoryId`='".$id."'";
          mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
          mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `categoryId`, `text`) VALUES ('".$userId."', 5, ".$id.", 'Rezepte in Kategorie neu sortiert')") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Sortierung geändert.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
          "</div>";
        } else {
          $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>";
          $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/adminCategories/itemSort?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
          "</div>";
        }
      } else {
        /**
         * ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/adminCategories/itemSort?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
        "</div>";
      }
    }
  }
} else {
  /**
   * Es wurde keine ID übergeben.
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
