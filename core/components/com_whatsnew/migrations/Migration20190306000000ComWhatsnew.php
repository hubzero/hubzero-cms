<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing items from 'components' menu (they have hard-coded menu links elsewhere)
 **/
class Migration20190306000000ComWhatsnew extends Base
{
	/**
	 * List of tables
	 *
	 * @var  array
	 **/
	public static $components = array(
		'com_whatsnew'
	);

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__menu'))
		{
			$components = array();
			foreach (self::$components as $name)
			{
				$components[] = $this->db->quote($name);
			}

			// Check for an admin menu entry...if it's not there, create it
			$query = "DELETE FROM `#__menu` WHERE `menutype` = 'main' AND `title` IN (" . implode(',', $components) . ");";
			$this->db->setQuery($query);
			$this->db->query();

			// Rebuild lft/rgt
			$this->rebuildMainMenu();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__menu'))
		{
			$components = array();
			foreach (self::$components as $option)
			{
				// Check for an admin menu entry...if it's not there, create it
				$query = "SELECT `id` FROM `#__menu` WHERE `menutype` = 'main' AND `title` = " . $this->db->quote($option);
				$this->db->setQuery($query);
				if ($this->db->loadResult())
				{
					continue;
				}

				$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = " . $this->db->quote($option);
				$this->db->setQuery($query);
				$component_id = $this->db->loadResult();

				if (!$component_id)
				{
					continue;
				}

				$alias = substr($option, 4);
				$enabled = 1;

				$query = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)";
				$query .= " VALUES ('main', '{$option}', '{$alias}', '', '{$alias}', 'index.php?option={$option}', 'component', {$enabled}, 1, 1, {$component_id}, 0, 0, 0, 0, '', 0, '', 0, 0, 0, '*', 1)";
				$this->db->setQuery($query);
				$this->db->query();

				// Rebuild lft/rgt
				$this->rebuildMainMenu();
			}
		}
	}

	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 * @return  integer  1 + value of root rgt on success, false on failure
	 */
	private function rebuildMainMenu($parentId = null, $leftId = 0, $level = 0, $path = '')
	{
		// If no parent is provided, try to find it.
		if ($parentId === null)
		{
			// Get the root item.
			$this->db->setQuery("SELECT id FROM `#__menu` WHERE parent_id = 0");
			$parentId = $this->db->loadResult();
			if ($parentId === false)
			{
				return false;
			}
		}

		// Build the structure of the recursive query.
		$rebuild = "SELECT id, alias FROM `#__menu` WHERE parent_id = %d ORDER BY parent_id ASC, ordering ASC, lft ASC";

		// Make a shortcut to database object.

		// Assemble the query to find all children of this node.
		$this->db->setQuery(sprintf($rebuild, (int) $parentId));
		$children = $this->db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildMainMenu($node->id, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query = "UPDATE `#__menu`
				SET lft=" . $this->db->quote((int) $leftId) . ",
				rgt=" . $this->db->quote((int) $rightId) . ",
				level=" . $this->db->quote((int) $level) . ",
				path=" . $this->db->quote($path) . "
				WHERE id=" . (int) $parentId;
		$this->db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion.
		if (!$this->db->execute())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}
}
