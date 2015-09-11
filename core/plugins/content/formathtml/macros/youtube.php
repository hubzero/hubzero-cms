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

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * macro class for displaying a youtube video
 */
class Youtube extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Embeds a Youtube Video into the Page';
		$txt['html'] = '<p>Embeds a Youtube Video into the Page. Accepts either full Youtube video URL or just Youtube Video ID (highlighted below).</p>
						<p><strong>Youtube URL:</strong> http://www.youtube.com/watch?v=<span class="highlight">FgfGOEpZEOw</span></p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Youtube(FgfGOEpZEOw)]]</code></li>
							<li><code>[[Youtube(http://www.youtube.com/watch?v=FgfGOEpZEOw)]]</code></li>
							<li><code>[[Youtube(FgfGOEpZEOw, 640, 380)]] - width 640px, height 380px</code></li>
							<li><code>[[Youtube(FgfGOEpZEOw, 100%)]] - width of 100%</code></li>
						</ul>
						<p>Displays:</p>
						<iframe src="https://youtube.com/embed/FgfGOEpZEOw" width="640px" height="390px" border="0"></iframe>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$content = $this->args;

		//declare the partial youtube embed url
		$youtube_url = 'https://www.youtube.com/embed/';

		//defaults
		$default_width = 640;
		$default_height = 380;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		//split up the args
		$args = array_map('trim', explode(',', $content));
		$url  = $args[0];

		$width  = (isset($args[1]) && $args[1] != '') ? $args[1] : $default_width;
		$height = (isset($args[2]) && $args[2] != '') ? $args[2] : $default_height;

		//check is user entered full youtube url or just Video Id
		if (strstr($url, 'http'))
		{
			//split the string into two parts
			//uri and query string
			$full_url_parts = explode('?', $url);

			//split apart any key=>value pairs in query string
			$query_string_parts = explode("%26%2338%3B", urlencode($full_url_parts[1]));

			//foreach query string parts
			//explode at equals sign
			//check to see if v is the first part and if it is set the second part to the video id
			foreach ($query_string_parts as $qsp)
			{
				$pairs_parts = explode("%3D", $qsp);
				if ($pairs_parts[0] == 'v')
				{
					$video_id = $pairs_parts[1];
					break;
				}
			}
		}
		else
		{
			$video_id = $url;
		}

		//append to the youtube url
		$youtube_url .= $video_id;

		//add wmode to url so that lightboxes appear over embedded videos
		$youtube_url .= '?wmode=transparent';

		//return the emdeded youtube video
		return '<iframe src="' . $youtube_url . '" width="' . $width . '" height="' . $height . '"></iframe>';
	}
}
