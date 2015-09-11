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
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying an image slider
 */
class SliderMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Creates a slider with the images passed in.";
		$txt['html'] = '<p>Creates a slider with the images passed in. Enter uploaded image names seperated by commas.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Slider(image1.jpg, image2.gif, image3.png)]]</code></li>
						</ul>';

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

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return;
		}

		//generate a unique id for the slider
		$id = uniqid();

		//get the group
		$gid = Request::getVar('cn');

		//get the group object based on gid
		$group = \Hubzero\User\Group::getInstance($gid);

		//check to make sure we have a valid group
		if (!is_object($group))
		{
			return;
		}

		//define a base url
		$base_url = DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');

		//seperate image list into array of images
		$slides = explode(',', $content);

		//array for checked slides
		$final_slides = array();

		//check each passed in slide
		foreach ($slides as $slide)
		{
			//check to see if image is external
			if (strpos($slide, 'http') === false)
			{
				$slide = trim($slide);

				//check if internal file actually exists
				if (is_file(PATH_APP . $base_url . DS . $slide))
				{
					$final_slides[] = $base_url . DS . $slide;
				}
			}
			else
			{
				$headers = get_headers($slide);
				if (strpos($headers[0], "OK") !== false)
				{
					$final_slides[] = $slide;
				}
			}
		}

		$html  = '';
		$html .= '<div class="wiki_slider">';
		$html .= '<div id="slider_' . $id . '">';
		foreach ($final_slides as $fs)
		{
			$html .= '<img src="' . $fs . '" alt="" />';
		}
		$html .= '</div>';
		$html .= '<div class="wiki_slider_pager" id="slider_' . $id . '_pager"></div>';
		$html .= '</div>';

		Document::addStyleSheet('plugins/wiki/parserdefault/macros/macro-assets/slider/slider.css');
		Document::addScript('plugins/wiki/parserdefault/macros/macro-assets/slider/slider.js');
		Document::addScriptDeclaration('
			jQuery(document).ready(function($) {
				$("#slider_' . $id . '").cycle({
					fx: \'scrollHorz\',
					speed: 450,
					pager: \'#slider_' . $id . '_pager\'
				});
			});
		');

		return $html;
	}
}
