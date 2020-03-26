<?php
/**
 * cookieCheck.php
 * 
 * Prüft ob ein gültiger Cookie gesetzt ist.
 */

if(isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) {
  /**
   * Cookieinhalt entschärfen und prüfen ob Inhalt ein sha256-Hash ist.
   */
  $sessionHash = defuse($_COOKIE['cooking']);
  if(preg_match('/[a-f0-9]{64}/i', $sessionHash, $match) === 1) {
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `users`.* FROM `userSessions` JOIN `users` ON `users`.`id`=`userSessions`.`userId` WHERE `sessionHash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Wenn eine Sitzung existiert wird das Cookie gesetzt und diverse Userdaten in Variablen geladen.
       */
      mysqli_query($dbl, "UPDATE `userSessions` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `sessionHash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie('cooking', $match[0], time()+(86400*30));
      $userrow = mysqli_fetch_array($result);
      $username = $userrow['username'];
      $userId = $userrow['id'];
      $sessionHash = $match[0];
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
       */
      setcookie('cooking', NULL, 0);
      header("Location: /login");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
     */
    setcookie('cooking', NULL, 0);
    header("Location: /login");
    die();
  }
} else {
  /**
   * Wenn kein oder ein leerer Cookie übergeben wurde wird auf die Loginseite weitergeleitet.
   */
  header("Location: /login");
  die();
}
?>
