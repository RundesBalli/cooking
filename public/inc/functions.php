<?php
/**
 * functions.php
 * 
 * Datei mit Funktionen für den Betrieb.
 */

/**
 * Entschärffunktion
 * 
 * @param  string $defuse_string String der "entschärft" werden soll, um ihn in einen DB-Query zu übergeben.
 * @param  bool   $trim          Gibt an ob Leerzeichen/-zeilen am Anfang und Ende entfernt werden sollen.
 * 
 * @return string Der vorbereitete, "entschärfte" String.
 */
function defuse($defuse_string, $trim = TRUE) {
  if($trim === TRUE) {
    $defuse_string = trim($defuse_string);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuse_string));
}

/**
 * Ausgabefunktion
 * 
 * @param  string $string String, der ausgegeben werden soll.
 * 
 * @return string Der vorbereitete String.
 */
function output($string) {
  return htmlentities($string, ENT_QUOTES);
}

/**
 * Slimdown
 * https://gist.github.com/jbroadway/2836900
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."slimdown.php");

/**
 * Sternfunktion
 * Mit dem Layout von @NLDev
 * @link https://gist.github.com/NLDev/464b08135138f1c1a135053a898b1a79
 * 
 * @param  float  $stars Die Anzahl der Sterne (0-5)
 * 
 * @return string Das fertige Div-Element
 */
function stars(float $stars = 0) {
  $layout = "<div class='rating' title='".$stars." Sterne'>
  <div class='rating-upper' style='width: ".($stars/5*100)."%'>
    <span>★</span>
    <span>★</span>
    <span>★</span>
    <span>★</span>
    <span>★</span>
  </div>
  <div class='rating-lower'>
    <span>★</span>
    <span>★</span>
    <span>★</span>
    <span>★</span>
    <span>★</span>
  </div>
</div>";
  return $layout;
}
?>
