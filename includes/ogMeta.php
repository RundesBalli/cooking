<?php
/**
 * ogMeta.php
 * 
 * Setzen der Standard OG Metadaten
 */
$ogMeta = array(
  'title'            => $ogConfig['name'],
  'type'             => 'article',
  'url'              => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
  'image'            => 'https://'.$_SERVER['HTTP_HOST'].'/src/ogFavicon.png',
  'image:secure_url' => 'https://'.$_SERVER['HTTP_HOST'].'/src/ogFavicon.png',
  'image:width'      => '300',
  'image:height'     => '300',
  'image:alt'        => $ogConfig['imgAlt'],
  'description'      => $ogConfig['description'],
  'locale'           => $ogConfig['locale'],
  'site_name'        => $ogConfig['sitename']
);
