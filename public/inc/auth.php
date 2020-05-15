<?php
/**
 * auth.php
 * 
 * Response Seite für die oAuth Schnittstelle
 */

/**
 * Titel
 */
$title = "Authentifizierung";
$content.= "<h1>Authentifizierung</h1>".PHP_EOL;

/**
 * Fehler Abfangen
 */
if(isset($_GET['error'])) {
  $content.= "<div class='warnbox'>Du hast den Login abgebrochen.</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'><a href='/loginRedirect'>nochmal versuchen</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='spacer-l'></div>".PHP_EOL;
} else {
  $error = 0;
  /**
   * authCode auf Richtigkeit überprüfen
   */
  if(preg_match("/^[0-9a-f]{32}$/i", defuse($_GET['authCode']), $match) === 1) {
    $authCode = $match[0];
  } else {
    $error = 1;
  }
  /**
   * userId auf Richtigkeit überprüfen
   */
  if(preg_match("/^[0-9a-f]{32}$/i", defuse($_GET['userId']), $match) === 1) {
    $userId = $match[0];
  } else {
    $error = 1;
  }
  /**
   * state auf Richtigkeit überprüfen
   */
  if(preg_match("/^[0-9a-f]{32}$/i", defuse($_GET['state']), $match) === 1) {
    $state = $match[0];
    /**
     * Prüfung ob der State Parameter vom auth mit dem Cookie übereinstimmt
     */
    if($_COOKIE['state'] != $state) {
      $error = 1;
    }
    /**
     * State Cookie entfernen
     */
    setcookie("state", NULL, 0);
  } else {
    $error = 1;
  }
  
  /**
   * Wenn ein Fehler aufgetreten ist, dann ist der Login fehlgeschlagen und ein Fehler wird ausgegeben.
   */
  if($error == 1) {
    $content.= "<div class='warnbox'>Ein Fehler ist aufgetreten.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'><a href='/loginRedirect'>nochmal versuchen</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    $content.= "<div class='spacer-l'></div>".PHP_EOL;
  } else {
    /**
     * Einbindung des apiCall
     */
    require_once($apiCall);

    /**
     * Wenn kein Fehler aufgetreten ist, wird mit den vorher überprüften Parametern das AuthToken angefragt.
     */
    $response = apiCall("https://pr0gramm.com/api/user/authtoken", array('authCode' => $authCode, 'userId' => $userId, 'clientId' => $clientId, 'clientSecret' => $clientSecret));
    if(preg_match("/^[0-9a-f]{32}$/i", defuse($response['accessToken']), $match) === 1) {
      $token = $match[0];
      /**
       * Mit dem AuthToken wird dann der Username angefragt, entschärft und validiert.
       * 
       * Hinweis zum Regex: Es gibt noch ganz alte User, die ein Binde- oder Unterstrich im Namen haben. Heutzutage ist es /[0-9a-z]{2,32}/i
       */
      if(preg_match("/^[0-9a-z-_]{2,32}$/i", defuse(apiCall("https://pr0gramm.com/api/user/name", NULL, $token)['name']), $match) === 1) {
        $username = $match[0];
      } else {
        /**
         * Es ist unwahrscheinlich, dass die pr0gramm-API einen ungültigen Nutzernamen zurückgibt.
         * An dieser Stelle ist es wahrscheinlicher, dass das Token ungültig/abgelaufen ist oder die oAuth Sitzung vom User beendet wurde.
         */
        header("Location: /login?e=oAuth");
        die();
      }

      /**
       * Abrufen der pr0grammUserId
       */
      $pr0grammUserId = (int)defuse(apiCall("https://pr0gramm.com/api/profile/info/?name=".$username)['user']['id']);

      /**
       * Prüfung ob der User sich schon einmal angemeldet hat.
       */
      $result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `pr0grammUserId`='".$pr0grammUserId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_num_rows($result) == 0) {
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
          userLog($innerrow['id'], 3, NULL, "Name `".$username."` mit einer anderen pr0gramm-ID `".$innerrow['pr0grammUserId']."` existent. Wurde umbenannt");
          mysqli_query($dbl, "DELETE FROM `userSessions` WHERE `userId`='".$innerrow['id']."'") OR DIE(MYSQLI_ERROR($dbl));
          userLog($innerrow['id'], 1, NULL, "Alle Sitzungen beendet");
        }

        /**
         * Neuanlage des Users, da nicht vorhanden.
         */
        mysqli_query($dbl, "INSERT INTO `users` (`username`, `pr0grammUserId`, `accessToken`) VALUES ('".$username."', '".$pr0grammUserId."', '".$token."')") OR DIE(MYSQLI_ERROR($dbl));
        $userId = mysqli_insert_id($dbl);
        userLog($userId, 2, NULL, "User `".$username."` hat sich zum ersten Mal eingeloggt");
      } else {
        /**
         * Abfrage der User-ID, Aktualisierung des Usernamens und des accessToken.
         */
        $row = mysqli_fetch_array($result);
        $userId = $row['id'];
        mysqli_query($dbl, "UPDATE `users` SET `username`='".$username."', `accessToken`='".$token."' WHERE `pr0grammUserId`='".$pr0grammUserId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if($row['username'] != $username) {
          userLog($userId, 3, NULL, "User `".$row['username']."` wurde in `".$username."` umbenannt");
        }
      }

      /**
       * Generierung der Sitzung
       */
      $sessionHash = hash("sha256", random_bytes(4096));
      setcookie("cooking", $sessionHash, time()+(86400*30));
      mysqli_query($dbl, "INSERT INTO `userSessions` (`userId`, `sessionHash`) VALUES ('".$userId."', '".$sessionHash."')") OR DIE(MYSQLI_ERROR($dbl));
      userLog($userId, 1, NULL, "Login");

      /**
       * Meldung, dass die Sitzung angelegt wurde und weiterleitung auf die Übersichtsseite.
       */
      $content.= "<div class='successbox'>Login erfolgreich.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'>Hallo ".output($username)."!</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'><a href='/overview'>Weiter zur Übersicht</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='spacer-l'></div>".PHP_EOL;
    } else {
      $content.= "<div class='warnbox'>Ein Fehler ist aufgetreten.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12 hover'><a href='/loginRedirect'>nochmal versuchen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $content.= "<div class='spacer-l'></div>".PHP_EOL;
    }
  }
}
?>
