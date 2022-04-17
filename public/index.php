<?php
/**
 * index.php
 * 
 * Cooking
 * 
 * Eine Sammlung leckerer Rezepte von Nezos
 * 
 * @author    RundesBalli <GitHub@rundesballi.com>
 * @copyright 2022 RundesBalli
 * @version   2.0
 * @see       https://github.com/RundesBalli/cooking
 */

/**
 * Einbinden des Konfigurations- und Funktionsloaders
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."_loader.php");

/**
 * Initialisieren des Outputs, des Standardtitels und der eventuell zusätzlichen Stile
 */
$content = "";
$title = "";
$additionalStyles = array();

/**
 * Herausfinden welche Seite angefordert wurde
 */
if((!isset($_GET['page']) OR empty($_GET['page'])) OR preg_match("/([a-z-\d]+)/i", $_GET['page'], $pageMatch) !== 1) {
  $getp = "start";
} else {
  $getp = $pageMatch[1];
}

if((!isset($_GET['action']) OR empty($_GET['action'])) OR preg_match("/([a-z-\d]+)/i", $_GET['action'], $actionMatch) !== 1) {
  $geta = NULL;
} else {
  $geta = $actionMatch[1];
}

/**
 * Das Seitenarray für die Seitenzuordnung
 */
$pageArray = array(
  /**
   * Fehlerseiten
   */
  '404'                   => '404.php',
  '403'                   => '403.php',

  /**
   * Standardseiten
   */
  'start'                 => 'start.php',

  /**
   * Administratorseiten
   */
  'adminLogin'            => 'adminLogin.php',
  'adminIndex'            => 'adminIndex.php',
  'adminLogout'           => 'adminLogout.php',
  'adminMarkdownInfo'     => 'adminMarkdownInfo.php',
  'adminLog'              => 'adminLog.php'
);

$actionArray = array(
  'adminSessions' => array(
    'show' => 'show.php',
    'del'  => 'del.php'
  ),
  'adminPassword' => array(
    'change' => 'change.php'
  ),
  'adminCategories' => array(
    'show'     => 'show.php',
    'add'      => 'add.php',
    'del'      => 'del.php',
    'edit'     => 'edit.php',
    'itemSort' => 'itemSort.php',
    'sort'     => 'sort.php'
  ),
  'adminFeaturedItems' => array(
    'show' => 'show.php',
    'add'  => 'add.php',
    'del'  => 'del.php'
  ),
  'adminItems' => array(
    'show'   => 'show.php',
    'add'    => 'add.php',
    'del'    => 'del.php',
    'edit'   => 'edit.php'
  ),
  'adminItemAssignments' => array(
    'show' => 'show.php',
    'add'  => 'add.php'
  )
);

/**
 * Prüfung ob die Unterseite im Array existiert, falls nicht 404
 */
if(isset($pageArray[$getp]) AND empty($geta)) {
  require_once(PAGE_INCLUDE_DIR.$pageArray[$getp]);
} elseif(!empty($geta) AND isset($actionArray[$getp][$geta])) {
  require_once(PAGE_INCLUDE_DIR.$getp.DIRECTORY_SEPARATOR.$actionArray[$getp][$geta]);
} else {
  require_once(PAGE_INCLUDE_DIR."404.php");
}

/**
 * Navigation
 */
$a = " class='active'";
$nav = "<a href='/'".($getp == "start" ? $a : NULL)."><span class='fas icon'>&#xf015;</span>Startseite</a>";

/**
 * Alle Kategorien auflisten
 */
$result = mysqli_query($dbl, "SELECT `title`, `shortTitle` FROM `categories` ORDER BY `sortIndex` ASC, `title` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) != 0) {
  while($row = mysqli_fetch_assoc($result)) {
    $nav.= "<a href='/kategorie/".output($row['shortTitle'])."'".(($getp == "showCategory" AND (isset($_GET['category']) AND $_GET['category'] == $row['shortTitle'])) ? $a : NULL)."><span class='far icon'>&#xf07c;</span>".output($row['title'])."</a>";
  }
}

/**
 * Administrator Navigation
 */
