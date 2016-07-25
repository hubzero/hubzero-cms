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

namespace Components\Resources\Reviews\Models;

use Hubzero\Database\Relational;
use Hubzero\Item\Vote;
use Request;
use Lang;
use Date;
//use User;

require_once __DIR__ . DS . 'comment.php';

/**
 * Resource review
 */
class Review extends Relational
{
	/**
	 * Flagged state
	 *
	 * @var  integer
	 */
	const STATE_FLAGGED = 3;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__resource_ratings';

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
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'comment'     => 'notempty',
		'resource_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'user_id'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'comment'
	);

	/**
	 * Base URL
	 *
	 * @var  string
	 */
	protected $base = null;

	/**
	 * Generates automatic user_id field value
	 *
	 * @return  int
	 * @since   2.0.0
	 **/
	public function automaticUserId()
	{
		return (int)User::get('id', 0);
	}

	/**
	 * Is the question open?
	 *
	 * @return  boolean
	 */
	public function isReported()
	{
		return ($this->get('state') == self::STATE_FLAGGED);
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Defines a belongs to one relationship between response and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
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
		return $this->oneShiftsToMany('Hubzero\Item\Vote', 'item_id', 'item_type');
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
			$vote->set('item_type', 'review');
			$vote->set('item_id', $this->get('id'));
			$vote->set('created_by', $user_id);
			$vote->set('ip', $ip);

			return $vote;
		}

		$user = $user_id ? User::getInstance($user_id) : User::getInstance();
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
			$vote->set('item_type', 'review');
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
			$this->addError(Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_NO_VOTE_FOUND'));
			return false;
		}

		if (!$vote)
		{
			$this->addError(Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_NO_VOTE_PROVIDED'));
			return false;
		}

		$al = $this->ballot($user_id, $ip);
		$al->set('item_type', 'review');
		$al->set('item_id', $this->get('id'));
		$al->set('created_by', $user_id);
		$al->set('ip', $ip);

		$vote = $al->automaticVote(['vote' => $vote]);

		if ($this->get('created_by') == $user_id)
		{
			$this->addError(Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_RECOMMEND_OWN'));
			return false;
		}

		if ($vote != $al->get('vote', 0))
		{
			/*if ($vote > 0)
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
			}*/

			$al->set('vote', $vote);

			if (!$al->save())
			{
				$this->addError($al->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Transform comment
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		return $this->comment;
	}

	/**
	 * Load a record by resource_id and user_id
	 *
	 * @param   integer  $resource_id
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByUser($resource_id, $user_id)
	{
		return self::all()
			->whereEquals('resource_id', $resource_id)
			->whereEquals('user_id', $user_id)
			->row();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->replies()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		// Remove vote logs
		foreach ($this->votes()->rows() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->addError($vote->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
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
			$this->base = 'index.php?option=com_resources&id=' . $this->get('item_id') . '&active=reviews';
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
				$link .= '&action=reply&category=review&refid=' . $this->get('id');
			break;

			case 'report':
				$link = 'index.php?option=com_support&task=reportabuse&category=review&id=' . $this->get('id') . '&parent=' . $this->get('resource_id');
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}
}
