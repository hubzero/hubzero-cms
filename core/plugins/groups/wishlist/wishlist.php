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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for wishlist
 */
class plgGroupsWishlist extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = \App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'wishlist',
			'title' => Lang::txt('PLG_GROUPS_WISHLIST'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f078'
		);

		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = 'wishlist';

		// The output array we're returning
		$arr = array(
			'html' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		//get the group members
		$members = $group->get('members');

		//if we want to return content
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if (User::isGuest()
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members'))
			{
				$url = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active, false, true);

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array(User::get('id'), $members)
			 && $group_plugin_acl == 'members'
			 && $authorized != 'admin')
			{
				$arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}
		}

		//instantiate database
		$database = App::get('db');

		// Set some variables so other functions have access
		$this->database = $database;
		$this->authorized = $authorized;
		$this->members = $members;
		$this->group = $group;
		$this->option = $option;
		$this->action = $action;

		//include com_wishlist files
		require_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';
		require_once Component::path('com_wishlist') . DS . 'site' . DS . 'controllers' . DS . 'wishlists.php';

		// Get the component parameters
		$this->config = Component::params('com_wishlist');

		Lang::load('com_wishlist') ||
		Lang::load('com_wishlist', Component::path('com_wishlist') . DS . 'site');

		//set some more vars
		$gid = $this->group->get('gidNumber');
		$cn = $this->group->get('cn');
		$category = 'group';
		$admin = 0;

		// Configure controller
		$controller = new \Components\Wishlist\Site\Controllers\Wishlists();

		// Get filters
		$filters = $controller->getFilters(0);
		$filters['limit'] = $this->params->get('limit');

		// Load some objects
		$wishlist = \Components\Wishlist\Models\Wishlist::oneByReference($gid, $category);

		// Get wishlist id
		$id = $wishlist->get('id');

		// Create a new list if necessary
		if (!$id)
		{
			// create private list for group
			$wishlist->set('category', $category);
			$wishlist->set('referenceid', $gid);
			$wishlist->set('public', 0);
			$wishlist->set('title', $cn . ' ' . Lang::txt('PLG_GROUPS_WISHLIST_NAME_GROUP'));
			$wishlist->stage();

			$id = $wishlist->get('id');
		}

		//if we dont have a wishlist display error
		if (!$wishlist->get('id'))
		{
			$arr['html'] = '<p class="error">' . Lang::txt('PLG_GROUPS_WISHLIST_ERROR_WISHLIST_NOT_FOUND') . '</p>';
			return $arr;
		}

		// Get list owners
		$owners = $wishlist->getOwners();

		//if user is guest and wishlist isnt public
		//if (!$wishlist->public && User::isGuest())
		//{
		//	$arr['html'] = '<p class="warning">' . Lang::txt('The Group Wishlist is not a publicly viewable list.') . '</p>';
		//	return $arr;
		//}

		// Authorize admins & list owners
		if (User::authorise($option, 'manage'))
		{
			$admin = 1;
		}

		//authorized based on wishlist
		if (in_array(User::get('id'), $owners['individuals']))
		{
			$admin = 2;
		}
		else if (in_array(User::get('id'), $owners['advisory']))
		{
			$admin = 3;
		}

		$entries = \Components\Wishlist\Models\Wish::all()
			->whereEquals('wishlist', $wishlist->get('id'));

		$w = $entries->getTableName();

		if ($filters['search'])
		{
			$entries
				->whereLike('subject', strtolower((string)$filters['search']), 1)
				->orWhereLike('about', strtolower((string)$filters['search']), 1)
				->resetDepth();
		}

		if ($filters['filterby'])
		{
			// list  filtering
			switch ($filters['filterby'])
			{
				case 'granted':
					$entries->whereEquals('status', 1);
					break;
				case 'open':
					$entries->whereEquals('status', 0);
					break;
				case 'accepted':
					$entries
						->whereIn('status', array(0, 6))
						->whereEquals('accepted', 1);
					break;
				case 'pending':
					$entries
						->whereEquals('accepted', 0)
						->whereEquals('status', 0);
					break;
				case 'rejected':
					$entries->whereEquals('status', 3);
					break;
				case 'withdrawn':
					$entries->whereEquals('status', 4);
					break;
				case 'deleted':
					$entries->whereEquals('status', 2);
					break;
				case 'useraccepted':
					$entries
						->whereEquals('accepted', 3)
						->where('status', '!=', 2);
					break;
				case 'private':
					$entries
						->whereEquals('private', 1)
						->where('status', '!=', 2);
					break;
				case 'public':
					$entries
						->whereEquals('private', 0)
						->where('status', '!=', 2);
					break;
				case 'assigned':
					$entries
						->where('status', '!=', 2)
						->whereRaw('assigned NOT NULL');
					break;
				case 'mine':
					$entries
						->where('status', '!=', 2)
						->whereEquals('assigned', User::get('id'));
				break;
				case 'submitter':
					$entries
						->where('status', '!=', 2)
						->whereEquals('proposed_by', User::get('id'));
					break;
				case 'all':
				default:
					$entries->where('status', '!=', 2);
					break;
			}
		}

		if (!$admin)
		{
			$entries->whereEquals('private', 0);
		}

		// If filtering by tags...
		if (isset($filters['tag']) && $filters['tag'])
		{
			$tags = $filters['tag'];
			if (is_string($tags))
			{
				$tags = trim($tags);
				$tags = preg_split("/(,|;)/", $tags);
			}

			foreach ($tags as $k => $tag)
			{
				$tags[$k] = strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
			}

			$to = '#__tags_object';
			$t  = '#__tags';
			$entries
				->join($to, $to . '.objectid', $w . '.id', 'left')
				->join($t, $to . '.tagid', $t . '.id', 'left')
				->whereEquals($to . '.tbl', 'wishlist')
				->whereIn($t . '.tag', $tags, 1)
				->group($w . '.id');
		}

		// Get a total
		$items = $entries->copy()->total();

		$arr['metadata']['count'] = $items;

		if ($return == 'html')
		{
			// Select vote totals
			$vote = \Components\Wishlist\Models\Vote::blank();

			$entries
				->select($entries->getTableName() . '.*')
				->select("(SELECT COUNT(*) FROM `" . $vote->getTableName() . "` AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=" . $entries->getTableName() . ".id)", 'positive')
				->select("(SELECT COUNT(*) FROM `" . $vote->getTableName() . "` AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=" . $entries->getTableName() . ".id)", 'negative');

			// list sorting
			if ($filters['sortby'])
			{
				switch ($filters['sortby'])
				{
					case 'date':
						$entries
							->order('status', 'asc')
							->order('proposed', 'desc');
						break;
					case 'submitter':
						$u = User::getTableName(); //'#__users';
						$entries
							->select($u . '.name', 'authorname')
							->join($u, $u . '.id', $entries->getTableName() . '.proposed_by', 'left')
							->order($u . '.name', 'asc');
						break;
					case 'feedback':
						$entries
							->order('positive', 'desc')
							->order('status', 'asc');
						break;
					case 'ranking':
						$entries
							->order('status', 'asc')
							->order('ranking', 'desc')
							->order('positive', 'desc')
							->order('proposed', 'desc');
						break;
					case 'bonus':
						$entries
							->select("(SELECT SUM(amount) FROM `#__users_transactions` WHERE category='wish' AND type='hold' AND referenceid=" . $entries->getTableName() . ".id)", 'bonus')
							->order('status', 'asc')
							->order('bonus', 'desc')
							->order('positive', 'desc')
							->order('proposed', 'desc');
						break;
					case 'all':
					default:
						$entries
							->order('accepted', 'desc')
							->order('status', 'asc')
							->order('proposed', 'desc');
						break;
				}
			}

			$rows = $entries
				->limit($filters['limit'])
				->start($filters['start'])
				->rows();

			// HTML output
			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('group', $this->group)
				->set('wishlist', $wishlist)
				->set('items', $items)
				->set('rows', $rows)
				->set('filters', $filters)
				->set('admin', $admin)
				->set('config', $this->config)
				->setErrors($this->getErrors());

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}
		return $arr;
	}

	/**
	 * Return count of items that will be deleted when group is deleted
	 * 
	 * @param   object  $group  Group being deleted
	 * @return  string
	 */
	public function onGroupDeleteCount($group)
	{
		// include com_wishlist files
		require_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';

		// Load some objects
		$wishlist = \Components\Wishlist\Models\Wishlist::oneByReference($group->get('gidNumber'), 'group');

		// Get wishlist id
		$id = $wishlist->get('id');

		// no id means no list
		if (!$id)
		{
			return Lang::txt('PLG_GROUPS_WISHLIST_LOG', 0);
		}

		// get wishes count
		$wishes = $wishlist->wishes()->total();

		// return message
		return Lang::txt('PLG_GROUPS_WISHLIST_LOG', $wishes);
	}

	/**
	 * Delete any associated wishes & lists when group is deleted
	 * 
	 * @param   object  $group  Group being deleted
	 * @return  string  Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// include com_wishlist files
		require_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';

		// Load some objects
		$wishlist = \Components\Wishlist\Models\Wishlist::oneByReference($group->get('gidNumber'), 'group');

		// Get wishlist id
		$id = $wishlist->get('id');

		// no id means no list
		if (!$id)
		{
			return '';
		}

		// Get wishes
		$wishes = $wishlist->wishes()->total();

		// delete wishlist
		$wishlist->destroy();

		// return message
		return Lang::txt('PLG_GROUPS_WISHLIST_LOG', $wishes);
	}
}
