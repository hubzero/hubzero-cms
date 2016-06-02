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
use Components\Members\Models\Member;
use Components\Members\Models\Quota;
use Components\Members\Models\Quota\Category;
use Filesystem;
use Request;
use Notify;
use Route;
use Html;
use User;
use Lang;
use App;

/**
 * Manage member quotas
 */
class Quotas extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.quotas', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display member quotas
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.quotas.search',
				'search',
				''
			)),
			'search_field' => urldecode(Request::getState(
				$this->_option . '.quotas.search_field',
				'search_field',
				'name'
			)),
			'sort' => Request::getState(
				$this->_option . '.quotas.sort',
				'filter_order',
				'user_id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.quotas.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			'class_alias' => Request::getState(
				$this->_option . '.quotas.class_alias',
				'class_alias',
				''
			)
		);

		$cats  = Category::blank()->getTableName();
		$users = Member::blank()->getTableName();

		$entries = Quota::all();

		$entries
			->select($entries->getTableName() . '.*')
			->select($cats . '.alias', 'class_alias')
			->select($users . '.name')
			->select($users . '.username')
			->join($cats, $cats . '.id', $entries->getTableName() . '.class_id', 'left')
			->join($users, $users . '.id', $entries->getTableName() . '.user_id', 'left');

		if ($filters['search'])
		{
			$entries->whereLike($users . '.name', strtolower((string)$filters['search']), 1)
				->orWhereLike($users . '.username', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['class_alias'])
		{
			$entries->whereEquals('class_alias', $filters['class_alias']);
		}

		// Get records
		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$classes = Category::all()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('classes', $classes)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit a member quota
	 *
	 * @param   integer  $id  ID of entry to edit
	 * @return  void
	 */
	public function editTask($row=null)
	{
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

			$row = Quota::oneOrNew($id);
		}

		// Build classes select option
		$categories = Category::all()
			->ordered()
			->rows();

		$selected = '';
		$options  = array(
			Html::select('option', '0', Lang::txt('COM_MEMBERS_QUOTA_CUSTOM'), 'value', 'text')
		);

		foreach ($categories as $class)
		{
			$options[] = Html::select('option', $class->get('id'), $class->get('alias'), 'value', 'text');
			if ($class->get('id') == $row->get('class_id'))
			{
				$selected = $class->get('id');
			}
		}

		$classes = Html::select('genericlist', $options, 'fields[class_id]', '', 'value', 'text', $selected, 'class_id', false, false);

		$du = $this->getQuotaUsageTask('array', $row->get('id'));

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('classes', $classes)
			->set('du', $du)
			->setLayout('edit')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save user quota
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming fields
		$fields = Request::getVar('fields', array(), 'post');

		// Load the profile
		$row = Quota::oneOrNew($fields['id']);

		if ($fields['class_id'])
		{
			$class = Category::oneOrNew($fields['class_id']);

			if ($class->get('id'))
			{
				$fields['hard_files']  = $class->get('hard_files');
				$fields['soft_files']  = $class->get('soft_files');
				$fields['hard_blocks'] = $class->get('hard_blocks');
				$fields['soft_blocks'] = $class->get('soft_blocks');
			}
		}

		$user = User::getInstance($fields['user_id']);

		if (!is_object($user) || !$user->get('id'))
		{
			Notify::error(Lang::txt('COM_MEMBERS_QUOTA_USER_NOT_FOUND'));
			return $this->editTask($row);
		}

		$fields['user_id'] = $user->get('id');

		$row->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_QUOTA_SAVE_SUCCESSFUL'));

		// Redirect
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Restore member to default quota class
	 *
	 * @return  void
	 */
	public function restoreDefaultTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			if (!Quota::setDefaultClass($ids))
			{
				Notify::error(Lang::txt('COM_MEMBERS_QUOTA_MISSING_DEFAULT_CLASS'));
			}
			else
			{
				Notify::success(Lang::txt('COM_MEMBERS_QUOTA_SET_TO_DEFAULT'));
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			Notify::warning(Lang::txt('COM_MEMBERS_QUOTA_DELETE_NO_ROWS'));
		}

		// Redirect
		$this->cancelTask();
	}

	/* ------------- */
	/* Classes tasks */
	/* ------------- */

	/**
	 * Display quota classes
	 *
	 * @return  void
	 */
	public function displayClassesTask()
	{
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.classes.sort',
				'filter_order',
				'id'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.classes.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get records
		$rows = Category::all()
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
	 * Create a new quota class
	 *
	 * @return  void
	 */
	public function addClassTask()
	{
		// Output the HTML
		$this->editClassTask();
	}

	/**
	 * Edit a quota class
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editClassTask($row=null)
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

			// Initiate database class and load info
			$row = Category::oneOrNew($id);
		}

		$user_count = Quota::all()
			->whereEquals('class_id', $row->get('id'))
			->count();

		/*$groups = array();
		$qcGroups = Group::all()
			->whereEquals('class_id', $row->get('id'))
			->rows();

		foreach ($qcGroups as $group)
		{
			$groups[] = $group->get('group_id');
		}*/

		// Output the HTML
		$this->view
			->set('row', $row)
			->set('user_count', $user_count)
			->setErrors($this->getErrors())
			->setLayout('editClass')
			->display();
	}

	/**
	 * Apply changes to a quota class item
	 *
	 * @return  void
	 */
	public function applyClassTask()
	{
		// Save without redirect
		$this->saveClassTask();
	}

	/**
	 * Save quota class
	 *
	 * @param   integer  $redirect  Whether or not to redirect after save
	 * @return  void
	 */
	public function saveClassTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming fields
		$fields = Request::getVar('fields', array(), 'post');

		$groups = array();
		if (isset($fields['groups']))
		{
			$groups = $fields['groups'];
			unset($fields['groups']);
		}

		// Load the record
		$row = Category::oneOrNew($fields['id'])->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editClassTask($row);
		}

		// Save class/access-group association
		if (!$row->setGroupIds($groups))
		{
			Notify::error($row->getError());
			return $this->editClassTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_QUOTA_CLASS_SAVE_SUCCESSFUL'));

		// Redirect
		if ($this->getTask() == 'applyClass')
		{
			return $this->editClassTask($row);
		}

		// Redirect
		$this->cancelClassTask();
	}

	/**
	 * Removes class(es)
	 *
	 * @return  void
	 */
	public function deleteClassTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				$row = Category::oneOrNew($id);

				if ($row->get('alias') == 'default')
				{
					// Output message and redirect
					Notify::warning(Lang::txt('COM_MEMBERS_QUOTA_CLASS_DONT_DELETE_DEFAULT'));
					return $this->cancelClassTask();
				}

				// Remove the record
				$row->destroy();

				// Restore all members of this class to default
				Quota::restoreDefaultClass($id);
			}
		}
		else // no rows were selected
		{
			// Output message and redirect
			Notify::warning(Lang::txt('COM_MEMBERS_QUOTA_DELETE_NO_ROWS'));
			return $this->cancelClassTask();
		}

		// Output messsage and redirect
		Notify::success(Lang::txt('COM_MEMBERS_QUOTA_DELETE_SUCCESSFUL'));
		return $this->cancelClassTask();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelClassTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=displayClasses', false)
		);
	}

	/**
	 * Get class values
	 *
	 * @return  void
	 */
	public function getClassValuesTask()
	{
		$class_id = Request::getInt('class_id', 0);

		$class = Category::oneOrNew($class_id);

		$return = array(
			'soft_files'  => $class->get('soft_files'),
			'hard_files'  => $class->get('hard_files'),
			'soft_blocks' => $class->get('soft_blocks'),
			'hard_blocks' => $class->get('hard_blocks')
		);

		echo json_encode($return);
		exit();
	}

	/**
	 * Get quota usage info for a given quota id
	 *
	 * @return  void
	 */
	public function getQuotaUsageTask($returnType='json', $id=NULL)
	{
		if (is_null($id))
		{
			$id = Request::getInt('id');
		}

		$info = array();
		$success = false;

		$return = array(
			'success' => $success,
			'info'    => $info,
			'used'    => 0,
			'percent' => 0
		);

		$quota = Quota::oneOrNew($id);
		$user  = User::getInstance($quota->get('user_id'));

		if (!$user || !$user->get('id'))
		{
			return $return;
		}

		$username = $user->get('username');

		$config = \Component::params('com_tools');
		$host = $config->get('storagehost');
		$used = 0;

		if ($username && $host)
		{
			$fp = @stream_socket_client($host, $errno, $errstr, 30);

			if (!$fp)
			{
				$info[] = "$errstr ($errno)\n";
			}
			else
			{
				$msg = '';

				fwrite($fp, "getquota user=" . $username . "\n");
				while (!feof($fp))
				{
					$msg .= fgets($fp, 1024);
				}
				fclose($fp);

				$tokens = preg_split('/,/',$msg);

				foreach ($tokens as $token)
				{
					if (!empty($token))
					{
						$t = preg_split('#=#', $token);
						$info[$t[0]] = (isset($t[1])) ? $t[1] : '';
					}
				}

				$used = (isset($info['softspace']) && $info['softspace'] != 0) ? bcdiv($info['space'], $info['softspace'], 6) : 0;
				$percent = round($used * 100);

				$success = true;
			}
		}

		$return = array(
			'success' => $success,
			'info'    => $info,
			'used'    => $used,
			'percent' => $percent
		);

		if ($returnType == 'json')
		{
			echo json_encode($return);
			exit();
		}

		return $return;
	}

	/**
	 * Display quota import page
	 *
	 * @return  void
	 */
	public function importTask()
	{
		// Output the HTML
		$this->view
			->set('config', $this->config)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Process quota import
	 *
	 * @return  void
	 */
	public function processImportTask()
	{
		// Import quotas
		$qfile     = Request::getVar('conf_text');
		$overwrite = Request::getInt('overwrite_existing', 0);

		if (empty($qfile))
		{
			// Output message and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=import', false),
				Lang::txt('COM_MEMBERS_QUOTA_NO_CONF_TEXT'),
				'warning'
			);
		}

		$lines   = explode("\n", $qfile);
		$classes = array();
		foreach ($lines as $line)
		{
			$line = trim($line);
			if (empty($line))
			{
				continue;
			}

			if (substr($line, 0, 1) == "#")
			{
				continue;
			}

			$args = preg_split('/\s+/', $line);
			switch ($args[0])
			{
				case 'class':
					$class = Category::all()
						->whereEquals('alias', $args[1])
						->row();

					if ($class->get('id') && !$overwrite)
					{
						continue;
					}

					$class->set('alias'      , $args[1]);
					$class->set('soft_blocks', $args[2]);
					$class->set('hard_blocks', $args[3]);
					$class->set('soft_files' , $args[4]);
					$class->set('hard_files' , $args[5]);
					$class->save();
				break;

				case 'user':
					if ($args[2] == 'ignore')
					{
						continue;
					}

					$user = User::getInstance($args[1]);
					if (!is_object($user) || !is_numeric($user->get('id')))
					{
						continue;
					}

					$user_id = $user->get('id');

					$class = Category::all()
						->whereEquals('alias', $args[2])
						->row();

					if (!$class->get('id'))
					{
						continue;
					}

					$quota = Quota::all()
						->whereEquals('user_id', $user_id)
						->row();

					if ($quota->get('id') && !$overwrite)
					{
						continue;
					}

					$quota->set('user_id'    , $user_id);
					$quota->set('class_id',    $class->get('id'));
					$quota->set('soft_blocks', $class->get('soft_blocks'));
					$quota->set('hard_blocks', $class->get('hard_blocks'));
					$quota->set('soft_files',  $class->get('soft_files'));
					$quota->set('hard_files',  $class->get('hard_files'));
					$quota->save();
				break;
			}
		}

		// Output message and redirect
		Notify::success(Lang::txt('COM_MEMBERS_QUOTA_CONF_IMPORT_SUCCESSFUL'));

		$this->cancelTask();
	}

	/**
	 * Check for registered users without quota entries and add them
	 *
	 * @return  void
	 */
	public function importMissingTask()
	{
		// Query for all members in the CMS
		$results = Member::all()
			->select('id')
			->rows();

		if ($results->count() > 0)
		{
			$updates = 0;

			$class = Category::defaultEntry();

			if (!$class->get('id'))
			{
				// Output message and redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=import', false),
					Lang::txt('COM_MEMBERS_QUOTA_MISSING_DEFAULT_CLASS'),
					'error'
				);
			}

			foreach ($results as $r)
			{
				$quota = Quota::all()
					->whereEquals('user_id', $r->get('id'))
					->row();

				if ($quota->get('id'))
				{
					continue;
				}

				$quota->set('user_id',     $r->get('id'));
				$quota->set('class_id',    $class->get('id'));
				$quota->set('soft_blocks', $class->get('soft_blocks'));
				$quota->set('hard_blocks', $class->get('hard_blocks'));
				$quota->set('soft_files',  $class->get('soft_files'));
				$quota->set('hard_files',  $class->get('hard_files'));
				$quota->save();

				$updates++;
			}
		}

		// Output message and redirect
		Notify::success(Lang::txt('COM_MEMBERS_QUOTA_MISSING_USERS_IMPORT_SUCCESSFUL', $updates));

		$this->cancelTask();
	}
}
