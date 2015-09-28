<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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