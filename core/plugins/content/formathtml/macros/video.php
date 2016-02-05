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
 * Wiki macro class for displaying a youtube video
 */
class Video extends Macro
{
	/**
	 * Container for parsed attributes
	 *
	 * @var  array
	 */
	protected $attr = array();

	/**
	 * Hubzero\Config\Registry
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Embeds a video into the Page';
		$txt['html'] = '<p>Embeds a video into the Page. Accepts either full video URL (YouTube, Vimeo, Kaltura, Blip TV) or a file name or path.</p>
						<p><strong>Youtube URL:</strong> http://www.youtube.com/watch?v=<span class="highlight">FgfGOEpZEOw</span></p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Video(MyVideo.m4v)]]</code></li>
							<li><code>[[Video(http://www.youtube.com/watch?v=FgfGOEpZEOw)]]</code></li>
							<li><code>[[Video(http://blip.tv/play/hNNNg4uIDAI.x?p=1)]]</code></li>
							<li><code>[[Video(http://player.vimeo.com/video/67115692)]]</code></li>
						</ul>
						<p>Size attributes may be given as single numeric values or with units (%, em, px). When an attribute name is given (e.g., width=600, height=338), order does not matter. Attribute values may be quoted but are not required to be. When a name attribute is not give (e.g., 600, 338), the first value will be set as width and the second value as height.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Video(MyVideo.m4v, width="600", height="338")]]</code></li>
							<li><code>[[Video(http://www.youtube.com/watch?v=FgfGOEpZEOw, width=600px, height=338px)]]</code></li>
							<li><code>[[Video(http://blip.tv/play/hNNNg4uIDAI.x?p=1, 640, 380)]]</code> - width 640px, height 380px</li>
							<li><code>[[Video(http://player.vimeo.com/video/67115692, 100%)]]</code> - width of 100%</li>
						</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		//get the args passed in
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		//declare the partial youtube embed url
		//$youtube_url = 'https://www.youtube.com/embed/';
		$video_url = '';

		//defaults
		$default_width  = 640;
		$default_height = 380;

		$this->config = \Component::params('com_wiki');
		if ($this->filepath != '')
		{
			$this->config->set('filepath', $this->filepath);
		}

		//split up the args
		$args = array_map('trim', explode(',', $content));
		$url  = $args[0];

		// We need to reset thinsg in case of multiple usage of macro
		$this->attr = array();

		// Get single attributes
		// EX: [[Image(myimage.png, nolink, right)]]
		$argues = preg_replace_callback('/[, ](left|right|top|center|bottom|[0-9]+(px|%|em)?)(?:[, ]|$)/i', array(&$this, 'parseSingleAttribute'), $content);
		// Get quoted attribute/value pairs
		// EX: [[Image(myimage.png, desc="My description, contains, commas")]]
		$argues = preg_replace_callback('/[, ](alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap|link)=(?:["\'])([^"]*)(?:["\'])/i', array(&$this, 'parseAttributeValuePair'), $content);
		// Get non-quoted attribute/value pairs
		// EX: [[Image(myimage.png, width=100)]]
		$argues = preg_replace_callback('/[, ](alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap|link)=([^"\',]*)(?:[, ]|$)/i', array(&$this, 'parseAttributeValuePair'), $content);

		$width  = (isset($this->attr['width']) && $this->attr['width'] != '')   ? $this->attr['width']  : $default_width;
		$height = (isset($this->attr['height']) && $this->attr['height'] != '') ? $this->attr['height'] : $default_height;

		//check is user entered full youtube url or just Video Id
		if (!strstr($url, 'http'))
		{
			// File path, so assume local
			if (strstr($url, '/'))
			{
				$video_url = $url;
			}
			else
			{
				// Just a file name.
				$video_url = $this->_path($url);

				if (!file_exists($video_url))
				{
					$video_url = $this->_path($url, true);

					if (!file_exists($video_url))
					{
						return '(video:' . $url . ' not found)' . $this->_path($url);
					}
				}
			}
		}
		else
		{
			$video_url = $url;
		}

		// Default to local
		$type = 'local';

		// YouTube
		if (stristr($video_url, 'youtube'))
		{
			$type = 'youtube';

			if (strstr($video_url, '?'))
			{
				//split the string into two parts
				//uri and query string
				$full_url_parts = explode('?', $video_url);

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
				$video_url = 'https://www.youtube.com/embed/' . $video_id . '?wmode=transparent';
			}
		}
		// Vimeo
		else if (stristr($video_url, 'vimeo'))
		{
			$type = 'vimeo';
		}
		// BlipTV
		else if (stristr($video_url, 'blip'))
		{
			$type = 'blip';
		}
		// Kaltura
		else if (stristr($video_url, 'kaltura'))
		{
			$type = 'kaltura';

			if (!stristr($video_url, 'iframeembed') && stristr($video_url, 'kmc'))
			{
				$partner_id = 0;
				$uiconf_id  = 0;
				$entry_id   = 0;

				$bits = explode('/', $video_url);
				foreach ($bits as $i => $bit)
				{
					if (strtolower($bit) == 'partner_id')
					{
						$partner_id = $bits[$i+1];
					}
					switch (strtolower($bit))
					{
						case 'partner_id':
							$partner_id = $bits[$i+1];
						break;

						case 'uiconf_id':
							$uiconf_id = $bits[$i+1];
						break;

						case 'entry_id':
							$entry_id = $bits[$i+1];
						break;
					}
				}
				$video_url = 'https://www.kaltura.com/p/' . $partner_id . '/sp/' . $partner_id . '00/embedIframeJs/uiconf_id/' . $uiconf_id . '/partner_id/' . $partner_id . '?iframeembed=true&playerId=movie' . rand(0, 1000) . '&entry_id=' . $entry_id . '&flashvars[autoPlay]=false';
			}
		}

		// Create the embed

		// Local
		if ($type == 'local')
		{
			$ext = strtolower(\Filesystem::extension($video_url));

			\Document::addStyleSheet('//releases.flowplayer.org/5.4.2/skin/minimalist.css');
			\Document::addScript('//releases.flowplayer.org/5.4.2/flowplayer.min.js');

			$html  = '<div class="flowplayer" style="width: ' . $width . 'px; height: ' . $height . 'px;">';
			$html .= '<video id="movie' . rand(0, 1000) . '" width="' . $width . '" height="' . $height . '" preload controls>';
			switch ($ext)
			{
				case 'mov':
				case 'mp4':
				case 'm4v':
					$html .= '<source src="' . $this->_link($url) . '" type="video/mp4" />';
				break;

				case 'ogg':
				case 'ogv':
					$html .= '<source src="' . $this->_link($url) . '" type="video/ogg" />';
				break;

				case 'webm':
					$html .= '<source src="' . $this->_link($url) . '" type="video/webm" />';
				break;
			}
			$html .= '</video>';
			$html .= '</div>';
		}
		// External
		else
		{
			$html = '<iframe id="movie' . rand(0, 1000) . '" src="' . $video_url . '" width="' . $width . '" height="' . $height . '" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}

		// Return the emdeded youtube video
		return $html;
	}

	/**
	 * Generate an absolute path to a file stored on the system
	 * Assumes $file is relative path but, if $file starts with / then assumes absolute
	 *
	 * @param   string  $file  Filename
	 * @param   bool    $alt
	 * @return  string
	 */
	private function _path($file, $alt=false)
	{
		if (substr($file, 0, 1) == DS)
		{
			$path = PATH_APP . $file;
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
			$path  = PATH_APP . DS . trim($this->config->get('filepath', '/site/wiki'), DS);
			$path .= ($this->pageid) ? DS . $this->pageid : '';
			$path .= DS . $file;
		}

		return $path;
	}

