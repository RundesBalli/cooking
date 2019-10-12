<?php
/**
 * adminmarkdowninfo.php
 * 
 * Seite für Markdowninfos.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

$title = "Markdown Info";

/**
 * Info / Einleitung
 */
$content.= "<h1>Markdown Info</h1>";
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Markdown steht in manchen Textfeldern zur Verfügung. Diese Textfelder sind besonders gekennzeichnet.</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Mehrzeilige Textfelder
 */
$content.= "<a name='multiline'></a>".PHP_EOL;
$content.= "<h1>Mehrzeilige Textfelder</h1>".PHP_EOL;
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Markdown-Code</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Beispielausgabe</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Ergänzungen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code># Foobar</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("# Foobar")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Zeile muss mit <code>#</code> beginnen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>[Link](https://RundesBalli.com)*</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("[Link](https://RundesBalli.com)*")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Link im neuen Tab</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>[Link](https://RundesBalli.com)</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("[Link](https://RundesBalli.com)")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Link im selben Tab</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>**Text**</code> oder <code>__Text__</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("**Fettschrift** oder __Fettschrift__")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>*Text*</code> oder <code>_Text_</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("*Kursivschrift* oder _Kursivschrift_")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>~~Text~~</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("~~durchgestrichen~~")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>`Inlinecode`</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("`Inlinecode`")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>* Butter<br>* Margarine<br>* Bratfett</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("* Butter\n* Margarine\n* Bratfett")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Eingerückte, unsortierte Liste</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>1. Butter<br>2. Margarine<br>3. Bratfett</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("1. Butter\n2. Margarine\n3. Bratfett")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Eingerückte, sortierte Liste</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='spacer-m'></div>".PHP_EOL;

/**
 * Einzeilige Textfelder
 */
$content.= "<a name='singleline'></a>".PHP_EOL;
$content.= "<h1>Einzeilige Textfelder</h1>".PHP_EOL;
$content.= "<div class='row highlight bold bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Markdown-Code</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Beispielausgabe</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Ergänzungen</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>[Link](https://RundesBalli.com)*</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("[Link](https://RundesBalli.com)*")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Link im neuen Tab</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>[Link](https://RundesBalli.com)</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("[Link](https://RundesBalli.com)")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Link im selben Tab</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>**Text**</code> oder <code>__Text__</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("**Fettschrift** oder __Fettschrift__")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>*Text*</code> oder <code>_Text_</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("*Kursivschrift* oder _Kursivschrift_")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

$content.= "<div class='row bordered'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><code>~~Text~~</code></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>".Slimdown::render("~~durchgestrichen~~")."</div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'></div>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
"</div>".PHP_EOL;

?>
