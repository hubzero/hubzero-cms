<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html;

use Hubzero\Utility\Date;
use Lang;

/**
 * Utility class for creating HTML Grids
 */
class Grid
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @param   integer  $i        The row index.
	 * @param   integer  $value    The value of the boolean field.
	 * @param   string   $taskOn   Task to turn the boolean setting on.
	 * @param   string   $taskOff  Task to turn the boolean setting off.
	 * @return  string   The boolean setting widget.
	 */
	public static function boolean($i, $value, $taskOn = null, $taskOff = null)
	{
		// Load the behavior.
		self::behavior();

		// Build the title.
		$title  = ($value) ? Lang::txt('JYES') : Lang::txt('JNO');
		$title .= '::' . Lang::txt('JGLOBAL_CLICK_TO_TOGGLE_STATE');

		// Build the <a> tag.
		$bool = ($value) ? 'true' : 'false';
		$task = ($value) ? $taskOff : $taskOn;
		$toggle = (!$task) ? false : true;
		$bool .= ($value) ? ' on' : ' off';

		if ($toggle)
		{
			$html = '<a class="state grid_' . $bool . ' hasTip" title="' . $title . '" rel="{\'id\':\'cb' . $i . '\', \'task\':\'' . $task . '\'}" href="#toggle"><span>' . $title . '</span></a>';
		}
		else
		{
			$html = '<a class="state grid_' . $bool . '"><span>' . $title . '</span></a>';
		}

		return $html;
	}

	/**
	 * Method to sort a column in a grid
	 *
	 * @param   string  $title          The link title
	 * @param   string  $order          The order field for the column
	 * @param   string  $direction      The current direction
	 * @param   string  $selected       The selected ordering
	 * @param   string  $task           An optional task override
	 * @param   string  $new_direction  An optional direction for the new column
	 * @return  string
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc')
	{
		$direction = strtolower($direction);
		$index = intval($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html  = '<a href="#" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');return false;" title="' . Lang::txt('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '" class="';
		if ($order == $selected)
		{
			$html .= 'active ' . ($direction == 'desc' ? 'asc' : 'desc') . ' ';
		}
		$html .= 'sort">' . Lang::txt($title);
		$html .= '</a>';

		return $html;
	}

	/**
	 * Method to create a checkbox for a grid row.
	 *
	 * @param   integer  $rowNum      The row index
	 * @param   integer  $recId       The record id
	 * @param   boolean  $checkedOut  True if item is checke out
	 * @param   string   $name        The name of the form element
	 * @return  mixed    String of html with a checkbox if item is not checked out, null if checked out.
	 */
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid')
	{
		if ($checkedOut)
		{
			return '';
		}

		return '<input type="checkbox" id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="Joomla.isChecked(this.checked);" title="' . Lang::txt('JGRID_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
	}

	/**
	 * Displays a checked out icon.
	 *
	 * @param   object   &$row        A data object (must contain checkedout as a property).
	 * @param   integer  $i           The index of the row.
	 * @param   string   $identifier  The property name of the primary key or index of the row.
	 * @return  string
	 */
	public static function checkedOut(&$row, $i, $identifier = 'id')
	{
		$userid = User::get('id');

		$result = false;
		if ($row instanceof \JTable)
		{
			$result = $row->isCheckedOut($userid);
		}
		else
		{
			$result = \JTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ($result)
		{
			$checked = self::_checkedOut($row);
		}
		else
		{
			if ($identifier == 'id')
			{
				$checked = self::id($i, $row->$identifier);
			}
			else
			{
				$checked = self::id($i, $row->$identifier, $result, $identifier);
			}
		}

		return $checked;
	}

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value   Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i       The index
	 * @param   string   $prefix  An optional prefix for the task
	 * @return  string
	 */
	public static function published($value, $i, $prefix = '')
	{
		if (is_object($value))
		{
			$value = $value->published;
		}

		$task   = $value ? 'unpublish' : 'publish';
		$alt    = $value ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
		$action = $value ? Lang::txt('JLIB_HTML_UNPUBLISH_ITEM') : Lang::txt('JLIB_HTML_PUBLISH_ITEM');

		$href = '<a href="#" class="state ' . ($value ? 'publish' : 'unpublish') . '" onclick="return listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '"><span>' . $alt . '</span></a>';

		return $href;
	}
	/**
	 * Method to create a select list of states for filtering
	 * By default the filter shows only published and unpublished items
	 *
	 * @param   string  $filter_state  The initial filter state
	 * @param   string  $published     The Text string for published
	 * @param   string  $unpublished   The Text string for Unpublished
	 * @param   string  $archived      The Text string for Archived
	 * @param   string  $trashed       The Text string for Trashed
	 * @return  string
	 */
	public static function state($filter_state = '*', $published = 'Published', $unpublished = 'Unpublished', $archived = null, $trashed = null)
	{
		$state = array(
			''  => '- ' . Lang::txt('JLIB_HTML_SELECT_STATE') . ' -',
			'P' => Lang::txt($published),
			'U' => Lang::txt($unpublished)
		);

		if ($archived)
		{
			$state['A'] = Lang::txt($archived);
		}

		if ($trashed)
		{
			$state['T'] = Lang::txt($trashed);
		}

		return \JHtml::_(
			'select.genericlist',
			$state,
			'filter_state',
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="Joomla.submitform();"',
				'list.select' => $filter_state,
				'option.key' => null
			)
		);
	}
	/**
	 * Method to create an icon for saving a new ordering in a grid
	 *
	 * @param   array   $rows   The array of rows of rows
	 * @param   string  $image  The image
	 * @param   string  $task   The task to use, defaults to save order
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public static function order($rows, $image = 'save.png', $task = 'saveorder')
	{
		$href = '<a href="javascript:saveorder(' . (count($rows) - 1) . ', \'' . $task . '\')" class="saveorder" title="' . Lang::txt('JLIB_HTML_SAVE_ORDER') . '"><span>' . Lang::txt('JLIB_HTML_SAVE_ORDER') . '</span></a>';

		return $href;
	}

	/**
	 * Method to create a checked out icon with optional overlib in a grid.
	 *
	 * @param   object   &$row     The row object
	 * @param   boolean  $tooltip  True if an overlib with checkout information should be created.
	 * @return  string   HTML for the icon and tooltip
	 */
	protected static function _checkedOut(&$row, $tooltip = 1)
	{
		$hover = '<span class="checkedout">';

		if ($tooltip && isset($row->checked_out_time))
		{
			$text = addslashes(htmlspecialchars($row->editor, ENT_COMPAT, 'UTF-8'));

			$date = with(new Date($row->checked_out_time))->toLocal(Lang::txt('DATE_FORMAT_LC1'));
			$time = with(new Date($row->checked_out_time))->toLocal('H:i');

			$hover = '<span class="editlinktip hasTip" title="' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '::' . $text . '<br />' . $date . '<br />' . $time . '">';
		}

		return $hover . Lang::txt('JLIB_HTML_CHECKED_OUT') . '</span>';
	}

	/**
	 * Method to build the behavior script and add it to the document head.
	 *
	 * @return  void
	 */
	public static function behavior()
	{
		static $loaded;

		if (!$loaded)
		{
			// Add the behavior to the document head.
			\JFactory::getDocument()->addScriptDeclaration(
				'jQuery(document).ready(function($){
					$("a.move_up, a.move_down, a.grid_true, a.grid_false, a.trash")
						.on("click", function(){
							if ($(this).attr("rel")) {
								args = jQuery.parseJSON($(this).attr("rel").replace(/\'/g, \'"\'));
								listItemTask(args.id, args.task);
							}
						});

					$("input.check-all-toggle").on("click", function(){
							if ($(this).checked) {
								$($(this).closest("form")).find("input[type=checkbox]").each(function(i){
									i.checked = true;
								})
							} else {
								$($(this).closest("form")).find("input[type=checkbox]").each(function(i){
									i.checked = false;
								})
							}
					});
				});'
			);

			$loaded = true;
		}
	}
}
