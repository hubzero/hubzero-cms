<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for questions
 */
class AnswersControllerQuestions extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->banking = JComponentHelper::getParams('com_members')->get('bankAccounts');

		if ($this->banking)
		{
			ximport('Hubzero_Bank');
		}

		parent::execute();
	}

	/**
	 * Short description for 'displayTask'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['tag']      = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tag',
			'tag',
			''
		);
		$this->view->filters['q']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.q',
			'q',
			''
		);
		$this->view->filters['filterby'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filterby',
			'filterby',
			'all'
		);

		// Paging
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		// Sorting
		$this->view->filters['sortby']   = '';
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'created'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));

		$aq = new AnswersQuestion($this->database);

		// Get a record count
		$this->view->total = $aq->getCount($this->view->filters);

		// Get records
		$this->view->results = $aq->getResults($this->view->filters);

		// Did we get any results?
		if (count($this->view->results) > 0)
		{
			$ip = Hubzero_Environment::ipAddress();
			$ar = new AnswersResponse($this->database);
			$at = new AnswersTags($this->database);

			// Do some processing on the results
			for ($i=0; $i < count($this->view->results); $i++)
			{
				$row =& $this->view->results[$i];

				if ($this->banking)
				{
					$row->points = $this->_getPointReward($row->id);
				}
				else
				{
					$row->points = 0;
				}

				$row->reports = $this->_getAbuseReports($row->id, 'question');

				// Get tags on this question
				$row->tags = $at->get_tags_on_object($row->id, 0, 0, 0);

				// Get responses
				$row->answers = count($ar->getRecords(array('ip' => $ip, 'qid' => $row->id)));
			}
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new question
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a question for editing
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (is_array($ids))
		{
			$id = $ids[0];
		}

		// Load object
		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$this->view->row = new AnswersQuestion($this->database);
			$this->view->row->load($id);
		}

		if ($id)
		{
			// Remove some tags so edit box only displays text (no HTML)
			$this->view->row->question = AnswersHtml::unpee($this->view->row->question);

			$tags_men = $this->_getTags($id, 0);
			$mytagarray = array();
			foreach ($tags_men as $tag_men)
			{
				$mytagarray[] = $tag_men->raw_tag;
			}
		}
		else
		{
			// Creating new
			$this->view->row->subject     = '';
			$this->view->row->question    = '';
			$this->view->row->created     = date('Y-m-d H:i:s', time());
			$this->view->row->created_by  = '';
			$this->view->row->state       = 0;

			$mytagarray = array();
		}

		// Get tags
		$this->view->tags = implode(', ', $mytagarray);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a question
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming data
		$question = JRequest::getVar('question', array(), 'post');
		$question = array_map('trim', $question);

		// Ensure we have at least one tag
		if (!$question['tags'])
		{
			$this->addComponentMessage(JText::_('Question must have at least 1 tag'), 'error');
			$this->editTask();
			return;
		}

		// Initiate extended database class
		$row = new AnswersQuestion($this->database);
		if (!$row->bind($question))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Updating entry
		$row->created = $row->created ? $row->created : date("Y-m-d H:i:s");
		$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('username');

		// Code cleaner
		$row->question = nl2br($row->question);

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Add the tag(s)
		$at = new AnswersTags($this->database);
		$at->tag_object($this->juser->get('id'), $row->id, $question['tags'], 1, 1);

		// Redirect back to the full questions list
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Question Successfully Saved')
		);
	}

	/**
	 * Delete one or more questions and associated data
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		if (count($ids) <= 0)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$aq = new AnswersQuestion($this->database);
		$ar = new AnswersResponse($this->database);
		$al = new AnswersLog($this->database);

		foreach ($ids as $id)
		{
			// Delete the question
			$aq->load(intval($id));
			$aq->state = 2;  // Deleted by user
			$aq->reward = 0;

			// Store new content
			if (!$aq->store())
			{
				JError::raiseError(500, $aq->getError());
				return;
			}

			if ($this->banking) {
				// Remove hold
				$BT = new Hubzero_Bank_Transaction($this->database);
				$reward = $BT->getAmount('answers', 'hold', $id);
				$BT->deleteRecords('answers', 'hold', $id);

				$creator =& JUser::getInstance($aq->created_by);

				// Make credit adjustment
				if (is_object($creator))
				{
					$BTL = new Hubzero_Bank_Teller($this->database, $creator->get('id'));
					$credit = $BTL->credit_summary();
					$adjusted = $credit - $reward;
					$BTL->credit_adjustment($adjusted);
				}
			}

			// Get all the answers for this question
			$ip = Hubzero_Environment::ipAddress();
			$answers = $ar->getRecords(array('ip' => $ip, 'qid' => $id));

			if ($answers)
			{
				foreach ($answers as $answer)
				{
					// Delete response's log entry
					if (!$al->deleteLog($answer->id))
					{
						JError::raiseError(500, $al->getError());
						return;
					}

					// Delete response
					if (!$ar->deleteResponse($answer->id))
					{
						JError::raiseError(500, $ar->getError());
						return;
					}
				}
			}

			// Delete all tag associations	
			$tagging = new AnswersTags($this->database);
			$tags = $tagging->remove_all_tags($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Question deleted')
		);
	}

	/**
	 * Set one or more questions to open
	 * 
	 * @return     void
	 */
	public function openTask()
	{
		$this->stateTask();
	}

	/**
	 * Set one or more questions to closed
	 * 
	 * @return     void
	 */
	public function closeTask()
	{
		$this->stateTask();
	}

	/**
	 * Set the state of one or more questions
	 * 
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$publish = ($this->_task == 'close') ? 1 : 0;

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($publish == 1) ? JText::_('close') : JText::_('open');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Select a question to ' . $action),
				'error'
			);
			return;
		}

		// Load the plugins
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();

		foreach ($ids as $id)
		{
			// Update record(s)
			$aq = new AnswersQuestion($this->database);
			$aq->load(intval($id));
			$aq->state = $publish;
			if ($publish == 1)
			{
				$aq->reward = 0;
			}
			if (!$aq->store())
			{
				JError::raiseError(500, $aq->getError());
				return;
			}

			if ($publish == 1)
			{
				$creator =& JUser::getInstance($aq->created_by);

				if ($this->banking)
				{
					// Remove hold
					$BT = new Hubzero_Bank_Transaction($this->database);
					$reward = $BT->getAmount('answers', 'hold', $id);
					$BT->deleteRecords('answers', 'hold', $id);

					// Make credit adjustment
					if (is_object($creator))
					{
						$BTL = new Hubzero_Bank_Teller($this->database, $creator->get('id'));
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $reward;
						$BTL->credit_adjustment($adjusted);
					}
				}

				// Call the plugin
				if (!$dispatcher->trigger('onTakeAction', array('answers_reply_submitted', array($creator->get('id')), $this->_option, $id)))
				{
					$this->setError(JText::_('Failed to remove alert.'));
				}
			}
		}

		// set message
		if ($publish == 1)
		{
			$message = JText::_(count($ids) . ' Item(s) successfully Closed');
		}
		else if ($publish == 0)
		{
			$message = JText::_(count($ids) . ' Item(s) successfully Opened');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}

	/**
	 * Cancel a task and redirect to default view
	 * 
	 * @return     void
	 */
	public function cancel()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Get the amount of point rewards for a question
	 * 
	 * @param      integer $id ID of question
	 * @return     mixed Return description (if any) ...
	 */
	private function _getPointReward($id)
	{
		// Check if question owner assigned a reward for answering his Q
		$BT = new Hubzero_Bank_Transaction($this->database);
		return $BT->getAmount('answers', 'hold', $id);
	}

	/**
	 * Get the count of abuse reports on a question
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _getAbuseReports($id, $cat)
	{
		// Incoming
		$filters = array();
		$filters['id'] = $id;
		$filters['category'] = $cat;
		$filters['state'] = 0;

		// Check for abuse reports on an item
		$ra = new ReportAbuse($this->database);

		return $ra->getCount($filters);
	}

	/**
	 * Get the tags on a question
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      mixed $tagger_id Parameter description (if any) ...
	 * @param      mixed $strength Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _getTags($id, $tagger_id=0, $strength=0)
	{
		$sql = "SELECT DISTINCT t.* FROM #__tags AS t, #__tags_object AS rt WHERE rt.objectid=" . $id . " AND rt.tbl='answers' AND rt.tagid=t.id";
		if ($tagger_id != 0)
		{
			$sql .= " AND rt.taggerid=" . $tagger_id;
		}
		if ($strength)
		{
			$sql .= " AND rt.strength=" . $strength;
		}
		$this->database->setQuery($sql);
		if ($this->database->query())
		{
			$tags = $this->database->loadObjectList();
		}
		else
		{
			$tags = NULL;
		}

		return $tags;
	}
}
