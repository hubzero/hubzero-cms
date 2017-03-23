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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use User;
use Lang;
use Date;

require_once __DIR__ . DS . 'attachment.php';
require_once __DIR__ . DS . 'comment.php';
require_once __DIR__ . DS . 'tags.php';
require_once __DIR__ . DS . 'plan.php';
require_once __DIR__ . DS . 'vote.php';
require_once __DIR__ . DS . 'rank.php';

/**
 * Model class for a wishlist item
 */
class Wish extends Relational
{
	/**
	 * Open state
	 *
	 * @var integer
	 */
	const WISH_STATE_OPEN    = 0;

	/**
	 * Granted state
	 *
	 * @var integer
	 */
	const WISH_STATE_GRANTED = 1;

	/**
	 * Deleted state
	 *
	 * @var integer
	 */
	const WISH_STATE_DELETED = 2;

	/**
	 * Rejected state
	 *
	 * @var integer
	 */
	const WISH_STATE_REJECTED = 3;

	/**
	 * Withdrawn state
	 *
	 * @var integer
	 */
	const WISH_STATE_WITHDRAWN = 4;

	/**
	 * Withdrawn state
	 *
	 * @var integer
	 */
	const WISH_STATE_ACCEPTED = 6;

	/**
	 * Flagged state
	 *
	 * @var integer
	 */
	const WISH_STATE_FLAGGED = 7;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'wishlist';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__wishlist_item';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'subject'    => 'notempty',
		'wishlist'  => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'proposed',
		'proposed_by'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'about'
	);

	/**
	 * Component configuration
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * Generates automatic proposed field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProposed($data)
	{
		if (!isset($data['proposed']) || !$data['proposed'])
		{
			$data['proposed'] = Date::of('now')->toSql();
		}
		return $data['proposed'];
	}

	/**
	 * Generates automatic proposed field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProposedBy($data)
	{
		if (!isset($data['proposed_by']) || !$data['proposed_by'])
		{
			$data['proposed_by'] = User::get('id');
		}
		return $data['proposed_by'];
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function proposer()
	{
		return $this->belongsToOne('Hubzero\User\User', 'proposed_by');
	}

	/**
	 * Get the owner of this entry
	 *
	 * @return  object
	 */
	public function assignee()
	{
		return $this->belongsToOne('Hubzero\User\User', 'assigned');
	}

	/**
	 * Get the owning wishlist of this entry
	 *
	 * @return  object
	 */
	public function wishlist()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Wishlist', 'wishlist');
	}

	/**
	 * Get the attachments on the wish
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Attachment', 'wish');
	}

	/**
	 * Get the plan for this wish
	 *
	 * @return  object
	 */
	public function plan()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Plan', 'wishid');
	}

	/**
	 * Return a formatted timestamp for the proposed datetime
	 *
	 * @param   string   $rtrn  What data to return
	 * @return  boolean
	 */
	public function proposed($rtrn='')
	{
		return $this->_date('proposed', $rtrn);
	}

	/**
	 * Return a formatted timestamp for the granted datetime
	 *
	 * @param   string   $rtrn  What data to return
	 * @return  boolean
	 */
	public function granted($rtrn='')
	{
		return $this->_date('granted', $rtrn);
	}

	/**
	 * Return a formatted timestamp for the due datetime
	 *
	 * @param   string   $rtrn  What data to return
	 * @return  boolean
	 */
	public function due($rtrn='')
	{
		return $this->_date('due', $rtrn);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $key   Field name to use
	 * @param   string  $rtrn  What data to return
	 * @return  string
	 */
	public function _date($key, $rtrn='')
	{
		$rtrn = strtolower($rtrn);

		if ($rtrn == 'date')
		{
			return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($rtrn == 'time')
		{
			return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get($key);
	}

	/**
	 * Determine if wish is open
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		return ($this->get('status') == static::WISH_STATE_OPEN);
	}

	/**
	 * Determine if wish was rejected
	 *
	 * @return  boolean
	 */
	public function isAccepted()
	{
		return ($this->get('status') == static::WISH_STATE_ACCEPTED);
	}

	/**
	 * Determine if wish was rejected
	 *
	 * @return  boolean
	 */
	public function isRejected()
	{
		return ($this->get('status') == static::WISH_STATE_REJECTED);
	}

	/**
	 * Determine if wish was withdrawn
	 *
	 * @return  boolean
	 */
	public function isWithdrawn()
	{
		return ($this->get('status') == static::WISH_STATE_WITHDRAWN);
	}

	/**
	 * Determine if wish was deleted
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('status') == static::WISH_STATE_DELETED);
	}

	/**
	 * Determine if wish was granted
	 *
	 * @return  boolean
	 */
	public function isGranted()
	{
		return ($this->get('status') == static::WISH_STATE_GRANTED);
	}

	/**
	 * Determine if wish is private
	 *
	 * @return  boolean
	 */
	public function isPrivate()
	{
		if ($this->get('private'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was reported for abuse
	 *
	 * @return  boolean
	 */
	public function isReported()
	{
		return ($this->get('status') == static::WISH_STATE_FLAGGED);
	}

	/**
	 * Return wish status in various formats
	 *
	 * @param   string  $as  Format to return data in [text, alias, note, number]
	 * @return  mixed   string|integer
	 */
	public function status($as='')
	{
		$state  = (!$this->get('ranked') && $this->get('status') != 1) ? 'new' : '';

		switch ($as)
		{
			case 'text':
				$ky = 'COM_WISHLIST_WISH_STATUS_';
				switch ($this->get('status'))
				{
					case static::WISH_STATE_ACCEPTED:
						$state = Lang::txt($ky . 'ACCEPTED');
						break;
					case static::WISH_STATE_WITHDRAWN:
						$state = Lang::txt($ky . 'WITHDRAWN');
						break;
					case static::WISH_STATE_REJECTED:
						$state = Lang::txt($ky . 'REJECTED');
						break;
					case static::WISH_STATE_DELETED:
						$state = Lang::txt($ky . 'DELETED');
						break;
					case static::WISH_STATE_GRANTED:
						$state = Lang::txt($ky . 'GRANTED');
						break;
					case static::WISH_STATE_OPEN:
					default:
						$state = ($this->get('accepted') == 1) ? Lang::txt($ky . 'ACCEPTED') : Lang::txt($ky . 'PENDING');
						break;
				}
			break;

			case 'alias':
				switch ($this->get('status'))
				{
					case static::WISH_STATE_ACCEPTED:
						$state = 'accepted';
						break;
					case static::WISH_STATE_WITHDRAWN:
						$state = 'withdrawn';
						break;
					case static::WISH_STATE_REJECTED:
						$state = 'rejected';
						break;
					case static::WISH_STATE_DELETED:
						$state = 'deleted';
						break;
					case static::WISH_STATE_GRANTED:
						$state = 'granted';
						break;
					case static::WISH_STATE_OPEN:
					default:
						$state = ($this->get('accepted') == 1) ? 'accepted' : 'pending';
						break;
				}
			break;

			case 'note':
				switch ($this->get('status'))
				{
					case static::WISH_STATE_ACCEPTED:
						$state  = Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED_INFO');
						$state .= $this->plan()->exists()
								? '; ' . Lang::txt('COM_WISHLIST_WISH_PLAN_STARTED')
								: '';
						$state .= $this->due() != '0000-00-00 00:00:00'
								? '; ' . Lang::txt('COM_WISHLIST_WISH_DUE_SET') . ' ' . $this->due()
								: '';
						break;
					case static::WISH_STATE_WITHDRAWN:
						$state = Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN_INFO');
						break;
					case static::WISH_STATE_REJECTED:
						$state = Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED_INFO');
						break;
					case static::WISH_STATE_DELETED:
						$state = Lang::txt('COM_WISHLIST_WISH_STATUS_DELETED_INFO');
						break;
					case static::WISH_STATE_GRANTED:
						$user = User::getInstance($this->get('granted_by'));
						$state = $this->granted() != '0000-00-00 00:00:00'
								? Lang::txt('on %s by %s', $this->granted('date'), $user->get('name'))
								: '';
					break;
					case static::WISH_STATE_OPEN:
					default:
						$state = Lang::txt('COM_WISHLIST_WISH_STATUS_PENDING_INFO');
						break;
				}
			break;

			case 'number':
			default:
				$state = $this->get('status');
			break;
		}

		return $state;
	}

	/**
	 * Get a ranking
	 *
	 * @param   string  $rtrn  Data format to return
	 * @return  mixed
	 */
	public function ranking($rtrn='importance')
	{
		if (!$this->get('myranking', null))
		{
			$model = Rank::oneByUserAndWish(User::get('id'), $this->get('id'));

			$this->set('myranking', $model);
		}

		return $this->get('myranking')->get($rtrn);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  String or array of extra params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->_adapter()->link($type, $params);
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			if (!$this->get('referenceid') || !$this->get('category'))
			{
				$wishlist = Wishlist::oneOrNew($this->get('wishlist'));
				$this->set('referenceid', $wishlist->get('referenceid'));
				$this->set('category', $wishlist->get('category'));
			}

			$scope = strtolower($this->get('category'));

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					//throw new \InvalidArgumentException(Lang::txt('Invalid category of "%s"', $scope));
					throw new RuntimeException(Lang::txt('Invalid category of "%s"', $scope), 404);
				}
				include_once $path;
			}

			$this->_adapter = new $cls($this->get('referenceid'));
			$this->_adapter->set('wishid', $this->get('id'));
			$this->_adapter->set('wishlist', $this->get('wishlist'));
		}
		return $this->_adapter;
	}

	/**
	 * Store changes to this offering
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		if (is_null($this->get('anonymous')))
		{
			$this->set('anonymous', 0);
		}

		$string = str_replace(
			array('&amp;', '&lt;',  '&gt;'),
			array('&#38;', '&#60;', '&#62;'),
			$this->get('about')
		);
		$this->set('about', \Hubzero\Utility\Sanitize::clean($string));

		return parent::save();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->comments()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		// Remove rankings
		foreach ($this->rankings()->rows() as $ranking)
		{
			if (!$ranking->destroy())
			{
				$this->addError($ranking->getError());
				return false;
			}
		}

		// Remove votes
		foreach ($this->votes()->rows() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->addError($vote->getError());
				return false;
			}
		}

		// Remove attachments
		foreach ($this->attachments()->rows() as $attachment)
		{
			if (!$attachment->destroy())
			{
				$this->addError($attachment->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Get tags on an entry
	 *
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($what))
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

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$field = 'about';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$params = array(
				'option'   => 'com_wishlist',
				'scope'    => 'wishlist',
				'pagename' => 'wishlist',
				'pageid'   => $this->get('id'),
				'filepath' => '',
				'domain'   => $this->get('wishlist')
			);

			$this->$property = Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}

	/**
	 * Rank an entry
	 *
	 * @param   integer  $effort
	 * @param   integer  $importance
	 * @return  boolean
	 */
	public function rank($effort, $importance)
	{
		$rank = Rank::oneByUserAndWish(User::get('id'), $this->get('id'));

		$rank->set(array(
			'wishid'     => $this->get('id'),
			'userid'     => User::get('id'),
			'voted'      => Date::toSql(),
			'importance' => $importance,
			'effort'     => $effort
		));

		if (!$rank->save())
		{
			$this->addError($rank->getError());
			return false;
		}

		return true;
	}

	/**
	 * Vote on the entry
	 *
	 * @param   mixed   $vote
	 * @return  boolean
	 */
	public function vote($vote)
	{
		if (!$this->isOpen())
		{
			$this->addError(Lang::txt('Cannot vote for closed wishes.'));
			return false;
		}

		if ($this->get('proposed_by') == User::get('id'))
		{
			$this->addError(Lang::txt('Cannot vote for your own entry.'));
			return false;
		}

		$vote = strtolower($vote);

		// Check if the user already voted
		$voted = Vote::oneByUserAndWish(User::get('id'), $this->get('id'));

		if ($voted->get('id'))
		{
			if ($vote == $voted->get('helpful'))
			{
				return true;
			}
		}

		// Create a new entry
		$voted->set(array(
			'referenceid' => $this->get('id'),
			'category'    => 'wish',
			'voter'       => User::get('id'),
			'ip'          => Request::ip(),
			'voted'       => Date::toSql(),
			'helpful'     => $vote
		));

		if (!$voted->save())
		{
			$this->addError($voted->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get a list or count of votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Vote', 'referenceid')->whereEquals('category', 'wish');
	}

	/**
	 * Get a list or count of comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneShiftsToMany(__NAMESPACE__ . '\\Comment', 'item_id', 'item_type')->whereEquals('parent', 0);
	}

	/**
	 * Get a list or count of ranks
	 *
	 * @param   string   $rtrn     Data format to return
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function rankings()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Rank', 'wishid');
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Value to return if key isn't found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = \Component::params('com_wishlist');
		}
		if ($key)
		{
			return $this->config->get($key, $default);
		}
		return $this->config;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action     Action to check
	 * @param   string   $assetType  Type of asset to check
	 * @param   integer  $assetId    ID of item to check access on
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view', $assetType='wish', $assetId=null)
	{
		if (!$this->config()->get('access-check-list-done', false)
		 || !$this->config()->get('access-check-wish-done', false))
		{

			// Has the list access check been performed?
			if (!$this->config()->get('access-check-list-done', false))
			{
				$wishlist = Wishlist::getInstance($this->get('wishlist'));
				$wishlist->access($action, 'list');
			}

			// Has the wish access check been performed?
			if (!$this->config()->get('access-check-wish-done', false))
			{
				// Set wish NOT viewable by default
				$this->config()->set('access-view-wish', false);

				// Can they see the list?
				if ($this->config()->get('access-view-list'))
				{
					$this->config()->set('access-create-wish', true);

					// If the wish is not private or (wish is private and user can manage the list)
					// set the wish to viewable
					if (!$this->isPrivate() || ($this->isPrivate() && $this->config()->get('access-manage-list')))
					{
						$this->config()->set('access-view-wish', true);
					}

					if ($this->config()->get('access-manage-list'))
					{
						$this->config()->set('access-view-wish', true);
						$this->config()->set('access-admin-wish', true);
						$this->config()->set('access-manage-wish', true);
						$this->config()->set('access-delete-wish', true);
						$this->config()->set('access-create-wish', true);
						$this->config()->set('access-edit-wish', true);
						$this->config()->set('access-edit-state-wish', true);
						$this->config()->set('access-edit-own-wish', true);
					}

					// Is the user logged in?
					if (!User::isGuest())
					{
						// Is the user the wish proposer?
						if (User::get('id') == $this->get('proposed_by'))
						{
							// Grant access to view and edit
							$this->config()->set('access-view-wish', true);
							$this->config()->set('access-edit-wish', true);
							$this->config()->set('access-edit-own-wish', true);
						}
					}
				}

				// Access check done
				$this->config()->set('access-check-wish-done', true);
			}
		}

		return $this->config()->get('access-' . $action . '-' . $assetType);
	}
}
