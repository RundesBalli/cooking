<!--
Entwicklung:
https://RundesBalli.com
https://github.com/RundesBalli/cooking
-->
<!DOCTYPE html>
<html lang="de">
  <head>
    <title>{TITLE}</title>
    <meta charset="utf-8">
    <meta name="robots" content="index, follow"/>
    <meta name="revisit-after" content="3 days"/>
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/assets/css/fontawesome.css" rel="stylesheet">
    {ADDITIONALSTYLES}
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/png">
    <meta name="theme-color" content="#554640">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {OGMETA}
  </head>
  <body>
    <div id="sidebar">
      <span class="fas burger">&#xf805;</span>
      <span class="header">{NAVTITLE}</span>
      <a class="toggle" id="toggle"></a>
      <div id="nav">
        {NAV}
      </div>
      <div id="footer">
        {FOOTER}
      </div>
    </div>
    <div id="content">
      {CONTENT}
    </div>
    <script type="text/javascript" src="/assets/js/toggleMenu.js"></script>
    <script type="text/javascript" src="/assets/js/slideshow.js"></script>
    <script type="text/javascript" src="/assets/js/shareLink.js"></script>
  </body>
</html>
