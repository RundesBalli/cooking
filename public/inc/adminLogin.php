<?php
/**
 * adminLogin.php
 * 
 * Seite zum Einloggen in den Administrationsbereich.
 */
$title = "Login";

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

/**
 * Kein Cookie gesetzt oder Cookie leer und Formular nicht übergeben.
 */
if((!isset($_COOKIE[$cookieName]) OR empty($_COOKIE[$cookieName])) AND !isset($_POST['submit'])) {
  $content.= "<h1 class='alignCenter'><span class='fas icon'>&#xf2f6;</span>Login</h1>";
  /**
   * Loginformular
   */
  $content.= "<form action='/adminLogin' method='post'>";
  $content.= "<div class='row hover'>".
  "<div class='col-s-12 col-l-3'>Name</div>".
  "<div class='col-s-12 col-l-9'><input type='text' name='username' placeholder='Name' autofocus></div>".
  "</div>";
  $content.= "<div class='row hover'>".
  "<div class='col-s-12 col-l-3'>Passwort</div>".
  "<div class='col-s-12 col-l-9'><input type='password' name='password' placeholder='Passwort'></div>".
  "</div>";
  $content.= "<div class='row hover'>".
  "<div class='col-s-12 col-l-3'>Einloggen</div>".
  "<div class='col-s-12 col-l-9'><input type='submit' name='submit' value='Einloggen'></div>".
  "</div>";
  $content.= "</form>";
} elseif((!isset($_COOKIE[$cookieName]) OR empty($_COOKIE[$cookieName])) AND isset($_POST['submit'])) {
  /**
   * Kein Cookie gesetzt oder Cookie leer und Formular wurde übergeben.
   */
  /**
   * Entschärfen der Usereingaben.
   */
  $username = defuse($_POST['username']);
  /**
   * Abfragen ob eine Übereinstimmung in der Datenbank vorliegt.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `accounts` WHERE `username`='".$username."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    /**
     * Wenn der User existiert, muss der Passworthash validiert werden.
     */
    $row = mysqli_fetch_assoc($result);
    if(password_verify($_POST['password'].$row['salt'], $row['password'])) {
      /**
       * Wenn das Passwort verifiziert werden konnte wird eine Sitzung generiert und im Cookie gespeichert.
       * Danach erfolg eine Weiterleitung zur adminIndex-Seite.
       */
      $adminSessionHash = hash('sha256', random_bytes(4096));
      mysqli_query($dbl, "INSERT INTO `sessions` (`accountId`, `hash`) VALUES ('".$row['id']."', '".$adminSessionHash."')") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `text`) VALUES ('".$row['id']."', 1, 'Login')") OR DIE(MYSQLI_ERROR($dbl));
      setcookie($cookieName, $adminSessionHash, time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
      header("Location: /adminIndex");
      die();
    } else {
      /**
       * Wenn das Passwort nicht verifiziert werden konnte wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
       */
      http_response_code(403);
      $content.= "<h1>Login gescheitert</h1>";
      $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>";
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminLogin'>Erneut versuchen</a></div>".
      "</div>";
    }
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1>Login gescheitert</h1>";
    $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminLogin'>Erneut versuchen</a></div>".
    "</div>";
  }
} else {
  /**
   * Wenn bereits ein Cookie gesetzt ist wird auf die adminIndex Seite weitergeleitet.
   */
  header("Location: /adminIndex");
  die();
}
?>
