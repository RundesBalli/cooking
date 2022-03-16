<?php
/**
 * adminCookie.php
 * 
 * Prüft ob ein gültiger Cookie gesetzt ist.
 */

if(isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) {
  /**
   * Cookieinhalt entschärfen und prüfen ob Inhalt ein sha256-Hash ist.
   */
  $adminSessionHash = defuse($_COOKIE[$cookieName]);
  if(preg_match('/^[a-f0-9]{64}$/i', $adminSessionHash, $match) === 1) {
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `accounts`.`id`, `accounts`.`username` FROM `sessions` JOIN `accounts` ON `accounts`.`id`=`sessions`.`accountId` WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Wenn eine Sitzung existiert wird der letzte Nutzungszeitpunkt aktualisiert und ein paar Accountvariablen geladen.
       */
      mysqli_query($dbl, "UPDATE `sessions` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      $adminRow = mysqli_fetch_array($result);
      mysqli_query($dbl, "UPDATE `accounts` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `id`='".$adminRow['id']."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie($cookieName, $match[0], time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
      $username = $adminRow['username'];
      $userId = $adminRow['id'];
      $sessionHash = $match[0];
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
       */
      setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
      header("Location: /adminLogin");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
     */
    setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
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
