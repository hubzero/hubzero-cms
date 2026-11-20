<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RandomImage;

use Hubzero\Module\Module;
use stdClass;
use Hubzero\Facades\Request;
use Hubzero\Facades\Lang;

/**
 * Module class for displaying a random image
 */
class RandomImage extends Module
{
    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        // [!] Legacy compatibility
        $params = $this->params;

        $link   = $params->get('link');

        $folder = self::getFolder($params);
        $images = self::getImages($params, $folder);

        if (!count($images)) {
            echo Lang::txt('MOD_RANDOM_IMAGE_NO_IMAGES');
            return;
        }

        $image = self::getRandomImage($params, $images);
        $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));

        require $this->getLayoutPath($params->get('layout', 'default'));
    }

    /**
     * Get a random image from a list
     *
     * @param   \Hubzero\Config\Registry  $params  Registry
     * @param   array<mixed>   $images  List of images in a directory
     * @return  string
     */
    public static function getRandomImage(&$params, $images)
    {
        $width  = $params->get('width');
        $height = $params->get('height');

        $i      = count($images);
        $random = random_int(0, $i - 1);
        $image  = $images[$random];
        $size   = getimagesize(PATH_APP . DS . $image->folder . DS . $image->name) ?: [0, 0];


        if ($width == '') {
            $width = 100;
        }

        // An image PHP can't read (getimagesize() failed) has no proportions to keep:
        // it is shown at the configured size
        if ($size[0] > 0 && $size[1] > 0) {
            if ($size[0] < $width) {
                $width = $size[0];
            }

            $coeff = $size[0] / $size[1];
            if ($height == '') {
                $height = (int) ($width / $coeff);
            } else {
                $newheight = min($height, (int) ($width / $coeff));
                if ($newheight < $height) {
                    $height = $newheight;
                } else {
                    $width = $height * $coeff;
                }
            }
        }

        $image->width  = $width;
        $image->height = $height;
        $image->folder = str_replace('\\', '/', $image->folder);

        return $image;
    }

    /**
     * Get a list of images from a folder
     *
     * @param   \Hubzero\Config\Registry  $params  Registry
     * @param   string  $folder  Directory to look in
     * @return  array<mixed>
     */
    public static function getImages(&$params, $folder)
    {
        $type   = $params->get('type', 'jpg');

        $files  = [];
        $images = [];

        $dir = PATH_APP . DS . $folder;

        // check if directory exists
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html') {
                        $files[] = $file;
                    }
                }
                closedir($handle);
            }

            $i = 0;
            foreach ($files as $img) {
                if (!is_dir($dir . DS . $img)) {
                    if (preg_match('/' . $type . '/', $img)) {
                        $images[$i] = new stdClass();
                        $images[$i]->name   = $img;
                        $images[$i]->folder = $folder;
                        $i++;
                    }
                }
            }
        }

        return $images;
    }

    /**
     * Get a folder
     *
     * @param   \Hubzero\Config\Registry  $params  Registry
     * @return  string
     */
    public static function getFolder(&$params)
    {
        $folder = (string) $params->get('folder');

        $LiveSite = Request::base();

        // if folder includes livesite info, remove
        if (str_starts_with($folder, $LiveSite)) {
            $folder = str_replace($LiveSite, '', $folder);
        }
        // if folder includes absolute path, remove
        if (str_starts_with($folder, PATH_APP)) {
            $folder = str_replace(PATH_APP, '', $folder);
        }
        $folder = str_replace('\\', DIRECTORY_SEPARATOR, $folder);
        return str_replace('/', DIRECTORY_SEPARATOR, $folder);
    }
}
