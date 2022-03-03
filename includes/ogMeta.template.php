<?php
/**
 * ogMeta.php
 * 
 * Standard OG Metadaten
 * @see https://ogp.me/
 */
$ogMeta = array(
  'title'            => '',
  'type'             => 'article',
  'url'              => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
  'image'            => 'https://'.$_SERVER['HTTP_HOST'].'/src/og_favicon.png',
  'image:secure_url' => 'https://'.$_SERVER['HTTP_HOST'].'/src/og_favicon.png',
  'image:width'      => '300',
  'image:height'     => '300',
  'image:alt'        => '',
  'description'      => '',
  'locale'           => 'de_DE',
  'site_name'        => ''
);
