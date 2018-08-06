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
 * A wiki macro for embedding or linking to files
 */
class File extends Macro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  string
	 */
	public $allowPartial = true;

	/**
	 * Container for element attributes
	 *
	 * @var  array
	 */
	private $attr = array();

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro alternately generates a link to a file or embeds the file for display (only valid for CDF files and images). The first argument is the filename.';
		$txt['html'] = '<p>This macro alternately generates a link to a file or embeds the file for display (only valid for CDF files and images). The first argument is the filename.</p>
			<ul>
				<li>digits and unit are interpreted as the <code>width</code> attribute (ex. 120, 25%) for the file/image</li>
				<li><code>right</code>, <code>left</code>, <code>top</code> or <code>bottom</code> are interpreted as the alignment for the file (currently, only applies to images)</li>
				<li><code>link=some Link...</code> replaces the link to the file source by the one specified using <code>link</code>. If no value is specified, the link is simply removed.</li>
				<li><code>nolink</code> means without link to image source (deprecated, use <code>link=</code>)</li>
				<li><code>key=value</code> style are interpreted as HTML attributes or CSS style indications for the file. Valid keys are:<br />
				align, border, width, height, alt, desc, title, longdesc, class, id and usemap</li>
				<li><code>border</code> can only be a number</li>
				<li><code>altimage</code> is only valid for CDF files. It is ignored for other file types.</li>
				<li><code>details</code> indicates wether to display file information (size, etc.). Defaults to: on, Accepts: false, no, off, hide, hidden, none.<br />
				The single attribute <code>nodetails</code> is an alternative.</li>
			</ul>
			<p>Examples:</p>
			<ul>
				<li><code>[[File(mydoc.pdf)]]</code> # simplest</li>
				<li><code>[[File(mydoc.pdf, alt="My document")]]</code> # ALT text. For images, this is the "alt" attribute. For files, the "title" attribute.</li>
				<li><code>[[File(mydoc.pdf, desc="My document")]]</code> # Link text. If none is provided, the filename will be used.</li>
				<li><code>[[File(mydoc.pdf, details="off")]]</code> or <code>[[File(mydoc.pdf, nodetails)]]</code> to link a file and NOT display file metadata.</li>
			</ul>
			<p>Examples (CDF):</p>
			<ul>
				<li><code>[[File(prog.cdf, width=300, height=500)]]</code> # with embed width and height sizes</li>
				<li><code>[[File(prog.cdf, width=300, height=500, altimage=prog.png)]]</code> # CDF file with sizes and alternate image to show if CDF plugin isn\'t available.</li>
				<li><code>[[File(prog.cdf, width=300, height=500, alt="A nifty program")]]</code> # CDF file with sizes and alternate content to show if CDF plugin isn\'t available.</li>
			</ul>';
		return $txt['html'];
	}

	/**
	 * Generate macro output based on passed arguments
	 *
	 * @return  string
	 */
	public function render()
	{
		$content = strip_tags($this->args);

		// Args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		// Parse arguments
		// We expect the first argument to be a filename
		$args   = explode(',', $content);
		$file   = array_shift($args);

		$this->attr   = array();
		$this->attr['href']  = '';
		$this->attr['style'] = array();
		$this->attr['details'] = true;

		// Get single attributes
		// EX: [[Image(myimage.png, nolink, right)]]
		$argues = preg_replace_callback('/[, ](left|right|top|center|bottom|nodetails|[0-9]+(px|%|em)?)(?:[, ]|$)/i', array(&$this, 'parseSingleAttribute'), $content);
		// Get quoted attribute/value pairs
		// EX: [[Image(myimage.png, desc="My description, contains, commas")]]
		$argues = preg_replace_callback('/[, ](alt|altimage|althref|desc|title|width|height|align|border|longdesc|class|id|usemap|link|rel|details)=(?:["\'])([^"\']*)(?:["\'])/i', array(&$this, 'parseAttributeValuePair'), $content);
		// Get non-quoted attribute/value pairs
		// EX: [[Image(myimage.png, width=100)]]
		$argues = preg_replace_callback('/[, ](alt|altimage|althref|desc|title|width|height|align|border|longdesc|class|id|usemap|link|rel|details)=([^"\',]*)(?:[, ]|$)/i', array(&$this, 'parseAttributeValuePair'), $content);

		$attr = $this->attr;

		// Get wiki config
		$this->config = \Component::params('com_wiki');
		if ($this->filepath != '')
		{
			$this->config->set('filepath', $this->filepath);
		}
		$imgs = explode(',', $this->config->get('img_ext', 'jpg, jpeg, jpe, gif, png'));
		$imgs = array_map('trim', $imgs);
		$imgs = array_map('strtolower', $imgs);
		$this->imgs = $imgs;

		$ret = false;
		// Is it numeric?
		if (is_numeric($file))
		{
			include_once \Component::path('com_wiki') . DS . 'models' . DS . 'attachment.php';

			// Get resource by ID
			$attach = \Components\Wiki\Models\Attachment::oneOrNew(intval($file));

			// Check for file existence
			if ($attach->filename && file_exists($this->_path($attach->filename)) || file_exists($this->_path($attach->filename, true)))
			{
				$attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : '';
				if (!$attr['desc'])
				{
					$attr['desc'] = ($attach->description) ? stripslashes($attach->description) : ''; //$attach->filename;
				}
				$attr['created'] = $attach->created;
				$attr['created_by'] = $attach->created_by;

				$ret = true;
			}
		}
		// Check for file existence
		else if (file_exists($this->_path($file)) || file_exists($this->_path($file, true)))
		{
			include_once \Component::path('com_wiki') . DS . 'models' . DS . 'attachment.php';

			// Get resource by ID
			$attach = \Components\Wiki\Models\Attachment::oneByFilename($file, $this->pageid);
			if ($attach->filename)
			{
				$attr['created'] = $attach->created;
				$attr['created_by'] = $attach->created_by;
			}

			$attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : $file;

			$ret = true;
		}

		// Does the file exist?
		if ($ret)
		{
			// $attr['desc'] = htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8');

			// Return HTML
			return $this->_embed($file, $attr);
		}
		else
		{
			// Return error message
			return '(file:' . $file . ' not found)';
		}
	}

	/**
	 * Parse attribute=value pairs
	 * EX: [[Image(myimage.png, desc="My description, contains, commas", width=200)]]
	 *
	 * @param   array $matches Values matching attr=val pairs
	 * @return  void
	 */
	public function parseAttributeValuePair($matches)
	{
		$key = strtolower(trim($matches[1]));
		$val = trim($matches[2]);

		$size   = '/^[0-9]+(%|px|em)+$/';
		$attrs  = '/(alt|altimage|althref|desc|title|width|height|align|border|longdesc|class|id|usemap|rel|details)=(.+)/';
		$quoted = "/(?:[\"'])(.*)(?:[\"'])$/";

		// Set width if just a pixel size is given
		// EX: [[File(myfile.jpg, width=120px)]]
		if (preg_match($size, $val, $matches) && $key != 'border')
		{
			if ($matches[0] && in_array($key, array('width', 'height')))
			{
				$this->attr['style'][$key] = $val;
				//$this->attr[$key] = $val;
				return;
			}
		}

		if (is_numeric($val) && in_array($key, array('width', 'height')))
		{

			$this->attr['style'][$key] = $val . 'px';
			$this->attr[$key] = $val;
		}
		// Specific call to NOT link an image
		// Links images by default
		if ($key == 'nolink')
		{
			$this->attr['href'] = 'none';
			return;
		}
		// Specific call to NOT show file details
		if ($key == 'details')
		{
			switch (strtolower($val))
			{
				case 'no':
				case 'none':
				case 'false':
				case 'off':
				case 'hide':
				case 'hidden':
					$this->attr['details'] = false;
				break;

				default:
					$this->attr['details'] = true;
				break;
			}
			return;
		}
		// Check for a specific link given
		if ($key == 'link')
		{
			$this->attr['href'] = 'none';

			if ($val)
			{
				$this->attr['href'] = $val;

				$urlPtrn  = "[^=\"\']*(https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
				if (preg_match("/$urlPtrn/", $val))
				{
					$this->attr['rel']  = 'external';
				}
			}
			return;
		}
		// Check for alignment, no key given
		// EX: [[File(myfile.jpg, left)]]
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
		return;
	}

	/**
	 * Handle single attribute values
	 * EX: [[Image(myimage.png, nolink, right)]]
	 *
	 * @param   array $matches Values matching the single attribute pattern
	 * @return  void
	 */
	public function parseSingleAttribute($matches)
	{
		$key = strtolower(trim($matches[1]));

		$ky = 'width';
		if (isset($this->attr['width']))
		{
			$ky = 'height';
		}

		// Set width if just a pixel size is given
		// EX: [[File(myfile.jpg, 120px)]]
		$size   = '/[0-9+](%|px|em)+$/';
		if (preg_match($size, $key, $matches))
		{
			if ($matches[0])
			{
				$this->attr['style'][$ky] = $key;
				//$this->attr[$ky] = $key;
				return;
			}
		}

		if (is_numeric($key))
		{
			$this->attr['style'][$ky] = $key . 'px';
			$this->attr[$ky] = $key;
		}

		// Specific call to NOT link an image
		// Links images by default
		if ($key == 'nolink')
		{
			$this->attr['href'] = 'none';
			return;
		}

		// Specific call to NOT display file metadata
		if ($key == 'nodetails')
		{
			$this->attr['details'] = false;
			return;
		}

		// Check for alignment, no key given
		// EX: [[File(myfile.jpg, left)]]
		if (in_array($key, array('left', 'right', 'top', 'bottom')))
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
			return;
		}

		return;
	}

	/**
	 * Generate an absolute path to a file stored on the system
	 * Assumes $file is relative path but, if $file starts with / then assumes absolute
	 *
	 * @param   $file    Filename
	 * @param   boolean  $alt
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
	 * Generate a link to a file
	 *
	 * If $file starts with (http|https|mailto|ftp|gopher|feed|news|file), then it's an external URL and returned
	 * @param   $file  Filename
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
		if (in_array(strtolower(\Filesystem::extension($file)), $this->imgs))
		{
			if (\Request::getString('format') == 'pdf')
			{
				return $this->_path($file);
			}
			$type = 'Image';
		}
		$link .= $this->pagename . DS . $type . ':' . $file;

		return \Route::url($link);
	}

	/**
	 * Generates HTML to either embed a file or link to file for download
	 *
	 * @param   string $file File to embed
	 * @param   array  $attr Attributes to apply to the HTML
	 * @return  string
	 */
	private function _embed($file, $attr=array())
	{
		$ext = strtolower(\Filesystem::extension($file));

		switch ($ext)
		{
			case 'unity3d':
				$attr['width']  = (isset($attr['width']) && $attr['width'])  ? $attr['width']  : 400;
				$attr['height'] = (isset($attr['height']) && $attr['height']) ? $attr['height'] : 400;
				if (isset($attr['style']['width']))
				{
					$attr['width'] = intval($attr['style']['width']);
				}
				if (isset($attr['style']['height']))
				{
					$attr['height'] = intval($attr['style']['height']);
				}
				$attr['href']   = (isset($attr['href']) && $attr['href'] && $attr['href'] != 'none')   ? $attr['href']   : $this->_link($file);

				/*if (!array_key_exists('alt', $attr)
				 && array_key_exists('altimage', $attr)
				 && $attr['altimage'] != ''
				 && file_exists($this->_path($attr['altimage'])))
				{
					//$attr['href'] = (array_key_exists('althref', $attr) && $attr['althref'] != '') ? $attr['althref'] : $attr['href'];
					$althref = (array_key_exists('althref', $attr) && $attr['althref'] != '') ? $attr['althref'] : $attr['href'];
					$attr['alt']  = '<a class="attachment" rel="internal" href="' . $althref . '" title="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '">';
					$attr['alt'] .= '<img src="' . $this->_link($attr['altimage']) . '" alt="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '" />';
					$attr['alt'] .= '</a>';
				}
				else
				{
					$althref = (array_key_exists('althref', $attr) && $attr['althref'] != '') ? $attr['althref'] : $attr['href'];
					$attr['alt']  = (isset($attr['alt'])) ? $attr['alt'] : '';
					$attr['alt'] .= '<a class="attachment" rel="internal" href="' . $althref . '" title="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '">' . $attr['desc'] . '</a>';
				}*/

				$rand = rand(0, 100000);

				$html  = '<script type="text/javascript" src="' . (\Request::scheme() == 'https' ? 'https://ssl-' : 'http://') . 'webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject2.js"></script>' . "\n";
				$html .= '<div id="unityPlayer' . $rand . '">
							<div class="missing">
								<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
									<img alt="Unity Web Player. Install now!" src="' . (\Request::scheme() == 'https' ? 'https://ssl-' : 'http://') . 'webplayer.unity3d.com/installation/getunity.png" width="193" height="63" />
								</a>
							</div>
						</div>' . "\n";
				$html .= '<script type="text/javascript">' . "\n";
				$html .= '<!--
							var config = {
								width: '.intval($attr['width']).',
								height: '.intval($attr['height']).',
								params: { enableDebugging:"0" }
							}
							var u = new UnityObject2(config);
							var unityObject = $("#unityPlayer'.$rand.'");
							u.initPlugin(unityObject, "'.$attr['href'].'");
							-->' . "\n";
				$html .= '</script>' . "\n";
			break;

			case 'cdf':
				$attr['width']  = (isset($attr['width']) && $attr['width'])  ? $attr['width']  : 400;
				$attr['height'] = (isset($attr['height']) && $attr['height']) ? $attr['height'] : 400;
				if (isset($attr['style']['width']))
				{
					$attr['width'] = intval($attr['style']['width']);
				}
				if (isset($attr['style']['height']))
				{
					$attr['height'] = intval($attr['style']['height']);
				}
				$attr['href']   = (isset($attr['href']) && $attr['href'] && $attr['href'] != 'none')   ? $attr['href']   : $this->_link($file);

				$rand = rand(0, 100000);

				if (!array_key_exists('alt', $attr)
				 && array_key_exists('altimage', $attr)
				 && $attr['altimage'] != ''
				 && file_exists($this->_path($attr['altimage'])))
				{
					//$attr['href'] = (array_key_exists('althref', $attr) && $attr['althref'] != '') ? $attr['althref'] : $attr['href'];
					$althref = (array_key_exists('althref', $attr) && $attr['althref'] != '') ? $attr['althref'] : $attr['href'];
					$attr['alt']  = '<a href="http://www.wolfram.com/cdf-player/" title="CDF Web Player. Install now!">';
					$attr['alt'] .= '<img src="' . $this->_link($attr['altimage']) . '" alt="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '" />';
					$attr['alt'] .= '</a>';
				}
				else
				{
					$attr['alt'] = '<div class="embedded-plugin" style="width: ' . intval($attr['width']) . 'px; height: ' . intval($attr['height']) . 'px;"><a class="missing-plugin" href="http://www.wolfram.com/cdf-player/" title="CDF Web Player. Install now!"><img alt="CDF Web Player. Install now!" src="' . \Request::scheme() . '://www.wolfram.com/cdf/images/cdf-player-black.png" width="187" height="41" /></a></div>';
				}

				$html  = '<script type="text/javascript" src="' . \Request::scheme() . '://www.wolfram.com/cdf-player/plugin/v2.1/cdfplugin.js"></script>';
				$html .= '<script type="text/javascript">';
				//$html .= '<!--';
				$html .= '	var cdf = new cdfplugin();';
				$html .= "var defaultContent = '" . $attr['alt'] . "';";
				$html .= '	if (defaultContent!= "") {';
				$html .= '		cdf.setDefaultContent(defaultContent);';
				$html .= '	}';
				$html .= '	cdf.embed(\'' . $attr['href'] . '\', ' . intval($attr['width']) . ', ' . intval($attr['height']) . ');';
				//$html .= ' -->';
				$html .= '</script>' . "\n";
				$html .= '<noscript>';
				$html .= '<div class="embedded-plugin" style="width: ' . intval($attr['width']) . 'px; height: ' . intval($attr['height']) . ';">';
				$html .= $attr['alt'];
				$html .= '</div>';
				$html .= '</noscript>' . "\n";
			break;

			default:
				$attr['alt'] = (isset($attr['alt'])) ? htmlentities($attr['alt'], ENT_COMPAT, 'UTF-8') : $attr['desc'];
				if (!$attr['alt'])
				{
					$attr['alt'] = $file;
				}

				if (in_array($ext, $this->imgs))
				{
					$styles = '';
					if (count($attr['style']) > 0)
					{
						$s = array();
						foreach ($attr['style'] as $k => $v)
						{
							$s[] = strtolower($k) . ':' . $v;
						}
						$styles = implode('; ', $s);
					}
					$attr['style'] = '';

					$attribs = array();
					foreach ($attr as $k => $v)
					{
						$k = strtolower($k);
						if ($k != 'href' && $k != 'rel' && $k != 'desc' && $v)
						{
							$attribs[] = $k . '="' . trim($v, '"') . '"';
						}
					}
					$html  = '<span class="figure"' . ($styles ? ' style="' . $styles . '"' : '') . '>';
					$img = '<img src="' . $this->_link($file) . '" ' . implode(' ', $attribs) . ' />';
					if ($attr['href'] == 'none')
					{
						$html .= $img;
					}
					else
					{
						$attr['href'] = ($attr['href']) ? $attr['href'] : $this->_link($file);
						$attr['rel'] = (isset($attr['rel'])) ? $attr['rel'] : 'lightbox';

						$html .= '<a rel="' . $attr['rel'] . '" href="' . $attr['href'] . '">' . $img . '</a>';
					}
					if (isset($attr['desc']) && $attr['desc'])
					{
						$html .= '<span class="figcaption">' . $attr['desc'] . '</span>';
					}
					$html .= '</span>';
				}
				else
				{
					$attr['details'] = isset($attr['details']) ? $attr['details'] : true;
					$attr['href'] = (isset($attr['href']) && $attr['href'] != '') ? $attr['href'] : $this->_link($file);
					$attr['rel']  = (isset($attr['rel']))  ? $attr['rel']  : 'internal';

					$size = null;
					if (file_exists($this->_path($file)))
					{
						$size = filesize($this->_path($file));
					}
					else if (file_exists($this->_path($file, true)))
					{
						$size = filesize($this->_path($file, true));
					}

					$attr['title'] = (!isset($attr['title']) || !$attr['title'] ? $attr['alt'] : $attr['title']);

					$html = '<a class="attachment" rel="' . $attr['rel'] . '" href="' . $attr['href'] . '" title="' . $attr['title'] . '">' . $attr['desc'] . '</a>';
					if ($size !== null && $attr['details'])
					{
						$html .= ' (<span class="file-atts">' . \Hubzero\Utility\Number::formatBytes($size);
						if (isset($attr['created_by']))
						{
							$user = \User::getInstance($attr['created_by']);
							$html .= ', ' . \Lang::txt('uploaded by %s ', stripslashes($user->get('name')));
						}
						if (isset($attr['created']))
						{
							$html .= ' ' . \Date::of($attr['created'])->relative();
						}
						$html .= '</span>)';
					}
				}
			break;
		}

		return $html;
	}
}
