<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki macro class for displaying a youtube video
 */
class VideoMacro extends WikiMacro
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
						<iframe src="http://youtube.com/embed/FgfGOEpZEOw" width="640px" height="390px" border="0"></iframe>';

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
		//$youtube_url = 'https://www.youtube.com/embed/';
		$video_url = '';

		//defaults 
		$default_width  = 640;
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
		if (!strstr($url, 'http')) 
		{
			/*if (strstr($url, '?'))
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
			}*/
			// File path, so assume local
			if (strstr($url, '/'))
			{
				$video_url = $url;
			}
			else
			{
				// Just a file name.
				$video_url = $this->_path($video_url);

				if (!file_exists($video_url))
				{
					$video_url = $this->_path($video_url, true);

					if (!file_exists($video_url))
					{
						return '(video:' . $url . ' not found)'. $this->_path($video_url);
					}
				}
			}
		} 
		else 
		{
			$video_url = $url;
		}

		$type = 'local';

		if (stristr($video_url, 'youtube'))
		{
			$type = 'youtube';
		}
		else if (stristr($video_url, 'vimeo'))
		{
			$type = 'vimeo';
		}
		else if (stristr($video_url, 'blip'))
		{
			$type = 'blip';
		}
		else if (stristr($video_url, 'kaltura'))
		{
			$type = 'kaltura';
		}

		if ($type == 'local')
		{
			jimport('joomla.filesystem.file');
			$ext = strtolower(JFile::getExt($video_url));

			$html = '<video id="movie' . rand(0, 1000) . '" width="' . $width . '" height="' . $height . '" preload controls>';
			switch ($ext)
			{
				case 'mov':
				case 'mp4':
				case 'm4v':
					$html .= '<source src="' . $video_url . '" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\' />';
				break;

				case 'ogv':
					$html .= '<source src="' . $video_url . '" type=\'video/ogg; codecs="theora, vorbis"\' />';
				break;

				case 'webm':
					$html .= '<source src="' . $video_url . '" type=\'video/webm; codecs="vp8, vorbis"\' />';
				break;
			}
			$html .= '<object type="application/x-shockwave-flash" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" width="' . $width . '" height="' . $height . '">
					<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
					<param name="allowFullScreen" value="true" />
					<param name="wmode" value="transparent" />
					<param name="flashvars" value=\'config={"clip":{"url":"' . $video_url . '","autoPlay":false,"autoBuffering":true}}\' />
				</object>
			</video>';
		}
		else
		{
			//add wmode to url so that lightboxes appear over embedded videos
			$video_url .= strstr($url, '?') ? '?' : '&';
			$video_url .= 'wmode=transparent';

			$html = '<iframe id="movie' . rand(0, 1000) . '" src="' . $video_url . '" width="' . $width . '" height="' . $height . '" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}

		//return the emdeded youtube video
		return $html;
	}

	/**
	 * Generate an absolute path to a file stored on the system
	 * Assumes $file is relative path but, if $file starts with / then assumes absolute
	 * 
	 * @param      $file  Filename
	 * @return     string
	 */
	private function _path($file, $alt=false)
	{
		if (substr($file, 0, 1) == DS) 
		{
			$path = JPATH_ROOT . $file;
		}
		else 
		{
			if ($alt)
			{
				$nid = null;
				$bits = explode('/', $this->config->get('filepath', '/site/wiki'));
				foreach ($bits as $bit)
				{
					if (is_numeric($bit))
					{
						$nid = $bit;
						$id = preg_replace('~^[0]*([1-9][0-9]*)$~', '$1', intval($bit));
						break;
					}
				}

				if ($nid)
				{
					$this->config->set('filepath', str_replace($nid, $id, $this->config->get('filepath')));
				}
			}
			$path  = JPATH_ROOT . DS . trim($this->config->get('filepath', '/site/wiki'), DS);
			$path .= ($this->pageid) ? DS . $this->pageid : '';
			$path .= DS . $file;
		}

		return $path;
	}
}
