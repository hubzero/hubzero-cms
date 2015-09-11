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
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Store\Helpers;

/**
 * Helper class for misc functions
 */
class Html
{
	/**
	 * Get a default image for the store item
	 *
	 * @param      string $option   Component name
	 * @param      string $item     Item ID
	 * @param      string $root     Root path
	 * @param      string $wpath    Base path for files
	 * @param      string $alt      Image alt text
	 * @param      string $category Item category
	 * @return     string HTML
	 */
	public static function productimage($option, $item, $root, $wpath, $alt, $category)
	{
		if ($wpath)
		{
			$wpath = DS . trim($wpath, DS) . DS;
		}

		$d = @dir($root . $wpath . $item);

		$images = array();
		$html = '';

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file($root . $wpath . $item . DS . $img_file) && substr($entry, 0, 1) != '.' && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png|swf#i", $img_file))
					{
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}
		else
		{
			if ($category == 'service')
			{
				$html = '<span class="nophoto"></span>';
			}
			else
			{
				$html = '<span class="nophoto premium"></span>';
			}
		}

		sort($images);
		$els = '';
		$k = 0;
		$g = 0;

		for ($i=0, $n=count($images); $i < $n; $i++)
		{
			$ext = \Filesystem::extension($images[$i]);
			$tn  = \Filesystem::name($images[$i]) . '-tn.';

			if (!is_file($root . $wpath . $item . DS . $tn . $ext))
			{
				$ext = 'gif';
			}

			$tn = $tn . $ext;

			if (is_file($root . $wpath . $item . DS . $tn))
			{
				$k++;
				$els .= '<a rel="lightbox" href="' . $wpath . $item . '/' . $images[$i] . '" title="' . $alt . '"><img src="' . $wpath . $item . '/' . $tn . '" alt="' . $alt . '" /></a>';
			}
		}

		if ($els)
		{
			$html .= $els;
		}
		return $html;
	}
}

