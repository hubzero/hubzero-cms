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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin to force .rss URLs to raw document mode
 */
class plgSystemXFeed extends \Hubzero\Plugin\Plugin
{
	/**
	 * Perform actions after initialization
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$bits = explode('?', $uri);
		$bit = $bits[0];

		if (!strpos($bit, '.'))
		{
			return;
		}

		$bi = explode('.', $bit);
		$b = end($bi);
		$b = strtolower($b);

		if ($b == 'rss' || $b == 'atom')
		{
			$_GET['no_html'] = 1;
			$_GET['format']  = 'raw';
			$_REQUEST['no_html'] = 1;
			$_REQUEST['format']  = 'raw';
		}
	}
}

