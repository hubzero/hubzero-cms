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

		// null base url for now
		$base_url = '';

		// needed objects
		$db     = \App::get('db');
		$option = \Request::getCmd('option');
		$config = \Component::params($option);

		// define a base url
		switch ($option)
		{
			case 'com_groups':
				$cn = \Request::getVar('cn');
				$group = Group::getInstance($cn);

				$base_url  = DS . trim($config->get('uploadpath', 'site/groups'), DS) . DS;
				$base_url .= $group->get('gidNumber') . DS . 'uploads';
			break;

			case 'com_resources':
				$row = new \Components\Resources\Tables\Resource($db);
				$row->load($this->pageid);

				$base_url  = DS . trim($config->get('uploadpath', 'site/resources'), DS) . DS;
				$base_url .= \Components\Resources\Helpers\Html::build_path($row->created, $this->pageid, '') . DS . 'media';
			break;
		}

		//seperate image list into array of images
		$slides = array_map('trim', explode(',', $content));

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

		\Document::addStyleSheet('plugins/content/formathtml/macros/macro-assets/slider/slider.css');
		\Document::addScript('plugins/content/formathtml/macros/macro-assets/slider/slider.js');
		\Document::addScriptDeclaration('
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
