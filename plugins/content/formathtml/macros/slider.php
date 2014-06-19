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
use Hubzero\User\Group;

/**
 * macro class for displaying an image slider
 */
class Slider extends Macro
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
		$cn = \JRequest::getVar('cn');

		//get the group object based on gid
		$group = Group::getInstance($cn);

		//check to make sure we have a valid group
		if (!is_object($group))
		{
			return;
		}

		//define a base url
		$base_url = DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber') . DS . 'uploads';

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
				if (is_file(JPATH_ROOT . $base_url . DS . $slide))
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

		$document = \JFactory::getDocument();
		$document->addStyleSheet('plugins/content/formathtml/macros/macro-assets/slider/slider.css');
		if (!\JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js');
		}
		$document->addScript('plugins/content/formathtml/macros/macro-assets/slider/slider.js');
		$document->addScriptDeclaration('
			var $jQ = jQuery.noConflict();

			$jQ(function() {
				$jQ("#slider_' . $id . '").cycle({
					fx: \'scrollHorz\',
					speed: 450,
					pager: \'#slider_' . $id . '_pager\'
				});
			});
		');

		return $html;
	}
}
