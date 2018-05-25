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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'todo.php';

require_once __DIR__ . DS . 'todoentry.php';
require_once __DIR__ . DS . 'comment.php';

use Hubzero\Base\Model;

/**
 * Project Todo model
 */
class Todo extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\Todo';

	/**
	 * Entry
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
	 * Registry
	 *
	 * @var object
	 */
	public $config = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');

		$this->_tbl = new \Components\Projects\Tables\Todo($this->_db);

		$this->config = \Component::params('com_projects');
	}

	/**
	 * Returns a reference to a todo model
	 *
	 * @param   mixed   $oid  TODO ID
	 * @return  object  Todo
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
	 * @param   integer  $projectid
	 * @param   mixed    $filters
	 * @return  array
	 */
	public function getListName($projectid = null, $filters=array())
	{
		$color = is_array($filters) ? $filters['todolist'] : $filters;
		return $this->_tbl->getListName($projectid, $color);
	}

	/**
	 * Get lists
	 *
	 * @param   integer  $projectid
	 * @return  array
	 */
	public function getLists($projectid = null)
	{
		return $this->_tbl->getTodoLists($projectid);
	}

	/**
	 * Set and get a specific offering
	 *
	 * @param   integer  $id
	 * @return  object
	 */
	public function entry($id=null)
	{
		if (!isset($this->_entry)
		 || ($id !== null && (int) $this->_entry->get('id') != $id))
		{
			$this->_entry = Entry::getInstance($id);
		}
		return $this->_entry;
	}

	/**
	 * Get a list of todo items
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 *
	 * @param   string  $rtrn
	 * @param   array   $filters
	 * @return  array
	 */
	public function entries($rtrn='list', $filters=array())
	{
		$results = array();

		switch (strtolower($rtrn))
		{
			case 'count':
				$filters['count'] = 1;
				return (int) $this->_tbl->getTodos(null, $filters);
			break;

			case 'list':
			case 'results':
			default:
				if ($results = $this->_tbl->getTodos(null, $filters))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new Entry($result);
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
	 * @param   boolean  $check  Perform data validation check?
	 * @return  boolean  False if error, True on success
	 */
	public function store($check=true)
	{
		// Do nothing here yet.
		return true;
	}
}
