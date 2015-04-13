<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Kb\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Kb\Models\Archive;
use Components\Kb\Models\Article;
use Components\Kb\Tables;
use Request;
use Config;
use Route;
use User;
use Lang;
use App;

/**
 * Controller class for knowledge base articles
 */
class Articles extends AdminController
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
		$this->registerTask('unpublish', 'state');
		$this->registerTask('publish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of articles
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'orphans' => Request::getState(
				$this->_option . '.' . $this->_controller . '.orphans',
				'orphans',
				0,
				'int'
			),
			'category' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category',
				'category',
				0,
				'int'
			),
			'section' => Request::getState(
				$this->_option . '.' . $this->_controller . '.section',
				'section',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
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
			),
			'state' => -1,
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			)
		);
		$this->view->filters['filterby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		$a = new Archive();

		// Get record count
		$this->view->total = $a->articles('count', $this->view->filters);

		// Get records
		$this->view->rows  = $a->articles('list', $this->view->filters);

		// Get the sections
		$this->view->sections = $a->categories('list', array(
			'access' => -1,
			'state' => -1,
			'empty' => true
		));
		if ($this->view->filters['section'] && $this->view->filters['section'] >= 0)
		{
			$this->view->categories = $a->categories('list', array(
				'section' => $this->view->filters['section'],
				'access' => -1,
				'state' => -1,
				'empty' => true
			), true);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load category
			$row = new Article($id);
		}

		$this->view->row = $row;

		// Fail if checked out not by 'me'
		if ($this->view->row->get('checked_out') && $this->view->row->get('checked_out') != User::get('id'))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_CHECKED_OUT'),
				'warning'
			);
			return;
		}

		if (!$this->view->row->exists())
		{
			$this->view->row->set('created_by', User::get('id'));
			$this->view->row->set('created', Date::toSql());
		}

		$this->view->params = new \JParameter(
			$this->view->row->get('params'),
			dirname(dirname(__DIR__)) . DS . 'kb.xml'
		);

		$c = new Archive();

		// Get the sections
		$this->view->sections = $c->categories('list', array('section' => 0, 'empty' => 1));

		/*
		$m = new KbModelAdminArticle();
		$this->view->form = $m->getForm();
		*/

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = new Article($fields['id']);
		if (!$row->bind($fields))
		{
			\JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Get parameters
		$params = Request::getVar('params', array(), 'post');

		$p = $row->param();

		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$p->set($k, $v);
			}
			$row->set('params', $p->toString());
		}

		// Store new content
		if (!$row->store(true))
		{
			\JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		//$row->checkin();

		// Save the tags
		$row->tag(
			Request::getVar('tags', '', 'post'),
			User::get('id')
		);

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_KB_ARTICLE_SAVED')
		);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or jexit('Invalid Token');

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// Delete the category
				$article = new Article(intval($id));
				$article->delete();
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_KB_ITEMS_REMOVED', count($ids))
		);
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$cid = Request::getInt('cid', 0);
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (count($ids) < 1)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				($state == 1 ? Lang::txt('COM_KB_SELECT_PUBLISH') : Lang::txt('COM_KB_SELECT_UNPUBLISH')),
				'error'
			);
			return;
		}

		// Update record(s)
		foreach ($ids as $id)
		{
			// Updating an article
			$row = new Article(intval($id));
			$row->set('state', $state);
			$row->store();
		}

		// Set message
		switch ($state)
		{
			case '-1':
				$message = Lang::txt('COM_KB_ARCHIVED', count($ids));
			break;
			case '1':
				$message = Lang::txt('COM_KB_PUBLISHED', count($ids));
			break;
			case '0':
				$message = Lang::txt('COM_KB_UNPUBLISHED', count($ids));
			break;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . ($cid ? '&section=' . $cid : ''), false),
			$message
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$filters = Request::getVar('filters', array());

		if (isset($filters['id']) && $filters['id'])
		{
			// Bind the posted data to the article object and check it in
			$article = new Tables\Article($this->database);
			$article->load(intval($filters['id']));
			$article->checkin();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Reset the hit count on an entry
	 *
	 * @return  void
	 */
	public function resethitsTask()
	{
		// Incoming
		$cid = Request::getInt('cid', 0);
		$id  = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's hits
		$article = new Article($id);
		$article->set('hits', 0);

		if (!$article->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$article->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Reset the vote count on an entry
	 *
	 * @return  void
	 */
	public function resetvotesTask()
	{
		// Incoming
		$cid = Request::getInt('cid', 0);
		$id  = Request::getInt('id', 0);

		// Make sure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_KB_NO_ID'),
				'error'
			);
		}

		// Load and reset the article's ratings
		$article = new Article($id);
		$article->set('helpful', 0);
		$article->set('nothelpful', 0);

		if (!$article->store())
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$article->getError(),
				'error'
			);
			return;
		}

		// Delete all the entries associated with this article
		$helpful = new Tables\Vote($this->database);
		$helpful->deleteVote($id);

		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

