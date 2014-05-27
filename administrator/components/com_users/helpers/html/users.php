<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Extended Utility class for the Users component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class JHtmlUsers
{
	/**
	 * Display an image.
	 *
	 * @param   string	$src  The source of the image
	 *
	 * @return  string  A <img> element if the specified file exists, otherwise, a null string
	 *
	 * @since   2.5
	 */
	public static function image($src)
	{
		$src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
		$file = JPATH_SITE . '/' . $src;

		jimport('joomla.filesystem.path');
		JPath::check($file);

		if (!file_exists($file))
		{
			return '';
		}

		return '<img src="' . JUri::root() . $src . '" alt="" />';
	}

	/**
	 * Displays an icon to add a note for this user.
	 *
	 * @param   integer  $userId  The user ID
	 *
	 * @return  string  A link to add a note
	 *
	 * @since   2.5
	 */
	public static function addNote($userId)
	{
		$title = JText::_('COM_USERS_ADD_NOTE');

		return '<a class="state notes" href="' . JRoute::_('index.php?option=com_users&task=note.add&u_id=' . (int) $userId) . '" title="' . $title . '"><span>' . JText::_('COM_USERS_NOTES') . '</span></a>';
	}

	/**
	 * Displays an icon to filter the notes list on this user.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return	string  A link to apply a filter
	 *
	 * @since   2.5
	 */
	public static function filterNotes($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = JText::_('COM_USERS_FILTER_NOTES');

		return '<a class="state filter" href="' . JRoute::_('index.php?option=com_users&view=notes&filter_search=uid:' . (int) $userId) . '" title="' . $title . '"><span>' . JText::_('COM_USERS_NOTES') . '</span></a>';
	}

	/**
	 * Displays a note icon.
	 *
	 * @param   integer  $count   The number of notes for the user
	 * @param   integer  $userId  The user ID
	 *
	 * @return	string  A link to a modal window with the user notes
	 *
	 * @since   2.5
	 */
	public static function notes($count, $userId)
	{
		if (empty($count))
		{
			return '';
		}

		$title = JText::plural('COM_USERS_N_USER_NOTES', $count);

		return '<a class="modal state notes"' .
			' href="' . JRoute::_('index.php?option=com_users&view=notes&tmpl=component&layout=modal&u_id=' . (int) $userId) . '"' .
			' rel="{handler: \'iframe\', size: {x: 800, y: 450}}" title="' . $title . '"><span>' . JText::_('COM_USERS_NOTES') . '</span></a>';
	}
}
