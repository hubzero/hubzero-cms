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

use Hubzero\Database\Relational;
use Hubzero\User\Profile;
use Request;
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'vote.php');
require_once(__DIR__ . DS . 'comment.php');

/**
 * Response model for Q&A
 */
class Response extends Relational
{
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
		'answer'  => 'notempty'
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
		'answer'
	);

	/**
	 * Base URL
	 *
	 * @var  string
	 */
	private $base = null;

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isReported()
	{
		return ($this->get('state') == 3);
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
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function replies()
	{
		return $this->oneShiftsToMany('Comment', 'item_id', 'item_type');
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
			$vote->set('item_type', 'response');
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
			$vote->set('item_type', 'response');
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
		$al->set('item_type', 'response');
		$al->set('item_id', $this->get('id'));
		$al->set('created_by', $user_id);
		$al->set('ip', $ip);

		$vote = $al->automaticVote(['vote' => $vote]);

		if ($this->get('created_by') == $user_id)
		{
			$this->setError(Lang::txt('COM_ANSWERS_NOTICE_RECOMMEND_OWN_QUESTION'));
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
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		if (!isset($this->base))
		{
			$this->base = 'index.php?option=com_answers&task=question&id=' . $this->get('question_id');
		}
		$link = $this->base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&reply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'accept':
				$link  = 'index.php?option=com_answers&task=accept&id=' . $this->get('question_id') . '&rid=' . $this->get('id');
				//$link .= '&task=accept&id' . $this->get('question_id') . '&rid=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=answer&id=' . $this->get('id') . '&parent=' . $this->get('question_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Mark a response as "Accepted"
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function accept()
	{
		$question = $this->question();

		if (!$question->get('id'))
		{
			$this->setError(Lang::txt('Question not found.'));
			return false;
		}

		if ($question->get('state') != 1)
		{
			// Mark it at the chosen one
			$question->set('state', 1);

			if (!$question->save(true))
			{
				$this->setError($question->getError());
				return false;
			}
		}

		// Un-mark any previous chosen responses
		foreach ($question->responses() as $response)
		{
			$response->set('state', 0);
			$response->save();
		}

		$this->set('state', 1);

		if (!$this->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Mark a response as "Rejected"
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function reject()
	{
		$this->set('state', 0);

		if (!$this->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Transform answer
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		return $this->answer;
	}

	/**
	 * Reset the vote count and log
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function reset()
	{
		// Reset the vote counts
		$this->set('helpful', 0);
		$this->set('nothelpful', 0);

		if (!$this->save())
		{
			return false;
		}

		// Clear the history of "helpful" clicks
		foreach ($this->votes() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->comments() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

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
}

