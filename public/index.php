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
  'team'              => 'team.php',

  /* Seiten zur Ansicht der Kategorien und Einträge */
  'showCategory'      => 'showCategory.php',
  'showItem'          => 'showItem.php',
  'vote'              => 'vote.php',
  'fav'               => 'fav.php',
  'search'            => 'search.php',

  /* Adminseiten */
  'adminLogin'        => 'adminLogin.php',
  'adminIndex'        => 'adminIndex.php',
  'adminLogout'       => 'adminLogout.php',
  'adminCategories'   => 'adminCategories.php',
  'adminItems'        => 'adminItems.php',
  'adminFiles'        => 'adminFiles.php',
  'adminIngredients'  => 'adminIngredients.php',
  'adminSessions'     => 'adminSessions.php',
  'adminMarkdownInfo' => 'adminMarkdownInfo.php',
  'adminLog'          => 'adminLog.php',
  'adminUserLog'      => 'adminUserLog.php',

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
 * Hinweis: Das Toggle-Element ist im Template enthalten.
 */
$a = " class='active'";
$nav = "<a href='/'".($getp == "start" ? $a : NULL)."><span class='fas icon'>&#xf015;</span>Startseite</a>";
$nav.= "<a href='/team'".($getp == "team" ? $a : NULL)."><span class='fas icon'>&#xf0c0;</span>Team</a>";
$nav.= "<a href='/search'".($getp == "search" ? $a : NULL)."><span class='fas icon'>&#xf002;</span>Suche</a>";

/**
 * Alle Kategorien auflisten
 */
$result = mysqli_query($dbl, "SELECT `title`, `shortTitle` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  while($row = mysqli_fetch_array($result)) {
    $nav.= "<a href='/kategorie/".output($row['shortTitle'])."'".(($getp == "showCategory" AND (isset($_GET['category']) AND $_GET['category'] == $row['shortTitle'])) ? $a : NULL)."><span class='far icon'>&#xf07c;</span>".output($row['title'])."</a>".PHP_EOL;
  }
}

/**
 * Adminnavigation
 * Wenn nicht eingeloggt, dann wird gar nichts angezeigt
 */
if((isset($_COOKIE['cookingAdmin']) AND !empty($_COOKIE['cookingAdmin'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cookingAdmin']), $match) === 1) {
  $nav.= "<hr>".PHP_EOL;
  $nav.= "<span class='warn bold'>Admin</span>".PHP_EOL;
  $nav.= "<a href='/adminIndex'".($getp == "adminIndex" ? $a : NULL)."><span class='fas icon'>&#xf0cb;</span>Index</a>".PHP_EOL;
  $nav.= "<a href='/adminCategories'".($getp == "adminCategories" ? $a : NULL)."><span class='far icon'>&#xf07c;</span>Kategorien</a>".PHP_EOL;
  $nav.= "<a href='/adminItems'".($getp == "adminItems" ? $a : NULL)."><span class='fas icon'>&#xf543;</span>Rezepte</a>".PHP_EOL;
  $nav.= "<a href='/adminIngredients'".($getp == "adminIngredients" ? $a : NULL)."><span class='fas icon'>&#xf4d8;</span>Zutaten</a>".PHP_EOL;
  $nav.= "<a href='/adminSessions'".($getp == "adminSessions" ? $a : NULL)."><span class='fas icon'>&#xf0c0;</span>Sitzungen</a>".PHP_EOL;
  $nav.= "<a href='/adminLog'".($getp == "adminLog" ? $a : NULL)."><span class='fas icon'>&#xf70e;</span>Log</a>".PHP_EOL;
  $nav.= "<a href='/adminUserLog'".($getp == "adminUserLog" ? $a : NULL)."><span class='fas icon'>&#xf70e;</span>UserLog</a>".PHP_EOL;
  $nav.= "<a href='/adminLogout'".($getp == "adminLogout" ? $a : NULL)."><span class='fas icon'>&#xf2f5;</span>Logout</a>".PHP_EOL;
}

/**
 * Nutzernavigation wenn eingeloggt
 */
$nav.= "<hr>".PHP_EOL;
$nav.= "<span class='bold'>Userbereich</span>".PHP_EOL;
if((isset($_COOKIE['cooking']) AND !empty($_COOKIE['cooking'])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE['cooking']), $match) === 1) {
  $nav.= "<a href='/overview'".($getp == "overview" ? $a : NULL)."><span class='fas icon'>&#xf0cb;</span>Übersicht</a>".PHP_EOL;
  $nav.= "<a href='/favs'".($getp == "favs" ? $a : NULL)."><span class='fas icon'>&#xf005;</span>Favoriten</a>".PHP_EOL;
  $nav.= "<a href='/logout'".($getp == "logout" ? $a : NULL)."><span class='fas icon'>&#xf2f5;</span>Logout</a>".PHP_EOL;
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
