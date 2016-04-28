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
use Hubzero\Base\ItemList;
use Components\Wishlist\Tables;
use Lang;
use User;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'wishlist.php');
require_once(__DIR__ . DS . 'wish.php');
require_once(__DIR__ . DS . 'owner.php');

/**
 * Wishlist model class
 */
class Wishlist extends Base
{
	/**
	 * Open state
	 *
	 * @var integer
	 */
	const WISHLIST_STATE_PRIVATE = 0;

	/**
	 * Granted state
	 *
	 * @var integer
	 */
	const WISHLIST_STATE_PUBLIC  = 1;

	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Wishlist\\Tables\\Wishlist';

	/**
	 * Container for interally cached data
	 *
	 * @var array
	 */
	private $_cache = array(
		'wish'         => null,
		'wishes.list'  => null,
		'wishes.count' => null,
		'wishes.first' => null,
		'owners.list0' => null,
		'owners.list1' => null
	);

	/**
	 * Adapter
	 *
	 * @var object
	 */
	private $_adapter = null;

	/**
	 * Constructor
	 *
	 * @param   string   $oid    Integer, array, or object
	 * @param   integer  $scope  Scope type [group, etc.]
	 * @return  void
	 */
	public function __construct($oid=null, $scope=null)
	{
		$this->_db = \App::get('db');

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of JTable.'));
			}

			if (is_numeric($oid))
			{
				if ($scope && is_string($scope))
				{
					$this->_tbl->loadByCategory($oid, $scope);
				}
				// Make sure $oid isn't empty
				// This saves a database call
				else if ($oid)
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_string($oid) && $scope)
			{
				$this->set('category', $scope);
				$this->set('referenceid', $oid);

				$oid = $this->_adapter()->item('id');

				$this->_tbl->loadByCategory($oid, $scope);
				$this->_adapter = null;
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}