if(isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) {
  $nav.= "<hr>";
  $nav.= "<span class='bold'>Admin</span>";
  $nav.= "<a href='/adminIndex'".($getp == "adminIndex" ? $a : NULL)."><span class='fas icon'>&#xf0cb;</span>Index</a>";
  $nav.= "<a href='/adminCategories/show'".($getp == "adminCategories" ? $a : NULL)."><span class='far icon'>&#xf07c;</span>Kategorien</a>";
  $nav.= "<a href='/adminItems/show'".($getp == "adminItems" ? $a : NULL)."><span class='fas icon'>&#xf543;</span>Rezepte</a>";
  $nav.= "<a href='/adminFeaturedItems/show'".($getp == "adminFeaturedItems" ? $a : NULL)."><span class='fas icon'>&#xf005;</span>Featured</a>";
  $nav.= "<a href='/adminSessions/show'".($getp == "adminSessions" ? $a : NULL)."><span class='fas icon'>&#xf51c;</span>Sitzungen</a>";
  $nav.= "<a href='/adminLog'".($getp == "adminLog" ? $a : NULL)."><span class='fas icon'>&#xf70e;</span>Log</a>";
  $nav.= "<a href='/adminMarkdownInfo'".($getp == "adminMarkdownInfo" ? $a : NULL)."><span class='fab icon'>&#xf60f;</span>MarkdownInfo</a>";
  $nav.= "<a href='/adminLogout'".($getp == "adminLogout" ? $a : NULL)."><span class='fas icon'>&#xf2f5;</span>Logout</a>";
}

/**
 * Footer
 */
$footer = "<span><a href='/imprint'>Impressum</a><a href='/privacy'>Datenschutz</a></span>";
$footer.= "<span>Ein Projekt von: <a href='https://nezos.wtf' target='_blank' rel='noopener'>Nezos</a></span>";
$footer.= "<span>Entwicklung durch: <a href='https://rundesballi.com' target='_blank' rel='noopener'>RundesBalli</a><a href='https://github.com/RundesBalli/cooking' target='_blank' rel='noopener'>GitHub</a></span>";

/**
 * Templateeinbindung
 */
$templateFile = __DIR__.DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."tpl".DIRECTORY_SEPARATOR."template.tpl";
$fp = fopen($templateFile, "r");

/**
 * Aufbereitung der Metadaten
 */
if(!empty($ogMeta)) {
  $ogData = array();
  foreach($ogMeta AS $key => $val) {
    $ogData[] = "<meta property='og:".output($key)."' content='".output($val)."'/>";
  }
}

if(!empty($additionalStyles)) {
  $addStyles = "";
  foreach($additionalStyles AS $key => $val) {
    $addStyles.= "<link href=\"/assets/css/".$val.".css\" rel=\"stylesheet\">";
  }
} else {
  $addStyles = NULL;
}

/**
 * Einsetzen der Inhalte
 */
$output = preg_replace(
  array(
    "/{TITLE}/im",
    "/{NAV}/im",
    "/{ADDITIONALSTYLES}/im",
    "/{NAVTITLE}/im",
    "/{CONTENT}/im",
    "/{FOOTER}/im",
    "/{OGMETA}/im"
  ),
  array(
    $ogConfig['sitename'].($title == "" ? "" : " - ".$title),
    $nav,
    $addStyles,
    $navTitle,
    $content,
    $footer,
    (!empty($ogMeta) ? PHP_EOL.implode(PHP_EOL, $ogData) : NULL)
  ),
  fread($fp, filesize($templateFile)));
fclose($fp);

/**
 * Tidy HTML Output
 * @see https://gist.github.com/RundesBalli/a5d20a8c92a9a004803980654e638cbb
 * @see https://api.html-tidy.org/tidy/quickref_5.6.0.html
 */

$tidyOptions = array(
  'indent' => TRUE,
  'output-xhtml' => TRUE,
  'wrap' => 200,
  'newline' => 'LF', /* LF = \n */
  'output-encoding' => 'utf8',
  'drop-empty-elements' => FALSE /* e.g. for placeholders */
);

$tidy = tidy_parse_string($output, $tidyOptions, 'UTF8');
tidy_clean_repair($tidy);
echo $tidy;
?>