	/**
	 * Parse attribute=value pairs
	 * EX: [[Image(myimage.png, desc="My description, contains, commas", width=200)]]
	 *
	 * @param   array  $matches  Values matching attr=val pairs
	 * @return  void
	 */
	public function parseAttributeValuePair($matches)
	{
		$key = strtolower(trim($matches[1]));
		$val = trim($matches[2]);

		$size   = '/^[0-9]+(%|px|em)$/';
		$attrs  = '/(alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap)=(.+)/';
		$quoted = "/(?:[\"'])(.*)(?:[\"'])$/";

		// Set width if just a pixel size is given
		// e.g., [[File(myfile.jpg, width=120px)]]
		if (preg_match($size, $val, $matches) && $key != 'border')
		{
			if ($matches[0])
			{
				$at = (isset($this->attr['width'])) ? 'height' : 'width';
				$this->attr['style'][$at] = $val;
				$this->attr[$at] = $val;
				return;
			}
		}

		// Set width if just a numeric size is given
		// e.g., [[File(myfile.jpg, width=120)]]
		if (is_numeric($val))
		{
			$at = (isset($this->attr['width'])) ? 'height' : 'width';
			$this->attr['style'][$at] = $val . 'px';
			$this->attr[$at] = $val;
			return;
		}

		// Check for alignment, no key given
		// e.g., [[File(myfile.jpg, align=left)]]
		if (in_array($key, array('left', 'right', 'top', 'bottom', 'center')))
		{
			if ($key == 'center')
			{
				$this->attr['style']['display'] = 'block';
				$this->attr['style']['margin-right'] = 'auto';
				$this->attr['style']['margin-left'] = 'auto';
			}
			else
			{
				$this->attr['style']['float'] = $key;
				if ($key == 'left')
				{
					$this->attr['style']['margin-right'] = '1em';
				}
				else if ($key == 'right')
				{
					$this->attr['style']['margin-left'] = '1em';
				}
			}
			return;
		}

		// Look for any other attributes
		if ($key == 'align')
		{
			if ($val == 'center')
			{
				$this->attr['style']['display'] = 'block';
				$this->attr['style']['margin-right'] = 'auto';
				$this->attr['style']['margin-left'] = 'auto';
			}
			else
			{
				$this->attr['style']['float'] = $val;
				if ($val == 'left')
				{
					$this->attr['style']['margin-right'] = '1em';
				}
				else if ($val == 'right')
				{
					$this->attr['style']['margin-left'] = '1em';
				}
			}
		}
		else if ($key == 'border')
		{
			$this->attr['style']['border'] = '#ccc ' . intval($val) . 'px solid';
		}
		else
		{
			$this->attr[$key] = $val;
		}
	}

