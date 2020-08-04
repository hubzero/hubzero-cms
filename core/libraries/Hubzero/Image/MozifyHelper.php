<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Image;

/**
 * Helper class for Converting Image to Table Mosaic
 */
class MozifyHelper
{
	/**
	 * Convert images in a string of HTML to mosaics
	 *
	 * @param   string   $html
	 * @param   integer  $mosaicSize
	 * @return  string
	 */
	public static function mozifyHtml($html = '', $mosaicSize = 5)
	{
		//get all image tags
		preg_match_all('/<img src="([^"]*)"([^>]*)>/', $html, $matches, PREG_SET_ORDER);

		//if we have matches mozify the images
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$config = array(
					'imageUrl'   => $match[1],
					'mosaicSize' => $mosaicSize
				);
				$him = new Mozify($config);
				$html = str_replace($match[0], $him->mozify(), $html);
			}
		}

		return $html;
	}
}
