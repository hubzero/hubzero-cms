<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Helpers;

use Hubzero\Access\Access;
use Hubzero\Base\Obj;
use User;
use Html;

/**
 * Languages component helper.
 */
class Utilities
{
	/**
	 * Returns an array of published state filter options.
	 *
	 * @return  array
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = Html::select('option', '1', 'JPUBLISHED');
		$options[] = Html::select('option', '0', 'JUNPUBLISHED');
		$options[] = Html::select('option', '-2', 'JTRASHED');
		$options[] = Html::select('option', '*', 'JALL');

		return $options;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result    = new Obj;
		$assetName = 'com_languages';

		$actions = Access::getActionsFromFile(Component::path($assetName) . '/config/access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}
}