	/**
	 * Handle single attribute values
	 * EX: [[Image(myimage.png, nolink, right)]]
	 *
	 * @param   array  $matches  Values matching the single attribute pattern
	 * @return  void
	 */
	public function parseSingleAttribute($matches)
	{
		$key = strtolower(trim($matches[1]));

		// Set width if just a pixel size is given
		// e.g., [[File(myfile.jpg, 120px)]]
		if (preg_match('/[0-9+](%|px|em)$/', $key, $matches))
		{
			if ($matches[0])
			{
				$at = (isset($this->attr['width'])) ? 'height' : 'width';
				$this->attr['style'][$at] = $key;
				$this->attr[$at] = $key;
				return;
			}
		}

		// Set width if just a numeric size is given
		// e.g., [[File(myfile.jpg, 120)]]
		if (is_numeric($key))
		{
			$at = (isset($this->attr['width'])) ? 'height' : 'width';
			$this->attr['style'][$at] = $key . 'px';
			$this->attr[$at] = $key;
			return;
		}

		// Check for alignment, no key given
		// e.g., [[File(myfile.jpg, left)]]
		if (in_array($key, array('left', 'right', 'top', 'bottom', 'center')))
		{
			if ($key == 'center')
			{
				$this->attr['style']['display'] = 'block';
				$this->attr['style']['margin-right'] = 'auto';
				$this->attr['style']['margin-left'] = 'auto';
			}
			else
			{
				$this->attr['style']['display'] = 'block';
				$this->attr['style']['float'] = $key;
				if ($key == 'left')
				{
					$this->attr['style']['margin-right'] = '1em';
				}
				else if ($key == 'right')
				{
					$this->attr['style']['margin-left'] = '1em';
				}
			}
		}
	}

	/**
	 * Generate a link to a file
	 * If $file starts with (http|https|mailto|ftp|gopher|feed|news|file), then it's an external URL and returned
	 *
	 * @param   string  $file  Filename
	 * @return  string
	 */
	private function _link($file)
	{
		$urlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|feed:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		if (preg_match("/$urlPtrn/", $file) || substr($file, 0, 1) == DS)
		{
			return $file;
		}

		$file = trim($file, DS);

		$link  = DS . substr($this->option, 4, strlen($this->option)) . DS;
		if ($this->scope)
		{
			$scope = trim($this->scope, DS);

			$link .= $scope . DS;
		}
		$type = 'File';
		$this->imgs = array('jpg', 'jpe', 'jpeg', 'gif', 'png');
		if (in_array(strtolower(\Filesystem::extension($file)), $this->imgs))
		{
			if (\Request::getVar('format') == 'pdf')
			{
				return $this->_path($file);
			}
			$type = 'Image';
		}
		$link .= $this->pagename . DS . $type . ':' . $file;

		return \Route::url($link);
	}
}
