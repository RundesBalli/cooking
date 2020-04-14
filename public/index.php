<?php
/**
 * index.php
 * 
 * pr0.cooking
 * 
 * Eine Sammlung leckerer Rezepte von Nezos
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2020 RundesBalli
 * @version   1.0
 * @see       https://github.com/RundesBalli/pr0cooking
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Initialisieren des Outputs und des Standardtitels
 */
$content = "";
$title = "";

/**
 * Erzeugen des Unique-User-Identifier
 */
$UUI = hash('sha256', $_SERVER['REMOTE_ADDR']."|".$_SERVER['HTTP_USER_AGENT']."|".$_SERVER['HTTP_ACCEPT']."|".$_SERVER['HTTP_ACCEPT_LANGUAGE']."|".$_SERVER['HTTP_ACCEPT_ENCODING']);

/**
 * Herausfinden welche Seite angefordert wurde
 */
if(!isset($_GET['p']) OR empty($_GET['p'])) {
  $getp = "start";
} else {
  preg_match("/([\d\w-]+)/i", $_GET['p'], $match);
  $getp = $match[1];
}

/**
 * Das Seitenarray für die Seitenzuordnung
 */
$pageArray = array(
  /* Standardseiten */
  'start'             => 'start.php',
  'imprint'           => 'imprint.php',

  /* Seiten zur Ansicht der Kategorien und Einträge */
  'showCategory'      => 'showCategory.php',
  'showItem'          => 'showItem.php',
  'vote'              => 'vote.php',
  'fav'               => 'fav.php',

  /* Adminseiten */
  'adminLogin'        => 'adminLogin.php',
  'adminIndex'        => 'adminIndex.php',
  'adminLogout'       => 'adminLogout.php',
  'adminCategories'   => 'adminCategories.php',
  'adminItems'        => 'adminItems.php',
  'adminFiles'        => 'adminFiles.php',
  'adminSessions'     => 'adminSessions.php',
  'adminMarkdownInfo' => 'adminMarkdownInfo.php',

  /* Userseiten */
  'login'             => 'login.php',
  'loginRedirect'     => 'loginRedirect.php',
  'logout'            => 'logout.php',
  'auth'              => 'auth.php',
  'overview'          => 'overview.php',
  'favs'              => 'favs.php',

  /* Fehlerseiten */
  '404'               => '404.php',
  '403'               => '403.php'
);

/**
 * Prüfung ob die Unterseite im Array existiert, falls nicht 404
 */
if(isset($pageArray[$getp])) {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR.$pageArray[$getp]);
} else {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."404.php");
}

/**
 * Navigation
 * Hinweis: Die Startseitenverlinkung und das Toggle-Element sind im Template enthalten.
 */
$nav = "";

/**
 * Alle Kategorien auflisten
 */
$result = mysqli_query($dbl, "SELECT `title`, `shortTitle` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  while($row = mysqli_fetch_array($result)) {
    $nav.= "<a href='/kategorie/".output($row['shortTitle'])."'><span class='far icon'>&#xf07c;</span>".output($row['title'])."</a>".PHP_EOL;
  }
}

/**
 * Adminnavigation
 * Wenn nicht eingeloggt, dann wird gar nichts angezeigt
 */
if((isset($_COOKIE['cookingAdmin']) AND !empty($_COOKIE['cookingAdmin'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cookingAdmin']), $match) === 1) {
  $nav.= "<hr>".PHP_EOL;
  $nav.= "<span class='warn bold'>Admin</span>".PHP_EOL;
  $nav.= "<a href='/adminIndex'><span class='fas icon'>&#xf0cb;</span>Index</a>".PHP_EOL;
  $nav.= "<a href='/adminCategories'><span class='far icon'>&#xf07c;</span>Kategorien bearbeiten</a>".PHP_EOL;
  $nav.= "<a href='/adminItems'><span class='fas icon'>&#xf543;</span>Rezepte bearbeiten</a>".PHP_EOL;
  $nav.= "<a href='/adminSessions'><span class='fas icon'>&#xf0c0;</span>Sitzungen</a>".PHP_EOL;
  $nav.= "<a href='/adminLogout'><span class='fas icon'>&#xf2f5;</span>Logout</a>".PHP_EOL;
}

/**
 * Nutzernavigation wenn eingeloggt
 */
$nav.= "<hr>".PHP_EOL;
$nav.= "<span class='bold'>Userbereich</span>".PHP_EOL;
if((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) {
  $nav.= "<a href='/overview'><span class='fas icon'>&#xf0cb;</span>Übersicht</a>".PHP_EOL;
  $nav.= "<a href='/favs'><span class='fas icon'>&#xf005;</span>Favoriten</a>".PHP_EOL;
  $nav.= "<a href='/logout'><span class='fas icon'>&#xf2f5;</span>Logout</a>".PHP_EOL;
} else {
  /**
   * Nutzernavigation wenn nicht eingeloggt
   */
  $nav.= "<a href='/login'><span class='fas icon'>&#xf2f6;</span>Login</a>".PHP_EOL;
}


/**
 * Templateeinbindung und Einsetzen der Variablen
 */
$templateFile = __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."template.tpl";
$fp = fopen($templateFile, "r");
echo preg_replace(array("/{TITLE}/im", "/{NAV}/im", "/{CONTENT}/im"), array(($title == "" ? "" : " - ".$title), $nav, $content), fread($fp, filesize($templateFile)));
fclose($fp);
?>