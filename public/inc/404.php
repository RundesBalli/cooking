<?php
/**
 * 404.php
 * 
 * 404 ErrorDocument.
 * Gibt die Fehlermeldung, sowie den angeforderten Pfad zurück.
 */
$title = "404";
http_response_code(404);
$content.= "<h1><span class='fas icon'>&#xf002;</span>404 - Not Found</h1>".PHP_EOL;
$content.= "<div class='infobox'>Die von dir angeforderte Ressource <span class='italic'>".output($_SERVER['REQUEST_URI'])."</span> existiert nicht.</div>".PHP_EOL;
?>
