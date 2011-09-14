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
 * Short description for 'ImageMacro'
 * 
 * Long description (if any) ...
 */
class ImageMacro extends WikiMacro
{

	/**
	 * Short description for 'description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
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
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function render()
	{
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content) {
			return '';
		}

		// parse arguments
        // we expect the 1st argument to be a filename (filespec)
		$args = split(',', $content);
		$file = array_shift($args);

		// style information
        $size_re = '/[0-9+](%|px)?$/';
        $attr_re = '/(align|border|width|height|alt|title|longdesc|class|id|usemap)=(.+)/';
        $quoted_re = "/(?:[\"'])(.*)(?:[\"'])$/";
        $attr = array();
        $style = array();
		$href = '';
        $link = '';
		$rel = 'lightbox';

		foreach ($args as $arg)
		{
			$arg = trim($arg);
			if (preg_match($size_re, $arg, $matches)) {
				// 'width' keyword
                $attr['width'] = $arg;
                continue;
			}
			if ($arg == 'nolink') {
                $link = 'none';
                continue;
			}
			if (substr($arg, 0, 5) == 'link=') {
                $bits = split('=', $arg);
				$val = trim(end($bits));
				$elt = $val; //extract_link($val);
				$href = 'none';
				if ($elt) {
					$href = $elt;
					$rel = 'external';
				}
                continue;
			}
			if (in_array($arg, array('left','right','top','bottom'))) {
				$style['float'] = $arg;
				continue;
			}
			preg_match($attr_re, $arg, $matches);
			if ($matches) {
				//print_r($matches);
				//foreach ($matches as $key=>$val) 
				//{
					$key = $matches[1];
					$val = $matches[2];
					$m = preg_match($quoted_re, $val, $m);
					if ($m) {
						$val = trim($m,'"');
						$val = trim($val,"'");
					}
					if ($key == 'align') {
						$style['float'] = $val;
					} else if ($key == 'border') {
						$style['border'] = ' '.intval($val).'px solid';
					} else {
						$attr[$key] = $val;
					}
				//}
			}
		}

		// parse file argument to get realm and id if contained.
		$parts = explode(':',$file);
		if (count($parts) == 3) { // realm:id:attachment-filename
			$realm = $parts[0];
			$id = $parts[1];
			$filename = $parts[2];
			// Realm not fully implemented
		} else if (count($parts) == 2) {
			$realm = $parts[0];
			$filename = $parts[1];
			// Realm not fully implemented
		} else if (count($parts) == 1) { // it's an attachment of the current resource
			$filename = $parts[0];
		} else {
			return '(Image('.$content.') failed - No file given)';
		}

		if (substr($file,0,1) == DS) {
			//$file = substr($file,1);

			$path = JPATH_ROOT.$file;

			$link = ($link) ? $link : $file;
			$href = ($href) ? $href : $file;
		} else {
			$config = JComponentHelper::getParams( 'com_wiki' );
			if ($this->filepath != '') {
				$config->set('filepath', $this->filepath);
			}

			$path  = JPATH_ROOT.$config->get('filepath');
			$path .= ($this->pageid) ? DS.$this->pageid : '';
			$path .= DS.$filename;

			/*
			$link  = $config->get('filepath');
			$link .= ($this->pageid) ? DS.$this->pageid : '';
			$link .= DS.$filename;
			*/
			$link  = substr($this->option,4,strlen($this->option)).DS;
			$link .= ($this->scope) ? $this->scope.DS : '';
			$link .= $this->pagename.DS.'Image:'.$filename;

			$href = ($href) ? $href : $link;
		}

		if (!is_file($path)) {
			return '(Image('.$content.') failed - File not found)<!-- '.$path.' -->';
		}

		if (count($style) > 0) {
			$s = array();
			foreach ($style as $k=>$v)
			{
				$s[] = $k.':'.$v;
			}
			$attr['style'] = implode('; ',$s);
		}

		$attribs = array();
		foreach ($attr as $k=>$v)
		{
			$attribs[] = $k.'="'.$v.'"';
		}

		$img = '<img src="'.$link.'" '.implode(' ',$attribs).' alt="'.$filename.'" />';

		if (!$href || $href == 'none') {
			return $img;
		} else {
			//return '['.$link.' '.$img.']';
			return '<a rel="'.$rel.'" href="'.$href.'" alt="">'.$img.'</a>';
		}
	}
}

