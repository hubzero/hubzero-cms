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
 * A wiki macro for embedding images
 */
class ImageMacro extends WikiMacro
{

	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     string
	 */
	public function description()
	{
		$txt = "Embed an image in wiki-formatted text.

The first argument is the file specification. The file specification may reference attachments or files in three ways:
 * `module:id:file`, where module can be either '''wiki''' or '''ticket''', to refer to the attachment named ''file'' of the specified wiki page or ticket.
 * `id:file`: same as above, but id is either a ticket shorthand or a Wiki page name.
 * `file` to refer to a local attachment named 'file'. This only works from within that wiki page or a ticket.

Also, the file specification may refer to repository files, using the `source:file` syntax (`source:file@rev` works also).

The remaining arguments are optional and allow configuring the attributes and style of the rendered `<img>` element:
 * digits and unit are interpreted as the size (ex. 120, 25%) for the image
 * `right`, `left`, `top` or `bottom` are interpreted as the alignment for the image
 * `link=some TracLinks...` replaces the link to the image source by the one specified using a TracLinks. If no value is specified, the link is simply removed.
 * `nolink` means without link to image source (deprecated, use `link=`)
 * `key=value` style are interpreted as HTML attributes or CSS style indications for the image. Valid keys are:
 * align, border, width, height, alt, title, longdesc, class, id and usemap
 * `border` can only be a number

Examples:
{{{
[[Image(photo.jpg)]]               # simplest
[[Image(photo.jpg, 120px)]]	       # with image width size
[[Image(photo.jpg, right)]]        # aligned by keyword
[[Image(photo.jpg, nolink)]]       # without link to source
[[Image(photo.jpg, align=right)]]  # aligned by attribute
[[Image(photo.jpg, 120px, class=mypic)]]  # with image width size and a CSS class
}}}

You can use image from other page, other ticket or other module.
{{{
[[Image(OtherPage:foo.bmp)]]       # if current module is wiki
[[Image(base/sub:bar.bmp)]]        # from hierarchical wiki page
[[Image(#3:baz.bmp)]]		       # if in a ticket, point to #3
[[Image(ticket:36:boo.jpg)]]
[[Image(source:/images/bee.jpg)]]  # straight from the repository!
[[Image(htdocs:foo/bar.png)]]      # image file in project htdocs dir.
}}}
";
		$txt = array();
		$txt['wiki'] = "Embed an image in wiki-formatted text. The first argument is the file specification. The remaining arguments are optional and allow configuring the attributes and style of the rendered `img` element:
 * digits and unit are interpreted as the size (ex. 120, 25%) for the image
 * `right`, `left`, `top` or `bottom` are interpreted as the alignment for the image
 * `link=some Link...` replaces the link to the image source by the one specified using Link. If no value is specified, the link is simply removed.
 * `nolink` means without link to image source (deprecated, use `link=`)
 * `key=value` style are interpreted as HTML attributes or CSS style indications for the image. Valid keys are:
 * align, border, width, height, alt, title, longdesc, class, id and usemap
 * `border` can only be a number

Examples:
 * Image(photo.jpg) # simplest
 * Image(photo.jpg, 120px) # with image width size
 * Image(photo.jpg, right) # aligned by keyword
 * Image(photo.jpg, nolink)       # without link to source
 * Image(photo.jpg, align=right)  # aligned by attribute
 * Image(photo.jpg, 120px, class=mypic) # with image width size and a CSS class
";
$txt['html'] = '<p>Embed an image in wiki-formatted text. The first argument is the file specification. The remaining arguments are optional and allow configuring the attributes and style of the rendered <code>&lt;img&gt;</code> element:</p>
<ul>
<li>digits and unit are interpreted as the size (ex. 120, 25%) for the image</li>
<li><code>right</code>, <code>left</code>, <code>top</code> or <code>bottom</code> are interpreted as the alignment for the image</li>
<li><code>link=some Link...</code> replaces the link to the image source by the one specified using Link. If no value is specified, the link is simply removed.</li>
<li><code>nolink</code> means without link to image source (deprecated, use <code>link=</code>)</li>
<li><code>key=value</code> style are interpreted as HTML attributes or CSS style indications for the image. Valid keys are:</li>
<li>align, border, width, height, alt, title, longdesc, class, id and usemap</li>
<li><code>border</code> can only be a number</li>
</ul>
<p>Examples:</p>
<ul>
<li><code>[[Image(photo.jpg)]]</code> # simplest</li>
<li><code>[[Image(photo.jpg, 120px)]]</code> # with image width size</li>
<li><code>[[Image(photo.jpg, right)]]</code> # aligned by keyword</li>
<li><code>[[Image(photo.jpg, nolink)]]</code>       # without link to source</li>
<li><code>[[Image(photo.jpg, align=right)]]</code>  # aligned by attribute</li>
<li><code>[[Image(photo.jpg, 120px, class=mypic)]]</code> # with image width size and a CSS class</li>
</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output based on passed arguments
	 * 
	 * @return     string HTML image tag on success or error message on failure
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
                $bits = split('=', $arg);
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

			if (!in_array(strtolower(JFile::getExt($file)), $this->imgs)) 
			{
				return '(Image(' . $content . ') failed - File provided is not an allowed image type)';
			}

			// Return HTML
			return $this->_embed($file, $attr);
		}	
		else 
		{
			// Return error message
			return '(Image(' . $content . ') failed - File not found)';
		}
	}
	
	/**
	 * Generate an absolute path to a file stored on the system
	 * Assumes $file is relative path but, if $file starts with / then assumes absolute
	 * 
	 * @param      string $file Filename
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
	 * @param      string $file Filename
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
		$link .= $this->pagename . DS . 'Image:' . $file;
		
		return JRoute::_($link);
	}
	
	/**
	 * Generates HTML to embed an <img>
	 * 
	 * @param      string $file File to embed
	 * @param      array  $attr Attributes to apply to the HTML
	 * @return     string
	 */
	private function _embed($file, $attr=array())
	{
		$attr['alt'] = (isset($attr['alt'])) ? htmlentities($attr['alt'], ENT_COMPAT, 'UTF-8') : $attr['desc'];
				
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
			$attr['rel']  = (isset($attr['rel'])) ? $attr['rel'] : 'lightbox';

			$html = '<a rel="' . $attr['rel'] . '" href="' . $attr['href'] . '">' . $img . '</a>';
		}
		
		return $html;
	}
}

