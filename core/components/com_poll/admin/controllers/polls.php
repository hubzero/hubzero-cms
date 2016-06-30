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

namespace Components\Poll\Admin\Controllers;

use Components\Poll\Tables\Poll;
use Hubzero\Component\AdminController;
use Exception;
use stdClass;
use Request;
use Config;
use User;
use Lang;

/**
 * Controller class for polls
 */
class Polls extends AdminController
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations to be used
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unpublish', 'publish');
		$this->registerTask('close', 'open');
		$this->registerTask('apply', 'save');
		$this->registerTask('add', 'edit');
	}

	/**
	 * Display a list of polls
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$db  = \App::get('db');

		$filter_order     = Request::getState(
			$this->_option . '.' . $this->_controller . '.filter_order',
			'filter_order',
			'm.id',
			'cmd'
		);
		$filter_order_Dir = Request::getState(
			$this->_option . '.' . $this->_controller . '.filter_order_Dir',
			'filter_order_Dir',
			'',
			'word'
		);
		$filter_state     = Request::getState(
			$this->_option . '.' . $this->_controller . '.filter_state',
			'filter_state',
			'',
			'word'
		);
		$search           = Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			'',
			'string'
		);
		if (strpos($search, '"') !== false)
		{
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = \JString::strtolower($search);

		$limit      = Request::getState(
			'global.list.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$limitstart = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$where = array();

		if ($filter_state)
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'm.published = 1';
			}
			else if ($filter_state == 'U' )
			{
				$where[] = 'm.published = 0';
			}
		}
		if ($search)
		{
			$where[] = 'LOWER(m.title) LIKE ' . $db->Quote('%' . $search . '%');
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		// sanitize $filter_order
		if (!in_array($filter_order, array('m.title', 'm.published', 'a.ordering', 'catname', 'm.voters', 'numoptions', 'm.lag', 'm.id')))
		{
			$filter_order = 'm.id';
		}
		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC')))
		{
			$filter_order_Dir = '';
		}

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$db->setQuery('SELECT COUNT(m.id) FROM `#__polls` AS m' . $where);
		$total = $db->loadResult();

		$query = 'SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions'
			. ' FROM `#__polls` AS m'
			. ' LEFT JOIN `#__users` AS u ON u.id = m.checked_out'
			. ' LEFT JOIN `#__poll_data` AS d ON d.pollid = m.id AND d.text <> ""'
			. $where
			. ' GROUP BY m.id'
			. $orderby
			;
		$db->setQuery($query, $limitstart, $limit);
		$rows = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			throw new Exception($db->stderr(), 500);
		}

		$lists = array();

		// State filter
		$lists['state']     = \Html::grid('states', $filter_state);

		// Table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		// Dearch filter
		$lists['search']    = $search;

		$this->view
			->set('user', User::getRoot())
			->set('lists', $lists)
			->set('items', $rows)
			->set('limit', $limit)
			->set('limitstart', $limitstart)
			->set('total', $total)
			->display();
	}

	/**
	 * Preview a poll
	 *
	 * @return  void
	 */
	public function previewTask()
	{
		Request::setVar('hidemainmenu', 1);
		Request::setVar('tmpl', 'component');

		$db = \App::get('db');

		$id = Request::getVar('cid', array(0));
		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		$poll = new Poll($db);
		$poll->load($id);

		$query = 'SELECT id, text'
			. ' FROM `#__poll_data`'
			. ' WHERE pollid = '. (int) $poll->id
			. ' ORDER BY id'
			;
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$this->view
			->set('poll', $poll)
			->set('options', $options)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $poll  Poll object
	 * @return  void
	 */
	public function editTask($poll=null)
	{
		Request::setVar('hidemainmenu', 1);

		$db = \App::get('db');
		$user = User::getRoot();

		if (!$poll)
		{
			$id = Request::getVar('cid', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$poll = new Poll($db);
			$poll->load($id);
		}

		// Fail if checked out not by 'me'
		if ($poll->isCheckedOut($user->get('id')))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('DESCBEINGEDITTED', Lang::txt('The poll'), $poll->title),
				'warning'
			);
			return;
		}

		if ($poll->id == 0)
		{
			$poll->published = 1;
		}

		$poll->checkout($user->get('id'));

		$query = 'SELECT id, text'
			. ' FROM `#__poll_data`'
			. ' WHERE pollid = '. (int) $poll->id
			. ' ORDER BY id'
			;
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->set('poll', $poll)
			->set('options', $options)
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

		$post = array(
			'id' => Request::getInt('id', 0, 'post'),
			'title' => Request::getVar('title', '', 'post'),
			'alias' => Request::getVar('alias', '', 'post'),
			'lag' => Request::getVar('lag', '', 'post'),
			'published' => Request::getVar('published', '', 'post'),
			'open' => Request::getVar('open', '', 'post'),
		);

		// Save the poll parent information
		$db = \App::get('db');
		$row = new Poll($db);

		if (!$row->bind($post))
		{
			throw new Exception($row->getError(), 500);
		}

		$isNew = ($row->id == 0);

		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}

		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}
		$row->checkin();

		// Save the poll options
		$options = Request::getVar('polloption', array(), 'post');

		foreach ($options as $i => $text)
		{
			//$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
			if ($isNew)
			{
				$obj = new stdClass();
				$obj->pollid = (int)$row->id;
				$obj->text   = $text;
				$db->insertObject('#__poll_data', $obj);
			}
			else
			{
				$obj = new stdClass();
				$obj->id     = (int)$i;
				$obj->text   = $text;
				$db->updateObject('#__poll_data', $obj, 'id');
			}
		}

		switch (Request::getVar('task', 'save'))
		{
			case 'apply':
				$msg  = Lang::txt('COM_POLL_ITEM_SAVED');
				$link = Route::url('index.php?option=com_poll&view=poll&task=edit&cid=' . $row->id, false);
			break;

			case 'save':
			default:
				$msg  = Lang::txt('COM_POLL_ITEM_SAVED');
				$link = Route::url('index.php?option=com_poll', false);
			break;
		}

		App::redirect($link, $msg);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$db  = \App::get('db');
		$cid = Request::getVar('cid', array(), '', 'array');
		\Hubzero\Utility\Arr::toInteger($cid);

		$msg = '';

		$poll = new Poll($db);

		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			if (!$poll->delete($cid[$i]))
			{
				$msg .= $poll->getError();
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false),
			$msg
		);
	}

	/**
	* Publishes or Unpublishes one or more records
	*
	* @return  void
	*/
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$cid = Request::getVar('cid', array(), '', 'array');
		\Hubzero\Utility\Arr::toInteger($cid);

		$publish = (Request::getVar('task') == 'publish' ? 1 : 0);

		if (count($cid) < 1)
		{
			$action = $publish ? 'COM_POLL_PUBLISH' : 'COM_POLL_UNPUBLISH';
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_POLL_SELECT_ITEM_TO', Lang::txt($action), true),
				'warning'
			);
			return;
		}

		$cids = implode( ',', $cid);

		$db   = \App::get('db');
		$user = User::getRoot();

		$query = 'UPDATE `#__polls`'
			. ' SET published = ' . (int) $publish
			. ' WHERE id IN (' . $cids . ')'
			. ' AND (checked_out = 0 OR (checked_out = ' . (int) $user->get('id') . '))';
		$db->setQuery($query);
		if (!$db->query())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		if (count($cid) == 1)
		{
			$row = new Poll($db);
			$row->checkin($cid[0]);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	* Mark a poll as open or closed
	*
	* @return  void
	*/
	public function openTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$cid = Request::getVar('cid', array(), '', 'array');
		\Hubzero\Utility\Arr::toInteger($cid);

		$publish = (Request::getVar('task') == 'open' ? 1 : 0);

		if (count($cid) < 1)
		{
			$action = $publish ? 'COM_POLL_OPEN' : 'COM_POLL_CLOSE';
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_POLL_SELECT_ITEM_TO', Lang::txt($action), true),
				'warning'
			);
			return;
		}

		$cids = implode( ',', $cid);

		$db   = \App::get('db');
		$user = User::getRoot();

		$query = 'UPDATE `#__polls`'
			. ' SET open = ' . (int) $publish
			. ' WHERE id IN (' . $cids . ')'
			. ' AND (checked_out = 0 OR (checked_out = ' . (int) $user->get('id') . '))';
		$db->setQuery($query);
		if (!$db->query())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		if (count($cid) == 1)
		{
			$row = new Poll($db);
			$row->checkin($cid[0]);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if ($id  = Request::getVar('id', 0, '', 'int'))
		{
			$db  = \App::get('db');
			$row = new Poll($db);
			$row->checkin($id);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}
}