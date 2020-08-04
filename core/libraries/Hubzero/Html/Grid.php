<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html;

use Hubzero\Html\Builder\Behavior;

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
		return Builder\Grid::boolean($i, $value, $taskOn, $taskOff);
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
		return Builder\Grid::sort($title, $order, $direction, $selected, $task, $new_direction);
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
		return Builder\Grid::id($rowNum, $recId, $checkedOut, $name);
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
		return Builder\Grid::checkedOut($row, $i, $identifier);
	}

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value     Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i         The index
	 * @param   string   $prefix    An optional prefix for the task
	 * @param   string   $checkbox  Checkbox ID prefix
	 * @return  string
	 */
	public static function published($value, $i, $prefix = '', $checkbox = 'cb')
	{
		return Builder\Grid::published($value, $i, $prefix, $checkbox);
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @param   array   $config  An array of configuration options.
	 *                           This array can contain a list of key/value pairs where values are boolean
	 *                           and keys can be taken from 'published', 'unpublished', 'archived', 'trash', 'all'.
	 *                           These pairs determine which values are displayed.
	 * @return  string  The HTML code for the select tag
	 */
	public static function publishedOptions($config = array())
	{
		return Builder\Grid::publishedOptions($config);
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
		return Builder\Grid::state($filter_state, $published, $unpublished, $archived, $trashed);
	}

	/**
	 * Method to create an icon for saving a new ordering in a grid
	 *
	 * @param   array   $rows  The array of rows of rows
	 * @param   string  $cls   Classname to apply
	 * @param   string  $task  The task to use, defaults to save order
	 * @return  string
	 */
	public static function order($rows, $cls = 'saveoder', $task = 'saveorder')
	{
		return Builder\Grid::order($rows, $cls, $task);
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
			Builder\Behavior::framework();

			$loaded = true;
		}
	}

	/**
	 * Creates a order-up action icon.
	 *
	 * @param   integer  $i         The row index.
	 * @param   string   $task      An optional task to fire.
	 * @param   mixed    $prefix    An optional task prefix or an array of options
	 * @param   string   $text      An optional text to display
	 * @param   boolean  $enabled   An optional setting for access control on the action.
	 * @param   string   $checkbox  An optional prefix for checkboxes.
	 * @return  string   The required HTML.
	 */
	public static function orderUp($i, $task = 'orderup', $prefix = '', $text = 'JLIB_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb')
	{
		return Builder\Grid::orderUp($i, $task, $prefix, $text, $enabled, $checkbox);
	}

	/**
	 * Creates a order-down action icon.
	 *
	 * @param   integer  $i         The row index.
	 * @param   string   $task      An optional task to fire.
	 * @param   mixed    $prefix    An optional task prefix or an array of options
	 * @param   string   $text      An optional text to display
	 * @param   boolean  $enabled   An optional setting for access control on the action.
	 * @param   string   $checkbox  An optional prefix for checkboxes.
	 * @return  string   The required HTML.
	 */
	public static function orderDown($i, $task = 'orderdown', $prefix = '', $text = 'JLIB_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb')
	{
		return Builder\Grid::orderDown($i, $task, $prefix, $text, $enabled, $checkbox);
	}

	/**
	 * Returns a isDefault state on a grid
	 *
	 * @param   integer       $value     The state value.
	 * @param   integer       $i         The row index
	 * @param   string|array  $prefix    An optional task prefix or an array of options
	 * @param   boolean       $enabled   An optional setting for access control on the action.
	 * @param   string        $checkbox  An optional prefix for checkboxes.
	 * @return  string  The HTML code
	 */
	public static function isdefault($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
	{
		return Builder\Grid::isdefault($value, $i, $prefix, $enabled, $checkbox);
	}

	/**
	 * Returns an action on a grid
	 *
	 * @param   integer  $i               The row index
	 * @param   string   $task            The task to fire
	 * @param   mixed    $prefix          An optional task prefix or an array of options
	 * @param   string   $text            An optional text to display
	 * @param   string   $active_title    An optional active tooltip to display if $enable is true
	 * @param   string   $inactive_title  An optional inactive tooltip to display if $enable is true
	 * @param   boolean  $tip             An optional setting for tooltip
	 * @param   string   $active_class    An optional active HTML class
	 * @param   string   $inactive_class  An optional inactive HTML class
	 * @param   boolean  $enabled         An optional setting for access control on the action.
	 * @param   boolean  $translate       An optional setting for translation.
	 * @param   string   $checkbox        An optional prefix for checkboxes.
	 * @return  string   The Html code
	 */
	public static function action($i, $task, $prefix = '', $text = '', $active_title = '', $inactive_title = '', $tip = false, $active_class = '', $inactive_class = '', $enabled = true, $translate = true, $checkbox = 'cb')
	{
		return Builder\Grid::action($i, $task, $prefix, $text, $active_title, $inactive_title, $tip, $active_class, $inactive_class, $enabled, $translate, $checkbox);
	}
}
