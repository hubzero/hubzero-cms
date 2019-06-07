<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @return  array
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
	 * @return  string
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
		$gid = Request::getString('cn');

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

		$html  = '<div class="wiki_slider">';
		$html .= '<div id="slider_' . $id . '" class="slider_macro">';
		foreach ($final_slides as $fs)
		{
			$html .= '<img src="' . $fs . '" alt="" />';
		}
		$html .= '</div>';
		$html .= '<div class="wiki_slider_pager" id="slider_' . $id . '_pager"></div>';
		$html .= '</div>';

		\Document::addStyleSheet(\Request::root() . 'core/plugins/wiki/parserdefault/macros/macro-assets/slider/slider.css?t=' . filemtime(__DIR__ . '/macro-assets/slider/slider.css'));
		\Document::addScript(\Request::root() . 'core/plugins/wiki/parserdefault/macros/macro-assets/slider/slider.js?t=' . filemtime(__DIR__ . '/macro-assets/slider/slider.js'));

		return $html;
	}
}
