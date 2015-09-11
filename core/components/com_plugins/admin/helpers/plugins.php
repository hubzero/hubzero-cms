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
