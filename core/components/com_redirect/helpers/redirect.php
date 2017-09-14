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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Helpers;

use Hubzero\Base\Object;
use Exception;
use User;
use Html;
use Lang;

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

		$path = dirname(__DIR__) . '/config/access.xml';

		$actions = \Hubzero\Access\Access::getActionsFromFile($path);
		$actions ?: array();

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

	/**
	 * Render a published/unpublished toggle
	 *
	 * @param   integer  $value      The state value.
	 * @param   integer  $i
	 * @param   boolean  $canChange  An optional setting for access control on the action.
	 * @return  string
	 */
	public static function published($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			1  => array('on', 'unpublish', 'JENABLED', 'COM_REDIRECT_DISABLE_LINK'),
			0  => array('off', 'publish', 'JDISABLED', 'COM_REDIRECT_ENABLE_LINK'),
			2  => array('archived', 'unpublish', 'JARCHIVED', 'JUNARCHIVE'),
			-2 => array('trash', 'publish', 'JTRASHED', 'COM_REDIRECT_ENABLE_LINK'),
		);
		$state = \Hubzero\Utility\Arr::getValue($states, (int) $value, $states[0]);
		$html  = '<span>' . Lang::txt($state[3]) . '</span>';
		if ($canChange)
		{
			$html = '<a class="state ' . $state[0] . '" href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.Lang::txt($state[3]).'">'. $html.'</a>';
		}

		return $html;
	}
}
