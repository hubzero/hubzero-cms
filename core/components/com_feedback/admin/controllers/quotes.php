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

namespace Components\Feedback\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Feedback\Models\Quote;
use Components\Members\Models\Member;
use Filesystem;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Feedback controller class for quotes
 */
class Quotes extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display a list of quotes
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.search',
				'search',
				''
			)),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.sortby',
				'filter_order',
				'date'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$record = Quote::all();

		if ($filters['search'])
		{
			$record->whereLike('fullname', $filters['search']);
		}

		$rows = $record
			->ordered('filter_order', 'filter_order_Dir')
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

		if (!is_object($row))
		{
			// Incoming ID
			$id = Request::getVar('id', array(0));
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$row = Quote::oneOrNew($id);
		}

		if (!$row->get('id'))
		{
			if ($username = Request::getVar('username', ''))
			{
				$profile = Member::oneByUsername($username);

				$row->set('fullname', $profile->get('name'));
				$row->set('org', $profile->get('organization'));
				$row->set('user_id', $profile->get('uidNumber'));
			}
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate model and bind the incoming data to it
		$row = Quote::oneOrNew($fields['id'])->set($fields);

		// Validate and save the data
		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($row);
		}

		// Build file path
		$path = $row->filespace() . DS . $row->get('id');

		if (is_dir($path))
		{
			// Remove pictures that were marked for deletion
			$existing = Request::getVar('existingPictures', array(), 'post', 'none', 2);
			$pictures = Filesystem::files($path);

			foreach ($pictures as $picture)
			{
				$picture = ltrim($picture, DS);

				if (!in_array($picture, $existing))
				{
					if (!Filesystem::delete($path . DS . $picture))
					{
						Notify::error(Lang::txt('Failed to remove picture "%s"', $picture));
					}
				}
			}
		}

		// Get the list of uploaded files
		$files = Request::getVar('files', null, 'files', 'array');

		if ($files)
		{
			if (!is_dir($path))
			{
				Filesystem::makeDirectory($path);
			}

			foreach ($files['name'] as $fileIndex => $file)
			{
				Filesystem::upload($files['tmp_name'][$fileIndex], $path . DS . $files['name'][$fileIndex]);
			}
		}

		// Notify the user that the entry was saved
		Notify::success(Lang::txt('COM_FEEDBACK_QUOTE_SAVED', $row->get('fullname')));

		if ($this->getTask() == 'apply')
		{
			// Display the edit form
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (!count($ids))
		{
			Notify::warning(Lang::txt('COM_FEEDBACK_SELECT_QUOTE_TO_DELETE'));

			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			$row = Quote::oneOrFail(intval($id));

			// Delete the quote
			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_FEEDBACK_REMOVED'));
		}

		$this->cancelTask();
	}
}
