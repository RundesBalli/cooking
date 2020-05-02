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
    $sessionHash = $match[0];
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `users`.* FROM `userSessions` JOIN `users` ON `users`.`id`=`userSessions`.`userId` WHERE `sessionHash`='".$sessionHash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      $userrow = mysqli_fetch_array($result);
      $synced = 0;
      /**
       * Wenn eine Sitzung existiert wird geprüft, ob der User neu gesynct werden muss (eine Stunde nach dem letzten Sync).
       * Falls ja, wird über das accessToken erneut der Name abgefragt, verglichen und ggf. aktualisiert.
       * Danach erfolgt die Ban Abfrage über das Profil, ohne Token.
       */
      if(strtotime($userrow['lastSynced']) < (time()-3600)) {
        $synced = 1;
        /**
         * Einbindung des apiCall
         */
        require_once($apiCall);
        
        /**
         * Der Username wird mit dem accessToken angefragt, entschärft und validiert.
         * Hinweis zum Regex: Es gibt noch ganz alte User, die ein Binde- oder Unterstrich im Namen haben. Heutzutage ist es /[0-9a-z]{2,32}/i
         */
        if(preg_match("/^[0-9a-z-_]{2,32}$/i", defuse(apiCall("https://pr0gramm.com/api/user/name", NULL, $userrow['accessToken'])['name']), $match) === 1) {
          $username = $match[0];
          
          /**
           * Abfrage des Users mit Usernamen. 
           */
          $response = apiCall("https://pr0gramm.com/api/profile/info/?name=".$username);

          /**
           * Prüfung ob die pr0grammUserId mit der API-userId übereinstimmt. Falls nicht wird die Sitzung beendet.
           */
          if($userrow['pr0grammUserId'] != $response['user']['id']) {
            setcookie('cooking', NULL, 0);
            header("Location: /login?e=mismatchingIds");
            die();
          }

          /**
           * Prüfung ob der API-Username mit dem Datenbank Usernamen übereinstimmt. Falls nicht, wird er aktualisiert.
           */
          if($userrow['username'] != $username) {
            /**
             * Für den unwahrscheinlichen Fall, dass ein User sich umbenannt hat, und der einloggende User sich den Namen ausgesucht hat,
             * wird geprüft, ob bereits ein solcher Username existiert, und falls ja wird der auf einen Randomwert geändert.
             */
            $innerresult = mysqli_query($dbl, "SELECT * FROM `users` WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
            if(mysqli_num_rows($innerresult) == 1) {
              /**
               * Es existiert bereits ein User mit dem Namen aber mit einer anderen pr0grammUserId, also wird der Name ungültig gemacht und alle Sitzungen beendet.
               */
              $innerrow = mysqli_fetch_array($innerresult);
              mysqli_query($dbl, "UPDATE `users` SET `username`='*".substr(md5(random_bytes(4096)), 0, 31)."' WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
              //-------------------------------------------------^ Der Stern ist dafür, dass man nicht nicht versehentlich einen richtigen Usernamen mit dem md5-Hash erzeugt.
              mysqli_query($dbl, "DELETE FROM `userSessions` WHERE `userId`='".$innerrow['id']."'") OR DIE(MYSQLI_ERROR($dbl));
            }

            /**
             * Aktualisierung des Nutzernamens
             */
            mysqli_query($dbl, "UPDATE `users` SET `username`='".$username."' WHERE `pr0grammUserId`='".defuse($response['user']['id'])."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          } else {
            mysqli_query($dbl, "UPDATE `users` SET `lastSynced`=CURRENT_TIMESTAMP WHERE `id`='".$userrow['id']."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          }

          /**
           * Prüfung ob der User gebannt ist. Falls ja, wird die Sitzung beendet.
           */
          if($response['user']['banned'] != 0) {
            mysqli_query($dbl, "DELETE FROM `userSessions` WHERE `userId`='".$userrow['id']."'") OR DIE(MYSQLI_ERROR($dbl));
            setcookie('cooking', NULL, 0);
            header("Location: /login?e=banned");
            die();
          }
        } else {
          /**
           * Es ist unwahrscheinlich, dass die pr0gramm-API einen ungültigen Nutzernamen zurückgibt.
           * An dieser Stelle ist es wahrscheinlicher, dass das Token ungültig/abgelaufen ist oder die oAuth Sitzung vom User beendet wurde.
           */
          mysqli_query($dbl, "DELETE FROM `userSessions` WHERE `userId`='".$userrow['id']."'") OR DIE(MYSQLI_ERROR($dbl));
          setcookie('cooking', NULL, 0);
          header("Location: /login?e=oAuth");
          die();
        }
      }
      /**
       * Wenn eine Sitzung existiert wird das Cookie gesetzt und diverse Userdaten in Variablen geladen.
       */
      mysqli_query($dbl, "UPDATE `userSessions` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `sessionHash`='".$sessionHash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie('cooking', $sessionHash, time()+(86400*30));
      if($synced == 0) {
        $username = $userrow['username'];
      }
      $userId = $userrow['id'];
      $sessionHash = $sessionHash;
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
       */
      setcookie('cooking', NULL, 0);
      header("Location: /login?e=invalidCookie");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
     */
    setcookie('cooking', NULL, 0);
    header("Location: /login?e=invalidCookie");
    die();
  }
} else {
  /**
   * Wenn kein oder ein leerer Cookie übergeben wurde wird auf die Loginseite weitergeleitet.
   */
  header("Location: /login?e=invalidCookie");
  die();
}
?>
