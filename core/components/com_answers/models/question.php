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

namespace Components\Answers\Models;

use Components\Answers\Tables;
use Components\Answers\Helpers;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use Hubzero\Bank\Transaction;
use Hubzero\Bank\Teller;
use Lang;
use User;
use Date;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'questionslog.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'question.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'economy.php');
require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'response.php');

/**
 * Answers mdoel class for a question
 */
class Question extends Base
{
	/**
	 * Open state
	 *
	 * @var integer
	 */
	const ANSWERS_STATE_OPEN   = 0;

	/**
	 * Closed state
	 *
	 * @var integer
	 */
	const ANSWERS_STATE_CLOSED = 1;

	/**
	 * Deleted
	 *
	 * @var integer
	 */
	const ANSWERS_STATE_DELETE = 2;

	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Answers\\Tables\\Question';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_answers.question.question';

	/**
	 * Comment
	 *
	 * @var object
	 */
	private $_comment = null;

	/**
	 * \Hubzero\Base\ItemList
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
	 * @var boolean
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
	 * @param   integer $oid Question ID
	 * @return  object
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
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Is the question closed?
	 *
	 * @return  boolean
	 */
	public function isClosed()
	{
		if ($this->get('state') == static::ANSWERS_STATE_CLOSED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		if ($this->get('state') == static::ANSWERS_STATE_OPEN)
		{
			return true;
		}
		return false;
	}

	/**
	 * Get reward value
	 *
	 * @param   string $val Value to return
	 * @return  string
	 */
	public function reward($val='reward')
	{
		if (!$this->config('banking'))
		{
			return 0;
		}

		if ($this->get('reward', -1) == 1)
		{
			$BT = new Transaction($this->_db);
			$this->set('reward', $BT->getAmount('answers', 'hold', $this->get('id')));

			$AE = new Helpers\Economy($this->_db);

			$this->set('marketvalue', round($AE->calculate_marketvalue($this->get('id'), 'maxaward')));
			$this->set('maxaward', round(2* $this->get('marketvalue', 0)/3 + $this->get('reward', 0)));

			$this->set('totalmarketvalue', $this->get('marketvalue', 0) + $this->get('reward', 0));

			$this->set('asker_earnings', round($this->get('marketvalue', 0)/3));
			$this->set('answer_earnings', (round(($this->get('marketvalue', 0))/3) + $this->get('reward', 0)) .' &mdash; ' . (round(2*(($this->get('marketvalue', 0))/3)) + $this->get('reward', 0)));
		}

		return $this->get($val, 0);
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
			if ($this->_comments instanceof ItemList)
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
				$this->_comment = Comment::getInstance($id);
			}
		}
		return $this->_comment;
	}

	/**
	 * Get a list of responses
	 *
	 * @param   string  $rtrn    Data type to return [count, list]
	 * @param   array   $filters Filters to apply to query
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed   Returns an integer or array depending upon format chosen
	 */
	public function comments($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Response($this->_db);

		if (!isset($filters['question_id']))
		{
			$filters['question_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state']    = 0;
		}
		if (!isset($filters['filterby']))
		{
			$filters['filterby'] = 'rejected';
		}
		if (!isset($filters['replies']))
		{
			$filters['replies'] = true;
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
						if ($filters['replies'] && $com->replies()->total())
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
				if (!($this->_comments instanceof ItemList) || $clear)
				{
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Response($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get a list of chosen responses
	 *
	 * @param   string $rtrn    Data type to return [count, list]
	 * @param   array  $filters Filters to apply to query
	 * @return  mixed  Returns an integer or array depending upon format chosen
	 */
	public function chosen($rtrn='list', $filters=array())
	{
		$tbl = new Tables\Response($this->_db);

		if (!isset($filters['question_id']))
		{
			$filters['question_id'] = $this->get('id');
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
					$this->set('chosen_count', $tbl->find('count', $filters));
				}
				return $this->get('chosen_count');
			break;

			case 'list':
			case 'results':
			default:
				if ($this->get('chosen', null) === null || !($this->get('chosen') instanceof ItemList))
				{
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Response($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->set('chosen', new ItemList($results));
				}
				return $this->get('chosen');
			break;
		}
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param   string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer $admin Include amdin tags? (defaults to no)
	 * @return  mixed
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->exists())
		{
			switch (strtolower($as))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new Tags($this->get('id'));

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Add a single tag to the entry
	 *
	 * @return  boolean
	 */
	public function addTag($tag=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->add($tag, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string $as Format to return state in [text, number]
	 * @return  mixed  String or Integer
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
	 * @param   string $type The type of link to return
	 * @return  string
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
				$link .= '&task=question&id=' . $this->get('id') . '#math';
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
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param   string  $as      Format to return content in [parsed, clean, raw]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('question.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_answers',
						'scope'    => 'question',
						'pagename' => $this->get('id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = str_replace(array('\"', "\'"), array('"', "'"), (string) $this->get('question', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('question.parsed', (string) $this->get('question', ''));
					$this->set('question', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = html_entity_decode(strip_tags($this->content('parsed')), ENT_COMPAT, 'UTF-8');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;

			case 'raw':
			default:
				$content = str_replace(array('\"', "\'"), array('"', "'"), $this->get('question'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}

		return $content;
	}

	/**
	 * Get the subject in various formats
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function subject($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('subject.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_answers',
						'scope'    => 'question',
						'pagename' => $this->get('id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) $this->get('subject', '');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						'com_answers.question.subject',
						&$this,
						&$config
					));

					$this->set('subject.parsed', (string) $this->get('subject', ''));
					$this->set('subject', $content);

					return $this->subject($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = html_entity_decode(strip_tags($this->subject('parsed')), ENT_COMPAT, 'UTF-8');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;

			case 'raw':
			default:
				$content = $this->get('subject');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}

		return $content;
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param   integer $user_id Optinal user ID to set as voter
	 * @return  integer
	 */
	public function voted($user_id=0)
	{
		if ($this->get('voted', -1) == -1)
		{
			$user = ($user_id) ? User::getInstance($user_id) : User::getRoot();

			// See if a person from this IP has already voted in the last week
			$aql = new Tables\QuestionsLog($this->_db);
			$this->set(
				'voted',
				$aql->checkVote($this->get('id'), Request::ip(), $user->get('id'))
			);
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param   integer  $vote     The vote [0, 1]
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @return  boolean  False if error, True on success
	 */
	public function vote($vote=0, $user_id=0)
	{
		if (!$this->exists())
		{
			$this->setError(Lang::txt('No record found'));
			return false;
		}

		if (!$vote)
		{
			$this->setError(Lang::txt('No vote provided'));
			return false;
		}

		$user = ($user_id) ? User::getInstance($user_id) : User::getRoot();

		$al = new Tables\QuestionsLog($this->_db);
		$al->question_id = $this->get('id');
		$al->ip          = Request::ip();
		$al->voter       = $user->get('id');

		if ($al->checkVote($al->question_id, $al->ip, $al->voter))
		{
			$this->setError(Lang::txt('COM_ANSWERS_NOTICE_ALREADY_VOTED_FOR_QUESTION'));
			return false;
		}

		if ($this->get('created_by') == $user->get('id'))
		{
			$this->setError(Lang::txt('COM_ANSWERS_NOTICE_RECOMMEND_OWN_QUESTION'));
			return false;
		}

		$this->set('helpful', (int) $this->get('helpful') + 1);

		if (!$this->store())
		{
			return false;
		}

		$al->expires = Date::add('1 week')->toSql();

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
	 * @param   integer $answer_id ID of response to be chosen
	 * @return  boolean False if error, True on success
	 */
	public function accept($answer_id=0)
	{
		if (!$answer_id)
		{
			$this->setError(Lang::txt('No answer ID provided.'));
			return false;
		}

		// Load the answer
		$answer = new Response($answer_id);
		if (!$answer->exists())
		{
			$this->setError(Lang::txt('Answer not found.'));
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

		// If banking is enabled
		if ($this->config('banking'))
		{
			// Accepted answer is same person as question submitter?
			if ($this->get('created_by') == $answer->get('created_by'))
			{
				$BT = new Transaction($this->_db);
				$reward = $BT->getAmount('answers', 'hold', $this->get('id'));

				// Remove hold
				$BT->deleteRecords('answers', 'hold', $this->get('id'));

				// Make credit adjustment
				$BTL_Q = new Teller($this->_db, User::get('id'));
				$BTL_Q->credit_adjustment($BTL_Q->credit_summary() - $reward);
			}
			else
			{
				// Calculate and distribute earned points
				$AE = new Economy($this->_db);
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
	 * Distribute points
	 *
	 * @return  void
	 */
	public function adjustCredits()
	{
		if ($this->get('reward'))
		{
			// Adjust credits
			// Remove hold
			$BT = new Transaction($this->_db);
			$reward = $BT->getAmount('answers', 'hold', $this->get('id'));
			$BT->deleteRecords('answers', 'hold', $this->get('id'));

			// Make credit adjustment
			if (is_object($this->creator()))
			{
				$BTL = new Teller($this->_db, $this->creator('id'));
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
	 * @return  boolean  False if error, True on success
	 */
	public function delete()
	{
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

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->check())
			{
				return false;
			}

			if ($this->_context)
			{
				$txt = $this->get('subject') . ' ' . $this->get('question') . ' ' . $this->get('tags');
				$results = $this->importPlugin('content')->trigger('onContentBeforeSave', array(
					$this->_context,
					&$txt,
					$this->exists()
				));
				foreach ($results as $result)
				{
					if ($result === false)
					{
						$this->setError(\Lang::txt('Content failed validation.'));
						return false;
					}
				}
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}
}

