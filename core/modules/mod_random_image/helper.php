<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\RandomImage;

use Hubzero\Module\Module;
use stdClass;
use Request;
use Lang;

/**
 * Module class for displaying a random image
 */
class Helper extends Module
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

		if (!count($images))
		{
			echo Lang::txt('MOD_RANDOM_IMAGE_NO_IMAGES');
			return;
		}

		$image = self::getRandomImage($params, $images);
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a random image from a list
	 *
	 * @param   object  $params  Registry
	 * @param   array   $images  List of images in a directory
	 * @return  string
	 */
	public static function getRandomImage(&$params, $images)
	{
		$width  = $params->get('width');
		$height = $params->get('height');

		$i      = count($images);
		$random = mt_rand(0, $i - 1);
		$image  = $images[$random];
		$size   = getimagesize(PATH_APP . DS . $image->folder . DS . $image->name);


		if ($width == '')
		{
			$width = 100;
		}

		if ($size[0] < $width)
		{
			$width = $size[0];
		}

		$coeff = $size[0] / $size[1];
		if ($height == '')
		{
			$height = (int) ($width/$coeff);
		}
		else
		{
			$newheight = min($height, (int) ($width/$coeff));
			if ($newheight < $height)
			{
				$height = $newheight;
			}
			else
			{
				$width = $height * $coeff;
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
	 * @param   object  $params  Registry
	 * @param   string  $folder  Directory to look in
	 * @return  array
	 */
	static function getImages(&$params, $folder)
	{
		$type   = $params->get('type', 'jpg');

		$files  = array();
		$images = array();

		$dir = PATH_APP . DS . $folder;

		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir))
			{
				while (false !== ($file = readdir($handle)))
				{
					if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html')
					{
						$files[] = $file;
					}
				}
			}
			closedir($handle);

			$i = 0;
			foreach ($files as $img)
			{
				if (!is_dir($dir . DS . $img))
				{
					if (preg_match('/' . $type . '/', $img))
					{
						$images[$i] = new stdClass;
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
	 * @param   object  $params  Registry
	 * @return  string
	 */
	static function getFolder(&$params)
	{
		$folder = $params->get('folder');

		$LiveSite = Request::base();

		// if folder includes livesite info, remove
		if (\JString::strpos($folder, $LiveSite) === 0)
		{
			$folder = str_replace($LiveSite, '', $folder);
		}
		// if folder includes absolute path, remove
		if (\JString::strpos($folder, PATH_APP) === 0)
		{
			$folder= str_replace(PATH_APP, '', $folder);
		}
		$folder = str_replace('\\', DIRECTORY_SEPARATOR, $folder);
		$folder = str_replace('/', DIRECTORY_SEPARATOR, $folder);

		return $folder;
	}
}
