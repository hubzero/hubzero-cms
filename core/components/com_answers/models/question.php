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

namespace Components\Answers\Models;

use Components\Answers\Helpers\Economy;
use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Config\Registry;
use Hubzero\User\Profile;
use Hubzero\Bank\Transaction;
use Hubzero\Bank\Teller;
use Request;
use Route;
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'response.php');

/**
 * Question model for Q&A
 */
class Question extends Relational
{
	/**
	 * Open state
	 *
	 * @var  integer
	 */
	const ANSWERS_STATE_OPEN   = 0;

	/**
	 * Closed state
	 *
	 * @var  integer
	 */
	const ANSWERS_STATE_CLOSED = 1;

	/**
	 * Deleted
	 *
	 * @var  integer
	 */
	const ANSWERS_STATE_DELETE = 2;

	/**
	 * Reported
	 *
	 * @var  integer
	 */
	const ANSWERS_STATE_REPORTED = 3;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'answers';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'subject'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 **/
	protected $parsed = array(
		'question'
	);

	/**
	 * Base URL
	 *
	 * @var  string
	 */
	private $_base = 'index.php?option=com_answers';

	/**
	 * Is the question closed?
	 *
	 * @return  boolean
	 */
	public function isClosed()
	{
		return ($this->get('state') == static::ANSWERS_STATE_CLOSED);
	}

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		return ($this->get('state') == static::ANSWERS_STATE_OPEN);
	}

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == static::ANSWERS_STATE_DELETE);
	}

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isReported()
	{
		return ($this->get('state') == static::ANSWERS_STATE_REPORTED);
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function creator()
	{
		if ($profile = Profile::getInstance($this->get('created_by')))
		{
			return $profile;
		}
		return new Profile;
	}

	/**
	 * Get a list of responses
	 *
	 * @return  object
	 */
	public function responses()
	{
		return $this->oneToMany('Components\Answers\Models\Response', 'question_id');
	}

	/**
	 * Get a list of chosen responses
	 *
	 * @return  object
	 */
	public function chosen()
	{
		return $this->responses()->whereEquals('state', 1);
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param   string   $as     Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer  $admin  Include amdin tags? (defaults to no)
	 * @return  mixed
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->get('id'))
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
	 * @param   string   $tags     Comma-separated list of tags to apply
	 * @param   integer  $user_id  Tagger ID
	 * @param   integer  $admin    Include amdin tags? (defaults to no)
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
	 * @param   string   $tags     Comma-separated list of tags to apply
	 * @param   integer  $user_id  Tagger ID
	 * @param   integer  $admin    Include amdin tags? (defaults to no)
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
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
	 */
	public function state($as='text')
	{
		$as = strtolower($as);

		if ($as == 'text')
		{
			return ($this->get('state') == static::ANSWERS_STATE_CLOSED ? 'closed' : 'open');
		}

		return $this->get('state');
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
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
	 * Get a list of votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return $this->oneShiftsToMany('Vote', 'item_id', 'item_type');
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       IP Address
	 * @return  integer
	 */
	public function ballot($user_id = 0, $ip = null)
	{
		if (User::isGuest())
		{
			$vote = new Vote();
			$vote->set('item_type', 'question');
			$vote->set('item_id', $this->get('id'));
			$vote->set('created_by', $user_id);
			$vote->set('ip', $ip);

			return $vote;
		}

		$user = $user_id ? User::getInstance($user_id) : User::getRoot();
		$ip   = $ip ?: Request::ip();

		// See if a person from this IP has already voted in the last week
		$votes = $this->votes();

		if ($user->get('id'))
		{
			$votes->whereEquals('created_by', $user->get('id'));
		}
		elseif ($ip)
		{
			$votes->whereEquals('ip', $ip);
		}

		$vote = $votes
			->ordered()
			->limit(1)
			->row();

		if (!$vote || !$vote->get('id'))
		{
			$vote = new Vote();
			$vote->set('item_type', 'question');
			$vote->set('item_id', $this->get('id'));
			$vote->set('created_by', $user_id);
			$vote->set('ip', $ip);
		}

		return $vote;
	}

	/**
	 * Vote for the entry
	 *
	 * @param   integer  $vote     The vote [0, 1]
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       Optional IP address
	 * @return  boolean  False if error, True on success
	 */
	public function vote($vote = 0, $user_id = 0, $ip = null)
	{
		if (!$this->get('id'))
		{
			$this->setError(Lang::txt('No record found'));
			return false;
		}

		if (!$vote)
		{
			$this->setError(Lang::txt('No vote provided'));
			return false;
		}

		$al = $this->ballot($user_id, $ip);
		$al->set('item_type', 'question');
		$al->set('item_id', $this->get('id'));
		$al->set('created_by', $user_id);
		$al->set('ip', $ip);

		$vote = $al->automaticVote(['vote' => $vote]);

		if ($this->get('created_by') == $user_id)
		{
			$this->setError(Lang::txt('Cannot vote for your own entry'));
			return false;
		}

		if ($vote != $al->get('vote', 0))
		{
			if ($vote > 0)
			{
				$this->set('helpful', (int) $this->get('helpful') + 1);
				if ($al->get('id'))
				{
					$this->set('nothelpful', (int) $this->get('nothelpful') - 1);
				}
			}
			else
			{
				if ($al->get('id'))
				{
					$this->set('helpful', (int) $this->get('helpful') - 1);
				}
				$this->set('nothelpful', (int) $this->get('nothelpful') + 1);
			}

			if (!$this->save())
			{
				return false;
			}

			$al->set('vote', $vote);

			if (!$al->save())
			{
				$this->setError($al->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Get reward value
	 *
	 * @param   string   $val  Value to return
	 * @return  integer
	 */
	public function reward($val='reward')
	{
		if (!$this->config('banking'))
		{
			return 0;
		}

		if ($this->get('reward', -1) == 1)
		{
			$db = \App::get('db');

			$BT = new Transaction($db);
			$this->set('reward', $BT->getAmount('answers', 'hold', $this->get('id')));

			$AE = new Economy($db);

			$this->set('marketvalue', round($AE->calculate_marketvalue($this->get('id'), 'maxaward')));
			$this->set('maxaward', round(2* $this->get('marketvalue', 0)/3 + $this->get('reward', 0)));

			$this->set('totalmarketvalue', $this->get('marketvalue', 0) + $this->get('reward', 0));

			$this->set('asker_earnings', round($this->get('marketvalue', 0)/3));
			$this->set('answer_earnings', ($this->get('asker_earnings') + $this->get('reward', 0)) . ' &mdash; ' . $this->get('maxaward'));
		}

		return $this->get($val, 0);
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->responses() as $response)
		{
			if (!$response->destroy())
			{
				$this->setError($response->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Remove vote logs
		foreach ($this->votes() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Default value if key not found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		$config = Component::params('com_answers');

		if ($key)
		{
			if ($key == 'banking' && $config->get('banking', -1) == -1)
			{
				$config->set('banking', Component::params('com_members')->get('bankAccounts'));
			}
			return $config->get($key, $default);
		}
		return $config;
	}

	/**
	 * Accept a response as the chosen answer
	 *
	 * @param   integer  $answer_id  ID of response to be chosen
	 * @return  boolean  False if error, True on success
	 */
	public function accept($answer_id=0)
	{
		if (!$answer_id)
		{
			$this->setError(Lang::txt('No answer ID provided.'));
			return false;
		}

		// Load the answer
		$answer = Response::oneOrFail($answer_id);

		// Mark it at the chosen one
		$answer->set('state', 1);
		if (!$answer->save())
		{
			$this->setError($answer->getError());
			return false;
		}

		// Mark the question as answered
		$this->set('state', 1);

		// If banking is enabled
		if ($this->config('banking'))
		{
			$db = \App::get('db');

			// Accepted answer is same person as question submitter?
			if ($this->get('created_by') == $answer->get('created_by'))
			{
				$BT = new Transaction($db);
				$reward = $BT->getAmount('answers', 'hold', $this->get('id'));

				// Remove hold
				$BT->deleteRecords('answers', 'hold', $this->get('id'));

				// Make credit adjustment
				$BTL_Q = new Teller($db, User::get('id'));
				$BTL_Q->credit_adjustment($BTL_Q->credit_summary() - $reward);
			}
			else
			{
				// Calculate and distribute earned points
				$AE = new Economy($db);
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
		return $this->save();
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
			$db = \App::get('db');

			// Adjust credits
			// Remove hold
			$BT = new Transaction($db);
			$reward = $BT->getAmount('answers', 'hold', $this->get('id'));
			$BT->deleteRecords('answers', 'hold', $this->get('id'));

			// Make credit adjustment
			if (is_object($this->creator()))
			{
				$BTL = new Teller($db, $this->get('created_by'));
				$credit = $BTL->credit_summary();
				$adjusted = $credit - $reward;
				$BTL->credit_adjustment($adjusted);
			}

			$this->set('reward', 0);
		}
	}
}

