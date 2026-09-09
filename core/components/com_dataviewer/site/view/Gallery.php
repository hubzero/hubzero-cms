<?php

/**
 * Image gallery view for the Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\View;

use Components\Dataviewer\Site\DvConfig;

class Gallery
{
    /**
     * Supported image extensions.
     *
     * @var  array
     */
    private static $imageTypes = ['png', 'gif', 'jpg', 'jpeg'];

    /**
     * Render the gallery page.
     *
     * @param   string  $hash  Session hash for the gallery path
     * @return  void
     */
    public static function render($hash)
    {
        $galleryList = \Hubzero\Facades\Session::get('dv.gallery.list', []);
        if (!isset($galleryList[$hash])) {
            print "<h2>Invalid Gallery ID or Your session may have expired</h2>"
                . "Please close this window and refresh the previous View/Page.";
            exit();
        }

        $httpPath = $galleryList[$hash];
        $pathParts = explode('/site/', $httpPath);
        $realPath = '/site/' . $pathParts[1];

        $absPath = PATH_ROOT . $realPath;
        if (!is_dir($absPath)) {
            print "<h2>Error: Missing images.</h2>";
            return;
        }

        // Scan directory for images using PHP instead of shell `find`
        $fileList = self::scanForImages($absPath);

        $imageList = [];
        $imageViewer = [];

        foreach ($fileList as $file) {
            $pi = pathinfo($file);
            $dirName = str_replace(PATH_ROOT, '', $pi['dirname']);
            $ext = strtolower($pi['extension']);

            $descFile = $pi['dirname'] . '/' . $pi['filename'] . '.txt';
            $desc = '';
            if (file_exists($descFile)) {
                $desc = htmlentities(
                    file_get_contents($descFile),
                    ENT_QUOTES,
                    'UTF-8'
                );
            }

            if (in_array($ext, self::$imageTypes)) {
                $basename = htmlspecialchars(
                    $pi['basename'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                $imageList[] = '<img title="' . $desc
                    . '" alt="' . $basename
                    . '" src="' . $dirName . '/small/' . $basename . '" />';
                $imageViewer[] = '<a title="Click to view the original image."'
                    . ' target="_blank" href="' . $dirName . '/' . $basename
                    . '"><img alt="' . $basename . '" src="' . $dirName
                    . '/medium/' . $basename
                    . '" style="display:none;" /></a>';
            }
        }

        $htmlPath = DvConfig::$html_path;
        ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Dataview: Image gallery</title>
        <link rel="stylesheet" type="text/css"
            href="/core/assets/css/jquery.ui.min.css" />
        <link rel="stylesheet" type="text/css"
            href="<?php echo htmlspecialchars($htmlPath); ?>/css/gallery.css" />
        <script src="/core/assets/js/jquery.js"></script>
        <script src="/core/assets/js/jquery.ui.min.js"></script>
        <script src="<?php echo htmlspecialchars($htmlPath); ?>/js/gallery.js"></script>
    </head>
    <body>
    <div id="dv_wrapper" class="ui-widget ui-widget-content ui-corner-all">
        <div id="dv_gallery_list" class="ui-widget ui-widget-header ui-corner-top">
            <table style="padding:0; margin:0;">
                <tr>
                    <?php foreach ($imageList as $img) : ?>
                    <td><?php echo $img; ?></td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </div>

        <div id="dv_gallery_viewer">
            <br />
            <?php echo implode("\n", $imageViewer); ?>
            <br />
            <div id="dv_gallery_desc" class="ui-widget ui-widget-content ui-corner-all"
                style="display:none; margin: 0 20px; border-style: inset;">
                The description will be displayed here...
            </div>
            <br />
        </div>

        <div class="dv_gallery_toolbar ui-widget ui-widget-header ui-corner-bottom">
            <span id="dv_gallery_dl_image">
                <a href="" target="_blank">
                    <img src="<?php echo htmlspecialchars($htmlPath); ?>/img/download-l.png"
                        alt="Click here to download the full size image."
                        title="Download Original Image (Right click and save image)"
                        style="border: 1px #DDD solid;" />
                </a>
            </span>
            &nbsp;
            <input type="checkbox" id="description" /><label for="description">Description</label>
            [ <span id="color">Background :
                <input type="radio" id="color1" name="color" value="#3C3C3C" checked="checked" />
                <label for="color1">Dark</label>
                <input type="radio" id="color2" name="color" value="#ECECEC" />
                <label for="color2">Light</label>
            </span> ]
            &nbsp;&nbsp;&nbsp;
            <button type="button" id="dv-gallery-close" style="color: red;">Close Window</button>
        </div>
    </div>
    <script>
        document.getElementById('dv-gallery-close').addEventListener('click', function() {
            window.close();
        });
    </script>
    </body>
</html>
        <?php
        exit(0);
    }

    /**
     * Recursively scan a directory for image files, excluding
     * 'small' and 'medium' thumbnail subdirectories.
     *
     * @param   string  $dir  Directory to scan
     * @return  array   List of absolute file paths
     */
    private static function scanForImages(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();

            // Skip thumbnail directories
            if (strpos($path, '/small/') !== false
                || strpos($path, '/medium/') !== false
            ) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (in_array($ext, self::$imageTypes)) {
                $files[] = $path;
            }
        }

        sort($files);
        return $files;
    }
}
