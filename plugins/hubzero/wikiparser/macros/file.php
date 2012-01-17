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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * A wiki macro for embedding or linking to files
 */
class FileMacro extends WikiMacro
{

	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     string
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
				<li><code>key=value</code> style are interpreted as HTML attributes or CSS style indications for the file. Valid keys are:</li>
				<li>align, border, width, height, alt, desc, title, longdesc, class, id and usemap</li>
				<li><code>border</code> can only be a number</li>
				<li><code>altimage</code> is only valid for CDF files. It is ignored for other file types.</li>
			</ul>
			<p>Examples:</p>
			<ul>
				<li><code>[[File(mydoc.pdf)]]</code> # simplest</li>
				<li><code>[[File(mydoc.pdf, alt="My document")]]</code> # ALT text. For images, this is the "alt" attribute. For files, the "title" attribute.</li>
				<li><code>[[File(mydoc.pdf, desc="My document")]]</code> # Link text. If none is provided, the filename will be used.</li>
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
	 * @return     string
	 */
	public function render()
	{
		$content = $this->args;
		
		// args will be null if the macro is called without parenthesis.
		if (!$content) 
		{
			return '';
		}
		
		// Parse arguments
        // We expect the 1st argument to be a filename
		$args   = explode(',', $content);
		$file   = array_shift($args);

		$size   = '/[0-9+](%|px)?$/';
		$attrs  = '/(alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap)=(.+)/';
		$quoted = "/(?:[\"'])(.*)(?:[\"'])$/";
		
		// Collected attributes
		$attr   = array();
		$attr['href']  = '';
		$attr['style'] = array();

		foreach ($args as $arg) 
		{
			$arg = trim($arg);

			// Set width if just a pixel size is given 
			// e.g., [[File(myfile.jpg, 120px)]]
			if (!strstr($arg, '=') && preg_match($size, $arg, $matches)) 
			{
				if ($matches[0])
				{
					$attr['width'] = $arg;
	                continue;
				}
			}
			// Specific call to NOT link an image
			// Links images by default
			if ($arg == 'nolink') 
			{
                $attr['href'] = 'none';
                continue;
			}
			// Check for a specific link given
			if (substr($arg, 0, 5) == 'link=') 
			{
				$attr['href'] = 'none';
                $bits = preg_split('#=#', $arg);
				$val = trim(end($bits));
				if ($val) 
				{
					$urlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
					if (preg_match("/$urlPtrn/", $val))
					{
						$attr['href'] = $val;
						$attr['rel']  = 'external';
					}
				}
                continue;
			}
			// Check for alignment, no key given
			// e.g., [[File(myfile.jpg, left)]]
			if (in_array($arg, array('left', 'right', 'top', 'bottom'))) 
			{
				$attr['style']['float'] = $arg;
				continue;
			}

			// Look for any other attributes
			preg_match($attrs, $arg, $matches);
			
			if ($matches) 
			{
				$key = strtolower($matches[1]);
				$val = $matches[2];
				$m = preg_match($quoted, $val, $m);
				if ($m) 
				{
					$val = trim($val, '"');
					$val = trim($val, "'");
				}
				if ($key == 'align') 
				{
					$attr['style']['float'] = $val;
				} 
				else if ($key == 'border') 
				{
					$attr['style']['border'] = '#ccc ' . intval($val) . 'px solid';
				} 
				else 
				{
					$attr[$key] = $val;
				}
				//$attr[$key] = $val;
			}
		}

		// Get wiki config
		$this->config = JComponentHelper::getParams('com_wiki');
		if ($this->filepath != '') 
		{
			$this->config->set('filepath', $this->filepath);
		}
		$imgs = explode(',', $this->config->get('img_ext'));
		array_map('trim', $imgs);
		array_map('strtolower', $imgs);
		$this->imgs = $imgs;

		$ret = false;
		// Is it numeric?
		if (is_numeric($file)) 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'attachment.php');

			// Get resource by ID
			$attach = new WikiPageAttachment($this->_db);
			$attach->load(intval($file));

