<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
				$cn = \Request::getString('cn');
				$group = Group::getInstance($cn);

				$base_url  = DS . trim($config->get('uploadpath', 'site/groups'), DS) . DS;
				$base_url .= $group->get('gidNumber') . DS . 'uploads';
			break;

			case 'com_resources':
				require_once \Component::path('com_resources') . '/models/entry.php';

				$row = \Components\Resources\Models\Entry::oneOrNew($this->pageid);

				$base_url  = DS . trim($config->get('uploadpath', 'site/resources'), DS) . DS;
				$base_url .= trim($row->relativePath(), DS) . DS . 'media';
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
			$html .= '<div id="slider_' . $id . '" class="slider_macro">';
			foreach ($final_slides as $fs)
			{
				$html .= '<img src="' . $fs . '" alt="" />';
			}
			$html .= '</div>';
			$html .= '<div class="wiki_slider_pager" id="slider_' . $id . '_pager"></div>';
		$html .= '</div>';

		\Document::addStyleSheet(\Request::root() . 'core/plugins/content/formathtml/macros/macro-assets/slider/slider.css?t=' . filemtime(__DIR__ . '/macro-assets/slider/slider.css'));
		\Document::addScript(\Request::root() . 'core/plugins/content/formathtml/macros/macro-assets/slider/slider.js?t=' . filemtime(__DIR__ . '/macro-assets/slider/slider.js'));

		return $html;
	}
}
