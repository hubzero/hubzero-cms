<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'attachment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'comment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'plan.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'vote.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'vote.php');

/**
 * Wishlist model class for a wish
 */
class WishlistModelWish extends WishlistModelAbstract
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
	protected $_tbl_name = 'Wish';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_wishlist.wish.about';

	/**
	 * WishlistModelAttachment
	 *
	 * @var object
	 */
	protected $_attachment = null;

	/**
	 * WishlistModelAdapter
	 *
	 * @var object
	 */
	private $_adapter = null;

	/**
	 * WishlistModelPlan
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
	 * @param   mixed $oid Integer (ID), string (alias), object or array
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
	 * @param   mixed  $oid ID (int) or array or object
	 * @return  object WishlistModelWish
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
	 * @param   string $property What data to return
	 * @param   mixed  $default  Default value
	 * @return  mixed
	 */
	public function proposer($property=null, $default=null)
	{
		if (!($this->_proposer instanceof \Hubzero\User\Profile))
		{
			$this->_proposer = \Hubzero\User\Profile::getInstance($this->get('proposed_by'));
			if (!$this->_proposer)
			{
				$this->_proposer = new \Hubzero\User\Profile();
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
	 * Get the attachment on the wish
	 *
	 * @return  object WishlistModelAttachment
	 */
	public function attachment()
	{
		if (!isset($this->_attachment))
		{
			$this->_attachment = WishlistModelAttachment::getInstance(0, $this->get('id'));
		}
		return $this->_attachment;
	}

	/**
	 * Return a formatted timestamp for the proposed datetime
	 *
	 * @param   string $rtrn What data to return
	 * @return  boolean
	 */
	public function proposed($rtrn='')
	{
		return $this->_date('proposed', $rtrn);
	}

	/**
	 * Return a formatted timestamp for the granted datetime
	 *
	 * @param   string $rtrn What data to return
	 * @return  boolean
	 */
	public function granted($rtrn='')
	{
		return $this->_date('granted', $rtrn);
	}

	/**
	 * Return a formatted timestamp for the due datetime
	 *
	 * @param   string $rtrn What data to return
	 * @return  boolean
	 */
	public function due($rtrn='')
	{
		return $this->_date('due', $rtrn);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string $key  Field name to use
	 * @param   string $rtrn What data to return
	 * @return  string
	 */
	public function _date($key, $rtrn='')
	{
		switch (strtolower($rtrn))
		{
			case 'date':
				return JHTML::_('date', $this->get($key), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get($key), JText::_('TIME_FORMAT_HZ1'));
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
	 * @param   string $as Format to return data in [text, alias, note, number]
	 * @return  mixed  string|integer
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
					case static::WISH_STATE_ACCEPTED:  $state = JText::_($ky . 'ACCEPTED');  break;
					case static::WISH_STATE_WITHDRAWN: $state = JText::_($ky . 'WITHDRAWN'); break;
					case static::WISH_STATE_REJECTED:  $state = JText::_($ky . 'REJECTED');  break;
					case static::WISH_STATE_DELETED:   $state = JText::_($ky . 'DELETED');   break;
					case static::WISH_STATE_GRANTED:   $state = JText::_($ky . 'GRANTED');   break;
					case static::WISH_STATE_OPEN:
					default:
						$state = ($this->get('accepted') == 1) ? JText::_($ky . 'ACCEPTED') : JText::_($ky . 'PENDING');
						/*if (!$this->get('ranked'))
						{
							$state = JText::_($ky . 'NEW');
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
						$state  = JText::_('COM_WISHLIST_WISH_STATUS_ACCEPTED_INFO');
						$state .= $this->plan()->exists()
								? '; ' . JText::_('COM_WISHLIST_WISH_PLAN_STARTED')
								: '';
						$state .= $this->due() != '0000-00-00 00:00:00'
								? '; ' . JText::_('COM_WISHLIST_WISH_DUE_SET') . ' ' . $this->due()
								: '';
					break;
					case static::WISH_STATE_WITHDRAWN: $state = JText::_('COM_WISHLIST_WISH_STATUS_WITHDRAWN_INFO'); break;
					case static::WISH_STATE_REJECTED:  $state = JText::_('COM_WISHLIST_WISH_STATUS_REJECTED_INFO');  break;
					case static::WISH_STATE_DELETED:   $state = JText::_('COM_WISHLIST_WISH_STATUS_DELETED_INFO');   break;
					case static::WISH_STATE_GRANTED:
						$state = $this->granted() != '0000-00-00 00:00:00'
								? JText::sprintf('on %s by %s', $this->granted('date'), $this->get('grantedby'))
								: '';
					break;
					case static::WISH_STATE_OPEN:
					default:
						$state = JText::_('COM_WISHLIST_WISH_STATUS_PENDING_INFO');
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
	 * @param   string $type   The type of link to return
	 * @param   mixed  $params String or array of extra params to append
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
				$wishlist = WishlistModelWishlist::getInstance($this->get('wishlist'));
				$this->set('referenceid', $wishlist->get('referenceid'));
				$this->set('category', $wishlist->get('category'));
			}

			$scope = strtolower($this->get('category'));

			$cls = 'WishlistModelAdapter' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(\JText::sprintf('Invalid category of "%s"', $scope));
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
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
	 */
	public function store($check=true)
	{
		if (!$this->get('anonymous'))
		{
			$this->set('anonymous', 0);
		}

		if (!parent::store($check))
		{
			return false;
		}

		return true;
	}

	/**
	 * Get tags on an entry
	 *
	 * @param   string  $what  Data format to return (string, array, cloud)
	 * @param   integer $admin Get admin tags? 0=no, 1=yes
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

		if (!($this->_cache['tag.cloud'] instanceof WishlistModelTags))
		{
			$this->_cache['tag.cloud'] = new WishlistModelTags($this->get('id'));
		}

		return $this->_cache['tag.cloud']->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string  $tags    Tags to apply
	 * @param   integer $user_id ID of tagger
	 * @param   integer $admin   Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		if (!($this->_cache['tag.cloud'] instanceof WishlistModelTags))
		{
			$this->_cache['tag.cloud'] = new WishlistModelTags($this->get('id'));
		}

		return $this->_cache['tag.cloud']->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  mixed String or Integer
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
				$content = str_replace(array('&lt;', '&gt;'), array('<', '>'), $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Get the plan for this wish
	 *
	 * @return  object WishlistModelPlan
	 */
	public function plan()
	{
		if (!($this->_plan instanceof WishlistModelPlan))
		{
			$this->_plan = new WishlistModelPlan(0, $this->get('id'));
		}

		return $this->_plan;
	}

	/**
	 * Get the record either immediately before or after the current one
	 * in a listing of records. 
	 *
	 * @param   string  $directtion [prev|next]
	 * @param   array   $filters    Filters to apply
	 * @param   integer $user_id    A user ID
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
			$user_id = JFactory::getUser()->get('id');
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
	 * @param   integer $effort
	 * @param   integer $importance
	 * @return  boolean
	 */
	public function rank($effort, $importance)
	{
		$juser = JFactory::getUser();

		$tbl = new WishRank($this->_db);
		$tbl->load_vote($juser->get('id'), $this->get('id'));

		$tbl->wishid     = $this->get('id');
		$tbl->userid     = $juser->get('id');
		$tbl->voted      = JFactory::getDate()->toSql();
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
			$this->setError(JText::_('Cannot vote for closed wishes.'));
			return false;
		}

		$juser = JFactory::getUser();

		if ($this->get('proposed_by') == $juser->get('id'))
		{
			$this->setError(JText::_('Cannot vote for your own entry.'));
			return false;
		}

		$tbl = new Vote($this->_db);

		$vote = strtolower($vote);

		// Check if the user already voted
		if ($voted = $tbl->checkVote($this->get('id'), 'wish', $juser->get('id')))
		{
			$tbl->loadVote($this->get('id'), 'wish', $juser->get('id'));
			if ($vote == $tbl->helpful)
			{
				return true;
			}
		}

		$tbl->referenceid = $this->get('id');
		$tbl->category    = 'wish';
		$tbl->voter       = $juser->get('id');
		$tbl->ip          = JRequest::ip();
		$tbl->voted       = JFactory::getDate()->toSql();
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
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
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
				if (!($this->_cache['votes.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new Vote($this->_db);

					$results = $tbl->getResults($filters);
					if (!$results)
					{
						$results = array();
					}
					$this->_cache['votes.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['votes.list'];
			break;
		}
	}

	/**
	 * Get a list or count of comments
	 *
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
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
				if (!($this->_cache['comments.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new WishlistModelComment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['comments.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['comments.list'];
			break;
		}
	}

	/**
	 * Get a list or count of ranks
	 *
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed
	 */
	public function rankings($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new WishRank($this->_db);

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
				if (!($this->_cache['ranks.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->get_votes($this->get('id')))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new WishlistModelVote($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['ranks.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['ranks.list'];
			break;
		}
	}

	/**
	 * Get a ranking
	 *
	 * @param   string $rtrn Data format to return
	 * @return  mixed
	 */
	public function ranking($rtrn='importance')
	{
		if (!$this->get('myranking', null))
		{
			$tbl = new WishRank($this->_db);
			$tbl->load_vote(JFactory::getUser()->get('id'), $this->get('id'));

			$this->set('myranking', $tbl);
		}

		return $this->get('myranking')->$rtrn;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action    Action to check
	 * @param   string  $assetType Type of asset to check
	 * @param   integer $assetId   ID of item to check access on
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='view', $assetType='wish', $assetId=null)
	{
		if (!$this->config()->get('access-check-list-done', false)
		 || !$this->config()->get('access-check-wish-done', false))
		{

			// Has the list access check been performed?
			if (!$this->config()->get('access-check-list-done', false))
			{
				$wishlist = WishlistModelWishlist::getInstance($this->get('wishlist'));
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
					$juser = JFactory::getUser();
					if (!$juser->get('guest'))
					{
						// Is the user the wish proposer?
						if ($juser->get('id') == $this->get('proposed_by'))
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
	 * @param   string  $what What to purge
	 * @return  boolean True on success, false if not
	 */
	public function purge($what)
	{
		$what = strtolower($what);

		switch ($what)
		{
			case 'rank':
			case 'ranks':
			case 'rankings':
				$objR = new WishRank($this->_db);
				if (!$objR->remove_vote($this->get('id')))
				{
					$this->setError($objR->getError());
					return false;
				}
			break;

			case 'vote':
			case 'votes':
			case 'feedback':
				$v = new Vote($this->_db);
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

