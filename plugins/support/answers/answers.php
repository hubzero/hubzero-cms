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

jimport('joomla.plugin.plugin');

/**
 * Support plugin class for com_answers entries
 */
class plgSupportAnswers extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Retrieves a row from the database
	 * 
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $parent   If the element has a parent element
	 * @return     array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') 
		{
			return null;
		}

		switch ($category)
		{
			case 'answer':
				$query  = "SELECT r.id, r.answer as text, NULL as subject, r.created";
				$query .= ", r.anonymous as anon, r.created_by as author, 'answer' as parent_category, NULL as href";
				$query .= " FROM #__answers_responses AS r";
				$query .= " WHERE r.state!=2 AND r.id=" . $refid;
			break;

			case 'question':
				$query  = "SELECT q.id, q.subject as text, q.created_by as author, q.question as subject, q.created";
				$query .= ", 'question' as parent_category, q.anonymous as anon, NULL as href";
				$query .= " FROM #__answers_questions AS q";
				$query .= " WHERE q.id=" . $refid;
			break;
		}

		$database =& JFactory::getDBO();
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows) 
		{
			foreach ($rows as $key => $row)
			{
				switch ($category)
				{
					case 'answer':
						$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_answers&task=question&id=' . $parent) : '';
					break;
					case 'question':
						$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id=' . $rows[$key]->id);
					break;
				}
			}
		}
		return $rows;
	}

	/**
	 * Looks up ancestors to find root element
	 * 
	 * @param      integer $parentid ID to check for parents of
	 * @param      string  $category Element type (determines table to look in)
	 * @return     integer
	 */
	public function getParentId($parentid, $category)
	{
		ximport('Hubzero_Comment');

		$database =& JFactory::getDBO();
		$refid = $parentid;

		if ($category == 'answercomment') 
		{
			$pdata = $this->parent($parentid);
			$category = $pdata->category;
			$refid = $pdata->referenceid;

			if ($pdata->category == 'answercomment') 
			{
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->category;
				$refid = $pdata->referenceid;

				if ($pdata->category == 'answercomment') 
				{
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->category;
					$refid = $pdata->referenceid;
				}
			}
		}

		if ($category == 'answer') 
		{
			$database->setQuery("SELECT qid FROM #__answers_responses WHERE id=" . $refid);
		 	return $database->loadResult();
		}

		if ($category == 'question') 
		{
		 	return $refid;
		}
	}

	/**
	 * Retrieve parent element
	 * 
	 * @param      integer $parentid ID of element to retrieve
	 * @return     object
	 */
	public function parent($parentid)
	{
		$database =& JFactory::getDBO();

		$parent = new Hubzero_Comment($database);
		$parent->load($parentid);

		return $parent;
	}

	/**
	 * Returns the appropriate text for category
	 * 
	 * @param      string  $category Element type (determines text)
	 * @param      integer $parentid ID of element to retrieve
	 * @return     string
	 */
	public function getTitle($category, $parentid)
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') 
		{
			return null;
		}

		switch ($category)
		{
			case 'answer':
				return JText::sprintf('Answer to question #%s', $parentid);
         	break;

			case 'question':
				return JText::sprintf('Question #%s', $parentid);
         	break;

			case 'answercomment':
				return JText::sprintf('Comment to an answer for question #%s', $parentid);
         	break;
		}
	}

	/**
	 * Removes an item reported as abusive
	 * 
	 * @param      integer $referenceid ID of the database table row
	 * @param      integer $parentid    If the element has a parent element
	 * @param      string  $category    Element type (determines table to look in)
	 * @param      string  $message     Message to user to append to
	 * @return     string
	 */
	public function deleteReportedItem($referenceid, $parentid, $category, $message)
	{
		if ($category != 'answer' && $category != 'question' && $category != 'answercomment') 
		{
			return null;
		}

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		switch ($category)
		{
			case 'answer':
				$database->setQuery("UPDATE #__answers_responses SET state='2' WHERE id=" . $referenceid);
				if (!$database->query()) 
				{
					$this->setError($database->getErrorMsg());
					return false;
				}

				$message .= JText::sprintf('This is to notify you that your answer to question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;

			case 'question':
				$upconfig =& JComponentHelper::getParams('com_members');
				$banking = $upconfig->get('bankAccounts');

				$reward = 0;
				if ($banking) 
				{
					$reward = $this->getReward($parentid);
				}
				$responders = array();

				// Get all the answers for this question
				$database->setQuery("SELECT r.id, r.created_by FROM #__answers_responses AS r WHERE r.qid=" . $referenceid);
				$answers = $database->loadObjectList();

				if ($answers) 
				{
					foreach ($answers as $answer)
					{
						// Delete response
						$database->setQuery("UPDATE #__answers_responses SET state='2' WHERE id=" . $answer->id);
						if (!$database->query()) 
						{
							$this->setError($database->getErrorMsg());
							return false;
						}

						// Collect responders names
						$responders[] = $answer->created_by;
					}
				}

				$database->setQuery("UPDATE #__answers_questions SET state='2', reward='0' WHERE id=" . $referenceid);
				if (!$database->query()) 
				{
					$this->setError($database->getErrorMsg());
					return false;
				}

				if ($banking && $reward) 
				{
					ximport('Hubzero_Bank');

					// Send email to people who answered question with reward
					if ($responders) 
					{
						foreach ($responders as $r)
						{
							$zuser =& JUser::getInstance($r);
							if (is_object($zuser)) 
							{
								if (SupportUtilities::checkValidEmail($zuser->get('email')) && $email) 
								{
									$jconfig =& JFactory::getConfig();

									$admin_email = $jconfig->getValue('config.mailfrom');
									$sub  = $jconfig->getValue('config.sitename') . ' Answers, Question #' . $referenceid . ' was removed';
									$from = $jconfig->getValue('config.sitename') . ' Answers';
									$hub  = array(
										'email' => $admin_email, 
										'name'  => $from
									);

									$mes  = 'You are receiving this email because you responded to a question, which has been removed by the site administrator. ';
									$mes .= 'As a result, no points for this question will be awarded. We appologize for inconvenience.' . "\r\n";
									$mes .= '----------------------------' . "\r\n\r\n";
									$mes .= 'QUESTION: ' . $referenceid . "\r\n";

									SupportUtilities::sendEmail($hub, $zuser->get('email'), $sub, $mes);
							 	}
							}
						}
					}

					// get id of asker
					$database->setQuery("SELECT created_by FROM #__answers_questions WHERE id=" . $parentid);
					$asker = $database->loadResult();

					if ($asker) 
					{
						$quser =& JUser::getInstance($asker);
						if (is_object($quser)) 
						{
							$asker_id = $quser->get('id');
						}

						if (isset($asker_id)) 
						{
							// Remove hold 
							$sql = "DELETE FROM #__users_transactions WHERE category='answers' AND type='hold' AND referenceid=" . $parentid . " AND uid='" . $asker_id . "'";
							$database->setQuery($sql);
							if (!$database->query()) 
							{
								$this->setError($database->getErrorMsg());
								return false;
							}

							// Make credit adjustment
							$BTL_Q = new Hubzero_Bank_Teller($database, $asker_id);
							$credit = $BTL_Q->credit_summary();
							$adjusted = $credit - $reward;
							$BTL_Q->credit_adjustment($adjusted);
						}
					}
				}

				$message .= JText::sprintf('This is to notify you that your question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;

			case 'answercomment':
				ximport('Hubzero_Comment');

				$comment = new Hubzero_Comment($database);
				$comment->load($referenceid);
				$comment->state = 2;
				if (!$comment->store()) 
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= JText::sprintf('This is to notify you that your comment on an answer to question #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
		}

		return $message;
	}

	/**
	 * Retrieves the reward (points) value on an item
	 * 
	 * @param      integer $id ID of item to look up
	 * @return     integer
	 */
	public function getReward($id)
	{
		$database =& JFactory::getDBO();

		// check if question owner assigned a reward for answering his Q 
		$sql = "SELECT amount FROM #__users_transactions WHERE category='answers' AND type='hold' AND referenceid=" . $id;
		$database->setQuery($sql);

		return $database->loadResult();
	}
}

