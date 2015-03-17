<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Redirect\Helpers;

use Hubzero\Base\Object;
use Exception;

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
	 * @return  object  JObject
	 */
	public static function getActions()
	{
		$assetName = self::$extension;

		$user    = \JFactory::getUser();
		$actions = \JAccess::getActions($assetName);
		$result  = new Object;

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
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
		$options[] = \JHtml::_('select.option', '*', 'JALL');
		$options[] = \JHtml::_('select.option', '1', 'JENABLED');
		$options[] = \JHtml::_('select.option', '0', 'JDISABLED');
		$options[] = \JHtml::_('select.option', '2', 'JARCHIVED');
		$options[] = \JHtml::_('select.option', '-2', 'JTRASHED');

		return $options;
	}

	/**
	 * Determines if the plugin for Redirect to work is enabled.
	 *
	 * @return  boolean
	 */
	public static function isEnabled()
	{
		$db = \JFactory::getDbo();
		$db->setQuery(
			'SELECT enabled' .
			' FROM #__extensions' .
			' WHERE folder = ' . $db->quote('system') .
			'  AND element = ' . $db->quote('redirect')
		);
		$result = (boolean) $db->loadResult();
		if ($error = $db->getErrorMsg())
		{
			throw new Exception($error, 500);
		}
		return $result;
	}
}
