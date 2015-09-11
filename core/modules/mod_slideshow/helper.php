<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Slideshow;

use Hubzero\Document\Assets;
use Hubzero\Module\Module;
use Filesystem;
use Lang;

/**
 * Module class for displaying a slideshow
 */
class Helper extends Module
{
	/**
	 * Path to find slideshow content
	 *
	 * @var string
	 */
	public $homedir = 'site/slideshow';

	/**
	 * Get module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");

		$image_dir = trim($this->params->get('image_dir', 'site/slideshow'), DS);

		$alias          = $this->params->get('alias', '');
		$height         = $this->params->get('height', '350');
		$width          = $this->params->get('width', '600');
		$timerdelay     = $this->params->get('timerdelay', '10000');
		$transitiontype = $this->params->get('transitiontype', 'fade');
		$random         = $this->params->get('random', 0);
		$xmlPrefix      = "slideshow-data";
		$noflash        = $this->params->get('stype', 0);
		$noflash_link   = $this->params->get('noflash_link', '');

		$swffile = rtrim(Assets::getModuleImage($this->module->module, 'banner' . $width . 'x' . $height . '.swf'), '.swf');

		// check for directory
		if (!is_dir(PATH_APP . DS . $image_dir ))
		{
			if (!Filesystem::makeDirectory(PATH_APP . DS . $image_dir ))
			{
				echo Lang::txt('failed to create image directory') . ' ' . $image_dir;
				$noflash = 1;
			}
			else
			{
				// use default images for this time
				$image_dir = 'modules/mod_slideshow/assets/flash/images';
			}
		}

		$images = array();
		$files = Filesystem::files(PATH_APP . DS . $image_dir, '.', false, true, array());
		if (count($files) == 0)
		{
			$image_dir = 'modules/mod_slideshow/assets/flash/images';
		}

		$noflash_file = 'modules/mod_slideshow/assets/flash/images/default_' . $width . 'x' . $height . '.jpg';

		if (is_dir(PATH_APP . DS . $image_dir))
		{
			$files = Filesystem::files(PATH_APP . DS . $image_dir, '.', false, true);
			foreach ($files as $file)
			{
				if (preg_match("/bmp|gif|jpg|png|swf$/i", strtolower($file)))
				{
					$images[] = $file;
				}
			}

			if (count($images) > 0)
			{
				// pick a random image  to display if flash doesn't work
				$noflash_file = $image_dir . DS . $images[array_rand($images)];

				if ($random)
				{
					// shuffle array
					shuffle($images);
				}

				// start xml output
				if (!$noflash)
				{
					$xml  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
					$xml .= " <slideshow>\n";
					$xml .= " <timerdelay>" . $timerdelay . "</timerdelay>\n";
					$xml .= " <transition>" . $transitiontype . "</transition>\n";
					for ($i=0, $n=count( $images ); $i < $n; $i++)
					{
						if (is_file(PATH_APP . DS . $image_dir . DS . $images[$i]))
						{
							$xml.= " <image src='" . htmlspecialchars($image_dir . DS . $images[$i]) . "'  />\n";
						}
					}
					$xml.= " </slideshow>\n";

					$xmlpath = PATH_APP . DS . $this->homedir . DS . $xmlPrefix;
					$xmlpath.= $alias ? '-' . $alias : '';
					$xmlpath.= '.xml';

					$fh = fopen($xmlpath, "w");
					fwrite($fh,utf8_encode($xml));
					fclose($fh);
				}
			}
			else
			{
				$noflash = 1;
			}
		}
		else
		{
			$noflash = 1;
		}

		if (!$noflash)
		{
			$this->js();
			$this->js('HUB.ModSlideshow.src="' . $swffile . '"; HUB.ModSlideshow.alias="' . $alias . '"; HUB.ModSlideshow.height="' . $height . '"; HUB.ModSlideshow.width="' . $width . '"');
		}

		$this->width = $width;
		$this->height = $height;
		$this->noflash_link = $noflash_link;
		$this->noflash_file = $noflash_file;

		require($this->getLayoutPath());
	}
}
