<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;
use Hubzero\Access\Asset;

/**
 * Migration macro to delete a component entry
 **/
class DeleteComponentEntry extends Macro
{
	/**
	 * Remove component entries from the appropriate table, depending on the CMS version
	 *
	 * @param   string  $name  Component name
	 * @return  bool
	 **/
	public function __invoke($name)
	{
		$table = '#__extensions';
		if ($this->db->tableExists('#__components'))
		{
			$table = '#__components';
		}

		if ($this->db->tableExists($table))
		{
			if (substr($name, 0, 4) !== 'com_')
			{
				$name = 'com_' . strtolower($name);
			}

			// Delete component entry
			$query = $this->db->getQuery()
				->delete($table)
				->whereEquals('name', $name)
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			// Remove the component container in the assets table
			$asset = Asset::oneByName($name);
			if ($asset && $asset->get('id'))
			{
				$asset->destroy();
			}

			$this->log(sprintf('Removed extension entry for component "%s"', $name));

			if ($this->db->tableExists('#__menu'))
			{
				// Check for an admin menu entry...if it's not there, create it
				$query = $this->db->getQuery()
					->delete('#__menu')
					->whereEquals('menutype', 'main')
					->whereEquals('title', $name)
					->toString();
				$this->db->setQuery($query);
				$this->db->query();

				// Rebuild lft/rgt
				$this->rebuildMenu();

				$this->log(sprintf('Removed menu entry for component "%s"', $name));
			}

			return true;
		}

		return false;
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
	private function rebuildMenu($parentId = null, $leftId = 0, $level = 0, $path = '')
	{
		// If no parent is provided, try to find it.
		if ($parentId === null)
		{
			// Get the root item.
			$query = $this->db->getQuery()
				->select('id')
				->from('#__menu')
				->whereEquals('parent_id', 0)
				->toString();

			$this->db->setQuery($query);
			$parentId = $this->db->loadResult();

			if ($parentId === false)
			{
				return false;
			}
		}

		// Build the structure of the recursive query.
		$rebuild = $this->db->getQuery()
			->select('id')
			->select('alias')
			->from('#__menu')
			->whereEquals('parent_id', (int) $parentId)
			->order('parent_id', 'asc')
			->order('ordering', 'asc')
			->order('lft', 'asc')
			->toString();

		// Assemble the query to find all children of this node.
		$this->db->setQuery($rebuild);
		$children = $this->db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildMenu($node->id, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query = $this->db->getQuery()
			->update('#__menu')
			->set(array(
				'lft'   => (int) $leftId,
				'rgt'   => (int) $rightId,
				'level' => (int) $level,
				'path'  => $path
			))
			->whereEquals('id', (int) $parentId)
			->toString();
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
