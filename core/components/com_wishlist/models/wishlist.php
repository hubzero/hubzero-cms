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
use Hubzero\User\Group;
use Lang;
use User;

require_once __DIR__ . DS . 'wish.php';
require_once __DIR__ . DS . 'owner.php';
require_once __DIR__ . DS . 'ownergroup.php';

/**
 * Wishlist model class
 */
class Wishlist extends Relational
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
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__wishlist';

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
		'title'       => 'notempty',
		'category'    => 'notempty',
		'referenceid' => 'positive|nonzero'
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
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'description'
	);

	/**
	 * Component configuration
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * Load a record by wishlist and groupid
	 *
	 * @param   integer  $referenceid
	 * @param   string   $category
	 * @return  object
	 */
	public static function oneByReference($referenceid, $category)
	{
		return self::all()
			->whereEquals('referenceid', $referenceid)
			->whereEquals('category', $category)
			->row();
	}

	/**
	 * Create the wishlist
	 *
	 * @return  boolean
	 */
	public function stage()
	{
		if ($this->get('id'))
		{
			return true;
		}

		if (!$this->_adapter()->exists())
		{
			$this->addError(Lang::txt('Item of category "%s" and ID of "%s" could not be found.', $this->get('category'), $this->get('referenceid')));
			return false;
		}

		$this->set('title', $this->get('title', $this->get('category') . ' #' . $this->get('referenceid')));
		$this->set('description', $this->_adapter()->title());
		$this->set('public', 1);

		return $this->save();
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
					throw new \RuntimeException(Lang::txt('Invalid category of "%s"', $scope), 404);
				}
				include_once $path;
			}

			$this->_adapter = new $cls($this->get('referenceid'));
			$this->_adapter->set('wishlist', $this->get('id'));
		}
		return $this->_adapter;
	}

	/**
	 * Determine if wishlist is public or private
	 *
	 * @return  boolean  True if public, false if not
	 */
	public function isPublic()
	{
		return ($this->get('public') == self::WISHLIST_STATE_PUBLIC);
	}

	/**
	 * Get a count or list of wishes
	 *
	 * @return  object
	 */
	public function wishes()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Wish', 'wishlist');
	}

	/**
	 * Get a list of owners
	 *
	 * @return  object
	 */
	public function owners()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Owner', 'wishlist');
	}

	/**
	 * Get a list of owners
	 *
	 * @return  object
	 */
	public function ownergroups()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Ownergroup', 'wishlist');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove wishes
		foreach ($this->wishes()->rows() as $wish)
		{
			if (!$wish->destroy())
			{
				$this->addError($wish->getError());
				return false;
			}
		}

		// Remove owners
		foreach ($this->owners()->rows() as $owner)
		{
			if (!$owner->destroy())
			{
				$this->addError($owner->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Remove one or more owners
	 *
	 * @param   string  $what  Owner type to remove
	 * @param   mixed   $data  integer|string|array
	 * @return  object
	 */
	public function removeOwner($what, $data)
	{
		$data = $this->_toArray($data);

		$what = strtolower($what);

		switch ($what)
		{
			case 'advisory':
			case 'individuals':
				$tbl = new Owner($this->_db);
			break;

			case 'groups':
				$tbl = new OwnerGroup($this->_db);
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

		return $this;
	}

	/**
	 * Add one or more owners
	 *
	 * @param   string  $what  Owner type to add
	 * @param   mixed   $data  integer|string|array
	 * @return  object
	 */
	public function addOwner($what, $data)
	{
		$data = $this->_toArray($data);

		$what = strtolower($what);

		switch ($what)
		{
			case 'advisory':
				if ($this->config('allow_advisory', 0))
				{
					foreach ($data as $datum)
					{
						$user = User::getInstance($datum);
						if (!$user->get('id'))
						{
							continue;
						}

						$record = Owner::oneByWishlistAndUser($this->get('id'), $datum);

						if ($record->isNew())
						{
							$record->set('wishlist', $this->get('id'));
							$record->set('userid', $datum);
							$record->set('type', 2);

							if (!$record->save())
							{
								$this->addError($record->getError());
							}
						}
					}
				}
			break;

			case 'individuals':
				foreach ($data as $datum)
				{
					$user = User::getInstance($datum);
					if (!$user->get('id'))
					{
						continue;
					}

					$record = Owner::oneByWishlistAndUser($this->get('id'), $datum);

					if ($record->isNew())
					{
						$record->set('wishlist', $this->get('id'));
						$record->set('userid', $datum);
						$record->set('type', 0);

						if (!$record->save())
						{
							$this->addError($record->getError());
						}
					}
				}
			break;

			case 'groups':
				foreach ($data as $datum)
				{
					$group = Group::getInstance($datum);
					if (!$group)
					{
						continue;
					}

					$record = Ownergroup::oneByWishlistAndGroup($this->get('id'), $datum);

					if ($record->isNew())
					{
						$record->set('wishlist', $this->get('id'));
						$record->set('groupid', $datum);

						if (!$record->save())
						{
							$this->addError($record->getError());
						}
					}
				}
			break;

			default:
				throw new \InvalidArgumentException(Lang::txt('Owner type "%s" not supported.', $what));
			break;
		}

		return $this;
	}

	/**
	 * Get a list of owners
	 *
	 * @param   object   $admingroup  Admin Group
	 * @param   integer  $native      Get groups assigned to this wishlist?
	 * @param   integer  $wishid      Wish ID
	 * @return  array
	 */
	public function getOwners($admingroup=null, $native=0, $wishid=0)
	{
		if (!$admingroup)
		{
			$admingroup = $this->config()->get('group');
		}

		$owners = array();

		// If private user list, add the user
		if ($this->get('category') == 'user')
		{
			$owners[] = $this->get('referenceid');
		}

		$owners += $this->_adapter()->owners();

		// Get groups
		$groups = $this->getOwnergroups($admingroup, $native);

		foreach ($groups as $g)
		{
			$group = Group::getInstance($g);

			if ($group && $group->get('gidNumber'))
			{
				$members  = $group->get('members');
				$managers = $group->get('managers');
				$members  = array_merge($members, $managers);

				foreach ($members as $member)
				{
					$owners[] = $member;
				}
			}
		}

		// Get individuals
		if (!$native)
		{
			foreach ($this->owners()->where('type', '!=', 2)->rows() as $result)
			{
				$owners[] = $result->userid;
			}
		}

		$owners = array_unique($owners);
		sort($owners);

		// Are we also including advisory committee?
		$wconfig = Component::params('com_wishlist');

		$advisory = array();

		if ($wconfig->get('allow_advisory'))
		{
			foreach ($this->owners()->whereEquals('type', 2)->rows() as $result)
			{
				$advisory[] = $result->userid;
			}
		}

		// Find out those who voted - for distribution of points
		if ($wishid)
		{
			$activeowners = array();

			$result = Rank::all()
				->whereEquals('wishid', $wishid)
				->whereIn('userid', $owners)
				->rows();

			if ($result->count() > 0)
			{
				foreach ($result as $r)
				{
					$activeowners[] = $r->userid;
				}

				$owners = $activeowners;
			}
		}

		$collect = array();
		$collect['individuals'] = $owners;
		$collect['groups']      = $groups;
		$collect['advisory']    = $advisory;

		return $collect;
	}

	/**
	 * Get the groups of a wishlist owner
	 *
	 * @param   string   $controlgroup  Control group name
	 * @param   integer  $native        Get groups assigned to this wishlist?
	 * @return  array
	 */
	public function getOwnergroups($controlgroup, $native=0)
	{
		$groups = array();

		// If private user list, add the user
		if ($this->get('category') == 'group')
		{
			$groups[] = $this->get('referenceid');
		}

		$groups += $this->_adapter()->groups();

		// if primary list, add all site admins
		if ($controlgroup && $this->get('category') == 'general')
		{
			$instance = Group::getInstance($controlgroup);

			if (is_object($instance))
			{
				$groups[] = $instance->get('gidNumber');
			}
		}

		if (!$native)
		{
			foreach ($this->ownergroups as $g)
			{
				$groups[] = $g->groupid;
			}
		}

		$groups = array_unique($groups);

		sort($groups);

		return $groups;
	}

	/**
	 * Turn a comma or space deliniated string into an array
	 *
	 * @param   mixed  $data
	 * @return  array
	 */
	public function _toArray($data='')
	{
		if (is_array($data))
		{
			return $data;
		}

		if (!strstr($data, ' ') && !strstr($data, ','))
		{
			return array($data);
		}

		$data = str_replace(' ', ',', $data);
		$arr  = explode(',', $data);
		$arr  = array_map('trim', $arr);
		foreach ($arr as $key => $value)
		{
			if ($value == '')
			{
				unset($arr[$key]);
			}
		}
		$arr  = array_unique($arr);

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

		$row = User::getInstance($user);

		return $row->get('id', 0);
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

		$g = Group::getInstance($group);

		if ($g)
		{
			return $g->get('gidNumber');
		}

		return 0;
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

				if ($this->get('id'))
				{
					// Get list administrators
					$owners = $this->getOwners($this->config('group'));
					$managers = $owners['individuals'];
					$advisory = $owners['advisory'];

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

		if ($this->wishes->count() > 0)
		{
			$owners = $this->getOwners($this->config('group'));
			$voters = $owners['individuals'] + $owners['advisory'];

			if (!count($voters))
			{
				return false;
			}

			foreach ($this->wishes as $item)
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
				if ($item->rankings->count() > 0)
				{
					$imp     = 0;
					$eff     = 0;
					$num     = 0;
					$skipped = 0; // how many times effort selection was skipped
					$divisor = 0;

					foreach ($item->rankings as $vote)
					{
						if (in_array($vote->get('userid'), $voters))
						{
							// vote must come from list owner!
							$num++;
							if ($votesplit && in_array($vote->get('userid'), $advisory))
							{
								$imp += $vote->get('importance') * $co_adv;
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
							$vote->destroy();
						}
					}

					// average values
					if (($votesplit && $divisor) || $num)
					{
						$imp = ($votesplit && $divisor) ? $imp/$divisor: $imp/$num;
						$eff = ($num - $skipped) != 0 ? $eff/($num - $skipped) : 0;
						$weight_i = ($num - $skipped) != 0 ? $weight_i : 7;

						// we need to factor in how many people voted
						$certainty = $co + $num/count($voters);

						$ranking += ($imp * $weight_i) * $certainty;
						$ranking += ($eff * $weight_e) * $certainty;
					}
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
				if (!$item->save())
				{
					$this->addError($item->getError());
					return false;
				}
			}
		}

		return true;
	}
}