		if ($scope && is_string($scope))
		{
			$this->set('category', $scope);
		}
	}

	/**
	 * Returns a reference to this model
	 *
	 * @param   string   $oid    Integer, array, or object
	 * @param   integer  $scope  Scope type [group, etc.]
	 * @return  object
	 */
	static function &getInstance($oid=null, $scope=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = $scope . '_';
		if (is_numeric($oid) || is_string($oid))
		{
			$key .= $oid;
		}
		else if (is_object($oid))
		{
			$key .= $oid->id;
		}
		else if (is_array($oid))
		{
			$key .= $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $scope);
		}

		return $instances[$key];
	}

	/**
	 * Get the underlying item the list is tied to (group, etc.)
	 *
	 * @param   string  $key
	 * @return  string
	 */
	public function item($key=null)
	{
		return $this->_adapter()->item($key);
	}

	/**
	 * Get the title for the wishlist
	 *
	 * @return  string
	 */
	public function title()
	{
		return $this->_adapter()->title();
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
	 * Append an item to the breadcrumb trail.
	 * If no item is provided, it will build the trail up to the list
	 *
	 * @param   string  $title  Breadcrumb title
	 * @param   string  $url    Breadcrumb URL
	 * @return  string
	 */
	public function pathway($title=null, $pathway=null)
	{
		return $this->_adapter()->pathway($title, $pathway);
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
			$scope = strtolower($this->get('category'));

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . DS . 'adapters' . DS . $scope . '.php';
				if (!is_file($path))
				{
					//throw new \InvalidArgumentException(Lang::txt('Invalid category of "%s"', $scope));
					throw new RuntimeException(Lang::txt('Invalid category of "%s"', $scope), 404);
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('referenceid'));
			$this->_adapter->set('wishlist', $this->get('id'));
		}
		return $this->_adapter;
	}

	/**
	 * Create the wishlist
	 *
	 * @return  boolean
	 */
	public function setup()
	{
		if ($this->exists())
		{
			return true;
		}

		if (!$this->_adapter()->exists())
		{
			$this->setError(Lang::txt('Item of category "%s" and ID of "%s" could not be found.', $this->get('category'), $this->get('referenceid')));
			return false;
		}

		$this->set('title', $this->_adapter()->title());

		$this->set('id', $this->_tbl->createlist(
			$this->get('category'),
			$this->get('referenceid'),
			1,
			$this->get('title'),
			$this->_adapter()->item('title')
		));

		if (!$this->get('id'))
		{
			$this->setError(Lang::txt('Failed to create wishlist for category "%s" and ID of "%s".', $this->get('category'), $this->get('referenceid')));
			return false;
		}

		return true;
	}

	/**
	 * Determine if wishlist is public or private
	 *
	 * @return  boolean  True if public, false if not
	 */
	public function isPublic()
	{
		if ($this->get('public') == self::WISHLIST_STATE_PUBLIC)
		{
			return true;
		}
		return false;
	}

	/**
	 * Set and get a specific wish
	 *
	 * @param   integer  $id  Wish ID
	 * @return  object
	 */
	public function wish($id=null)
	{
		if (!($this->_cache['wish'] instanceof Wish)
		 || ($id !== null && (int) $this->_cache['wish']->get('id') != $id))
		{
			$this->_cache['wish'] = null;

			if ($this->_cache['wishes.list'] instanceof ItemList)
			{
				foreach ($this->_cache['wishes.list'] as $key => $wish)
				{
					if ((int) $wish->get('id') == $id || (string) $wish->get('alias') == $id)
					{
						$this->_cache['wish'] = $wish;
						break;
					}
				}
			}

			if (!$this->_cache['wish'])
			{
				$this->_cache['wish'] = Wish::getInstance($id, $this->get('scope'), $this->get('scope_id'));
			}

			if (!$this->_cache['wish']->exists())
			{
				$this->_cache['wish']->set('scope', $this->get('scope'));
				$this->_cache['wish']->set('scope_id', $this->get('scope_id'));
			}
		}

		return $this->_cache['wish'];
	}

	/**
	 * Get a count or list of wishes
	 *
	 * @param   string   $rtrn     What data to return [count, list, first]
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function wishes($rtrn='', $filters=array(), $clear=false)
	{
		if (!isset($filters['wishlist']))
		{
			$filters['wishlist'] = (int) $this->get('id');
		}

		$tbl = new Tables\Wish($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_cache['wishes.count']) || $clear)
				{
					$this->_cache['wishes.count'] = (int) $tbl->get_count($this->get('id'), $filters, $this->get('admin'), User::getInstance());
				}
				return $this->_cache['wishes.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['wishes.list'] instanceof ItemList) || $clear)
				{
					if ($results = $tbl->get_wishes($this->get('id'), $filters, $this->get('admin'), User::getInstance()))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Wish($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['wishes.list'] = new ItemList($results);
				}
				return $this->_cache['wishes.list'];
			break;
		}
	}

	/**
	 * Get a list of owners
	 *
	 * @param   string   $rtrn    What data to return [count, list, first]
	 * @param   integer  $native
	 * @return  array
	 */
	public function owners($rtrn='', $native=0)
	{
		$tbl = new Tables\Owner($this->_db);

		if (!is_array($this->_cache['owners.list' . $native]))
		{
			$category = $this->get('category');
			$this->_tbl->$category = $this->_adapter()->item();
			if ($data = $tbl->get_owners($this->get('id'), $this->config('group', 'hubadmin'), $this->_tbl, $native))
			{
				$results = $data;
			}
			else
			{
				$results = array(
					'individuals' => array(),
					'groups'      => array(),
					'advisory'    => array()
				);
			}
			$this->_cache['owners.list' . $native] = $results;
		}

		if ($rtrn && isset($this->_cache['owners.list' . $native][$rtrn]))
		{
			return $this->_cache['owners.list' . $native][$rtrn];
		}

		return $this->_cache['owners.list' . $native];
	}

	/**
	 * Remove one or more owners
	 *
	 * @param   string  $what  Owner type to remove
	 * @param   mixed   $data  integer|string|array
	 * @return  object
	 */
	public function remove($what, $data)
	{
		$data = $this->_toArray($data);

		$what = strtolower($what);

		switch ($what)
		{
			case 'advisory':
			case 'individuals':
				$tbl = new Tables\Owner($this->_db);
			break;

			case 'groups':
				$tbl = new Tables\OwnerGroup($this->_db);
			break;

			default:
				throw new \InvalidArgumentException(Lang::txt('Owner type not supported.'));
			break;
		}

		foreach ($data as $result)
		{
			switch ($what)
			{
				case 'advisory':
				case 'individuals':
					$user_id = (int) $this->_userId($result);

					if (!$tbl->delete_owner($this->get('id'), $user_id, $this->config('group', 'hubadmin')))
					{
						$this->setError($tbl->getError());
						continue;
					}
				break;

				case 'groups':
					$group_id = (int) $this->_groupId($result);

					if (!$tbl->delete_owner_group($this->get('id'), $group_id, $this->config('group', 'hubadmin')))
					{
						$this->setError($tbl->getError());
						continue;
					}
				break;
			}
		}

		// Reset the owners lists
		$this->_cache['owners.list0'] = null;
		$this->_cache['owners.list1'] = null;

		return $this;
	}

	/**
	 * Add one or more owners
	 *
	 * @param   string  $what  Owner type to add
	 * @param   mixed   $data  integer|string|array
	 * @return  object
	 */
	public function add($what, $data)
	{
		$data = $this->_toArray($data);

		$what = strtolower($what);

		switch ($what)
		{
			case 'advisory':
				if ($this->config('allow_advisory', 0))
				{
					$tbl = new Tables\Owner($this->_db);

					if (!$tbl->save_owners($this->get('id'), $this->config(), $data, 2))
					{
						$this->setError($tbl->getError());
					}
				}
			break;

			case 'individuals':
				$tbl = new Tables\Owner($this->_db);

				if (!$tbl->save_owners($this->get('id'), $this->config(), $data))
				{
					$this->setError($tbl->getError());
				}
			break;

			case 'groups':
				$tbl = new Tables\OwnerGroup($this->_db);

				if (!$tbl->save_owner_groups($this->get('id'), $this->config(), $data))
				{
					$this->setError($tbl->getError());
				}
			break;

			default:
				//throw new \InvalidArgumentException(Lang::txt('Owner type not supported.'));
				throw new RuntimeException("Lang::txt('Owner type not supported.')", 404);
			break;
		}

		// Reset the owners lists
		$this->_cache['owners.list0'] = null;
		$this->_cache['owners.list1'] = null;

		return $this;
	}

	/**
	 * Turn a comma or space deliniated string into an array
	 *
	 * @param   string  $string
	 * @return  array
	 */
	public function _toArray($string='')
	{
		if (is_array($string))
		{
			return $string;
		}

		if (!strstr($data, ' ') && !strstr($data, ','))
		{
			return array($string);
		}

		$string = str_replace(' ', ',', $string);
		$arr    = explode(',', $string);
		$arr    = array_map('trim', $arr);
		foreach ($arr as $key => $value)
		{
			if ($value == '')
			{
				unset($arr[$key]);
			}
		}
		$arr    = array_unique($arr);

		return $arr;
	}

	/**
	 * Return an ID for a user
	 *
	 * @param  mixed   $user  User ID or username
	 * @return integer
	 */
	private function _userId($user)
	{
		if (is_numeric($user))
		{
			return $user;
		}

		$this->_db->setQuery("SELECT `id` FROM `#__users` WHERE `username`=" . $this->_db->quote($user));

		if (($result = $this->_db->loadResult()))
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Return an ID for a group
	 *
	 * @param   mixed   $group  Group ID or cn
	 * @return  integer
	 */
	private function _groupId($group)
	{
		if (is_numeric($group))
		{
			return $group;
		}

		$this->_db->setQuery("SELECT `gidNumber` FROM `#__xgroups` WHERE `cn`=" . $this->_db->quote($group));

		if (($result = $this->_db->loadResult()))
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action     Action to check
	 * @param   string   $assetType  Type of asset to check
	 * @param   integer  $assetId    ID of item to check access on
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view', $assetType='list', $assetId=null)
	{
		if (!$this->config()->get('access-check-list-done', false))
		{
			$this->set('admin', 0);
			$this->config()->set('access-view-' . $assetType, true);

			if (!User::isGuest())
			{
				if ($assetType == 'wish')
				{
					$this->config()->set('access-create-' . $assetType, true);
					$this->config()->set('access-edit-own-' . $assetType, true);
				}

				$asset  = 'com_wishlist';
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config()->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
				$this->config()->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
				if ($this->config()->get('access-manage-' . $assetType))
				{
					$this->set('admin', 1);
				}
				// Permissions
				$this->config()->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
				$this->config()->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
				$this->config()->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));

				if ($this->exists())
				{
					// Get list administrators
					$managers = $this->owners('individuals');
					$advisory = $this->owners('advisory');

					if (in_array(User::get('id'), $managers))
					{
						$this->config()->set('access-manage-' . $assetType, true);
						$this->config()->set('access-admin-' . $assetType, true);
						$this->config()->set('access-create-' . $assetType, true);
						$this->config()->set('access-delete-' . $assetType, true);
						$this->config()->set('access-edit-state-' . $assetType, true);

						$this->set('admin', 2);  // individual group manager
					}
					if (in_array(User::get('id'), $advisory))
					{
						$this->config()->set('access-edit-' . $assetType, true);
						$this->config()->set('access-edit-state-' . $assetType, true);
						$this->config()->set('access-manage-' . $assetType, true);

						$this->set('admin', 3);  // advisory committee member
					}
				}
			}

			$this->config()->set('access-check-list-done', true);
		}

		return $this->config()->get('access-' . $action . '-' . $assetType);
	}

	/**
	 * Rank the wishes in this list
	 *
	 * @return  boolean
	 */
	public function rank()
	{
		// do we give more weight to votes coming from advisory committee?
		$votesplit = $this->config('votesplit', 0);

		if ($this->wishes()->total() > 0)
		{
			$managers = $this->owners('individuals');
			$advisory = $this->owners('advisory');

			$voters = array_merge($managers, $advisory);
			if (!count($voters))
			{
				return false;
			}

			foreach ($this->wishes() as $item)
			{
				$weight_e = 4;
				$weight_i = 5;
				$weight_f = 0.5;
				$f_threshold = 5;
				$co     = 0.5;
				$co_adv = 0.8;
				$co_reg = 0.2;

				//$votes = $item->votes(); //$objR->get_votes($item->id);
				$ranking = 0;

				// first consider votes by list owners
				if ($item->rankings()->total() > 0)
				{
					$imp     = 0;
					$eff     = 0;
					$num     = 0;
					$skipped = 0; // how many times effort selection was skipped
					$divisor = 0;

					foreach ($item->rankings() as $vote)
					{
						if (in_array($vote->get('userid'), $voters))
						{
							// vote must come from list owner!
							$num++;
							if ($votesplit && in_array($vote->get('userid'), $advisory))
							{
								$imp += $vote->importance * $co_adv;
								$divisor += $co_adv;
							}
							else if ($votesplit)
							{
								$imp += $vote->get('importance') * $co_reg;
								$divisor += $co_reg;
							}
							else
							{
								$imp += $vote->get('importance');
							}
							if ($vote->get('effort') != 6)
							{ // ignore "don't know" selection
								$eff += $vote->get('effort');
							}
							else
							{
								$skipped++;
							}
						}
						else
						{
							// need to clean up this vote! looks like owners list changed since last voting
							//$remove = $objR->remove_vote($item->id, $vote->userid);
							$vote->delete();
						}
					}

					// average values
					$imp = ($votesplit && $divisor) ? $imp/$divisor: $imp/$num;
					$eff = ($num - $skipped) != 0 ? $eff/($num - $skipped) : 0;
					$weight_i = ($num - $skipped) != 0 ? $weight_i : 7;

					// we need to factor in how many people voted
					$certainty = $co + $num/count($voters);

					$ranking += ($imp * $weight_i) * $certainty;
					$ranking += ($eff * $weight_e) * $certainty;
				}

				// determine weight of community feedback
				$f = $item->get('positive', 0) + $item->get('negative', 0);
				$q = $f/$f_threshold;
				//$weight_f = ($weight_f >= 1) ? ($weight_f + $q * $weight_f) : $weight_f;
				$weight_f = ($q >= 1) ? ($weight_f + $q * $weight_f) : $weight_f;

				$ranking += ($item->get('positive', 0) * $weight_f);
				$ranking -= ($item->get('negative', 0) * $weight_f);

				// Do not allow negative ranking
				$ranking = ($ranking < 0) ? 0 : $ranking;

				// save calculated priority
				$item->set('ranking', $ranking);

				// store new content
				if (!$item->store(false))
				{
					$this->setError($item->getError());
					return false;
				}
			}
		}

		return true;
	}
}

