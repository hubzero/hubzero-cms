<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Redirect\Helpers;

use Hubzero\Base\Object;
use Exception;
use User;
use Html;

/**
 * Redirect component helper.
 */
class Redirect
{
	/**
	 * Component name
	 *
	 * @var  string
	 */
	public static $extension = 'com_redirect';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object  Object
	 */
	public static function getActions()
	{
		$assetName = self::$extension;

		$actions = \JAccess::getActions($assetName);
		$result  = new Object;

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  string  The HTML code for the select tag
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '*', 'JALL');
		$options[] = Html::select('option', '1', 'JENABLED');
		$options[] = Html::select('option', '0', 'JDISABLED');
		$options[] = Html::select('option', '2', 'JARCHIVED');
		$options[] = Html::select('option', '-2', 'JTRASHED');

		return $options;
	}

	/**
	 * Determines if the plugin for Redirect to work is enabled.
	 *
	 * @return  boolean
	 */
	public static function isEnabled()
	{
		return \Plugin::isEnabled('system', 'redirect');
	}
}
