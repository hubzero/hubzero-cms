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

namespace Components\Kb\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Request;
use Lang;
use Date;
use User;

require_once __DIR__ . DS . 'vote.php';

/**
 * Knowledgebase model for a comment
 */
class Comment extends Relational
{
	/**
	 * Database state constants
	 */
	const STATE_FLAGGED = 3;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'kb';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Base URL
	 *
	 * @var  string
	 */
	private $_base = 'index.php?option=com_kb';

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'content'
	);

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'content'  => 'notempty',
		'entry_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('created');

		if ($as == 'date')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $dt;
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Was the entry reported?
	 *
	 * @return  boolean  True if reported, False if not
	 */
	public function isReported()
	{
		return ($this->get('state') == 3);
	}

	/**
	 * Get either a count of or list of replies
	 *
	 * @param   array  $filters  Filters to apply to query
	 * @return  object
	 */
	public function replies($filters = array())
	{
		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('entry_id');
		}

		$entries = self::blank()->whereEquals('parent', (int) $this->get('id'));

		if (isset($filters['state']))
		{
			$entries->whereEquals('state', (int) $filters['state']);
		}

		if (isset($filters['entry_id']))
		{
			$entries->whereEquals('entry_id', (int) $filters['entry_id']);
		}

		return $entries;
	}

	/**
	 * Get parent comment
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent', 0));
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
		$link  = $this->_base;
		if (!$this->get('section'))
		{
			$article = Article::oneOrFail($this->get('entry_id'));

			$this->set('category', $article->category()->get('alias'));
			$this->set('article', $article->get('alias'));
		}
		$link .= '&section=' . $this->get('section');
		$link .= '&alias=' . $this->get('article');

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'article':
				// Return as is
			break;

			case 'edit':
				$link .= '&action=edit&comment=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&action=delete&comment=' . $this->get('id');
			break;

			case 'reply':
				$link .= '&reply=' . $this->get('id') . '#c' . $this->get('id');
			break;

			case 'vote':
				$link  = $this->_base . '&task=vote&category=comment&id=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=kb&id=' . $this->get('id') . '&parent=' . $this->get('entry_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
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
		return $this->oneShiftsToMany('Vote', 'object_id', 'type');
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       IP Address
	 * @return  integer
	 */
	public function voted($user_id = 0, $ip = null)
	{
		if ($this->get('voted', -1) == -1)
		{
			$user = ($user_id) ? User::getInstance($user_id) : User::getInstance();
			$ip   = ($ip ?: Request::ip());

			// See if a person from this IP has already voted in the last week
			$previous = Vote::find($this->get('id'), $user->get('id'), $ip, 'comment');

			$this->set('voted', $previous->get('vote'));
		}

		return $this->get('voted', 0);
	}

	/**
	 * Vote for the entry
	 *
	 * @param   integer  $vote     The vote [-1, 1, like, dislike, yes, no, positive, negative]
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @return  boolean  False if error, True on success
	 */
	public function vote($vote = 0, $user_id = 0)
	{
		if ($this->isNew())
		{
			$this->setError(Lang::txt('No record found'));
			return false;
		}

		$al = new Vote();

		$vote = $al->automaticVote(array('vote' => $vote));

		if ($vote === 0)
		{
			$this->setError(Lang::txt('No vote provided'));
			return false;
		}

		$user = ($user_id) ? User::getInstance($user_id) : User::getInstance();

		$al->set('object_id', $this->get('id'));
		$al->set('type', 'comment');
		$al->set('ip', Request::ip());
		$al->set('user_id', $user->get('id'));
		$al->set('vote', $vote);

		// Has user voted before?
		$previous = $al->find($al->get('object_id'), $al->get('user_id'), $al->get('ip'), $al->get('type'));
		if ($previous->get('vote'))
		{
			$voted = $al->automaticVote(array('vote' => $previous->get('vote')));

			// If the old vote is not the same as the new vote
			if ($voted != $vote)
			{
				// Remove old vote
				$previous->destroy();

				// Reset the vote count
				switch ($voted)
				{
					case 'like':
						$this->set('helpful', (int) $this->get('helpful') - 1);
					break;

					case 'dislike':
						$this->set('nothelpful', (int) $this->get('nothelpful') - 1);
					break;
				}
			}
			else
			{
				return true;
			}
		}

		if ($this->get('created_by') == $user->get('id'))
		{
			$this->setError(Lang::txt('COM_KB_NOTICE_CANT_VOTE_FOR_OWN'));
			return false;
		}

		switch ($vote)
		{
			case 'like':
				$this->set('helpful', (int) $this->get('helpful') + 1);
			break;

			case 'dislike':
				$this->set('nothelpful', (int) $this->get('nothelpful') + 1);
			break;
		}

		// Store the changes to vote count
		if (!$this->save())
		{
			return false;
		}

		// Store the vote log
		if (!$al->save())
		{
			$this->setError($al->getError());
			return false;
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
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove comments
		foreach ($this->replies()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		foreach ($this->votes()->rows() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->addError($vote->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
