<?php
/**
 * adminMarkdownInfo.php
 * 
 * Seite mit Infos zum Markdownparser.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder, die Rezept-Darstellung und für die Druckausgabe
 */
$additionalStyles[] = "item";

$title = "Markdown Info";

/**
 * Info / Einleitung
 */
$content.= "<h1><span class='fab icon'>&#xf60f;</span>Markdown Info</h1>";
$content.= "<div class='row'>".
"<div class='col-s-12 col-l-12'>Markdown steht in manchen Textfeldern zur Verfügung. Diese Textfelder sind besonders gekennzeichnet.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Mehrzeilige Textfelder
 */
$content.= "<a name='multiline'></a>";
$content.= "<h2>Mehrzeilige Textfelder</h2>";
$content.= "<div class='row highlight bold'>".
"<div class='col-s-12 col-l-4'>Markdown-Code</div>".
"<div class='col-s-12 col-l-4'>Beispielausgabe</div>".
"<div class='col-s-12 col-l-4'>Ergänzungen</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code># Foobar</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("# Foobar")."</div>".
"<div class='col-s-12 col-l-4'>Zeile muss mit <code>#</code> beginnen</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>[Link](https://RundesBalli.com)*</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("[Link](https://RundesBalli.com)*")."</div>".
"<div class='col-s-12 col-l-4'>Link im neuen Tab</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>[Link](https://RundesBalli.com)</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("[Link](https://RundesBalli.com)")."</div>".
"<div class='col-s-12 col-l-4'>Link im selben Tab</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>**Text**</code> oder <code>__Text__</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("**Fettschrift** oder __Fettschrift__")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>*Text*</code> oder <code>_Text_</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("*Kursivschrift* oder _Kursivschrift_")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>~~Text~~</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("~~durchgestrichen~~")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>`Inlinecode`</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("`Inlinecode`")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>---</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("---")."</div>".
"<div class='col-s-12 col-l-4'>Kleiner Abstandshalter (30px)</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>* Butter<br>* Margarine<br>* Bratfett</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("* Butter\n* Margarine\n* Bratfett")."</div>".
"<div class='col-s-12 col-l-4'>Eingerückte, unsortierte Liste</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>1. Butter<br>2. Margarine<br>3. Bratfett</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("1. Butter\n2. Margarine\n3. Bratfett")."</div>".
"<div class='col-s-12 col-l-4'>Eingerückte, sortierte Liste</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>;;Lorem Ipsum dolor sit amet;;</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render(";;Lorem Ipsum dolor sit amet;;")."</div>".
"<div class='col-s-12 col-l-4'>Kachel für einzelne Zubereitungsschritte in Rezepten</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='spacer-m'></div>";

/**
 * Einzeilige Textfelder
 */
$content.= "<a name='singleline'></a>";
$content.= "<h2>Einzeilige Textfelder</h2>";
$content.= "<div class='row highlight bold'>".
"<div class='col-s-12 col-l-4'>Markdown-Code</div>".
"<div class='col-s-12 col-l-4'>Beispielausgabe</div>".
"<div class='col-s-12 col-l-4'>Ergänzungen</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>[Link](https://RundesBalli.com)*</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("[Link](https://RundesBalli.com)*")."</div>".
"<div class='col-s-12 col-l-4'>Link im neuen Tab</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>[Link](https://RundesBalli.com)</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("[Link](https://RundesBalli.com)")."</div>".
"<div class='col-s-12 col-l-4'>Link im selben Tab</div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>**Text**</code> oder <code>__Text__</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("**Fettschrift** oder __Fettschrift__")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>*Text*</code> oder <code>_Text_</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("*Kursivschrift* oder _Kursivschrift_")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

$content.= "<div class='row bordered'>".
"<div class='col-s-12 col-l-4'><code>~~Text~~</code></div>".
"<div class='col-s-12 col-l-4'>".Slimdown::render("~~durchgestrichen~~")."</div>".
"<div class='col-s-12 col-l-4'></div>".
"<div class='col-s-12 col-l-0'><div class='spacer-s'></div></div>".
"</div>";

?>
