<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Users component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class UsersHelper
{
	/**
	 * @var    Object  A cache for the available actions.
	 * @since  1.6
	 */
	protected static $actions;

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		Submenu::addEntry(
			Lang::txt('COM_USERS_SUBMENU_MEMBERS'),
			Route::url('index.php?option=com_members&view=users'),
			$vName == 'users'
		);

		// Groups and Levels are restricted to core.admin
		$canDo = self::getActions();

		if ($canDo->get('core.admin'))
		{
			Submenu::addEntry(
				Lang::txt('COM_USERS_SUBMENU_GROUPS'),
				Route::url('index.php?option=com_users&view=groups'),
				$vName == 'groups'
			);
			Submenu::addEntry(
				Lang::txt('COM_USERS_SUBMENU_LEVELS'),
				Route::url('index.php?option=com_users&view=levels'),
				$vName == 'levels'
			);
			Submenu::addEntry(
				Lang::txt('COM_USERS_SUBMENU_NOTES'),
				Route::url('index.php?option=com_users&view=notes'),
				$vName == 'notes'
			);

			$extension = Request::getString('extension');
			Submenu::addEntry(
				Lang::txt('COM_USERS_SUBMENU_NOTE_CATEGORIES'),
				Route::url('index.php?option=com_categories&extension=com_users'),
				$vName == 'categories' || $extension == 'com_users'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  Object
	 *
	 * @since   1.6
	 * @todo    Refactor to work with notes
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			self::$actions = new \Hubzero\Base\Object;

			$actions = JAccess::getActions('com_users');

			foreach ($actions as $action)
			{
				self::$actions->set($action->name, User::authorise($action->name, 'com_users'));
			}
		}

		return self::$actions;
	}

	/**
	 * Get a list of filter options for the blocked state of a user.
	 *
	 * @return  array  An array of Option elements.
	 *
	 * @since   1.6
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('JENABLED'));
		$options[] = Html::select('option', '1', Lang::txt('JDISABLED'));

		return $options;
	}

	/**
	 * Get a list of filter options for the activated state of a user.
	 *
	 * @return  array  An array of Option elements.
	 *
	 * @since   1.6
	 */
	static function getActiveOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('COM_USERS_ACTIVATED'));
		$options[] = Html::select('option', '1', Lang::txt('COM_USERS_UNACTIVATED'));

		return $options;
	}

	/**
	 * Get a list of filter options for the approved state of a user.
	 *
	 * @return  array  An array of Option elements.
	 *
	 * @since   1.6
	 */
	static function getApprovedOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('COM_USERS_UNAPPROVED'));
		$options[] = Html::select('option', '1', Lang::txt('COM_USERS_APPROVED_MANUALLY'));
		$options[] = Html::select('option', '2', Lang::txt('COM_USERS_APPROVED_AUTOMATICALLY'));

		return $options;
	}

	/**
	 * Get a list of the user groups for filtering.
	 *
	 * @return  array  An array of Option elements.
	 *
	 * @since   1.6
	 */
	static function getGroups()
	{
		$db = App::get('db');
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN '.$db->quoteName('#__usergroups').' AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id, a.title, a.lft, a.rgt' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
			return null;
		}

		foreach ($options as &$option)
		{
			$option->text = str_repeat('- ', $option->level).$option->text;
		}

		return $options;
	}

	/**
	 * Creates a list of range options used in filter select list
	 * used in com_users on users view
	 *
	 * @return  array
	 *
	 * @since   2.5
	 */
	public static function getRangeOptions()
	{
		$options = array(
			Html::select('option', 'today', Lang::txt('COM_USERS_OPTION_RANGE_TODAY')),
			Html::select('option', 'past_week', Lang::txt('COM_USERS_OPTION_RANGE_PAST_WEEK')),
			Html::select('option', 'past_1month', Lang::txt('COM_USERS_OPTION_RANGE_PAST_1MONTH')),
			Html::select('option', 'past_3month', Lang::txt('COM_USERS_OPTION_RANGE_PAST_3MONTH')),
			Html::select('option', 'past_6month', Lang::txt('COM_USERS_OPTION_RANGE_PAST_6MONTH')),
			Html::select('option', 'past_year', Lang::txt('COM_USERS_OPTION_RANGE_PAST_YEAR')),
			Html::select('option', 'post_year', Lang::txt('COM_USERS_OPTION_RANGE_POST_YEAR')),
		);
		return $options;
	}
}
