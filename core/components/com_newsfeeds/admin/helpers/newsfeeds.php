<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Newsfeeds component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @since		1.6
 */
class NewsfeedsHelper
{
	public static $extension = 'com_newsfeeds';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		Submenu::addEntry(
			Lang::txt('COM_NEWSFEEDS_SUBMENU_NEWSFEEDS'),
			Route::url('index.php?option=com_newsfeeds&view=newsfeeds'),
			$vName == 'newsfeeds'
		);
		Submenu::addEntry(
			Lang::txt('COM_NEWSFEEDS_SUBMENU_CATEGORIES'),
			Route::url('index.php?option=com_categories&extension=com_newsfeeds'),
			$vName == 'categories'
		);
		if ($vName=='categories')
		{
			Toolbar::title(
				Lang::txt('COM_CATEGORIES_CATEGORIES_TITLE', Lang::txt('com_newsfeeds')),
				'newsfeeds-categories'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	Object
	 */
	public static function getActions($categoryId = 0, $newsfeedId = 0)
	{
		$result	= new \Hubzero\Base\Object;

		if (empty($categoryId))
		{
			$assetName = 'com_newsfeeds';
			$level = 'component';
		}
		else
		{
			$assetName = 'com_newsfeeds.category.'.(int) $categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions('com_newsfeeds', $level);

		foreach ($actions as $action)
		{
			$result->set($action->name,	User::authorise($action->name, $assetName));
		}

		return $result;
	}
}
