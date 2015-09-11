<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Plugins\Admin\Helpers;

use Hubzero\Base\Object;
use Exception;
use Filesystem;
use Html;
use User;

/**
 * Plugins component helper.
 */
class Plugins
{
	/**
	 * Extension name
	 *
	 * @var  string
	 */
	public static $extension = 'com_plugins';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result    = new Object;
		$assetName = self::$extension;

		$actions = \JAccess::getActions($assetName);

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function stateOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '1', 'JENABLED');
		$options[] = Html::select('option', '0', 'JDISABLED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function folderOptions()
	{
		$db    = \App::get('db');
		$query = $db->getQuery(true);

		$query->select('DISTINCT(folder) AS value, folder AS text');
		$query->from('#__extensions');
		$query->where($db->quoteName('type').' = '.$db->quote('plugin'));
		$query->order('folder');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			throw new Exception($error, 500);
		}

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  object
	 */
	public function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		$data = new Object;

		// Check of the xml file exists
		$filePath = Filesystem::cleanPath($templateBaseDir.'/templates/'.$templateDir.'/templateDetails.xml');
		if (is_file($filePath))
		{
			$xml = \JInstaller::parseXMLInstallFile($filePath);

			if ($xml['type'] != 'template')
			{
				return false;
			}

			foreach ($xml as $key => $value)
			{
				$data->set($key, $value);
			}
		}

		return $data;
	}
}
