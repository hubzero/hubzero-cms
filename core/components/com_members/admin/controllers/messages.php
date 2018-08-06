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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Message;
use Notify;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * Manage messaging settings
 */
class Messages extends AdminController
{
	/**
	 * Display a list of messaging settings
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$filters = array(
			'component' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.component',
				'component',
				''
			)),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'c.name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		$obj = Message\Component::blank();

		// Get a record count
		$total = $obj->getCount($filters, true);

		// Get records
		$rows = $obj->getRecords($filters, true);

		$components = $obj->getComponents();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', $total)
			->set('rows', $rows)
			->set('components', $components)
			->display();
	}

	/**
	 * Create a new record
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Output the HTML
		$this->editTask();
	}

	/**
	 * Edit a record
	 *
	 * @param   object  $row  Database row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			// Initiate database class and load info
			$row = Message\Component::oneOrNew($id);
		}

		$this->view->row = $row;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			Notify::error($error);
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry and display edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask();
	}

	/**
	 * Save an entry and redirect to main listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		// Load the record
		$row = Message\Component::blank()->set($fields);

		// Store content
		if (!$row->save())
		{
			$this->setError($row->getError(), 'error');
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('Message Action saved'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Delete a record
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			$notify = Message\Notify::blank();

			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = Message\Component::oneOrFail($id);

				// Remove any associations
				$notify->deleteByType($row->get('action'));

				// Remove the record
				$row->destroy();
			}
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('Message Action removed'));

		$this->cancelTask();
	}

	/**
	 * Delete a record
	 *
	 * @return  void
	 */
	public function settingsTask()
	{
		$id = Request::getInt('id', 0);

		$member = User::getInstance($id);
		if (!$member || !$member->get('id'))
		{
			// Output messsage and redirect
			App::abort(404, Lang::txt('Unknown or invalid member ID'));
		}

		$database = App::get('db');

		$xmc = Message\Component::blank();
		$components = $xmc->getRecords();

		/*if ($components)
		{
			$this->setError(Lang::txt('No vlaues found'));
		}*/

		$settings = array();
		foreach ($components as $component)
		{
			$settings[$component->get('action')] = array();
		}

		// Fetch message methods
		$notimethods = Event::trigger('xmessage.onMessageMethods', array());

		// A var for storing the default notification method
		$default_method = null;

		// Instantiate our notify object
		$notify = Message\Notify::blank();

		// Get the user's selected methods
		$methods = $notify->getRecords($member->get('id'));
		if ($methods->count())
		{
			foreach ($methods as $method)
			{
				$settings[$method->get('type')]['methods'][] = $method->get('method');
				$settings[$method->get('type')]['ids'][$method->get('method')] = $method->get('id');
			}
		}
		else
		{
			$default_method = \Plugin::params('members', 'messages')->get('default_method');
		}

		// Fill in any settings that weren't set.
		foreach ($settings as $key => $val)
		{
			if (count($val) <= 0)
			{
				// If the user has never changed their settings, set up the defaults
				if ($default_method !== null)
				{
					$settings[$key]['methods'][] = 'internal';
					$settings[$key]['methods'][] = $default_method;
					$settings[$key]['ids']['internal'] = 0;
					$settings[$key]['ids'][$default_method] = 0;
				}
				else
				{
					$settings[$key]['methods'] = array();
					$settings[$key]['ids'] = array();
				}
			}
		}

		$this->view
			->set('member', $member)
			->set('settings', $settings)
			->set('notimethods', $notimethods)
			->set('components', $components)
			->display();
	}

	/**
	 * Save settings
	 *
	 * @return  void
	 */
	public function savesettingsTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			return $this->settingsTask();
		}

		$id = Request::getInt('id', 0);

		$member = User::getInstance($id);
		if (!$member || !$member->get('id'))
		{
			// Output messsage and redirect
			App::abort(404, Lang::txt('Unknown or invalid member ID'));
		}

		$database = App::get('db');

		// Incoming
		$settings = Request::getArray('settings', array());
		$ids = Request::getArray('ids', array());

		// Ensure we have data to work with
		if ($settings && count($settings) > 0)
		{
			// Loop through each setting
			foreach ($settings as $key => $value)
			{
				foreach ($value as $v)
				{
					if ($v)
					{
						// Instantiate a Notify object and set its values
						$notify = Message\Notify::blank();
						$notify->set('uid', $member->get('id'));
						$notify->set('method', $v);
						$notify->set('type', $key);
						$notify->set('priority', 1);

						// Do we have an ID for this setting?
						// Determines if the store() method is going to INSERT or UPDATE
						if ($ids[$key][$v] > 0)
						{
							$notify->set('id', $ids[$key][$v]);
							$ids[$key][$v] = -1;
						}

						// Save
						if (!$notify->save())
						{
							Notify::error(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->get('method')));
						}
					}
				}
			}

			foreach ($ids as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					if ($v > 0)
					{
						$notify = Message\Notify::oneOrFail($v);
						$notify->destroy();
					}
				}
			}

			// If they previously had everything turned off, we need to remove that entry saying so
			$notify = Message\Notify::blank();
			$records = $notify->getRecords($member->get('uidNumber'), 'all');
			if ($records)
			{
				foreach ($records as $record)
				{
					$record->destroy();
				}
			}
		}
		else
		{
			// This creates a single entry to let the system know that the user has explicitly chosen "none" for all options
			// It ensures we can know the difference between someone who has never changed their settings (thus, no database entries)
			// and someone who purposely wants everything turned off.
			$notify = Message\Notify::blank();
			$notify->set('uid', $member->get('uidNumber'));

			$records = $notify->getRecords($member->get('uidNumber'), 'all');
			if (!$records->count())
			{
				$notify->deleteByUser($member->get('uidNumber'));
				$notify->set('method', 'none');
				$notify->set('type', 'all');
				$notify->set('priority', 1);
				if (!$notify->save())
				{
					$this->setError(Lang::txt('PLG_MEMBERS_MESSAGES_ERROR_NOTIFY_FAILED', $notify->get('method')));
				}
			}
		}

		$tmpl = Request::getWord('tmpl');

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=settings&id=' . $id . ($tmpl ? '&tmpl=' . $tmpl : ''), false)
		);
	}

	/**
	 * Build a select list of methods
	 *
	 * @param   array   $notimethods  Methods
	 * @param   string  $name         Field name
	 * @param   array   $values       Option values
	 * @param   array   $ids          Option IDs
	 * @return  string
	 */
	public static function selectMethod($notimethods, $name, $values=array(), $ids=array())
	{
		$out = '';
		$i = 0;
		foreach ($notimethods as $notimethod)
		{
			$out .= '<td>' . "\n";
			$out .= "\t" . '<input type="checkbox" name="settings[' . $name . '][]" class="opt-' . $notimethod . '" value="' . $notimethod . '"';
			$out .= (in_array($notimethod, $values))
						  ? ' checked="checked"'
						  : '';
			$out .= ' />' . "\n";
			$out .= "\t" . '<input type="hidden" name="ids[' . $name . '][' . $notimethod . ']" value="';
			if (isset($ids[$notimethod]))
			{
				$out .= $ids[$notimethod];
			}
			else
			{
				$out .= '0';
			}
			$out .= '" />' . "\n";
			$out .= "\t" . '</td>' . "\n";
			$i++;
		}
		return $out;
	}
}
