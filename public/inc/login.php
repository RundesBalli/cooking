<?php
/**
 * login.php
 * 
 * Login
 */

/**
 * Titel
 */
$title = "Einloggen";
$content.= "<h1><span class='fas icon'>&#xf2f6;</span>Einloggen</h1>".PHP_EOL;

/**
 * Prüfung ob ein Cookie gesetzt ist und ob es leer ist.
 * Wenn ein Cookie gesetzt ist, dann wird auf die Übersichtsseite weitergeleitet,
 * falls nicht wird der Login über pr0gramm angeboten.
 */
if(!isset($_COOKIE['cooking']) OR empty($_COOKIE['cooking'])) {
  if(!empty($_GET['e'])) {
    if($_GET['e'] == "mismatchingIds") {
      $errmsg = "Die gespeicherten Daten stimmen nicht mit den pr0gramm-Daten überein. Bitte neu einloggen.";
    } elseif($_GET['e'] == "banned") {
      $errmsg = "Du bist auf pr0gramm gesperrt und kannst daher den Nutzerbereich auch nicht benutzen.";
    } elseif($_GET['e'] == "invalidCookie") {
      $errmsg = "Dein Cookie ist ungültig. Bitte neu einloggen.";
    } elseif($_GET['e'] == "oAuth") {
      $errmsg = "Die oAuth Sitzung ist ungültig. Bitte neu einloggen.";
    } else {
      $errmsg = "Ein unbekannter Fehler ist aufgetreten. Bitte neu einloggen.";
    }
    $content.= "<div class='warnbox'>".$errmsg."</div>".PHP_EOL;
  }
  $content.= "<div class='infobox'>Auf dieser Seite werden Cookies verwendet! Mit dem Fortfahren stimmst du dem zu!</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'><a href='/loginRedirect'>mit pr0gramm anmelden</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-l'></div>".PHP_EOL;
} else {
  header("Location: /overview");
  die();
}
?>
