<?php
/**
 * adminCookie.php
 * 
 * Prüft ob ein gültiger Cookie gesetzt ist.
 */

if(isset($_COOKIE['cookingAdmin']) AND !empty($_COOKIE['cookingAdmin'])) {
  /**
   * Cookieinhalt entschärfen und prüfen ob Inhalt ein sha256-Hash ist.
   */
  $adminSessionHash = defuse($_COOKIE['cookingAdmin']);
  if(preg_match('/[a-f0-9]{64}/i', $adminSessionHash, $match) === 1) {
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `accounts`.`username` FROM `accountSessions` JOIN `accounts` ON `accounts`.`id`=`accountSessions`.`userId` WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Wenn eine Sitzung existiert wird der letzte Nutzungszeitpunkt aktualisiert und der Username in die Variable $username geladen.
       */
      mysqli_query($dbl, "UPDATE `accountSessions` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie('cookingAdmin', $match[0], time()+(6*7*86400));
      $username = mysqli_fetch_array($result)['username'];
      $adminSessionHash = $match[0];
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
       */
      setcookie('cookingAdmin', NULL, 0);
      header("Location: /adminLogin");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
     */
    setcookie('cookingAdmin', NULL, 0);
    header("Location: /adminLogin");
    die();
  }
} else {
  /**
   * Wenn kein oder ein leerer Cookie übergeben wurde wird auf die Loginseite weitergeleitet.
   */
  header("Location: /adminLogin");
  die();
}
?>
