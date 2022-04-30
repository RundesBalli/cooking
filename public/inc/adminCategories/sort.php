<?php
/**
 * adminCategories/sort.php
 * 
 * Sortieren der Kategorien.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Kategorien sortieren";
$content.= "<h1><span class='fas icon'>&#xf0dc;</span>Kategorien sortieren</h1>";

if(!isset($_POST['submit'])) {
  /**
   * Wenn das Formular noch nicht übergeben wurde, dann zeig es an.
   */
  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle`, `sortIndex` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Wenn noch keine Kategorien angelegt wurden.
     */
    $content.= "<div class='infobox'>Noch keine Kategorien angelegt.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } elseif(mysqli_num_rows($result) == 1) {
    /**
     * Wenn erst eine Kategorie angelegt wurde.
     */
    $content.= "<div class='infobox'>Es wurde erst eine Kategorie angelegt. Ein Sortieren hätte keine Auswirkungen.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Formularanzeige
     */
    $content.= "<form action='/adminCategories/sort' method='post' autocomplete='off'>";
    /**
     * Sitzungstoken
     */
    $content.= "<input type='hidden' name='token' value='".output($sessionHash)."'>";
    /**
     * Tabellenüberschrift
     */
    $content.= "<div class='row highlight bold bordered'>".
    "<div class='col-s-4 col-l-2'>Sortierindex</div>".
    "<div class='col-s-8 col-l-10'>Kategorie</div>".
    "<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
    "</div>";
    /**
     * Durchgehen der einzelnen Kategorien.
     */
    $tabindex = 0;
    while($row = mysqli_fetch_assoc($result)) {
      $tabindex++;
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-4 col-l-2'><input type='number' name='cat[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'></div>".
      "<div class='col-s-8 col-l-10'><a href='/kategorie/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
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
    if(isset($_POST['cat']) AND is_array($_POST['cat'])) {
      /**
       * Array sortieren nach Wert
       */
      asort($_POST['cat']);
      $index = 0;
      $query = "UPDATE `categories` SET `sortIndex` = CASE ";
      foreach($_POST['cat'] as $key => $val) {
        $key = (int)defuse($key);
        $index+= 10;
        $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
      }
      $query.= "ELSE '9999999' END";
      mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$userId."', 5, 'Kategorien neu sortiert')") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='successbox'>Sortierung geändert.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminCategories/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminCategories/sort'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
      "</div>";
    }
  } else {
    /**
     * Ungültiges Sitzungstoken
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminCategories/sort'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".
    "</div>";
  }
}
?>
