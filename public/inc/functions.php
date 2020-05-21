<?php
/**
 * functions.php
 * 
 * Datei mit Funktionen für den Betrieb.
 */

/**
 * Entschärffunktion
 * 
 * @param  string $defuseString String der "entschärft" werden soll, um ihn in einen DB-Query zu übergeben.
 * @param  bool   $trim         Gibt an ob Leerzeichen/-zeilen am Anfang und Ende entfernt werden sollen.
 * 
 * @return string Der vorbereitete, "entschärfte" String.
 */
function defuse($defuseString, $trim = TRUE) {
  if($trim === TRUE) {
    $defuseString = trim($defuseString);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuseString));
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
 * @see https://gist.github.com/jbroadway/2836900
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."slimdown.php");

/**
 * Fractionizer
 * @see https://gist.github.com/RundesBalli/a987971322ce7122e223393901fd90ec
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."fractionizer.php");

/**
 * Sternfunktion
 * Mit dem Layout von @NLDev
 * @link https://gist.github.com/NLDev/464b08135138f1c1a135053a898b1a79
 * 
 * @param  float  $stars Die Anzahl der Sterne (0-5)
 * @param  int    $voteCount Die Anzahl an Votes
 * 
 * @return string Das fertige Div-Element
 */
function stars(float $stars = 0, $voteCount = NULL) {
  $layout = "<div class='rating' title='".number_format($stars, 2, ",", ".")." Sterne".($voteCount !== NULL ? " (".number_format($voteCount, 0, ",", ".")." Stimmen)" : NULL)."'>
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

/**
 * Adminlog Funktion
 * Zum loggen aller Administrator Handlungen
 * 
 * @param int    $userId      userId des Administrators oder NULL bei Systemaktion
 * @param int    $logLevel    logLevel der Aktion
 * @param int    $itemId      itemId falls es ein Rezept betrifft, sonst NULL
 * @param int    $categoryId  categoryId falls es eine Kategorie betrifft, sonst NULL
 * @param string $text        optionaler Text
 */
function adminLog($userId = NULL, int $logLevel, $itemId = NULL, $categoryId = NULL, $text = NULL) {
  global $dbl;

  /**
   * Prüfung, ob die userId existiert. Falls nicht wird sie genullt.
   */
  if($userId !== NULL) {
    $userId = (int)defuse($userId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `accounts` WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $userId = NULL;
    }
  }

  /**
   * Prüfung ob das logLevel existiert. Falls nicht, wird es auf "User-/Systemaktion" (1) gesetzt.
   */
  if(is_int($logLevel)) {
    $logLevel = defuse($logLevel);
    $result = mysqli_query($dbl, "SELECT `id` FROM `logLevel` WHERE `id`='".$logLevel."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $logLevel = 1;
    }
  } else {
    $logLevel = 1;
  }

  /**
   * Prüfung ob die itemId existiert. Falls nicht wird sie genullt.
   */
  if($itemId !== NULL) {
    $itemId = (int)defuse($itemId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `items` WHERE `id`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $itemId = NULL;
    }
  }

  /**
   * Prüfung ob die categoryId existiert. Falls nicht wird sie genullt.
   */
  if($categoryId !== NULL) {
    $categoryId = (int)defuse($categoryId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `categories` WHERE `id`='".$categoryId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $categoryId = NULL;
    }
  }

  /**
   * Entschärfen des Textes, sofern vorhanden.
   */
  if($text !== NULL) {
    $text = defuse($text);
  }

  /**
   * Eintragen ins Log
   */
  mysqli_query($dbl, "INSERT INTO `adminLog` (`userId`, `logLevel`, `itemId`, `categoryId`, `text`) VALUES (".($userId !== NULL ? "'".$userId."'" : "NULL").", '".$logLevel."', ".($itemId !== NULL ? "'".$itemId."'" : "NULL").", ".($categoryId !== NULL ? "'".$categoryId."'" : "NULL").", ".($text !== NULL ? "'".$text."'" : "NULL").")") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_affected_rows($dbl) != 0) {
    return true;
  } else {
    return false;
  }
}

/**
 * Userlog Funktion
 * Zum loggen aller User Handlungen
 * 
 * @param int    $userId      userId des Users
 * @param int    $logLevel    logLevel der Aktion
 * @param int    $itemId      itemId falls es ein Rezept betrifft, sonst NULL
 * @param string $text        optionaler Text
 */
function userLog($userId = NULL, int $logLevel, $itemId = NULL, $text = NULL) {
  global $dbl;

  /**
   * Prüfung, ob die userId existiert. Falls nicht wird sie genullt.
   */
  if($userId !== NULL) {
    $userId = (int)defuse($userId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `users` WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $userId = NULL;
    }
  } else {
    $userId = NULL;
  }

  /**
   * Prüfung ob das logLevel existiert. Falls nicht, wird es auf "User-/Systemaktion" (1) gesetzt.
   */
  if(is_int($logLevel)) {
    $logLevel = defuse($logLevel);
    $result = mysqli_query($dbl, "SELECT `id` FROM `logLevel` WHERE `id`='".$logLevel."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $logLevel = 1;
    }
  } else {
    $logLevel = 1;
  }

  /**
   * Prüfung ob die itemId existiert. Falls nicht wird sie genullt.
   */
  if($itemId !== NULL) {
    $itemId = (int)defuse($itemId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `items` WHERE `id`='".$itemId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $itemId = NULL;
    }
  }

  /**
   * Entschärfen des Textes, sofern vorhanden.
   */
  if($text !== NULL) {
    $text = defuse($text);
  }

  /**
   * Eintragen ins Log
   */
  mysqli_query($dbl, "INSERT INTO `userLog` (`userId`, `logLevel`, `itemId`, `text`) VALUES (".($userId !== NULL ? "'".$userId."'" : "NULL").", '".$logLevel."', ".($itemId !== NULL ? "'".$itemId."'" : "NULL").", ".($text !== NULL ? "'".$text."'" : "NULL").")") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_affected_rows($dbl) != 0) {
    return true;
  } else {
    return false;
  }
}
?>
