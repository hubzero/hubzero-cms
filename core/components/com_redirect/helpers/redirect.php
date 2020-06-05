<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Helpers;

use Hubzero\Base\Obj;
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

		$result  = new Obj;

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
			$html = '<a class="grid-action state ' . $state[0] . '" href="#" data-id="cb'.$i.'" data-task="'.$state[1].'" title="'.Lang::txt($state[3]).'">'. $html.'</a>';
		}

		return $html;
	}
}
