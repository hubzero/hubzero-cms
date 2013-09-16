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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Bank');

/**
 * Answers Economy class:
 * Stores economy funtions for com_answers
 */
class AnswersEconomy extends JObject
{
	/**
	 * Database
	 * 
	 * @var object
	 */
	var $_db = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		$this->_db = $db;
	}

	/**
	 * Get questions
	 * 
	 * @return     array
	 */
	public function getQuestions()
	{
		// get all closed questions
		$sql = "SELECT q.id, q.created_by AS q_owner, a.created_by AS a_owner
				FROM #__answers_questions AS q LEFT JOIN #__answers_responses AS a ON q.id=a.qid AND a.state=1
				WHERE q.state=1";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Calculate the market value
	 * 
	 * @param      integer $id   Question ID
	 * @param      string  $type Transaction type
	 * @return     mixed 
	 */
	public function calculate_marketvalue($id, $type='regular')
	{
		if ($id === NULL) 
		{
			$id = $this->qid;
		}
		if ($id === NULL) 
		{
			return false;
		}

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');

		// Get point values for actions
		$BC = new Hubzero_Bank_Config($this->_db);
		$p_Q  = $BC->get('ask');
		$p_A  = $BC->get('answer');
		$p_R  = $BC->get('answervote');
		$p_RQ = $BC->get('questionvote');
		$p_A_accepted = $BC->get('accepted');

		$calc = 0;

		// Get actons and sum up
		$ar = new AnswersTableResponse($this->_db);
		$result = $ar->getActions($id);

		if ($type != 'royalty') 
		{
			$calc += $p_Q;  // ! this is different from version before code migration !
			$calc += (count($result))*$p_A;
		}

		// Calculate as if there is at leat one answer
		if ($type == 'maxaward' && count($result)==0) 
		{
			$calc += $p_A;
		}

		for ($i=0, $n=count($result); $i < $n; $i++)
		{
			$calc += ($result[$i]->helpful)*$p_R;
			$calc += ($result[$i]->nothelpful)*$p_R;
			if ($result[$i]->state == 1 && $type != 'royalty') 
			{
				$accepted = 1;
			}
		}

		if (isset($accepted) or $type == 'maxaward') 
		{
			$calc += $p_A_accepted;
		}

		// Add question votes
		$aq = new AnswersTableQuestion($this->_db);
		$aq->load($id);
		if ($aq->state != 2) 
		{
			$calc += $aq->helpful * $p_RQ;
		}

		$calc = ($calc) ? $calc : '0';

		return $calc;
	}

	/**
	 * Distribute points
	 * 
	 * @param      integer $qid      Question ID
	 * @param      integer $Q_owner  Question owner
	 * @param      integer $BA_owner Account owner
	 * @param      string  $type     Transaction type
	 * @return     void
	 */
	public function distribute_points($qid, $Q_owner, $BA_owner, $type)
	{
		$juser =& JFactory::getUser();

		if ($qid === NULL) 
		{
			$qid = $this->qid;
		}
		$cat = 'answers';
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');

		$points = $this->calculate_marketvalue($qid, $type);

		$BT = new Hubzero_Bank_Transaction($this->_db);
		$reward = $BT->getAmount($cat, 'hold', $qid);
		$reward = ($reward) ? $reward : '0';
		$share = $points/3;

		$BA_owner_share = $share + $reward;
		$A_owner_share  = 0;

		// Calculate commissions for other answers
		$ar = new AnswersTableResponse($this->_db);
		$result = $ar->getActions($qid);

		$n = count($result);
		$eligible = array();

		if ($n > 1) 
		{
			// More than one answer found
			for ($i=0; $i < $n; $i++)
			{
				// Check if a regular answer has a good rating (at least 50% of positive votes)
				if (($result[$i]->helpful + $result[$i]->nothelpful) >= 3
				 && ($result[$i]->helpful >= $result[$i]->nothelpful)
				 && $result[$i]->state=='0') 
				{
					$eligible[] = $result[$i]->created_by;
				}
			}
			if (count($eligible) > 0) 
			{
				// We have eligible answers
				$A_owner_share = $share/$n;
			} 
			else 
			{
				// Best A owner gets remaining thrid
				$BA_owner_share += $share;
			}
		} 
		else 
		{
			// Best A owner gets remaining 3rd
			$BA_owner_share += $share;
		}

		// Reward asker
		$q_user =& JUser::getInstance($Q_owner);
		if (is_object($q_user) && $q_user->get('id')) 
		{
			$BTL_Q = new Hubzero_Bank_Teller($this->_db , $q_user->get('id'));
			//$BTL_Q->deposit($Q_owner_share, 'Commission for posting a question', $cat, $qid);
			// Separate comission and reward payment
			// Remove credit
			$credit = $BTL_Q->credit_summary();
			$adjusted = $credit - $reward;
			$BTL_Q->credit_adjustment($adjusted);

			if (intval($share) > 0) 
			{
				$share_msg = ($type=='royalty') ? JText::sprintf('Royalty payment for posting question #%s', $qid) : JText::sprintf('Commission for posting question #%s', $qid);
				$BTL_Q->deposit($share, $share_msg, $cat, $qid);
			}
			// withdraw reward amount
			if ($reward) 
			{
				$BTL_Q->withdraw($reward, JText::sprintf('Reward payment for your question #%s', $qid), $cat, $qid);
			}
		}

		// Reward others
		//$ba_user =& JUser::getInstance($BA_owner);
		ximport('Hubzero_User_Profile');
		$ba_user = Hubzero_User_Profile::getInstance($BA_owner);
		if (is_object($ba_user) && $ba_user->get('id')) 
		{
			// Reward other responders
			if (count($eligible) > 0) 
			{
				foreach ($eligible as $e)
				{
					$auser = Hubzero_User_Profile::getInstance($e);
					if (is_object($auser) && $auser->get('id') && is_object($ba_user) && $ba_user->get('id') && $ba_user->get('id') != $auser->get('id')) 
					{
						$BTL_A = new Hubzero_Bank_Teller($this->_db , $auser->get('id'));
						if (intval($A_owner_share) > 0) 
						{
							$A_owner_share_msg = ($type=='royalty') ? JText::sprintf('Royalty payment for answering question #%s', $qid) : JText::sprintf('Answered question #%s that was recently closed', $qid);
							$BTL_A->deposit($A_owner_share, $A_owner_share_msg , $cat, $qid);
						}
					}
					// is best answer eligible for extra points?
					if (is_object($auser) && $auser->get('id') &&  is_object($ba_user) && $ba_user->get('id') && ($ba_user->get('id') == $auser->get('id'))) 
					{
						$ba_extra = 1;
					}
				}
			}

			// Reward best answer
			$BTL_BA = new Hubzero_Bank_Teller($this->_db , $ba_user->get('id'));

			if (isset($ba_extra)) 
			{
				$BA_owner_share += $A_owner_share;
			}

			if (intval($BA_owner_share) > 0) 
			{
				$BA_owner_share_msg = ($type=='royalty') ? JText::sprintf('Royalty payment for answering question #%s', $qid) : JText::sprintf('Answer for question #%s was accepted', $qid);
				$BTL_BA->deposit($BA_owner_share, $BA_owner_share_msg, $cat, $qid);
			}
		}

		// Remove hold if exists
		if ($reward) 
		{
			$BT = new Hubzero_Bank_Transaction($this->_db);
			$BT->deleteRecords('answers', 'hold', $qid);
		}
	}
}

