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

namespace Components\Members\Helpers;

use Hubzero\Base\Object;
use Request;
use Submenu;
use Route;
use Lang;
use Html;
use User;

/**
 * Members admin helper
 */
class Admin
{
	/**
	 * A cache for the available actions.
	 *
	 * @var  object
	 */
	protected static $actions;

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object  Object
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			self::$actions = new Object;

			$actions = \JAccess::getActions('com_members');

			foreach ($actions as $action)
			{
				self::$actions->set($action->name, User::authorise($action->name, 'com_members'));
			}
		}

		return self::$actions;
	}

	/**
	 * Get a list of filter options for the blocked state of a user.
	 *
	 * @return  array  An array of Option elements.
	 */
	public static function getStateOptions()
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
	 */
	public static function getActiveOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('COM_MEMBERS_ACTIVATED'));
		$options[] = Html::select('option', '1', Lang::txt('COM_MEMBERS_UNACTIVATED'));

		return $options;
	}

	/**
	 * Get a list of filter options for the approved state of a user.
	 *
	 * @return  array  An array of Option elements.
	 */
	public static function getApprovedOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = Html::select('option', '0', Lang::txt('COM_MEMBERS_UNAPPROVED'));
		$options[] = Html::select('option', '1', Lang::txt('COM_MEMBERS_APPROVED_MANUALLY'));
		$options[] = Html::select('option', '2', Lang::txt('COM_MEMBERS_APPROVED_AUTOMATICALLY'));

		return $options;
	}

	/**
	 * Get a list of the user groups for filtering.
	 *
	 * @return  array  An array of Option elements.
	 */
	public static function getAccessGroups()
	{
		/*
		$ug = Usergroup::all();
		$options = $ug
			->select('a.id', 'value')
			->select('a.title', 'text')
			->select('b.id', 'level', true)
			->from($ug->getTableName(), 'a')
			->joinRaw($ug->getTableName(), 'a.lft > b.lft AND a.rgt < b.rgt', 'left')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft', 'asc')
			->rows();
		*/

		$db = \App::get('db');
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
			throw new \Exception($db->getErrorMsg(), 500);
			return null;
		}

		foreach ($options as &$option)
		{
			//$option->set('text', str_repeat('- ', $option->get('level')) . $option->get('text'));
			$option->text = str_repeat('- ', $option->level) . $option->text;
		}

		return $options;
	}

	/**
	 * Creates a list of range options used in filter select list
	 * used in com_users on users view
	 *
	 * @return  array
	 */
	public static function getRangeOptions()
	{
		$options = array(
			Html::select('option', 'today', Lang::txt('COM_MEMBERS_OPTION_RANGE_TODAY')),
			Html::select('option', 'past_week', Lang::txt('COM_MEMBERS_OPTION_RANGE_PAST_WEEK')),
			Html::select('option', 'past_1month', Lang::txt('COM_MEMBERS_OPTION_RANGE_PAST_1MONTH')),
			Html::select('option', 'past_3month', Lang::txt('COM_MEMBERS_OPTION_RANGE_PAST_3MONTH')),
			Html::select('option', 'past_6month', Lang::txt('COM_MEMBERS_OPTION_RANGE_PAST_6MONTH')),
			Html::select('option', 'past_year', Lang::txt('COM_MEMBERS_OPTION_RANGE_PAST_YEAR')),
			Html::select('option', 'post_year', Lang::txt('COM_MEMBERS_OPTION_RANGE_POST_YEAR')),
		);
		return $options;
	}

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	public static function getUserInput($name, $id, $value, $class = null, $size = null, $onchange = null, $readonly = false)
	{
		// Initialize variables.
		$html = array();
		$link = Route::url('index.php?option=com_members&controller=members&task=modal&tmpl=component&field=' . $id);

		// Initialize some field attributes.
		$attr  = $class ? ' class="' . (string) $class . '"' : '';
		$attr .= $size ? ' size="' . (int) $size . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = (string) $onchange;

		// Load the modal behavior script.
		Html::behavior('modal', 'a.modal_' . $id);

		// Build the script.
		$script = array();
		$script[] = '	function jSelectUser_' . $id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $id . '_name").value = title;';
		$script[] = '			' . $onchange;
		$script[] = '		}';
		$script[] = '		$.fancybox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		Document::addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$user = User::getInstance($value);
		if (!$user || !$user->get('id'))
		{
			$user->set('name', Lang::txt('JLIB_FORM_SELECT_USER'));
		}

		// Create a dummy text field with the user name.
		if (!$readonly)
		{
			$html[] = '<div class="input-modal">';
			$html[] = '	<span class="input-cell">';
		}
		$html[] = '		<input type="text" id="' . $id . '_name"' . ' value="' . htmlspecialchars($user->get('name'), ENT_COMPAT, 'UTF-8') . '" disabled="disabled"' . $attr . ' />';

		// Create the user select button.
		if (!$readonly)
		{
			$html[] = '	</span>';
			$html[] = '	<span class="input-cell">';
			$html[] = '		<a class="button modal_' . $id . '" title="' . Lang::txt('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '			' . Lang::txt('JLIB_FORM_CHANGE_USER');
			$html[] = '		</a>';
			$html[] = '	</span>';
			$html[] = '</div>';
		}

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $id . '_id" name="' . $name . '" value="' . (int) $value . '" />';

		return implode("\n", $html);
	}
}
