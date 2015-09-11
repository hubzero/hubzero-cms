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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Request;

/**
 * Url Asset handler class
 */
class Url extends Content
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
			'action_message' => 'As a standard link',
			'responds_to'    => array('url')
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		$url = Request::getVar('content');

		// Allow for multiple entries at once
		if (strstr($url, ','))
		{
			$urls = explode(',', $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, ' '))
		{
			$urls = explode(' ', $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, "\n"))
		{
			$urls = explode("\n", $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, "\r\n"))
		{
			$urls = explode("\r\n", $url);
			$urls = array_map('trim', $urls);
		}
		else
		{
			$urls = (array) $url;
		}

		$return = array();

		foreach ($urls as $url)
		{
			if (!preg_match('/^(http[s]*\:\/\/)?([0-9A-Za-z\.\/\-\=\:\?\_\&\%\~]+)$/', $url, $matches))
			{
				return array('error'=>'Content did not match the pre-defined filter');
			}

			// Try to help users out by being a little smarter about url provided
			if (!preg_match('/^http[s]*\:\/\//', $url) && strstr($url, '.'))
			{
				$url = 'http://' . $url;
			}

			$this->asset['title']   = $matches[2];
			$this->asset['type']    = 'url';
			$this->asset['subtype'] = 'link';
			$this->asset['url']     = $url;

			// Return info
			$r = parent::create();
			$return[] = $r['assets'];
		}

		return $return;
	}

	/**
	 * Preview method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 **/
	public function preview($asset)
	{
		if (preg_match('/http[s]*:\/\/(www\.)?youtube.com\/watch\?v=([0-9A-Za-z_-]+)/', $asset->get('url'), $matches))
		{
			if (isset($matches[2]) && !empty($matches[2]))
			{
				$content  = '';
				$content .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/';
				$content .= $matches[2];
				$content .= '?rel=0" frameborder="0" allowfullscreen></iframe>';
				return array('type'=>'content', 'value'=>$content);
			}
			else
			{
				return array('type'=>'default');
			}
		}
		else
		{
			return array('type'=>'default');
		}
	}
}