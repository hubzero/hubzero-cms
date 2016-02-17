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

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Models\Orm\Handler;
use Components\Tools\Models\Orm\Rule;
use Components\Tools\Models\Orm\Tool;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'handler.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'rule.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'tool.php';

/**
 * Administrative tools controller for file handlers
 */
class Handlers extends AdminController
{
	/**
	 * Display a list of handler
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = [
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'tool.title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		];

		$this->view->rows = Handler::all()->paginated('limitstart')->ordered('filter_order', 'filter_order_Dir')->rows();

		// Display results
		$this->view->display();
	}

	/**
	 * Create a new handler
	 *
	 * @return void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->view->task = 'edit';
		$this->editTask();
	}

	/**
	 * Edit a handler
	 *
	 * @param  object $handler the handler to edit
	 * @return void
	 */
	public function editTask($handler = null)
	{
		// Hide the menu, force users to save or cancel
		Request::setVar('hidemainmenu', 1);

		if (!isset($handler) || !is_object($handler))
		{
			// Incoming - expecting an array
			$cid = Request::getVar('id', array(0));
			if (!is_array($cid))
			{
				$cid = array($cid);
			}
			$uid = $cid[0];

			$handler = Handler::oneOrNew($uid);
		}

		// Output the HTML
		$this->view->row = $handler;
		$this->view->display();
	}

	/**
	 * Save a handler
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$handler = Handler::oneOrNew(Request::getInt('id'))->set([
			'tool_id' => Request::getInt('tool'),
			'prompt'  => Request::getString('prompt')
		]);

		$rules = [];
		// Set the rule info on the handler
		foreach (Request::getVar('rules', array(), 'post') as $rule)
		{
			// First check and make sure we don't save a completely empty rule
			if (empty($rule['extension']) && empty($rule['quantity']))
			{
				break;
			}

			$rules[] = Rule::oneOrNew(isset($rule['id']) ? $rule['id'] : 0)->set($rule);
		}

		// Save the handler info
		if (!$handler->save())
		{
			// Something went wrong...return errors
			foreach ($handler->getErrors() as $error)
			{
				Notify::error($error);
			}

			// Attach the rules so that we don't lose them
			$handler->attach('rules', $rules);

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($handler);
			return;
		}

		// Now try to save the rules
		if (!$handler->rules()->saveAll($rules))
		{
			foreach ($rules as $rule)
			{
				// Something went wrong...return errors
				foreach ($rule->getErrors() as $error)
				{
					Notify::error($error);
				}
			}

			// Attach the rules so that we don't lose them
			$handler->attach('rules', $rules);

			$this->view->setLayout('edit');
			$this->view->task = 'edit';
			$this->editTask($handler);
			return;
		}

		// Grab the array ids
		$ids = array_map(function ($rule)
		{
			return $rule->id;
		}, $rules);

		// Now process the implicit deletes
		foreach (Rule::whereEquals('handler_id', $handler->id) as $rule)
		{
			if (!in_array($rule->id, $ids))
			{
				$rule->destroy();
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_HANDLER_SUCCESSFULLY_SAVED')
		);
	}

	/**
	 * Delete a handler
	 *
	 * @return void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming (expecting an array)
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0)
		{
			// Loop through the array of ID's and delete
			foreach ($ids as $id)
			{
				$handler = Handler::oneOrFail($id);

				// Delete the rules first, then the handler itself
				if (!$handler->rules->destroyAll() || !$handler->destroy())
				{
					App::redirect(
						Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
						Lang::txt('COM_TOOLS_HANDLERS_DELETE_FAILED')
					);
					return;
				}
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_HANDLERS_SUCCESSFULLY_DELETED', count($ids))
		);
	}
}
