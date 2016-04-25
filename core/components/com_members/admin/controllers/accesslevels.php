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

namespace Components\Members\Admin\Controllers;

use Hubzero\Access\Viewlevel;
use Hubzero\Access\Group as Accessgroup;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Manage user Access Levels
 */
class Accesslevels extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.access', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display password blacklist
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$entries = Viewlevel::all();

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!$row)
		{
			// Incoming
			$id = Request::getVar('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Viewlevel::oneOrNew($id);
		}

		$row->set('rules', json_decode($row->get('rules')));

		$groups = Accessgroup::all()
			->order('lft', 'asc')
			->rows();

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('groups', $groups)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming password blacklist edits
		$fields = Request::getVar('fields', array(), 'post');

		// Load the record
		$row = Viewlevel::onrOrNew($fields['id'])->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_ACCESSLEVEL_SAVE_SUCCESS'));

		// Fall through to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes one or mroe entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->option))
		{
			return $this->cancelTask();
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Populate the list once.
			$levelsInUse = array();

			$db = App::get('db');
			$query = $db->getQuery(true)
				->select('DISTINCT access');

			// Get all the tables and the prefix
			$tables = $db->getTableList();
			$prefix = $db->getPrefix();

			foreach ($tables as $table)
			{
				// Get all of the columns in the table
				$fields = $db->getTableColumns($table);

				// We are looking for the access field.  If custom tables are using something other
				// than the 'access' field they are on their own unfortunately.
				// Also make sure the table prefix matches the live db prefix (eg, it is not a "bak_" table)
				if ((strpos($table, $prefix) === 0) && (isset($fields['access'])))
				{
					// Lookup the distinct values of the field.
					$query->clear('from')
						->from($db->quoteName($table));
					$db->setQuery($query);

					$values = $db->loadColumn();
					$error  = $db->getErrorMsg();

					// Check for DB error.
					if ($error)
					{
						Notify::error($error);
						continue;
					}

					$levelsInUse = array_merge($levelsInUse, $values);
				}
			}

			// Get uniques
			$levelsInUse = array_unique($levelsInUse);

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = Viewlevel::oneOrNew($id);

				if (in_array($row->get('id'), $levelsInUse))
				{
					Notify::warning(Lang::txt('COM_MEMBERS_ERROR_VIEW_LEVEL_IN_USE', $row->get('id'), $row->get('title')));
					continue;
				}

				// Remove the record
				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				$i++;
			}
		}
		else // no rows were selected
		{
			Notify::warning(Lang::txt('COM_MEMBERS_ACCESSLEVELS_DELETE_NO_ROW_SELECTED'));
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_ACCESSLEVELS_DELETE_SUCCESS'));
		}

		$this->cancelTask();
	}
}
