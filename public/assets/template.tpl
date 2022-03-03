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
    <link href="/assets/{STYLE}.css" rel="stylesheet">
    <link href="/assets/style.css" rel="stylesheet">
    {ADDITIONALSTYLES}
    <link rel="shortcut icon" href="/assets/favicon.png" type="image/png">
    <meta name="theme-color" content="#554640">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {OGMETA}
  </head>
  <body>
    <div id="sidebar" class="no-print">
      <a href="/" id="headerimg"><img src="/assets/header-{STYLE}.png" alt="Header"></a>
      <a class="toggle" id="toggle"></a>
      {NAV}
      <div id="footer" class="no-print">
        {FOOTER}
      </div>
    </div>
    <div id="content">
      {CONTENT}
    </div>
  </body>
</html>
