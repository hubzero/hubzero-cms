<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Helpers;

use Hubzero\Access\Access;
use Hubzero\Base\Obj;
use Hubzero\Utility\Arr;
use User;
use Html;
use Lang;

/**
 * Helpers
 */
class Utilities
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	public static function state($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			-2 => array('trashed', 'unpublish', 'JTRASHED', 'COM_MESSAGES_MARK_AS_UNREAD'),
			1 => array('published', 'unpublish', 'COM_MESSAGES_OPTION_READ', 'COM_MESSAGES_MARK_AS_UNREAD'),
			0 => array('unpublished', 'publish', 'COM_MESSAGES_OPTION_UNREAD', 'COM_MESSAGES_MARK_AS_READ')
		);
		$state = Arr::getValue($states, (int) $value, $states[0]);
		$html  = '<span class="state ' . $state[0] . '"><span>' . Lang::txt($state[2]) . '</span></span>';
		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . Lang::txt($state[3]) . '">' . $html . '</a>';
		}

		return $html;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result	= new Obj;

		$actions = Access::getActionsFromFile(dirname(__DIR__) . DS . 'config' . DS . 'access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, 'com_messages'));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return  array
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '1', Lang::txt('COM_MESSAGES_OPTION_READ'));
		$options[] = Html::select('option', '0', Lang::txt('COM_MESSAGES_OPTION_UNREAD'));
		$options[] = Html::select('option', '-2', Lang::txt('JTRASHED'));

		return $options;
	}
}
