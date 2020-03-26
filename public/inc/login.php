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
