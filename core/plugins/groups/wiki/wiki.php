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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for wiki
 */
class plgGroupsWiki extends \Hubzero\Plugin\Plugin
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
			'name' => 'wiki',
			'title' => Lang::txt('PLG_GROUPS_WIKI'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f072'
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
		$active = 'wiki';

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

		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'editor.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'parser.php');

		$book = new Components\Wiki\Models\Book('group', $group->get('gidNumber'));
		$arr['metadata']['count'] = $book->pages()
			->whereEquals('state', Components\Wiki\Models\Page::STATE_PUBLISHED)
			->total();

		if ($arr['metadata']['count'] <= 0)
		{
			if ($result = $book->scribe($option))
			{
				$this->setError($result);
			}

			$arr['metadata']['count'] = $book->pages()
				->whereEquals('state', Components\Wiki\Models\Page::STATE_PUBLISHED)
				->total();
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//get the group members
			$members = $group->get('members');

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
				$url = $_SERVER['REQUEST_URI'];
				if (!Hubzero\Utility\Uri::isInternal($url))
				{
					$url = Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active);
				}

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

			// Set some variables for the wiki
			/*$scope = trim(Request::getVar('scope', ''));
			if (!$scope)
			{
				Request::setVar('scope', $group->get('cn') . DS . $active);
			}*/

			// Import some needed libraries
			switch ($action)
			{
				case 'upload':
				case 'download':
				case 'deletefolder':
				case 'deletefile':
				case 'media':
					$controllerName = 'media';
				break;

				case 'history':
				case 'compare':
				case 'approve':
				case 'deleterevision':
					$controllerName = 'history';
				break;

				case 'editcomment':
				case 'addcomment':
				case 'savecomment':
				case 'reportcomment':
				case 'removecomment':
				case 'comments':
					$controllerName = 'comments';
				break;

				case 'delete':
				case 'edit':
				case 'save':
				case 'rename':
				case 'saverename':
				default:
					$controllerName = 'pages';
				break;
			}

			$pagename = trim(Request::getVar('pagename', ''));

			if (substr(strtolower($pagename), 0, strlen('image:')) == 'image:'
			 || substr(strtolower($pagename), 0, strlen('file:')) == 'file:')
			{
				$controllerName = 'media';
				$action = 'download';
			}

			Request::setVar('task', $action);

			Lang::load('com_wiki') ||
			Lang::load('com_wiki', Component::path('com_wiki') . DS . 'site');

			if (!file_exists(Component::path('com_wiki') . DS . 'site' . DS . 'controllers' . DS . $controllerName . '.php'))
			{
				$controllerName = 'pages';
			}
			require_once(Component::path('com_wiki') . DS . 'site' . DS . 'controllers' . DS . $controllerName . '.php');
			$controllerName = '\\Components\\Wiki\\Site\\Controllers\\' . ucfirst($controllerName);

			// Instantiate controller
			$controller = new $controllerName(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'site',
				'scope'     => 'group',
				'scope_id'  => $group->get('gidNumber')
			));

			// Catch any echoed content with ob
			ob_start();
			$controller->execute();
			$controller->redirect();
			$content = ob_get_contents();
			ob_end_clean();

			$this->css()
			     ->js();

			// Return the content
			$arr['html'] = $content;
		}

		// Return the output
		return $arr;
	}

	/**
	 * Remove any associated resources when group is deleted
	 *
	 * @param   object  $group  Group being deleted
	 * @return  string  Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Start the log text
		$log = Lang::txt('PLG_GROUPS_WIKI_LOG') . ': ';

		$pages = $this->getPages($group->get('gidNumber'));

		if ($pages->count() > 0)
		{
			// Loop through all the IDs for pages associated with this group
			foreach ($pages as $page)
			{
				$page->set('state', \Components\Wiki\Models\Page::STATE_DELETED);
				$page->save();

				// Add the page ID to the log
				$log .= $page->get('id') . ' ' . "\n";
			}
		}
		else
		{
			$log .= Lang::txt('PLG_GROUPS_WIKI_NO_RESULTS_FOUND')."\n";
		}

		// Return the log
		return $log;
	}

	/**
	 * Return a count of items that will be removed when group is deleted
	 *
	 * @param   object  $group  Group to delete
	 * @return  string
	 */
	public function onGroupDeleteCount($group)
	{
		return Lang::txt('PLG_GROUPS_WIKI_LOG') . ': ' . $this->getPages($group->get('gidNumber'))->count();
	}

	/**
	 * Get a list of page IDs associated with this group
	 *
	 * @param   integer  $gid
	 * @return  array
	 */
	public function getPages($gid=NULL)
	{
		// Import needed libraries
		include_once(PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'page.php');

		// Start the log text
		$log = Lang::txt('PLG_GROUPS_WIKI_LOG') . ': ';

		$pages = \Components\Wiki\Models\Page::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $gid)
			->rows();

		return $pages;
	}
}
