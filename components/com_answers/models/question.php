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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'response.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'iterator.php');

/**
 * Courses model class for a forum
 */
class AnswersModelQuestion extends AnswersModelAbstract
{
	/**
	 * Table class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'AnswersTableQuestion';

	/**
	 * Class scope
	 * 
	 * @var string
	 */
	protected $_scope = 'question';

	/**
	 * AnswersModelComment
	 * 
	 * @var object
	 */
	private $_comment = null;

	/**
	 * AnswersModelIterator
	 * 
	 * @var object
	 */
	private $_comments = null;

	/**
	 * Comment count
	 * 
	 * @var integer
	 */
	private $_comments_count = null;

	/**
	 * Flag for if authorization checks have been run
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * URL for this entry
	 * 
	 * @var string
	 */
	private $_base = 'index.php?option=com_answers';

	/**
	 * Returns a reference to a question model
	 *
	 * This method must be invoked as:
	 *     $offering = AnswersModelQuestion::getInstance($id);
	 *
	 * @param      integer $oid Question ID
	 * @return     object ForumModelCourse
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new AnswersModelQuestion($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Is the question closed?
	 * 
	 * @return     boolean
	 */
	public function isClosed()
	{
		if ($this->get('state') == ANSWERS_STATE_CLOSED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the question open?
	 * 
	 * @return     boolean
	 */
	public function isOpen()
	{
		if ($this->get('state') == ANSWERS_STATE_OPEN) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Is there a reward?
	 * 
	 * @return     boolean
	 */
	public function reward($val='reward')
	{
		if (!$this->config('banking'))
		{
			return 0;
		}

		if ($this->get('reward', -1) == -1)
		{
			$BT = new Hubzero_Bank_Transaction($this->_db);
			$this->set('reward', $BT->getAmount('answers', 'hold', $this->get('id')));

			$AE = new AnswersEconomy($this->_db);
			
			$this->set('marketvalue', round($AE->calculate_marketvalue($this->get('id'), 'maxaward')));
			$this->set('maxaward', round(2* $this->get('marketvalue', 0)/3 + $this->get('reward', 0)));

			$this->set('totalmarketvalue', $this->get('marketvalue', 0) + $this->get('reward', 0));

			$this->set('asker_earnings', round($this->get('marketvalue', 0)/3));
			$this->set('answer_earnings', (round(($this->get('marketvalue', 0))/3) + $this->get('reward', 0)) .' &mdash; ' . (round(2*(($this->get('marketvalue', 0))/3)) + $this->get('reward', 0)));
		}

		return $this->get($val, 0);
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Set and get a specific comment
	 * 
	 * @return     void
	 */
	public function comment($id=null)
	{
		if (!isset($this->_comment) 
		 || ($id !== null && (int) $this->_comment->get('id') != $id))
		{
			$this->_comment = null;

			// See if we already have a list of comments that we can look through
			if (isset($this->_comments) && is_a($this->_comments, 'AnswersModelIterator'))
			{
				foreach ($this->_comments as $key => $comment)
				{
					if ((int) $comment->get('id') == $id)
					{
						$this->_comment = $comment;
						break;
					}
				}
			}

			// Nothing found so far?
			if (!$this->_comment)
			{
				// Load the record
				$this->_comment = AnswersModelComment::getInstance($id);
			}
		}
		return $this->_comment;
	}

	/**
	 * Get a list of responses
	 * 
	 * @param      string $rtrn    Data type to return [count, list]
	 * @param      array  $filters Filters to apply to query
	 * @return     mixed Returns an integer or array depending upon format chosen
	 */
	public function comments($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new AnswersTableResponse($this->_db);

		if (!isset($filters['qid']))
		{
			$filters['qid'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state']    = 1;
		}
		if (!isset($filters['filterby']))
		{
			$filters['filterby'] = 'rejected';
		}
		$filters['sort']     = 'created';
		$filters['sort_Dir'] = 'DESC';

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || $clear)
				{
					$total = 0;

					if (!($c = $this->get('comments'))) 
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($c as $com)
					{
						$total++;
						if ($com->replies()) 
						{
							foreach ($com->replies() as $rep)
							{
								$total++;
								if ($rep->replies()) 
								{
									$total += $rep->replies()->total();
								}
							}
						}
					}

					$this->_comments_count = $total;
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!is_a($this->_comments, 'AnswersModelIterator') || $clear)  //!isset($this->_comments) || 
				{
					if ($results = $tbl->getResults($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new AnswersModelResponse($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new AnswersModelIterator($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get a list of chosen responses
	 * 
	 * @param      string $rtrn    Data type to return [count, list]
	 * @param      array  $filters Filters to apply to query
	 * @return     mixed Returns an integer or array depending upon format chosen
	 */
	public function chosen($rtrn='list', $filters=array())
	{
		$tbl = new AnswersTableResponse($this->_db);

		if (!isset($filters['qid']))
		{
			$filters['qid'] = $this->get('id');
		}
		$filters['state']    = 1;
		$filters['filterby'] = 'accepted';
		$filters['sort']     = 'created';
		$filters['sort_Dir'] = 'DESC';

		switch (strtolower($rtrn))
		{
			case 'count':
				if ($this->get('chosen_count', null) === null)
				{
					$this->set('chosen_count', $tbl->getCount($filters));
				}
				return $this->get('chosen_count');
			break;

			case 'list':
			case 'results':
			default:
				if ($this->get('chosen', null) === null || !is_a($this->get('chosen'), 'AnswersModelIterator'))
				{
					if ($results = $tbl->getResults($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new AnswersModelResponse($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->set('chosen', new AnswersModelIterator($results));
				}
				return $this->get('chosen');
			break;
		}
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 * 
	 * @param      string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param      integer $admin Include amdin tags? (defaults to no)
	 * @return     boolean
	 */
	public function tags($as='cloud', $admin=0)
	{
		$cloud = new AnswersModelTags($this->get('id'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 * 
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new AnswersModelTags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string $as Format to return state in [text, number]
	 * @return     mixed String or Integer
	 */
	public function state($as='text')
	{
		$as = strtolower($as);

		if ($as == 'text')
		{
			switch ($this->get('state'))
			{
				case 1:
					return 'closed';
				break;
				case 0:
				default:
					return 'open';
				break;
			}
		}
		else
		{
			return $this->get('state');
		}
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
	{
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&task=delete&id=' . $this->get('id');
			break;

			case 'answer':
				$link .= '&task=answer&id=' . $this->get('id') . '#comments';
			break;

			case 'comments':
				$link .= '&task=question&id=' . $this->get('id') . '#comments';
			break;

			case 'math':
				$link .= '&task=math&id=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=question&id=' . $this->get('id');
			break;

			case 'permalink':
			default:
				$link .= '&task=question&id=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	/*public function published($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), ANSWERS_DATE_FORMAT, ANSWERS_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), ANSWERS_TIME_FORMAT, ANSWERS_DATE_TIMEZONE);
			break;

			default:
				return $this->get('created');
			break;
		}
	}*/

	/**
	 * Get the content of the record. 
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 * 
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($this->get('question_parsed'))
				{
					return $this->get('question_parsed');
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				//$config = JComponentHelper::getParams($option);

				$p =& Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => 'com_answers',
					'scope'    => 'question',
					'pagename' => $this->get('id'),
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => ''
				);

				$this->set('question_parsed', $p->parse(stripslashes($this->get('question')), $wikiconfig));

				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($this->get('question_parsed'), $shorten, 0, 0);
					if (substr($content, -7) == '&#8230;') 
					{
						$content .= '</p>';
					}
					return $content;
				}

				return $this->get('question_parsed');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;

			case 'raw':
			default:
				return $this->get('question');
			break;
		}
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function subject($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($this->get('subject_parsed'))
				{
					return $this->get('subject_parsed');
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				//$config = JComponentHelper::getParams($option);

				$p =& Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => 'com_answers',
					'scope'    => 'question',
					'pagename' => $this->get('id'),
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => ''
				);

				$this->set('subject_parsed', $p->parse(stripslashes($this->get('subject')), $wikiconfig));

				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($this->get('subject_parsed'), $shorten, 0, 0);
					if (substr($content, -7) == '&#8230;') 
					{
						$content .= '</p>';
					}
					return $content;
				}

				return $this->get('subject_parsed');
			break;

			case 'clean':
				$content = strip_tags($this->subject('parsed'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;

			case 'raw':
			default:
				return $this->get('subject');
			break;
		}
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param      integer $user_id Optinal user ID to set as voter
	 * @return     integer
	 */
	public function voted($user_id=0)
	{
		if ($this->get('voted', -1) == -1)
		{
			$juser = ($user_id) ? JUser::getInstance($user_id) : JFactory::getUser();

			// See if a person from this IP has already voted in the last week
			$aql = new AnswersTableQuestionsLog($this->_db);
			$this->set(
				'voted', 
				$aql->checkVote($this->get('id'), Hubzero_Environment::ipAddress(), $juser->get('id'))
			);
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param      integer $vote    The vote [0, 1]
	 * @param      integer $user_id Optinal user ID to set as voter
	 * @return     boolean False if error, True on success
	 */
	public function vote($vote=0, $user_id=0)
	{
		if (!$this->exists())
		{
			$this->setError(JText::_('No record found'));
			return false;
		}

		if (!$vote)
		{
			$this->setError(JText::_('No vote provided'));
			return false;
		}

		$juser = ($user_id) ? JUser::getInstance($user_id) : JFactory::getUser();

		$al = new AnswersTableQuestionsLog($this->_db);
		$al->qid   = $this->get('id');
		$al->ip    = Hubzero_Environment::ipAddress();
		$al->voter = $juser->get('id');

		if ($al->checkVote($al->qid, $al->ip, $al->voter))
		{
			$this->setError(JText::_('COM_ANSWERS_NOTICE_ALREADY_VOTED_FOR_QUESTION'));
			return false;
		}

		if ($this->get('created_by') == $juser->get('username')) 
		{
			$this->setError(JText::_('COM_ANSWERS_NOTICE_RECOMMEND_OWN_QUESTION'));
			return false;
		}

		$this->set('helpful', (int) $this->get('helpful') + 1);

		if (!$this->store()) 
		{
			return false;
		}

		$al->expires = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)); // in a week

		if (!$al->check()) 
		{
			$this->setError($al->getError());
			return false;
		}
		if (!$al->store()) 
		{
			$this->setError($al->getError());
			return false;
		}

		return true;
	}

	/**
	 * Accept a response as the chosen answer
	 *
	 * @param     integer $answer_id ID of response to be chosen
	 * @return    boolean False if error, True on success
	 */
	public function accept($answer_id=0)
	{
		if (!$answer_id)
		{
			$this->setError(JText::_('No answer ID provided.'));
			return false;
		}

		// Load the answer
		$answer = new AnswersModelResponse($answer_id);
		if (!$answer->exists())
		{
			$this->setError(JText::_('Answer not found.'));
			return false;
		}
		// Mark it at the chosen one
		$answer->set('state', 1);
		if (!$answer->store(true)) 
		{
			$this->setError($answer->getError());
			return false;
		}

		// Mark the question as answered
		$this->set('state', 1);

		// If this question has a reward...
		if ($this->get('reward'))
		{
			// Accepted answer is same person as question submitter?
			if ($this->get('created_by') == $answer->get('created_by'))
			{
				$BT = new Hubzero_Bank_Transaction($this->_db);
				$reward = $BT->getAmount('answers', 'hold', $this->get('id'));

				// Remove hold
				$BT->deleteRecords('answers', 'hold', $this->get('id'));

				// Make credit adjustment
				$BTL_Q = new Hubzero_Bank_Teller($this->_db, JFactory::getUser()->get('id'));
				$BTL_Q->credit_adjustment($BTL_Q->credit_summary() - $reward);
			}
			else 
			{
				// Calculate and distribute earned points
				$AE = new AnswersEconomy($this->_db);
				$AE->distribute_points(
					$this->get('id'), 
					$this->get('created_by'), 
					$answer->get('created_by'), 
					'closure'
				);
			}

			// Set the reward value
			$this->set('reward', 0);
		}

		// Save changes
		return $this->store(true);
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->_authorized)
		{
			// Set NOT viewable by default
			// We need to ensure the forum is published first
			$this->params->set('access-view-entry', false);

			if ($this->exists() && $this->isAvailable())
			{
				$this->params->set('access-view-entry', true);
			}

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a forum
				//$this->params->set('access-create-entry', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_blog', 'manage')) 
					{
						$this->params->set('access-admin-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}
				else 
				{
					$this->params->set('access-admin-entry', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-entry', $juser->authorise('core.manage', $this->get('id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-entry') 
				 && !$this->params->get('access-manage-entry'))
				{
					// Does the forum exist?
					/*if (!$this->exists())
					{
						// Give editing access if the blog doesn't exist
						// i.e., it's a new forum
						switch ($this->get('scope'))
						{
							case 'site':
							break;
							
							case 'member':
							break;
						}
						$this->params->set('access-view-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
					// Check if they're the forum creator or forum manager
					else*/
					if ($this->get('created_by') == $juser->get('id')) 
					{
						// Give full access
						$this->params->set('access-view-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}

	/**
	 * Distribute points
	 *
	 * @return    void
	 */
	public function adjustCredits()
	{
		if ($this->get('reward'))
		{
			// Adjust credits
			// Remove hold
			$BT = new Hubzero_Bank_Transaction($this->_db);
			$reward = $BT->getAmount('answers', 'hold', $this->get('id'));
			$BT->deleteRecords('answers', 'hold', $this->get('id'));

			// Make credit adjustment
			if (is_object($this->creator()))
			{
				$BTL = new Hubzero_Bank_Teller($this->_db, $this->creator('id'));
				$credit = $BTL->credit_summary();
				$adjusted = $credit - $reward;
				$BTL->credit_adjustment($adjusted);
			}

			$this->set('reward', 0);
		}
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			$this->setError(JText::_('Database not found.'));
			return false;
		}

		// Can't delete what doesn't exist
		if (!$this->exists()) 
		{
			return true;
		}

		// Adjust credits
		$this->adjustCredits();

		// Remove comments
		foreach ($this->comments('list', array('filterby' => 'all')) as $comment)
		{
			if (!$comment->delete())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Attempt to delete the record
		return parent::delete();
	}
}

