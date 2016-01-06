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

use Hubzero\User\Profile;
use Hubzero\Utility\String;
use Hubzero\Base\ItemList;
use Components\Wishlist\Tables;
use User;
use Lang;
use Date;

require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'attachment.php');
require_once(__DIR__ . DS . 'comment.php');
require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'plan.php');
require_once(__DIR__ . DS . 'vote.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'wish.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'vote.php');

/**
 * Wishlist model class for a wish
 */
class Wish extends Base
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
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\Wishlist\\Tables\\Wish';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_wishlist.wish.about';

	/**
	 * Attachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * Adapter
	 *
	 * @var object
	 */
	private $_adapter = null;

	/**
	 * Plan
	 *
	 * @var object
	 */
	private $_plan = null;

	/**
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	private $_proposer = null;

	/**
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	private $_owner = null;

	/**
	 * Cached data
	 *
	 * @var array
	 */
	private $_cache = array(
		'tag.cloud'        => null,
		'comments.count'   => null,
		'comments.list'    => null,
		'comments.authors' => null,
		'votes.count'      => null,
		'votes.list'       => null,
		'votes.positive'   => null,
		'votes.negative'   => null,
		'ranks.list'       => null
	);

	/**
	 * Constructor
	 * 
	 * @param   mixed  $oid  Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		if ($this->exists())
		{
			if ($this->get('positive') === null)
			{
				$this->set('positive', $this->votes('positive'));
			}
			if ($this->get('negative') === null)
			{
				$this->set('negative', $this->votes('negative'));
			}
		}
	}

	/**
	 * Returns a reference to this model
	 *
	 * @param   mixed   $oid  ID (int) or array or object
	 * @return  object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string  $property  What data to return
	 * @param   mixed   $default   Default value
	 * @return  mixed
	 */
	public function proposer($property=null, $default=null)
	{
		if (!($this->_proposer instanceof Profile))
		{
			$this->_proposer = Profile::getInstance($this->get('proposed_by'));
			if (!$this->_proposer)
			{
				$this->_proposer = new Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_proposer->getPicture($this->get('anonymous'));
			}
			return $this->_proposer->get($property, $default);
		}
		return $this->_proposer;
	}

	/**
	 * Get the owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string  $property  What data to return
	 * @param   mixed   $default   Default value
	 * @return  mixed
	 */
	public function owner($property=null, $default=null)
	{
		if (!($this->_owner instanceof Profile))
		{
			$this->_owner = Profile::getInstance($this->get('assigned'));
			if (!$this->_owner)
			{
				$this->_owner = new Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_owner->getPicture();
			}
			return $this->_owner->get($property, $default);
		}
		return $this->_owner;
	}

	/**
	 * Get the attachment on the wish
	 *
	 * @return  object WishlistModelAttachment
	 */
	public function attachment()
	{
		if (!isset($this->_attachment))
		{
			$this->_attachment = Attachment::getInstance(0, $this->get('id'));
		}
		return $this->_attachment;
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
		switch (strtolower($rtrn))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Determine if wish is open
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		if ($this->get('status') == static::WISH_STATE_OPEN)
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was rejected
	 *
	 * @return  boolean
	 */
	public function isAccepted()
	{
		if ($this->get('status') == static::WISH_STATE_ACCEPTED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was rejected
	 *
	 * @return  boolean
	 */
	public function isRejected()
	{
		if ($this->get('status') == static::WISH_STATE_REJECTED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was withdrawn
	 *
	 * @return  boolean
	 */
	public function isWithdrawn()
	{
		if ($this->get('status') == static::WISH_STATE_WITHDRAWN)
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was deleted
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		if ($this->get('status') == static::WISH_STATE_DELETED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine if wish was granted
	 *
	 * @return  boolean
	 */
	public function isGranted()
	{
		if ($this->get('status') == static::WISH_STATE_GRANTED)
		{
			return true;
		}
		return false;
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
		if ($this->get('status') == static::WISH_STATE_FLAGGED)
		{
			return true;
		}
		return false;
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
					case static::WISH_STATE_ACCEPTED:  $state = Lang::txt($ky . 'ACCEPTED');  break;
					case static::WISH_STATE_WITHDRAWN: $state = Lang::txt($ky . 'WITHDRAWN'); break;
					case static::WISH_STATE_REJECTED:  $state = Lang::txt($ky . 'REJECTED');  break;
					case static::WISH_STATE_DELETED:   $state = Lang::txt($ky . 'DELETED');   break;
					case static::WISH_STATE_GRANTED:   $state = Lang::txt($ky . 'GRANTED');   break;
					case static::WISH_STATE_OPEN:
					default:
						$state = ($this->get('accepted') == 1) ? Lang::txt($ky . 'ACCEPTED') : Lang::txt($ky . 'PENDING');
						/*if (!$this->get('ranked'))
						{
							$state = Lang::txt($ky . 'NEW');
						}*/
					break;
				}
			break;

			case 'alias':
				switch ($this->get('status'))
				{
					case static::WISH_STATE_ACCEPTED:  $state = 'accepted';  break;
					case static::WISH_STATE_WITHDRAWN: $state = 'withdrawn'; break;
					case static::WISH_STATE_REJECTED:  $state = 'rejected';  break;
					case static::WISH_STATE_DELETED:   $state = 'deleted';   break;
					case static::WISH_STATE_GRANTED:   $state = 'granted';   break;
					case static::WISH_STATE_OPEN:
					default:
						$state = ($this->get('accepted') == 1) ? 'accepted' : 'pending';
						/*if (!$this->get('ranked'))
						{
							$state = 'new';
						}*/
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
					case static::WISH_STATE_WITHDRAWN: $state = Lang::txt('COM_WISHLIST_WISH_STATUS_WITHDRAWN_INFO'); break;
					case static::WISH_STATE_REJECTED:  $state = Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED_INFO');  break;
					case static::WISH_STATE_DELETED:   $state = Lang::txt('COM_WISHLIST_WISH_STATUS_DELETED_INFO');   break;
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
				$wishlist = Wishlist::getInstance($this->get('wishlist'));
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
				include_once($path);
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
	 * @param   boolean  $check  Perform data validation check?
	 * @return  boolean  False if error, True on success
	 */
	public function store($check=true)
	{
		if (!$this->get('anonymous'))
		{
			$this->set('anonymous', 0);
		}

		$string = str_replace(
			array('&amp;', '&lt;',  '&gt;'),
			array('&#38;', '&#60;', '&#62;'),
			$this->get('about')
		);
		$this->set('about', \Hubzero\Utility\Sanitize::clean($string));

		if (!parent::store($check))
		{
			return false;
		}

		return true;
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
		if (!$this->exists())
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

		if (!($this->_cache['tag.cloud'] instanceof Tags))
		{
			$this->_cache['tag.cloud'] = new Tags($this->get('id'));
		}

		return $this->_cache['tag.cloud']->render($what, array('admin' => $admin));
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
		if (!($this->_cache['tag.cloud'] instanceof Tags))
		{
			$this->_cache['tag.cloud'] = new Tags($this->get('id'));
		}

		return $this->_cache['tag.cloud']->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  mixed    String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('about.parsed', null);

				if ($content == null)
				{
					$config = array(
						'option'   => 'com_wishlist',
						'scope'    => 'wishlist',
						'pagename' => 'wishlist',
						'pageid'   => $this->get('id'),
						'filepath' => '',
						'domain'   => $this->get('wishlist')
					);

					$this->set('about', stripslashes($this->get('about')));
					$content = $this->get('about');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('about.parsed', $this->get('about'));
					$this->set('about', $content);

					return $this->get('about.parsed') ? $this->content($as, $shorten) : NULL;
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('about'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
				//$content = str_replace(array('&lt;', '&gt;'), array('<', '>'), $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Get the plan for this wish
	 *
	 * @return  object
	 */
	public function plan()
	{
		if (!($this->_plan instanceof Plan))
		{
			$this->_plan = new Plan(0, $this->get('id'));
		}

		return $this->_plan;
	}

	/**
	 * Get the record either immediately before or after the current one
	 * in a listing of records. 
	 *
	 * @param   string   $directtion  [prev|next]
	 * @param   array    $filters     Filters to apply
	 * @param   integer  $user_id     A user ID
	 * @return  boolean
	 */
	public function neighbor($direction, $filters=array(), $user_id=null)
	{
		$direction = strtolower($direction);
		if ($direction != 'prev' && $direction != 'next')
		{
			return null;
		}

		if ($user_id === null)
		{
			$user_id = User::get('id');
		}

		return $this->_tbl->getWishId(
			$direction,
			$this->get('id'),
			$this->get('wishlist'),
			$this->get('admin', 0),
			$user_id,
			$filters
		);
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
		$tbl = new Tables\Wish\Rank($this->_db);
		$tbl->load_vote(User::get('id'), $this->get('id'));

		$tbl->wishid     = $this->get('id');
		$tbl->userid     = User::get('id');
		$tbl->voted      = Date::toSql();
		$tbl->importance = $importance;
		$tbl->effort     = $effort;

		if (!$tbl->check())
		{
			$this->setError($tbl->getError());
			return false;
		}
		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
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
			$this->setError(Lang::txt('Cannot vote for closed wishes.'));
			return false;
		}

		if ($this->get('proposed_by') == User::get('id'))
		{
			$this->setError(Lang::txt('Cannot vote for your own entry.'));
			return false;
		}

		$tbl = new \Components\Wishlist\Tables\Vote($this->_db);

		$vote = strtolower($vote);

		// Check if the user already voted
		if ($voted = $tbl->checkVote($this->get('id'), 'wish', User::get('id')))
		{
			$tbl->loadVote($this->get('id'), 'wish', User::get('id'));
			if ($vote == $tbl->helpful)
			{
				return true;
			}
		}

		$tbl->referenceid = $this->get('id');
		$tbl->category    = 'wish';
		$tbl->voter       = User::get('id');
		$tbl->ip          = Request::ip();
		$tbl->voted       = Date::toSql();
		$tbl->helpful     = $vote;

		if (!$tbl->check())
		{
			$this->setError($tbl->getError());
			return false;
		}
		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get a list or count of votes
	 *
	 * @param   string   $rtrn     Data format to return
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function votes($rtrn='list', $filters=array(), $clear = false)
	{
		if (!isset($filters['id']))
		{
			$filters['id'] = $this->get('id');
		}
		if (!isset($filters['category']))
		{
			$filters['category'] = 'wish';
		}

		switch (strtolower($rtrn))
		{
			case 'positive':
			case 'negative':
			case 'count':
				if (!is_numeric($this->_cache['votes.count']) || $clear)
				{
					$this->_cache['votes.count']    = 0;
					$this->_cache['votes.positive'] = 0;
					$this->_cache['votes.negative'] = 0;

					foreach ($this->votes('list') as $vote)
					{
						if ($vote->helpful == 'yes')
						{
							$this->_cache['votes.positive']++;
						}
						else
						{
							$this->_cache['votes.negative']++;
						}
						$this->_cache['votes.count']++;
					}
				}
				return $this->_cache['votes.' . $rtrn];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['votes.list'] instanceof ItemList) || $clear)
				{
					$tbl = new \Components\Wishlist\Tables\Vote($this->_db);

					$results = $tbl->getResults($filters);
					if (!$results)
					{
						$results = array();
					}
					$this->_cache['votes.list'] = new ItemList($results);
				}
				return $this->_cache['votes.list'];
			break;
		}
	}

	/**
	 * Get a list or count of comments
	 *
	 * @param   string   $rtrn     Data format to return
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function comments($rtrn='list', $filters=array(), $clear = false)
	{
		$tbl = new \Hubzero\Item\Comment($this->_db);

		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('id');
		}
		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = 'wish';
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = 0;
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(static::APP_STATE_PUBLISHED, static::APP_STATE_FLAGGED);
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_cache['comments.count']) || $clear)
				{
					$this->_cache['comments.count'] = 0;

					if (!$this->_cache['comments.list'])
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($c as $com)
					{
						$this->_cache['comments.count']++;
						if ($com->replies()->total())
						{
							foreach ($com->replies() as $rep)
							{
								$this->_cache['comments.count']++;
								if ($rep->replies()->total())
								{
									$this->_cache['comments.count'] += $rep->replies()->total();
								}
							}
						}
					}
				}
				return $this->_cache['comments.count'];
			break;

			case 'authors':
				if (!is_array($this->_cache['comments.authors']) || $clear)
				{
					$this->_cache['comments.authors'] = array();

					if (!$this->_cache['comments.authors'])
					{
						$c = $this->comments('list', $filters);
					}
					foreach ($c as $com)
					{
						$this->_cache['comments.authors'][] = $com->get('added_by');
						if ($com->replies()->total())
						{
							foreach ($com->replies() as $rep)
							{
								$this->_cache['comments.authors'][] = $rep->get('added_by');
								if ($rep->replies()->total())
								{
									foreach ($rep->replies() as $res)
									{
										$this->_cache['comments.authors'][] = $res->get('added_by');
									}
								}
							}
						}
					}
					$this->_cache['comments.authors'] = array_unique($this->_cache['comments.authors']);
				}
				return $this->_cache['comments.authors'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['comments.list'] instanceof ItemList) || $clear)
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Comment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['comments.list'] = new ItemList($results);
				}
				return $this->_cache['comments.list'];
			break;
		}
	}

	/**
	 * Get a list or count of ranks
	 *
	 * @param   string   $rtrn     Data format to return
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function rankings($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Wish\Rank($this->_db);

		if (!isset($filters['wish']))
		{
			$filters['wish'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				return $this->rankings('list')->total();
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['ranks.list'] instanceof ItemList) || $clear)
				{
					if ($results = $tbl->get_votes($this->get('id')))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Vote($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['ranks.list'] = new ItemList($results);
				}
				return $this->_cache['ranks.list'];
			break;
		}
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
			$tbl = new Tables\Wish\Rank($this->_db);
			$tbl->load_vote(User::get('id'), $this->get('id'));

			$this->set('myranking', $tbl);
		}
		// Not ranked?
		if (!$this->get('myranking')->id)
		{
			return NULL;
		}

		return $this->get('myranking')->$rtrn;
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

	/**
	 * Purge data associated with this wish
	 *
	 * @param   string   $what  What to purge
	 * @return  boolean  True on success, false if not
	 */
	public function purge($what)
	{
		$what = strtolower($what);

		switch ($what)
		{
			case 'rank':
			case 'ranks':
			case 'rankings':
				$objR = new Tables\Wish\Rank($this->_db);
				if (!$objR->remove_vote($this->get('id')))
				{
					$this->setError($objR->getError());
					return false;
				}
			break;

			case 'vote':
			case 'votes':
			case 'feedback':
				$v = new \Components\Wishlist\Tables\Vote($this->_db);
				if (!$v->deleteVotes(array('id' => $this->get('id'), 'category' => 'wish')))
				{
					$this->setError($v->getError());
					return false;
				}
			break;

			case 'plan':
				$plan = $this->plan();
				if (!$plan->delete())
				{
					$this->setError($plan->getError());
					return false;
				}
			break;

			case 'comment':
			case 'comments':
				foreach ($this->comments() as $comment)
				{
					if (!$comment->delete())
					{
						$this->setError($comment->getError());
						return false;
					}
				}
			break;

			default:
			break;
		}

		return true;
	}
}

