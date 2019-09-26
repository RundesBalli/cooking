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
 * @return string Der vorbereitete, "entschärfte" String
 */
function defuse($defuse_string, $trim = TRUE) {
  if($trim === TRUE) {
    $defuse_string = trim($defuse_string);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, $defuse_string);
}

/**
 * Slimdown
 * https://gist.github.com/jbroadway/2836900
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."Slimdown.php");

/**
 * Sternfunktion
 * Mit dem Layout von @NLDev
 * @link https://github.com/NLDev
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
