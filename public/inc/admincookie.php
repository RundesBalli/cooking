<?php
/**
 * admincookie.php
 * 
 * Prüft ob ein gültiger Cookie gesetzt ist.
 */

if(isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) {
  /**
   * Cookieinhalt entschärfen und prüfen ob Inhalt ein sha256-Hash ist.
   */
  $sessionhash = defuse($_COOKIE['cooking']);
  if(preg_match('/[a-f0-9]{64}/i', $sessionhash, $match) === 1) {
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `accounts`.`username` FROM `sessions` JOIN `accounts` WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Wenn eine Sitzung existiert wird der letzte Nutzungszeitpunkt aktualisiert und der Username in die Variable $username geladen.
       */
      mysqli_query($dbl, "UPDATE `sessions` SET `lastactivity`=CURRENT_TIMESTAMP WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie('cooking', $match[0], time()+(6*7*86400));
      $username = mysqli_fetch_array($result)['username'];
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User ausgeloggt.
       */
      header("Location: /adminlogout");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User ausgeloggt.
     */
    header("Location: /adminlogout");
    die();
  }
} else {
  /**
   * Wenn kein oder ein leerer Cookie übergeben wurde wird auf die Loginseite weitergeleitet.
   */
  header("Location: /adminlogin");
  die();
}
?>