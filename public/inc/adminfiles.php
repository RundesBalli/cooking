<?php
/**
 * adminfiles.php
 * 
 * Seite um Rezepten Bilder und Thumbnails hinzuzufügen.
 * Es erfolgt der Direkteinstieg in das Rezept (über /adminitems).
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('admincookie.php');

/**
 * Entschärfen der übergebenen ID
 */
$id = (int)defuse($_GET['id']);

/**
 * Prüfen ob das Rezept existiert.
 */
$result = mysqli_query($dbl, "SELECT `id`, `title` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  /**
   * Falls das Rezept nicht existiert, wird ein 404er und eine Fehlermeldung zurückgegeben.
   */
  $title = "Dateiverwaltung";
  $content.= "<h1>Dateiverwaltung</h1>".PHP_EOL;
  http_response_code(404);
  $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".$id."</span> existiert nicht.</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminitems/list'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
  "</div>".PHP_EOL;
} else {
  /**
   * Falls das Rezept existiert, dann wird geprüft was gemacht werden soll.
   */
  $row = mysqli_fetch_array($result);
  if(!isset($_GET['action'])) {
    /**
     * Wenn keine Action übergeben wurde, dann erfolgt eine Umleitung zur Auflistung aller Rezepte.
     */
    header("Location: /adminfiles/list/".$id);
    die();
  } elseif($_GET['action'] == 'list') {
    /**
     * Dateien auflisten
     */
    $title = "Dateiverwaltung - Bilder anzeigen";
    $content.= "<h1>Dateiverwaltung - Bilder anzeigen</h1>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><span class='highlight bold'>Aktionen:</span> <a href='/adminfiles/add/".$id."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a> - <a href='/adminfiles/sort/".$id."'><span class='fas icon'>&#xf0dc;</span>Sortieren</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
    /**
     * Thumbnail
     */
    $content.= "<h2>Thumbnail</h2>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Kein Thumbnail vorhanden.
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keinen Thumbnail.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } elseif(mysqli_num_rows($result) == 1) {
      /**
       * Thumbnail vorhanden. Auflistung.
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Dateiname</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Aktionen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      $row = mysqli_fetch_array($result);
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'><a href='/img/thumb-".$row['itemid']."-".$row['filehash'].".png' target='_blank'>/img/thumb-".$row['itemid']."-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><a href='/adminfiles/del/".$id."/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Mehrere Thumbnails vorhanden, was nicht sein darf. Löschung aller Thumbnails und Aufforderung zum erneuten Hochladen.
       */
      while($row = mysqli_fetch_array($result)) {
        unlink($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."thumb-".$row['itemid']."-".$row['filehash'].".png");
      }
      mysqli_query($dbl, "DELETE FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1'") OR DIE(MYSQLI_ERROR($dbl));
      $content.= "<div class='warnbox'>Das Rezept hat Fehler im Thumbnail. Er wurde gelöscht und muss neu hochgeladen werden.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
    /**
     * Bilder
     */
    $content.= "<div class='spacer-m'></div>".PHP_EOL;
    $content.= "<h2>Bilder</h2>".PHP_EOL;
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      /**
       * Noch keine Bilder vorhanden
       */
      $content.= "<div class='infobox'>Das Rezept hat noch keine Bilder.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/add/".$id."'><span class='fas icon'>&#xf067;</span>Hinzufügen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Bilder vorhanden. Auflistung nach Sortierindex.
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>Dateiname</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Sortierindex</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>Aktionen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      while($row = mysqli_fetch_array($result)) {
        $links = array(
          "<a href='/img/img-".$row['itemid']."-full-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-full-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
          "<a href='/img/img-".$row['itemid']."-big-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-big-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
          "<a href='/img/img-".$row['itemid']."-medium-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-medium-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
          "<a href='/img/img-".$row['itemid']."-small-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-small-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>"
        );
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-8 col-l-8 col-xl-8'>".implode("<br>", $links)."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'>".$row['sortIndex']."</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-2 col-l-2 col-xl-2'><a href='/adminfiles/del/".$id."/".$row['id']."' class='nowrap'><span class='fas icon'>&#xf2ed;</span>Löschen</a></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    }
  } elseif($_GET['action'] == 'add') {
    /**
     * Dateien hinzufügen.
     */
    $title = "Dateiverwaltung - Bild hinzufügen";
    $content.= "<h1>Dateiverwaltung - Bild hinzufügen</h1>".PHP_EOL;
    if(isset($_POST['submit'])) {
      /**
       * Formular wurde abgesendet, Upload verarbeiten.
       */
      if($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        /**
         * Keine Fehlermeldung seitens PHP, also Upload schonmal in Ordnung.
         */
        if($_FILES['file']['size'] > 20971520) {
          /**
           * Datei über 20 MB groß.
           */
          $content.= "<div class='warnbox'>Datei zu groß. Muss 10 MB oder kleiner sein.</div>".PHP_EOL;
        } else {
          /**
           * Dateigröße passt. Nun wird geprüft welchen Mimetype das Bild hat.
           */
          list($width, $height, $type) = getimagesize($_FILES['file']['tmp_name']);
          if($type === IMAGETYPE_PNG OR $type === IMAGETYPE_JPEG) {
            /**
             * Es liegt ein image/png oder image/jpg Bild vor.
             * Nun werden die Pfad- und Namensvariablen gesetzt und es wird geprüft ob es sich hierbei um einen
             * Thumbnail oder um ein Bild handelt und die Mindestgröße wird abgefragt.
             */
            if($_POST['type'] == 'thumb') {
              /**
               * Prüfen ob ein Thumbnail vorliegt
               */
              $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
              if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result);
                unlink($uploaddir."thumb-".$id."-".$row['filehash'].".png");
                mysqli_query($dbl, "DELETE FROM `images` WHERE `itemid`='".$id."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                $content.= "<div class='infobox'>Der bestehende Thumbnail wurde entfernt.</div>".PHP_EOL;
              }
              /**
               * Mindestgröße 300x300px
               */
              if($width < 300 OR $height < 300) {
                $content.= "<div class='warnbox'>Der Thumbnail ist zu klein. Er muss mindestens 300x300px groß sein.</div>".PHP_EOL;
              } else {
                /**
                 * Thumbnailgröße ok. Nun wird die Bilder-Ressource erstellt.
                 */
                if($type === IMAGETYPE_PNG) {
                  $image = imagecreatefrompng($_FILES['file']['tmp_name']);
                } elseif($type === IMAGETYPE_JPEG) {
                  $image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
                } else {
                  unlink($_FILES['file']['tmp_name']);
                  die();
                }
                
                /**
                 * Die neue Bildressource wird erstellt.
                 */
                $thumb = imagecreatetruecolor(300, 300);
                
                /**
                 * Die Verhältnisse und Startpunkte auf dem Quellbild werden ausgerechnet.
                 */
                if($width >= $height) {
                  $src_x = ($width-$height)/2;
                  $src_y = 0;
                  $src_w = $height;
                  $src_h = $height;
                } else {
                  $src_x = 0;
                  $src_y = ($height-$width)/2;
                  $src_w = $width;
                  $src_h = $width;
                }

                /**
                 * Das Bild wird kopiert, gespeichert, und in die Datenbank eingetragen
                 */
                $filehash = substr(md5(random_bytes(4096)), 0, 16);
                imagecopyresampled($thumb, $image, 0, 0, $src_x, $src_y, 300, 300, $src_w, $src_h);
                imagepng($thumb, $uploaddir."thumb-".$id."-".$filehash.".png");
                imagedestroy($thumb);
                imagedestroy($image);
                unlink($_FILES['file']['tmp_name']);
                mysqli_query($dbl, "INSERT INTO `images` (`itemid`, `thumb`, `filehash`) VALUES ('".$id."', 1, '".$filehash."')") OR DIE(MYSQLI_ERROR($dbl));
                $content.= "<div class='successbox'>Der Thumbnail wurde erfolgreich hochgeladen.</div>".PHP_EOL;
              }
            } else {
              /**
               * Prüfen ob bereits zwei Bilder existieren.
               */
              $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='0'") OR DIE(MYSQLI_ERROR($dbl));
              if(mysqli_num_rows($result) == 2) {
                $content.= "<div class='warnbox'>Es können maximal zwei Bilder pro Rezept hochgeladen werden. Bitte vorher eins löschen.</div>".PHP_EOL;
              } else {
                /**
                 * Mindestgröße 1500x1500px
                 */
                if($width < 1500 OR $height < 1500) {
                  $content.= "<div class='warnbox'>Das Bild ist zu klein. Es muss mindestens 1500x1500px groß sein.</div>".PHP_EOL;
                } else {
                  /**
                   * Bildgröße ok. Nun wird die Bilder-Ressource erstellt.
                   */
                  if($type === IMAGETYPE_PNG) {
                    $image = imagecreatefrompng($_FILES['file']['tmp_name']);
                  } elseif($type === IMAGETYPE_JPEG) {
                    $image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
                  } else {
                    unlink($_FILES['file']['tmp_name']);
                    die();
                  }

                  /**
                   * Die neuen Bildressourcen werden erstellt.
                   */
                  $picture_full = imagecreatetruecolor(1500, 1500);
                  $picture_small = imagecreatetruecolor(600, 600);
                  $picture_medium = imagecreatetruecolor(1000, 1000);
                  $picture_big = imagecreatetruecolor(1200, 1200);

                  /**
                   * Die Verhältnisse und Startpunkte auf dem Quellbild werden ausgerechnet.
                   */
                  if($width >= $height) {
                    $src_x = ($width-$height)/2;
                    $src_y = 0;
                    $src_w = $height;
                    $src_h = $height;
                  } else {
                    $src_x = 0;
                    $src_y = ($height-$width)/2;
                    $src_w = $width;
                    $src_h = $width;
                  }

                  /**
                   * Das Bild wird in alle Formate kopiert und gespeichert.
                   */
                  $filehash = substr(md5(random_bytes(4096)), 0, 16);
                  //full: 1500x1500px
                  imagecopyresampled($picture_full, $image, 0, 0, $src_x, $src_y, 1500, 1500, $src_w, $src_h);
                  imagepng($picture_full, $uploaddir."img-".$id."-full-".$filehash.".png");
                  imagedestroy($picture_full);

                  //small: 600x600px
                  imagecopyresampled($picture_small, $image, 0, 0, $src_x, $src_y, 600, 600, $src_w, $src_h);
                  imagepng($picture_small, $uploaddir."img-".$id."-small-".$filehash.".png");
                  imagedestroy($picture_small);

                  //medium: 1000x1000px
                  imagecopyresampled($picture_medium, $image, 0, 0, $src_x, $src_y, 1000, 1000, $src_w, $src_h);
                  imagepng($picture_medium, $uploaddir."img-".$id."-medium-".$filehash.".png");
                  imagedestroy($picture_medium);

                  //big: 1200x1200px
                  imagecopyresampled($picture_big, $image, 0, 0, $src_x, $src_y, 1200, 1200, $src_w, $src_h);
                  imagepng($picture_big, $uploaddir."img-".$id."-big-".$filehash.".png");
                  imagedestroy($picture_big);

                  /**
                   * Bildressource wieder freigeben und Quelldatei löschen.
                   */
                  imagedestroy($image);
                  unlink($_FILES['file']['tmp_name']);

                  /**
                   * Eintrag in die Datenbank
                   */
                  mysqli_query($dbl, "INSERT INTO `images` (`itemid`, `thumb`, `filehash`) VALUES ('".$id."', 0, '".$filehash."')") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='successbox'>Das Bild wurde erfolgreich hochgeladen.</div>".PHP_EOL;
                }
              }
            }
          } else {
            /**
             * Kein image/png und kein image/jpg Bild
             */
            $content.= "<div class='warnbox'>Es sind nur .png und .jpg Bilder zugelassen.</div>".PHP_EOL;
          }
        }
      } elseif($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        /**
         * Keine Datei geschickt.
         */
        $content.= "<div class='warnbox'>Es wurde keine Datei ausgewählt.</div>".PHP_EOL;
      } else {
        /**
         * Alle anderen Fehler. Sind aber eher unrelevant.
         * https://www.php.net/manual/de/features.file-upload.errors.php
         */
        $content.= "<div class='warnbox'>Fehler beim Upload.</div>".PHP_EOL;
      }
      /**
       * Link zum Zurückkommen.
       */
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } else {
      /**
       * Wenn noch kein Formular abgesendet wurde, dann zeig es an.
       */
      $content.= "<form action='/adminfiles/add/".$id."' method='post' autocomplete='off' enctype='multipart/form-data'>".PHP_EOL;
      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bezeichnung</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'>Feld</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>Ergänzungen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Bild
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Datei</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='hidden' name='MAX_FILE_SIZE' value='20971520'><input type='file' name='file' tabindex='1' autofocus></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* nur `.jpg` und `.png` Dateien erlaubt\n* Thumbnail: Mindestens 300x300px\n* Bild: Mindestens 1500x1500px\n* Jedes Bild wird quadratisch zugeschnitten und automatisch in alle Größen verkleinert\n* Der Zuschnitt richtet sich nach dem Zentrum des Bildes\n* EXIF-Daten werden entfernt\n* Maximal 20MB Dateigröße")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Bild oder Thumb?
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Typ</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='radio' name='type' tabindex='2' id='thumb' value='thumb'><label for='thumb'>Thumbnail</label><br><input type='radio' name='type' tabindex='3' id='pic' value='pic' checked><label for='pic'>Bild</label></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'>".Slimdown::render("* Wenn bereits ein Thumbnail vorhanden ist wird er gelöscht und der neue wird aktiv")."</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-3 col-xl-2'>Bild hochladen</div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-4 col-xl-4'><input type='submit' name='submit' value='Hochladen' tabindex='4'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-4 col-l-5 col-xl-6'></div>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } elseif($_GET['action'] == 'del') {
    /**
     * Datei löschen
     */
    $title = "Dateiverwaltung - Bild löschen";
    $content.= "<h1>Dateiverwaltung - Bild löschen</h1>".PHP_EOL;

    /**
     * Entschärfen der übergebenen Image-ID
     */
    $imageid = (int)defuse($_GET['imageid']);

    /**
     * Prüfen ob ein Eintrag mit der Image-ID und der Item-ID existiert.
     */
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `id`='".$imageid."' AND `itemid`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Bildeintrag existiert.
       */
      if(!isset($_POST['submit'])) {
        /**
         * Formular wurde noch nicht gesendet.
         */
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Soll das Bild wirklich gelöscht werden?.</div>".PHP_EOL.
        "</div>".PHP_EOL;
        /**
         * Es wird ein "verwirrendes" Select-Feld gebaut, damit die "ja"-Option jedes mal woanders steht und man bewusster löscht.
         */
        $options = array(1 => "Ja, wirklich löschen", 2 => "nein, nicht löschen", 3 => "nope", 4 => "auf keinen Fall", 5 => "nö", 6 => "hab es mir anders überlegt");
        $options1 = array();
        foreach($options as $key => $val) {
          $options1[] = "<option value='".$key."'>".$val."</option>".PHP_EOL;
        }
        shuffle($options1);
        $content.= "<form action='/adminfiles/del/".$id."/".$imageid."' method='post' autocomplete='off'>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><select name='selection'>".PHP_EOL."<option value='' selected disabled hidden>Bitte wählen</option>".PHP_EOL.implode("", $options1)."</select></div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-12 col-l-4 col-xl-4'><input type='submit' name='submit' value='Handeln'></div>".PHP_EOL.
        "<div class='col-x-0 col-s-0 col-m-0 col-l-4 col-xl-4'></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      } else {
        /**
         * Formular wurde abgesendet. Jetzt muss das Select Feld geprüft werden.
         */
        if(isset($_POST['selection']) AND $_POST['selection'] == 1) {
          /**
           * Kann gelöscht werden
           */
          $row = mysqli_fetch_array($result);
          array_map('unlink', glob($uploaddir."*-".$row['filehash'].".png"));
          mysqli_query($dbl, "DELETE FROM `images` WHERE `id`='".$imageid."' AND `itemid`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Bild erfolgreich gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          /**
           * Im Select wurde etwas anderes als "ja" ausgewählt.
           */
          $content.= "<div class='infobox'>Bild wurde nicht gelöscht.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminitems/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      }
    } else {
      /**
       * Ungültige Image-ID / Item-ID Kombination.
       */
      http_response_code(404);
      $content.= "<div class='warnbox'>Es gibt kein Bild mit dieser ID-Kombination.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } elseif($_GET['action'] == 'sort') {
    /**
     * Bilder sortieren
     */
    $title = "Dateiverwaltung - Bilder sortieren";
    $content.= "<h1>Dateiverwaltung - Bilder sortieren</h1>".PHP_EOL;

    /**
     * Abfragen ob es Bilder gibt und falls ja wie viele.
     */
    $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemid`='".$id."' AND `thumb`='0' ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
    $imageCount = mysqli_num_rows($result);
    if($imageCount == 0) {
      /**
       * Keine Bilder vorhanden.
       */
      $content.= "<div class='warnbox'>Es existieren keine Bilder für dieses Rezept.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } elseif($imageCount == 1) {
      /**
       * Nur ein Bild vorhanden. Eine Sortierung würde keinen Sinn machen.
       */
      $content.= "<div class='infobox'>Es gibt nur ein Bild für dieses Rezept. Eine Sortierung macht keinen Sinn.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht.</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    } elseif($imageCount == 2) {
      /**
       * Zwei Bilder vorhanden. Sortierung kann stattfinden.
       */
      if(!isset($_POST['submit'])) {
        /**
         * Wenn kein Formular übergeben wurde, dann zeig es an.
         */
        $content.= "<form action='/adminfiles/sort/".$id."' method='post' autocomplete='off'>".PHP_EOL;
        /**
         * Tabellenüberschrift
         */
        $content.= "<div class='row highlight bold bordered'>".PHP_EOL.
        "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'>Sortierindex</div>".PHP_EOL.
        "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'>Bild</div>".PHP_EOL.
        "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
        "</div>".PHP_EOL;
        /**
         * Durchgehen der einzelnen Zuweisungen.
         */
        $tabindex = 0;
        while($row = mysqli_fetch_array($result)) {
          $tabindex++;
          $links = array(
            "<a href='/img/img-".$row['itemid']."-full-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-full-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
            "<a href='/img/img-".$row['itemid']."-big-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-big-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
            "<a href='/img/img-".$row['itemid']."-medium-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-medium-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>",
            "<a href='/img/img-".$row['itemid']."-small-".$row['filehash'].".png' target='_blank'>/img/img-".$row['itemid']."-small-".$row['filehash'].".png<span class='fas iconright'>&#xf35d;</span></a>"
          );
          $content.= "<div class='row hover bordered'>".PHP_EOL.
          "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='number' name='sortIndex[".$row['id']."]' value='".$row['sortIndex']."' min='1' tabindex='".$tabindex."'></div>".PHP_EOL.
          "<div class='col-x-8 col-s-8 col-m-9 col-l-10 col-xl-10'>".implode("<br>", $links)."</div>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-0 col-l-0 col-xl-0'><div class='spacer-s'></div></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
        $tabindex++;
        $content.= "<div class='row hover bordered'>".PHP_EOL.
        "<div class='col-x-4 col-s-4 col-m-3 col-l-2 col-xl-2'><input type='submit' name='submit' value='ändern' tabindex='".$tabindex."'></div>".PHP_EOL.
        "</div>".PHP_EOL;
        $content.= "</form>".PHP_EOL;
      } else {
        /**
         * Formularauswertung
         */
        if(isset($_POST['sortIndex']) AND is_array($_POST['sortIndex'])) {
          asort($_POST['sortIndex']);
          $index = 0;
          $query = "UPDATE `images` SET `sortIndex` = CASE ";
          foreach($_POST['sortIndex'] as $key => $val) {
            $key = (int)defuse($key);
            $index+= 10;
            $query.= "WHEN `id`='".$key."' THEN '".$index."' ";
          }
          $query.= "ELSE '9999999' END WHERE `itemid`='".$id."'";
          mysqli_query($dbl, $query) OR DIE(MYSQLI_ERROR($dbl));
          $content.= "<div class='successbox'>Sortierung geändert.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/list/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        } else {
          $content.= "<div class='warnbox'>Ungültige Werte übergeben.</div>".PHP_EOL;
          $content.= "<div class='row'>".PHP_EOL.
          "<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'><a href='/adminfiles/sort/".$id."'><span class='fas icon'>&#xf359;</span>Zurück zur Sortierung</a></div>".PHP_EOL.
          "</div>".PHP_EOL;
        }
      }
    }
  } else {
    /**
     * Umleitung falls eine action übergeben wurde, aber nichts zutrifft.
     */
    header("Location: /adminfiles/list/".$id);
    die();
  }
}
?>
