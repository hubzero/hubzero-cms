<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
