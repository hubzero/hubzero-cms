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
use Hubzero\Utility\String;
use Request;
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'vote.php');

/**
 * Comment model
 */
class Comment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'item';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__item_comments';

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
		'content'   => 'notempty',
		'item_id'   => 'positive|nonzero',
		'item_type' => 'notempty'
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
	 * URL for this entry
	 *
	 * @var  string
	 */
	private $base = null;

	/**
	 * Return a formatted timestamp
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
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('item_id');
		}

		$entries = self::all()
			->whereEquals('parent', (int) $this->get('id'))
			->whereEquals('item_type', 'response')
			->whereEquals('item_id', (int) $filters['item_id']);

		if (isset($filters['state']))
		{
			$entries->whereIn('state', (array) $filters['state']);
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
			$vote->set('item_type', 'comment');
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
			$vote->set('item_type', 'comment');
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
		$al->set('item_type', 'comment');
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
				$this->set('positive', (int) $this->get('positive') + 1);
				if ($al->get('id'))
				{
					$this->set('negative', (int) $this->get('negative') - 1);
				}
			}
			else
			{
				if ($al->get('id'))
				{
					$this->set('positive', (int) $this->get('positive') - 1);
				}
				$this->set('negative', (int) $this->get('negative') + 1);
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
			if (!$this->get('question_id'))
			{
				$answer = Response::oneOrNew($this->get('item_id'));

				$this->set('question_id', $answer->get('question_id'));
			}
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

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=itemcomment&id=' . $this->get('id') . '&parent=' . $this->get('question_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
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
		foreach ($this->replies() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		foreach ($this->votes() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}

