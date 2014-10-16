<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.todo.php');

require_once(__DIR__ . '/todoentry.php');

/**
 * Project Todo model
 */
class ProjectModelTodo extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'ProjectTodo';

	/**
	 * ProjectModelTodoEntry
	 *
	 * @var object
	 */
	private $_entry = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_entries = null;

	/**
	 * JParameter
	 *
	 * @var object
	 */
	public $config = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new ProjectTodo($this->_db);

		$this->config = JComponentHelper::getParams('com_projects');
	}

	/**
	 * Returns a reference to a todo model
	 *
	 * @param      mixed $oid TODO ID
	 * @return     object KbModelArticle
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Get list name
	 *
	 * @param      mixed $filters array
	 * @return     array
	 */
	public function getListName($projectid = NULL, $filters=array())
	{
		$color = is_array($filters) ? $filters['todolist'] : $filters;
		return $this->_tbl->getListName($projectid, $color);
	}

	/**
	 * Get lists
	 *
	 * @param      mixed $filters array
	 * @return     array
	 */
	public function getLists($projectid = NULL)
	{
		return $this->_tbl->getTodoLists($projectid);
	}

	/**
	 * Set and get a specific offering
	 *
	 * @return     void
	 */
	public function entry($id=null)
	{
		if (!isset($this->_entry)
		 || ($id !== null && (int) $this->_entry->get('id') != $id))
		{
			$this->_entry = ProjectModelTodoEntry::getInstance($id);
		}
		return $this->_entry;
	}

	/**
	 * Get a list of todo items
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function entries($rtrn='list', $filters=array())
	{
		$results = array();

		switch (strtolower($rtrn))
		{
			case 'count':
				$filters['count'] = 1;
				return (int) $this->_tbl->getTodos(NULL, $filters);
			break;

			case 'list':
			case 'results':
			default:
				if ($results = $this->_tbl->getTodos(NULL, $filters))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new ProjectModelTodoEntry($result);
					}
				}
				else
				{
					$results = array();
				}
				return new \Hubzero\Base\ItemList($results);
			break;
		}
		return null;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Do nothing here yet.
	}
}

