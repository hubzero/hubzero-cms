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

namespace Components\Messages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Messages\Models\Message;

/**
 * Messages list controller class.
 */
class Messages extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'date_time'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'desc'
			)
		);

		$entries = Message::all()
			->including(['from', function ($from){
				$from
					->select('id')
					->select('name');
			}])
			->whereEquals('user_id_to', (int) User::get('id'));

		if ($filters['search'])
		{
			$entries
				->whereLike('subject', strtolower((string)$filters['search']), 1)
				->whereLike('message', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['state'])
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}
		else
		{
			$entries->whereIn('state', array(0, 1));
		}

		// Get records
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
	 * View an entry
	 *
	 * @return  void
	 */
	public function viewTask()
	{
		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getInt('message_id', 0);

		// Load the article
		$row = Message::oneOrNew($id);

		if ($row->get('user_id_to') != User::get('id'))
		{
			Notify::warning(Lang::txt('JERROR_ALERTNOAUTHOR'));
			return $this->cancelTask();
		}

		if (!$row->get('state'))
		{
			$row->set('state', 1);
			$row->save();
		}

		// Output the HTML
		$this->view
			->set('item', $row)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Message::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('user_id_from', User::get('id'));
			$row->set('date_time', Date::toSql());
		}

		// Output the HTML
		$this->view
			->set('item', $row)
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = Message::oneOrNew($fields['message_id'])->set($fields);

		// Store new content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$this->mail($row);

		Notify::success(Lang::txt('COM_BLOG_ENTRY_SAVED'));

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->_task == 'publish' ? Message::STATE_PUBLISHED : Message::STATE_UNPUBLISHED;

		// Incoming
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$message = Message::oneOrNew(intval($id));
			$message->set('state', $state);

			if ($message->get('user_id_to') !== User::get('id'))
			{
				Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				continue;
			}

			// Store new content
			if (!$message->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			switch ($this->_task)
			{
				case 'publish':
					$message = Lang::txt('COM_BLOG_ITEMS_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_BLOG_ITEMS_UNPUBLISHED', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());

		if (count($ids) > 0)
		{
			$removed = 0;

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				$message = Message::oneOrFail(intval($id));

				if ($message->get('user_id_to') !== User::get('id'))
				{
					Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
					continue;
				}

				// Delete the entry
				if (!$message->destroy())
				{
					Notify::error($message->getError());
					continue;
				}

				$removed++;
			}
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_BLOG_ENTRIES_DELETED'));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Reply to an existing message.
	 *
	 * This is a simple redirect to the compose form.
	 *
	 * @return  void
	 */
	public function replyTask()
	{
		if ($replyId = Request::getInt('reply_id'))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&task=edit&reply_id=' . $replyId));
		}
		else
		{
			Notify::warning(Lang::txt('COM_MESSAGES_INVALID_REPLY_ID'));
			$this->cancelTask();
		}
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	protected function mail($message)
	{
		if ($this->config->get('mail_on_new', true))
		{
			// Load the user details (already valid from table check).
			$fromUser = $message->from;
			$toUser   = $message->to;

			$debug = \Config::get('debug_lang');
			$default_language = \Component::params('com_languages')->get('administrator');

			/*$lang = Lang::getInstance($toUser->getParam('admin_language', $default_language), $debug);
			$lang->load('com_messages', PATH_APP) ||
			$lang->load('com_messages', PATH_CORE . '/components/com_messages/admin');*/

			$siteURL  = Request::root() . 'administrator/index.php?option=com_messages&view=message&message_id=' . $table->message_id;
			$sitename = \Config::get('sitename');

			$subject = Lang::txt('COM_MESSAGES_NEW_MESSAGE_ARRIVED', $sitename);
			$msg     = Lang::txt('COM_MESSAGES_PLEASE_LOGIN', $siteURL);

			$mailer = new \Hubzero\Mail\Message();
			$return = $mailer
				->addFrom($fromUser->email, $fromUser->name)
				->addTo($toUser->email)
				->setSubject($subject)
				->setBody($msg)
				->send();
		}
	}
}
