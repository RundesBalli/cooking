<?php
/**
 * adminFiles/add.php
 * 
 * Hinzufügen eines Bildes zu einem Rezept
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(PAGE_INCLUDE_DIR.'adminCookie.php');

/**
 * Laden der zusätzlichen CSS Datei für die Inputfelder
 */
$additionalStyles[] = "input";

$title = "Dateiverwaltung - Bild hinzufügen";
$content.= "<h1>Dateiverwaltung - Bild hinzufügen</h1>";

/**
 * Prüfen ob eine id übergeben wurde
 */
if(!empty($_GET['id'])) {
  /**
   * Es wurde eine ID übergeben, jetzt wird geprüft ob das Rezept existiert.
   */
  $id = (int)defuse($_GET['id']);

  $result = mysqli_query($dbl, "SELECT `id`, `title`, `shortTitle` FROM `items` WHERE `id`='".$id."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));

  if(mysqli_num_rows($result) == 0) {
    /**
     * Das Rezept existiert nicht, daher wird ein 404er und eine Fehlermeldung zurückgegeben.
     */
    http_response_code(404);
    $content.= "<div class='warnbox'>Das Rezept mit der ID <span class='italic'>".output($id)."</span> existiert nicht.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Das Rezept existiert. Anzeige grundlegender Infos.
     */
    $row = mysqli_fetch_assoc($result);
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><span class='highlight bold'>Rezept:</span> <a href='/rezept/".output($row['shortTitle'])."' target='_blank'>".output($row['title'])."<span class='fas iconright'>&#xf35d;</span></a></div>".
    "</div>";
    $content.= "<div class='spacer-m'></div>";

    if(isset($_POST['submit'])) {
      /**
       * Formular wurde abgesendet, Upload verarbeiten.
       */
      if($_POST['token'] == $sessionHash) {
        if($_FILES['file']['error'] === UPLOAD_ERR_OK) {
          /**
           * Keine Fehlermeldung seitens PHP, also Upload schonmal in Ordnung.
           */
          if($_FILES['file']['size'] > $maxFileSize) {
            /**
             * Datei zu groß.
             */
            $content.= "<div class='warnbox'>Datei zu groß. Muss ".number_format($maxFileSize, 0, ",", ".")." Bytes".(($maxFileSize/1024/1024) > 0 ? " (".number_format(($maxFileSize/1024/1024), 1, ",", ".")." MB)" : NULL)." oder kleiner sein.</div>";
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
                 * Prüfen ob ein Thumbnail vorliegt. Falls ja wird er gelöscht.
                 */
                $result = mysqli_query($dbl, "SELECT * FROM `images` WHERE `itemId`='".$id."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                if(mysqli_num_rows($result) == 1) {
                  $row = mysqli_fetch_array($result);
                  unlink($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."thumb-".$id."-".$row['fileHash'].".png");
                  mysqli_query($dbl, "DELETE FROM `images` WHERE `itemId`='".$id."' AND `thumb`='1' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='infobox'>Der bestehende Thumbnail wurde entfernt.</div>";
                }

                /**
                 * Mindestgröße 300x300px
                 */
                if($width < 300 OR $height < 300) {
                  $content.= "<div class='warnbox'>Der Thumbnail ist zu klein. Er muss mindestens 300x300px groß sein.</div>";
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
                  $fileHash = substr(md5(random_bytes(4096)), 0, 16);
                  imagecopyresampled($thumb, $image, 0, 0, $src_x, $src_y, 300, 300, $src_w, $src_h);
                  imagepng($thumb, $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."thumb-".$id."-".$fileHash.".png");
                  imagedestroy($thumb);
                  imagedestroy($image);
                  unlink($_FILES['file']['tmp_name']);
                  mysqli_query($dbl, "INSERT INTO `images` (`itemId`, `thumb`, `fileHash`) VALUES ('".$id."', 1, '".$fileHash."')") OR DIE(MYSQLI_ERROR($dbl));
                  mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 2, ".$id.", 'Thumbnail hinzugefügt')") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='successbox'>Der Thumbnail wurde erfolgreich hochgeladen.</div>";
                }
              } else {
                /**
                 * Mindestgröße 600x600px
                 */
                if($width < 600 OR $height < 600) {
                  $content.= "<div class='warnbox'>Das Bild ist zu klein. Es muss mindestens 600x600px groß sein.</div>";
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
                   * Die neue Bildressource wird erstellt.
                   */
                  $newImage = imagecreatetruecolor(600, 600);

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
                   * Das Bild wird in das Endformat verkleinert
                   */
                  $fileHash = substr(md5(random_bytes(4096)), 0, 16);
                  //600x600px
                  imagecopyresampled($newImage, $image, 0, 0, $src_x, $src_y, 600, 600, $src_w, $src_h);
                  imagepng($newImage, $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."img-".$id."-".$fileHash.".png");
                  imagedestroy($newImage);

                  /**
                   * Bildressource wieder freigeben und Quelldatei löschen.
                   */
                  imagedestroy($image);
                  unlink($_FILES['file']['tmp_name']);

                  /**
                   * Bildbeschreibung
                   */
                  if(!empty($_POST['description'])) {
                    if(preg_match('/^.{2,100}$/', $_POST['description'], $match) === 1) {
                      $description = defuse($match[0]);
                    } else {
                      $description = NULL;
                      $content.= "<div class='infobox'>Die Bildbeschreibung ist ungültig und wurde entfernt. Sie darf nur zwischen 2 und 100 Zeichen beinhalten.</div>";
                    }
                  } else {
                    $description = NULL;
                  }

                  /**
                   * Eintrag in die Datenbank
                   */
                  mysqli_query($dbl, "INSERT INTO `images` (`itemId`, `thumb`, `fileHash`, `description`) VALUES ('".$id."', 0, '".$fileHash."', ".($description != NULL ? "'".$description."'" : "NULL").")") OR DIE(MYSQLI_ERROR($dbl));
                  mysqli_query($dbl, "INSERT INTO `log` (`accountId`, `logLevel`, `itemId`, `text`) VALUES ('".$userId."', 2, ".$id.", 'Bild hinzugefügt')") OR DIE(MYSQLI_ERROR($dbl));
                  $content.= "<div class='successbox'>Das Bild wurde erfolgreich hochgeladen.</div>";
                }
              }
            } else {
              /**
               * Kein image/png und kein image/jpg Bild
               */
              $content.= "<div class='warnbox'>Es sind nur .png und .jpg Bilder zugelassen.</div>";
            }
          }
        } elseif($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
          /**
           * Keine Datei geschickt.
           */
          $content.= "<div class='warnbox'>Es wurde keine Datei ausgewählt.</div>";
        } else {
          /**
           * Alle anderen Fehler. Sind aber eher unrelevant.
           * https://www.php.net/manual/de/features.file-upload.errors.php
           */
          $content.= "<div class='warnbox'>Fehler beim Upload.</div>";
        }
      } else {
        /**
         * Ungültiges Sitzungstoken
         */
        http_response_code(403);
        $content.= "<div class='warnbox'>Ungültiges Token.</div>";
      }

      /**
       * Links zum Zurückkommen.
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/add?id=".output($id)."'><span class='fas icon'>&#xf302;</span>Ein weiteres Bild hochladen</a></div>".
      "<div class='col-s-12 col-l-12'><a href='/adminFiles/show?id=".output($id)."'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
      "</div>";
    } else {
      /**
       * Wenn noch kein Formular abgesendet wurde, dann zeig es an.
       */
      $content.= "<form action='/adminFiles/add?id=".output($id)."' method='post' autocomplete='off' enctype='multipart/form-data'>";

      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionHash."'>";

      /**
       * Tabellenüberschrift
       */
      $content.= "<div class='row highlight bold bordered'>".
      "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
      "<div class='col-s-12 col-l-4'>Feld</div>".
      "<div class='col-s-12 col-l-5'>Ergänzungen</div>".
      "</div>";

      /**
       * Bild
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Datei</div>".
      "<div class='col-s-12 col-l-4'><input type='hidden' name='MAX_FILE_SIZE' value='".$maxFileSize."'><input type='file' name='file' tabindex='1' autofocus></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* nur `.jpg` und `.png` Dateien erlaubt\n* Thumbnail: mindestens 300x300px\n* Bild: mindestens 600x600px\n* Jedes Bild wird quadratisch zugeschnitten und automatisch verkleinert\n* Der Zuschnitt richtet sich nach dem Zentrum des Bildes\n* EXIF-Daten werden entfernt\n* Maximal ".number_format($maxFileSize, 0, ",", ".")." Bytes".(($maxFileSize/1024/1024) > 0 ? " (".number_format(($maxFileSize/1024/1024), 1, ",", ".")." MB)" : NULL)." Dateigröße")."</div>".
      "</div>";

      /**
       * Bild oder Thumb?
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Typ</div>".
      "<div class='col-s-12 col-l-4'><input type='radio' name='type' tabindex='2' id='thumb' value='thumb'><label for='thumb'>Thumbnail</label><br><input type='radio' name='type' tabindex='3' id='pic' value='pic' checked><label for='pic'>Bild</label></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* Wenn bereits ein Thumbnail vorhanden ist wird er gelöscht und der neue wird aktiv")."</div>".
      "</div>";

      /**
       * Bildbeschreibung bei "Bild"
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Bildbeschreibung</div>".
      "<div class='col-s-12 col-l-4'><input type='text' name='description' tabindex='3' placeholder='Bildbeschreibung'></div>".
      "<div class='col-s-12 col-l-5'>".Slimdown::render("* nur bei Bildern möglich, nicht bei Thumbnails\n* Wird am unteren Bildrand eingeblendet\n* Kein Markdown möglich\n* Spätere Änderung nicht möglich")."</div>".
      "</div>";

      /**
       * Absenden
       */
      $content.= "<div class='row hover bordered'>".
      "<div class='col-s-12 col-l-3'>Bild hochladen</div>".
      "<div class='col-s-12 col-l-4'><input type='submit' name='submit' value='Hochladen' tabindex='4'></div>".
      "<div class='col-s-12 col-l-5'></div>".
      "</div>";
    }
  }
} else {
  /**
   * Es wurde keine ID übergeben.
   */
  http_response_code(400);
  $content.= "<div class='warnbox'>Es wurde keine ID übergeben.</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/adminItems/show'><span class='fas icon'>&#xf359;</span>Zurück zur Übersicht</a></div>".
  "</div>";
}
?>
