<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Admin\Helpers\Html;

use Hubzero\Utility\Arr;
use Lang;

/**
 * HTML helper
 */
abstract class ContentAdministrator
{
	/**
	 * @param   int    $value  The state value
	 * @param   int    $i
	 * @param   bool   $canChange
	 * @return  string
	 */
	static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0 => array(
				'disabled.png',
				'articles.featured',
				'COM_CONTENT_UNFEATURED',
				'COM_CONTENT_TOGGLE_TO_FEATURE'
			),
			1 => array(
				'featured.png',
				'articles.unfeatured',
				'COM_CONTENT_FEATURED',
				'COM_CONTENT_TOGGLE_TO_UNFEATURE'
			),
		);
		$state = Arr::getValue($states, (int) $value, $states[1]);
		$html  = \Html::asset('image', 'admin/'.$state[0], Lang::txt($state[2]), null, true);
		if ($canChange)
		{
			$html = '<a href="#" class="state ' . ($value ? 'yes' : 'no') . '" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.Lang::txt($state[3]).'">'. $html.'</a>';
		}

		return $html;
	}
}
