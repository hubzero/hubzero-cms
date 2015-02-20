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

namespace Components\Poll\Controllers;

use Components\Poll\Tables\Poll;
use Hubzero\Component\AdminController;
use Exception;
use JPagination;
use JRoute;
use JRequest;
use JFactory;
use JString;
use JText;
use JHTML;
use JArrayHelper;
use stdClass;

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
		$app = JFactory::getApplication();
		$db  = JFactory::getDBO();

		$filter_order     = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filter_order',
			'filter_order',
			'm.id',
			'cmd'
		);
		$filter_order_Dir = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filter_order_Dir',
			'filter_order_Dir',
			'',
			'word'
		);
		$filter_state     = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filter_state',
			'filter_state',
			'',
			'word'
		);
		$search           = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			'',
			'string'
		);
		if (strpos($search, '"') !== false)
		{
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$limit      = $app->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$app->getCfg('list_limit'),
			'int'
		);
		$limitstart = $app->getUserStateFromRequest(
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

		jimport('joomla.html.pagination');
		$pagination = new JPagination(
			$total,
			$limitstart,
			$limit
		);

		$query = 'SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions'
			. ' FROM `#__polls` AS m'
			. ' LEFT JOIN `#__users` AS u ON u.id = m.checked_out'
			. ' LEFT JOIN `#__poll_data` AS d ON d.pollid = m.id AND d.text <> ""'
			. $where
			. ' GROUP BY m.id'
			. $orderby
			;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			throw new Exception($db->stderr(), 500);
		}

		$lists = array();

		// State filter
		$lists['state']     = JHTML::_('grid.state', $filter_state);

		// Table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		// Dearch filter
		$lists['search']    = $search;

		$user = JFactory::getUser();

		$this->view
			->set('user', $user)
			->set('lists', $lists)
			->set('items', $rows)
			->set('pagination', $pagination)
			->display();
	}

	/**
	 * Preview a poll
	 *
	 * @return  void
	 */
	public function previewTask()
	{
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('tmpl', 'component');

		$db = JFactory::getDBO();

		$id = JRequest::getVar('cid', array(0));
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
		JRequest::setVar('hidemainmenu', 1);

		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		if (!$poll)
		{
			$id = JRequest::getVar('cid', array(0));
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
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option, false),
				JText::sprintf('DESCBEINGEDITTED', JText::_('The poll'), $poll->title),
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
		JRequest::checkToken() or jexit('Invalid Token');

		$post = JRequest::get('post');

		// Save the poll parent information
		$db = JFactory::getDBO();
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
		$options = JArrayHelper::getValue($post, 'polloption', array(), 'array');

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

		switch (JRequest::getVar('task', 'save'))
		{
			case 'apply':
				$msg  = JText::_('COM_POLL_ITEM_SAVED');
				$link = JRoute::_('index.php?option=com_poll&view=poll&task=edit&cid=' . $row->id, false);
			break;

			case 'save':
			default:
				$msg  = JText::_('COM_POLL_ITEM_SAVED');
				$link = JRoute::_('index.php?option=com_poll', false);
			break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$db  = JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		$msg = '';

		$poll = new TablePoll($db);

		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			if (!$poll->delete($cid[$i]))
			{
				$msg .= $poll->getError();
			}
		}

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option, false),
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
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		$publish = (JRequest::getVar('task') == 'publish' ? 1 : 0);

		if (count($cid) < 1)
		{
			$action = $publish ? 'COM_POLL_PUBLISH' : 'COM_POLL_UNPUBLISH';
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option, false),
				JText::sprintf('COM_POLL_SELECT_ITEM_TO', JText::_($action), true),
				'warning'
			);
			return;
		}

		$cids = implode( ',', $cid);

		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

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

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option, false)
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
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		$publish = (JRequest::getVar('task') == 'open' ? 1 : 0);

		if (count($cid) < 1)
		{
			$action = $publish ? 'COM_POLL_OPEN' : 'COM_POLL_CLOSE';
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option, false),
				JText::sprintf('COM_POLL_SELECT_ITEM_TO', JText::_($action), true),
				'warning'
			);
			return;
		}

		$cids = implode( ',', $cid);

		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

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

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option, false)
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
		JRequest::checkToken() or jexit('Invalid Token');

		if ($id  = JRequest::getVar('id', 0, '', 'int'))
		{
			$db  = JFactory::getDBO();
			$row = new Poll($db);
			$row->checkin($id);
		}

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option, false)
		);
	}
}