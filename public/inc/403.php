<?php
/**
 * 403.php
 * 
 * 403 ErrorDocument.
 * Gibt die Fehlermeldung, sowie den angeforderten Pfad zurück.
 */
$title = "403";
http_response_code(403);
$content.= "<h1><span class='fas icon'>&#xf057;</span>403 - Forbidden</h1>";
$content.= "<div class='warnbox'>Du hast keine Berechtigung auf die von dir angeforderte Ressource <span class='italic'>".output($_SERVER['REQUEST_URI'])."</span> zuzugreifen.</div>";
?>