			// Check for file existence
			if ($attach->filename && file_exists($this->_path($attach->filename))) 
			{
				$attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : '';
				if (!$attr['desc'])
				{
					$attr['desc'] = ($attach->description) ? stripslashes($attach->description) : $attach->filename;
				}

				$ret = true;
			}
		}
		// Check for file existence
		else if (file_exists($this->_path($file))) 
		{
			$attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : $file;
			
			$ret = true;
		} 
		
		// Does the file exist?
		if ($ret)
		{
			jimport('joomla.filesystem.file');
			//$attr['desc'] = htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8');
			
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
	 * Generate an absolute path to a file stored on the system
	 * Assumes $file is relative path but, if $file starts with / then assumes absolute
	 * 
	 * @param      $file  Filename
	 * @return     string
	 */
	private function _path($file)
	{
		if (substr($file, 0, 1) == DS) 
		{
			$path = JPATH_ROOT . $file;
		}
		else 
		{
			$path  = JPATH_ROOT . $this->config->get('filepath');
			$path .= ($this->pageid) ? DS . $this->pageid : '';
			$path .= DS . $file;
		}
		
		return $path;
	}
	
	/**
	 * Generate a link to a file
	 * If $file starts with (http|https|mailto|ftp|gopher|feed|news|file), then it's an external URL and returned
	 * 
	 * @param      $file  Filename
	 * @return     string
	 */
	private function _link($file)
	{
		$urlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|feed:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		if (preg_match("/$urlPtrn/", $file))
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
		if (in_array(strtolower(JFile::getExt($file)), $this->imgs)) 
		{
			$type = 'Image';
		}
		$link .= $this->pagename . DS . $type . ':' . $file;
		
		return JRoute::_($link);
	}
	
	/**
	 * Generates HTML to either embed a file or link to file for download
	 * 
	 * @param      string $file File to embed
	 * @param      array  $attr Attributes to apply to the HTML
	 * @return     string
	 */
	private function _embed($file, $attr=array())
	{
		$ext = strtolower(JFile::getExt($file));
		
		switch ($ext)
		{
			case 'cdf':
				$attr['width']  = (isset($attr['width']) && $attr['width'])  ? $attr['width']  : 500;
				$attr['height'] = (isset($attr['height']) && $attr['height']) ? $attr['height'] : 700;
				$attr['href']   = (isset($attr['href']) && $attr['href'] && $attr['href'] != 'none')   ? $attr['href']   : $this->_link($file);

				if (!array_key_exists('alt', $attr) 
				 && array_key_exists('altimage', $attr) 
				 && $attr['altimage'] != ''
				 && file_exists($this->_path($attr['altimage']))) 
				{
					$attr['alt']  = '<a class="attachment" rel="internal" href="' . $attr['href'] . '" title="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '">';
					$attr['alt'] .= '<img src="' . $this->_link($attr['altimage']) . '" alt="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '" />';
					$attr['alt'] .= '</a>';
				} 
				else 
				{
					$attr['alt']  = (isset($attr['alt'])) ? $attr['alt'] : '';
					$attr['alt'] .= '<a class="attachment" rel="internal" href="' . $attr['href'] . '" title="' . htmlentities($attr['desc'], ENT_COMPAT, 'UTF-8') . '">' . $attr['desc'] . '</a>';
				}
				
				$uri = JURI::getInstance();
				
				$html  = '<script type="text/javascript" src="' . $uri->getScheme() . '://www.wolfram.com/cdf-player/plugin/v2.1/cdfplugin.js"></script>' . "\n";
				$html .= '<script type="text/javascript">' . "\n";
				$html .= "\t" . 'var cdf = new cdfplugin();' . "\n";
				$html .= "\t" . 'var defaultContent = "' . addslashes($attr['alt']) . '"' . "\n";
			    $html .= "\t" . 'if (defaultContent != "") {' . "\n";
			    $html .= "\t\t" . 'cdf.setDefaultContent(defaultContent);' . "\n";
			    $html .= "\t" . '}' . "\n";
				$html .= "\t" . 'cdf.embed(\'' . $attr['href'] . '\', ' . intval($attr['width']) . ', ' . intval($attr['height']) . ');' . "\n";
				$html .= '</script>' . "\n";
				$html .= '<noscript>' . "\n";
				//$html .= "\t" . '<a class="attachment" href="' . $attr['href'] . '">' . $attr['alt'] . '</a>' . "\n";
				$html .= $attr['alt'];
				$html .= '</noscript>' . "\n";
			break;
			
			default:
				$attr['alt'] = (isset($attr['alt'])) ? htmlentities($attr['alt'], ENT_COMPAT, 'UTF-8') : $attr['desc'];
				
				if (in_array($ext, $this->imgs)) 
				{
					if (count($attr['style']) > 0) 
					{
						$s = array();
						foreach ($attr['style'] as $k => $v)
						{
							$s[] = strtolower($k) . ':' . $v;
						}
						$attr['style'] = implode('; ', $s);
					}
					else 
					{
						$attr['style'] = '';
					}
					
					$attribs = array();
					foreach ($attr as $k => $v)
					{
						$k = strtolower($k);
						if ($k != 'href' && $k != 'rel' && $k != 'desc' && $v)
						{
							$attribs[] = $k . '="' . $v . '"';
						}
					}
					$img = '<img src="' . $this->_link($file) . '" ' . implode(' ', $attribs) . '" />';
					if ($attr['href'] == 'none') 
					{
						$html = $img;
					} 
					else 
					{
						$attr['href'] = ($attr['href']) ? $attr['href'] : $this->_link($file);
						$attr['rel'] = (isset($attr['rel'])) ? $attr['rel'] : 'lightbox';

						$html = '<a rel="' . $attr['rel'] . '" href="' . $attr['href'] . '">' . $img . '</a>';
					}
				}
				else
				{
					$attr['href'] = (isset($attr['href']) && $attr['href'] != '') ? $attr['href'] : $this->_link($file);
					$attr['rel']  = (isset($attr['rel']))  ? $attr['rel']  : 'internal';
					
					$html = '<a class="attachment" rel="' . $attr['rel'] . '" href="' . $attr['href'] . '" title="' . $attr['alt'] . '">' . $attr['desc'] . '</a>';
				}
			break;
		}
		
		return $html;
	}
}

