<?php
/**
 * passwd.php
 * 
 * Datei zum Ändern eines Administratorpassworts.
 * 
 * @param string $argv[1] Benutzername
 * @param string $argv[2] Passwort
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Prüfen ob das Script in der Konsole läuft.
 */
if(php_sapi_name() != 'cli') {
  die("Das Script kann nur per Konsole ausgeführt werden.\n\n");
}

/**
 * Abfragen ob diese Aktion wirklich durchgeführt werden soll.
 */
echo "Bist du dir sicher, dass du dieses Skript ausführen willst?\nAlle Tabelleninhalte der folgenden Kategorien werden geleert:\n- items\n- categories\n- votes\n- clicks\n- categoryItems\n\nWenn du dir sicher bist schreib \"ja\"\n";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'ja'){
  die("Ok, beende.\n");
}

/**
 * Leeren der Tabellen.
 */
mysqli_query($dbl, "SET FOREIGN_KEY_CHECKS=0") OR DIE(MYSQLI_ERROR($dbl));
echo "Leere Querverweise\n";
mysqli_query($dbl, "TRUNCATE TABLE `categoryItems`") OR DIE(MYSQLI_ERROR($dbl));
echo "Leere Rezepte\n";
mysqli_query($dbl, "TRUNCATE TABLE `items`") OR DIE(MYSQLI_ERROR($dbl));
echo "Leere Kategorien\n";
mysqli_query($dbl, "TRUNCATE TABLE `categories`") OR DIE(MYSQLI_ERROR($dbl));
echo "Leere Votes\n";
mysqli_query($dbl, "TRUNCATE TABLE `votes`") OR DIE(MYSQLI_ERROR($dbl));
echo "Leere Klicks\n";
mysqli_query($dbl, "TRUNCATE TABLE `clicks`") OR DIE(MYSQLI_ERROR($dbl));
mysqli_query($dbl, "SET FOREIGN_KEY_CHECKS=1") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Arrays mit Beispielnamen für Kategorien und Rezepten.
 */
$catNames = array("Beispielkategorie", "Zufallskategorie", "Kategorie", "Lorem Ipsum Kategorie");
$itemNames = array("Beispielrezept", "Zufallsrezept", "Rezept", "Lorem Ipsum Rezept");

/**
 * Anlegen der Kategorien.
 */
echo "Lege Kategorien an.\n";
for($i = 1; $i < 8; $i++) {
  mysqli_query($dbl, "INSERT INTO `categories` (`title`, `shortTitle`, `sortIndex`, `description`, `shortDescription`) VALUES ('".$catNames[array_rand($catNames)].rand(10000,99999)."', '".$i."-".md5(random_bytes(128))."', '".rand(10, 99999)."', 'LOREM IPSUM\nBeispieltext **MEHRZEILIG**', 'Beispieltext **EINZEILIG**')") OR DIE(MYSQLI_ERROR($dbl));
}

/**
 * Anlegen der Rezepte.
 */
echo "Lege Rezepte an.\n";
for($i = 1; $i < 101; $i++) {
  mysqli_query($dbl, "INSERT INTO `items` (`title`, `shortTitle`, `text`, `ingredients`, `persons`, `cost`, `difficulty`, `duration`) VALUES ('".$itemNames[array_rand($itemNames)].rand(10000,99999)."', '".$i."-".md5(random_bytes(128))."', 'LOREM IPSUM\nBeispieltext **MEHRZEILIG**', '- Inhaltsstoff 1\n- Inhaltsstoff 2', '".rand(1, 10)."', '".rand(1, 3)."', '".rand(1, 4)."', '".rand(1, 6)."')") OR DIE(MYSQLI_ERROR($dbl));
}

/**
 * Anlegen der Querverweise.
 */
echo "Lege Querverweise an.\n";
for($i = 0; $i < 400; $i++) {
  if(mysqli_query($dbl, "INSERT INTO `categoryItems` (`categoryId`, `itemId`, `sortIndex`) VALUES ('".rand(1, 7)."', '".rand(1, 100)."', '".rand(10, 99999)."')") === FALSE) {
    if(mysqli_errno($dbl) == 1062) {
      /**
       * Wenn ein Doppelter Eintrag vorlag wird der Vorgang erneut durchlaufen.
       */
      $i--;
    } else {
      die(mysqli_error($dbl));
    }
  }
}

/**
 * Anlegen der Klicks und Votes.
 * Nicht jeder Klick hat auch einen Vote.
 */
echo "Generiere Klicks und Votes.\n";
for($i = 1; $i < 11; $i++) {
  $queryClicks = array();
  $queryVotes = array();
  for($j = 0; $j < 100000; $j++) {
    $hash = hash('sha256', random_bytes(128));
    $itemId = rand(1, 100);
    $queryClicks[] = "('".$itemId."', '".$hash."')";
    if(rand(0, 8) == 8) {
      $queryVotes[] = "('".$itemId."', '".$hash."', '".rand(1, 5)."')";
    }
  }
  echo "Durchgang ".$i."/10\n";
  echo "  Lege ".count($queryClicks)." Klicks an.\n";
  mysqli_query($dbl, "INSERT INTO `clicks` (`itemId`, `hash`) VALUES ".implode(",", $queryClicks)) OR DIE(MYSQLI_ERROR($dbl));
  echo "  Lege ".count($queryVotes)." Votes an.\n";
  mysqli_query($dbl, "INSERT INTO `votes` (`itemId`, `hash`, `stars`) VALUES ".implode(",", $queryVotes)) OR DIE(MYSQLI_ERROR($dbl));
}

echo "\nFertig.\n";
?>
