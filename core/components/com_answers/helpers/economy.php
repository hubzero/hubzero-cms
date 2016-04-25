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

namespace Components\Answers\Helpers;

use Components\Answers\Models\Question;
use Components\Answers\Models\Response;
use Hubzero\Base\Object;
use Hubzero\Bank\Config;
use Hubzero\Bank\Transaction;
use Hubzero\Bank\Teller;
use Lang;
use User;

/**
 * Answers Economy class:
 * Stores economy funtions for com_answers
 */
class Economy extends Object
{
	/**
	 * Database
	 *
	 * @var  object
	 */
	protected $_db = NULL;

	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db = $db;
	}

	/**
	 * Get questions
	 *
	 * @return  array
	 */
	public function getQuestions()
	{
		// get all closed questions
		$this->_db->setQuery(
			"SELECT q.id, q.created_by AS q_owner, a.created_by AS a_owner
			FROM `#__answers_questions` AS q
			LEFT JOIN `#__answers_responses` AS a ON q.id=a.question_id AND a.state=1
			WHERE q.state=1"
		);
		return $this->_db->loadObjectList();
	}

	/**
	 * Calculate the market value
	 *
	 * @param   integer  $id    Question ID
	 * @param   string   $type  Transaction type
	 * @return  mixed
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

		require_once(dirname(__DIR__) . DS . 'models' . DS . 'question.php');

		// Get point values for actions
		$BC = Config::values();
		$p_Q  = $BC->get('ask');
		$p_A  = $BC->get('answer');
		$p_R  = $BC->get('answervote');
		$p_RQ = $BC->get('questionvote');
		$p_A_accepted = $BC->get('accepted');

		$calc = 0;

		// Get actons and sum up
		$results = Response::all()
			->whereEquals('question_id', $id)
			->where('state', '!=', 2)
			->rows();

		if ($type != 'royalty')
		{
			$calc += $p_Q;  // ! this is different from version before code migration !
			$calc += ($results->count())*$p_A;
		}

		// Calculate as if there is at leat one answer
		if ($type == 'maxaward' && $results->count() == 0)
		{
			$calc += $p_A;
		}

		foreach ($results as $result)
		{
			$calc += ($result->get('helpful'))*$p_R;
			$calc += ($result->get('nothelpful'))*$p_R;
			if ($result->get('state') == 1 && $type != 'royalty')
			{
				$accepted = 1;
			}
		}

		if (isset($accepted) or $type == 'maxaward')
		{
			$calc += $p_A_accepted;
		}

		// Add question votes
		$aq = Question::oneOrNew($id);
		if ($aq->get('state') != 2)
		{
			$calc += $aq->get('helpful') * $p_RQ;
		}

		$calc = ($calc) ? $calc : '0';

		return $calc;
	}

	/**
	 * Distribute points
	 *
	 * @param   integer  $qid       Question ID
	 * @param   integer  $Q_owner   Question owner
	 * @param   integer  $BA_owner  Account owner
	 * @param   string   $type      Transaction type
	 * @return  void
	 */
	public function distribute_points($qid, $Q_owner, $BA_owner, $type)
	{
		if ($qid === NULL)
		{
			$qid = $this->qid;
		}
		$cat = 'answers';

		require_once(dirname(__DIR__) . DS . 'models' . DS . 'question.php');

		$points = $this->calculate_marketvalue($qid, $type);

		$reward = Transaction::getAmount($cat, 'hold', $qid);
		$reward = ($reward) ? $reward : '0';
		$share = $points/3;

		$BA_owner_share = $share + $reward;
		$A_owner_share  = 0;

		// Calculate commissions for other answers
		$results = Response::all()
			->whereEquals('question_id', $qid)
			->where('state', '!=', 2)
			->rows();

		$n = $results->count();
		$eligible = array();

		if ($n > 1)
		{
			// More than one answer found
			foreach ($results as $result)
			{
				// Check if a regular answer has a good rating (at least 50% of positive votes)
				if (($result->get('helpful') + $result->get('nothelpful')) >= 3
				 && ($result->get('helpful') >= $result->get('nothelpful'))
				 && $result->get('state') == 0)
				{
					$eligible[] = $result->get('created_by');
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
		$q_user = User::getInstance($Q_owner);
		if (is_object($q_user) && $q_user->get('id'))
		{
			$BTL_Q = new Teller($q_user->get('id'));
			//$BTL_Q->deposit($Q_owner_share, 'Commission for posting a question', $cat, $qid);
			// Separate comission and reward payment
			// Remove credit
			$credit = $BTL_Q->credit_summary();
			$adjusted = $credit - $reward;
			$BTL_Q->credit_adjustment($adjusted);

			if (intval($share) > 0)
			{
				$share_msg = ($type=='royalty') ? Lang::txt('Royalty payment for posting question #%s', $qid) : Lang::txt('Commission for posting question #%s', $qid);
				$BTL_Q->deposit($share, $share_msg, $cat, $qid);
			}
			// withdraw reward amount
			if ($reward)
			{
				$BTL_Q->withdraw($reward, Lang::txt('Reward payment for your question #%s', $qid), $cat, $qid);
			}
		}

		// Reward others
		$ba_user = User::getInstance($BA_owner);

		if (is_object($ba_user) && $ba_user->get('id'))
		{
			// Reward other responders
			if (count($eligible) > 0)
			{
				foreach ($eligible as $e)
				{
					$auser = User::getInstance($e);
					if (is_object($auser) && $auser->get('id') && is_object($ba_user) && $ba_user->get('id') && $ba_user->get('id') != $auser->get('id'))
					{
						$BTL_A = new Teller($auser->get('id'));
						if (intval($A_owner_share) > 0)
						{
							$A_owner_share_msg = ($type=='royalty') ? Lang::txt('Royalty payment for answering question #%s', $qid) : Lang::txt('Answered question #%s that was recently closed', $qid);
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
			$BTL_BA = new Teller($ba_user->get('id'));

			if (isset($ba_extra))
			{
				$BA_owner_share += $A_owner_share;
			}

			if (intval($BA_owner_share) > 0)
			{
				$BA_owner_share_msg = ($type=='royalty') ? Lang::txt('Royalty payment for answering question #%s', $qid) : Lang::txt('Answer for question #%s was accepted', $qid);
				$BTL_BA->deposit($BA_owner_share, $BA_owner_share_msg, $cat, $qid);
			}
		}

		// Remove hold if exists
		if ($reward)
		{
			$BT = Transaction::deleteRecords('answers', 'hold', $qid);
		}
	}
}

