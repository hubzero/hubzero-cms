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
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
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
	 * @param      object  $group      Current group
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
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
		require_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php');
		require_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'site' . DS . 'controllers' . DS . 'wishlists.php');

		// Get the component parameters
		$this->config = Component::params('com_wishlist');

		Lang::load('com_wishlist') ||
		Lang::load('com_wishlist', PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'site');

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
		$obj = new \Components\Wishlist\Tables\Wishlist($this->database);
		$objWish = new \Components\Wishlist\Tables\Wish($this->database);
		$objOwner = new \Components\Wishlist\Tables\Owner($this->database);

		// Get wishlist id
		$id = $obj->get_wishlistID($gid, $category);

		// Create a new list if necessary
		if (!$id)
		{
			// create private list for group
			if (\Hubzero\User\Group::exists($gid))
			{
				$group = \Hubzero\User\Group::getInstance($gid);
				$id = $obj->createlist($category, $gid, 0, $cn . ' ' . Lang::txt('PLG_GROUPS_WISHLIST_NAME_GROUP'));
			}
		}

		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $gid, $category);

		//if we dont have a wishlist display error
		if (!$wishlist)
		{
			$arr['html'] = '<p class="error">' . Lang::txt('PLG_GROUPS_WISHLIST_ERROR_WISHLIST_NOT_FOUND') . '</p>';
			return $arr;
		}

		// Get list owners
		$owners = $objOwner->get_owners($id, $this->config->get('group'), $wishlist);

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

		//get item count
		$items = $objWish->get_count($id, $filters, $admin);

		$arr['metadata']['count'] = $items;

		if ($return == 'html')
		{
			// Get wishes
			$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, User::getInstance());

			// HTML output
			// Instantiate a view
			$view = $this->view('default', 'browse');

			// Pass the view some info
			$view->option = $option;
			//$view->owners = $owners;
			$view->group = $this->group;
			$view->wishlist = $wishlist;
			$view->items = $items;
			$view->filters = $filters;
			$view->admin = $admin;
			$view->config = $this->config;

			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}
		return $arr;
	}

	/**
	 * Return count of items that will be deleted when group is deleted
	 * 
	 * @param      object $group Group being deleted
	 * @return     string
	 */
	public function onGroupDeleteCount($group)
	{
		// include com_wishlist files
		require_once PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php';

		// Load some objects
		$database = App::get('db');
		$wishlist = new \Components\Wishlist\Tables\Wishlist($database);
		$wish     = new \Components\Wishlist\Tables\Wish($database);

		// Get wishlist id
		$id = $wishlist->get_wishlistID($group->get('gidNumber'), 'group');

		// no id means no list
		if (!$id)
		{
			return Lang::txt('PLG_GROUPS_WISHLIST_LOG', 0);
		}

		// get wishes count
		$wishes = $wish->get_count($id, array(
			'filterby' => 'all'
		), 1);

		// return message
		return Lang::txt('PLG_GROUPS_WISHLIST_LOG', $wishes);
	}

	/**
	 * Delete any associated wishes & lists when group is deleted
	 * 
	 * @param      object $group Group being deleted
	 * @return     string Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// include com_wishlist files
		require_once PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php';

		// Load some objects
		$database = App::get('db');
		$wishlist = new \Components\Wishlist\Tables\Wishlist($database);
		$wish     = new \Components\Wishlist\Tables\Wish($database);

		// Get wishlist id
		$id = $wishlist->get_wishlistID($group->get('gidNumber'), 'group');

		// no id means no list
		if (!$id)
		{
			return '';
		}

		// Get wishes
		$wishes = $wish->get_wishes($id, array(
			'filterby' => 'all',
			'sortby'   => ''
		), 1);

		// delete each wish
		foreach ($wishes as $item)
		{
			$wish->load($item->id);
			$wish->delete();
		}

		// delete wishlist
		$wishlist->load($id);
		$wishlist->delete();

		// return message
		return Lang::txt('PLG_GROUPS_WISHLIST_LOG', count($wishes));
	}
}

