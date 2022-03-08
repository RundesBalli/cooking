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
 * Initialisieren des Outputs und des Standardtitels
 */
$content = "";
$title = "";

/**
 * Herausfinden welche Seite angefordert wurde
 */
if((!isset($_GET['page']) OR empty($_GET['page'])) OR preg_match("/([a-z-\d]+)/i", $_GET['page'], $pageMatch) !== 1) {
  $getp = "start";
} else {
  $getp = $pageMatch[1];
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

/**
 * Einsetzen der Inhalte
 */
$output = preg_replace(
  array(
    "/{TITLE}/im",
    "/{NAV}/im",
    "/{CONTENT}/im",
    "/{FOOTER}/im",
    "/{OGMETA}/im"
  ),
  array(
    $ogConfig['sitename'].($title == "" ? "" : " - ".$title),
    $nav,
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
