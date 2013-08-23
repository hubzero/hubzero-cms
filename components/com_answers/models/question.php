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

define('ANSWERS_STATE_OPEN',    0);
define('ANSWERS_STATE_CLOSED',  1);
define('ANSWERS_STATE_DELETED', 2);

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');

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
	protected $_tbl_name = 'AnswersQuestion';

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
	 * Is the question open?
	 * 
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if ($this->get('state') == ANSWERS_STATE_DELETED) 
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
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function comment($id=null)
	{
		if (!isset($this->_comment) 
		 || ($id !== null && (int) $this->_comment->get('id') != $id))
		{
			$this->_comment = null;
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
			if (!$this->_comment)
			{
				$this->_comment = AnswersModelComment::getInstance($id);
			}
		}
		return $this->_comment;
	}

	/**
	 * Get a list of categories for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function comments($rtrn='list', $filters=array())
	{
		$tbl = new AnswersResponse($this->_db);

		if (!isset($filters['qid']))
		{
			$filters['qid'] = $this->get('id');
		}
		$filters['state']    = 1;
		$filters['filterby'] = 'rejected';
		$filters['sort']     = 'created';
		$filters['sort_Dir'] = 'DESC';

		switch (strtolower($rtrn))
		{
			case 'count':
				if ($this->get('comments_count', null) === null)
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
					$this->set('comments_count', $total);
				}
				return $this->get('comments_count', 0);
			break;

			case 'list':
			case 'results':
			default:
				if ($this->get('comments', null) === null || !is_a($this->get('comments'), 'AnswersModelIterator'))
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
					$this->set('comments', new AnswersModelIterator($results));
				}
				return $this->get('comments');
			break;
		}
	}

	/**
	 * Get a list of categories for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function chosen($rtrn='list', $filters=array())
	{
		$tbl = new AnswersResponse($this->_db);

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
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function tags($what='cloud', $admin=0)
	{
		$bt = new AnswersTags($this->_db);

		$tags = null;

		$what = strtolower(trim($what));
		switch ($what)
		{
			case 'array':
				$tags = $bt->get_tags_on_object($this->get('id'), 0, 0, null, 0, $admin);
			break;

			case 'string':
				$tags = $bt->get_tag_string($this->get('id'));
			break;

			case 'cloud':
				$tags = $bt->get_tag_cloud(0, 0, $this->get('id'));
			break;
		}

		return $tags; 
	}

	/**
	 * Tag the entry
	 * 
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0)
	{
		$bt = new AnswersTags($this->_db);

		if (!$user_id)
		{
			$juser = JFactory::getUser();
			$user_id = $juser->get('id');
		}

		return $bt->tag_object(
			$user_id, 
			$this->get('id'), 
			trim($tags), 
			1, 
			1
		);
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
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=com_answers';
		}
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
	public function published($as='')
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
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string  $as      Format to return state in [text, number]
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
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
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
}

