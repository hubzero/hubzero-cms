<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

/**
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * @since		1.6
 */
class MessagesHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */

	public static function addSubmenu($vName)
	{
		Submenu::addEntry(
			Lang::txt('COM_MESSAGES_ADD'),
			Route::url('index.php?option=com_messages&view=message&layout=edit'),
			$vName == 'message'
		);

		Submenu::addEntry(
			Lang::txt('COM_MESSAGES_READ'),
			Route::url('index.php?option=com_messages'),
			$vName == 'messages'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	Object
	 */
	public static function getActions()
	{
		$result	= new \Hubzero\Base\Object;

		$actions = JAccess::getActions('com_messages');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, 'com_messages'));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= Html::select('option',	'1',	Lang::txt('COM_MESSAGES_OPTION_READ'));
		$options[]	= Html::select('option',	'0',	Lang::txt('COM_MESSAGES_OPTION_UNREAD'));
		$options[]	= Html::select('option',	'-2',	Lang::txt('JTRASHED'));
		return $options;
	}
}
