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

use Hubzero\Component\AdminController;
use Hubzero\Password\Rule;
use Notify;
use Request;
use Config;
use Route;
use Html;
use Lang;
use App;

/**
 * Manage members password rules
 */
class PasswordRules extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.passwords', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('orderup', 'order');
		$this->registerTask('orderdown', 'order');

		parent::execute();
	}

	/**
	 * Display a list of password rules
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'ordering'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort_Dir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$rows = Rule::all()
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// If count is zero, i.e. no records, let's add some default password rules
		if (!$rows->count())
		{
			// Add default rules if we don't have any already
			Rule::defaultContent();

			$rows = Rule::all()
				->order($filters['sort'], $filters['sort_Dir'])
				->paginated('limitstart', 'limit')
				->rows();
		}

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit a password rule
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!$row)
		{
			// Incoming
			$id = Request::getVar('id', array(0));

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Rule::oneOrNew($id);
		}

		$rules_list = $this->rulesList($row->get('rule'));

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('rules_list', $rules_list)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save password rule
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.create', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming password rule edits
		$fields = Request::getVar('fields', array(), 'post');

		// Load the record
		$row = Rule::oneOrNew($fields['id'])->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_SAVE_SUCCESS'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Reorder rules
	 *
	 * @return  void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$cid = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		$id  = $cid[0];
		$inc = ($this->getTask() == 'orderup' ? -1 : 1);

		$row = Rule::oneOrFail($id);
		$row->move($inc);

		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_ORDERING_SAVED'));

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Save order
	 *
	 * @return  void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get the id's
		$cid = Request::getVar('id', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($cid, array(0));

		// Get total and order values
		$total = count($cid);
		$order = Request::getVar('order', array(0), 'post', 'array');
		\Hubzero\Utility\Arr::toInteger($order, array(0));

		// Update ordering values
		for ($i=0; $i < $total; $i++)
		{
			$row = Rule::oneOrFail((int) $cid[$i]);

			if ($row->get('ordering') != $order[$i])
			{
				$row->set('ordering', $order[$i]);

				if (!$row->save())
				{
					App::abort(500, $row->getError());
				}
			}
		}

		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_ORDERING_SAVED'));

		// Output message and redirect
		$this->cancelTask();
	}

	/**
	 * Toggle a password rule between enabled and disabled
	 *
	 * @return  void
	 */
	public function toggle_enabledTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming (we're expecting an array)
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Loop through the IDs
		foreach ($ids as $id)
		{
			// Load the billboard
			$row = Rule::oneOrFail($id);

			$enabled = ($row->get('enabled') ? 0 : 1);

			$row->set('enabled', $enabled);

			if (!$row->save())
			{
				App::abort(500, $row->getError());
			}
		}

		// Output message and redirect
		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_TOGGLE_ENABLED'));

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Restore default password rules (found in password_rules table class)
	 *
	 * @return  void
	 */
	public function restore_default_contentTask()
	{
		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Get the object
		Rule::defaultContent(true);

		// Output message and redirect
		Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_RESTORED'));

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes [a] password rule(s)
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		if (!is_array($ids))
		{
			$ids = array($ids);
		}

		$i = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = Rule::oneOrFail($id);

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
			Notify::warning(Lang::txt('COM_MEMBERS_PASSWORD_RULES_DELETE_NO_ROW_SELECTED'));
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_PASSWORD_RULES_DELETE_SUCCESS'));
		}

		$this->cancelTask();
	}

	/**
	 * Build rules select list
	 *
	 * @param   string  $current_rule
	 * @return  void
	 */
	public function rulesList($current_rule='')
	{
		$rules   = array();
		$rules[] = Html::select('option', 'minClassCharacters',  'minClassCharacters',  'value', 'text');
		$rules[] = Html::select('option', 'minPasswordLength',   'minPasswordLength',   'value', 'text');
		$rules[] = Html::select('option', 'maxPasswordLength',   'maxPasswordLength',   'value', 'text');
		$rules[] = Html::select('option', 'minUniqueCharacters', 'minUniqueCharacters', 'value', 'text');
		$rules[] = Html::select('option', 'notBlacklisted',      'notBlacklisted',      'value', 'text');
		$rules[] = Html::select('option', 'notNameBased',        'notNameBased',        'value', 'text');
		$rules[] = Html::select('option', 'notUsernameBased',    'notUsernameBased',    'value', 'text');
		$rules[] = Html::select('option', 'notReused',           'notReused',           'value', 'text');
		$rules[] = Html::select('option', 'notStale',            'notStale',            'value', 'text');

		$rselected = $current_rule;

		return Html::select('genericlist', $rules, 'fields[rule]', '', 'value', 'text', $rselected, 'field-rule', false, false);
	}
}
