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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for group members
 */
class plgGroupsFiles extends \Hubzero\Plugin\Plugin
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
			'name' => 'files',
			'title' => Lang::txt('PLG_GROUPS_FILES'),
			'default_access' => $this->params->get('plugin_access','members'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon' => 'f0c5'
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
		$returnhtml = true;
		$active = 'files';

		// The output array we're returning
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		//get this area details
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$returnhtml = false;
			}
		}

		// Set some variables so other functions have access
		$this->authorized = $authorized;
		$this->action = $action;
		$this->_option = $option;
		$this->group = $group;
		$this->name = substr($option, 4, strlen($option));

		// Only perform the following if this is the active tab/plugin
		if ($returnhtml)
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
				$url = Route::url('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active, false, true);

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

			// Append to document the title
			Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_GROUPS_FILES'));

			$this->path = PATH_APP . DS . trim($this->params->get('uploadpath', '/site/groups'), DS) . DS . $this->group->get('gidNumber');

			$arr['html'] = $this->_browse();
		}

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of latest blog entries
	 *
	 * @return     string
	 */
	private function _browse()
	{
		$view = $this->view('filebrowser')
			->set('option', $this->option)
			->set('group', $this->group)
			->set('config', $this->params)
			->set('task', $this->action)
			->set('authorized', $this->authorized)
			->set('notifications', array());

		//get rel path to start
		$view->activeFolder = Request::getVar('path', '/');

		// make sure we have an active folder
		if ($view->activeFolder == '')
		{
			$view->activeFolder = '/uploads';
		}

		$view->activeFolder = '/' . trim($view->activeFolder, '/');

		// regular groups can only access inside /uploads
		if (!$this->group->isSuperGroup())
		{
			$pathInfo = pathinfo($view->activeFolder);
			if ($pathInfo['dirname'] != '/uploads')
			{
				$view->activeFolder = '/uploads';
			}
		}

		// make sure we have a path
		$this->_createGroupFolder($this->path);

		// get list of folders
		$folders = Filesystem::directoryTree($this->path, '.', 10);

		// build recursive folder trees
		$folderTree             = $this->_buildFolderTree($folders);
		$view->folderTree = $this->_buildFolderTreeHtml($folderTree);
		$view->folderList = $this->_buildFolderTreeSelect($folderTree);

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Create group folder id doesnt exist
	 *
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	private function _createGroupFolder($path)
	{
		// create base group folder
		if (!Filesystem::exists($path))
		{
			Filesystem::makeDirectory($path);
		}

		// create uploads file
		if (!Filesystem::exists($path . DS . 'uploads'))
		{
			Filesystem::makeDirectory($path . DS . 'uploads');
		}
	}

	/**
	 * Build Folder tree based on path
	 *
	 * @param  [type]  $folders   [description]
	 * @param  integer $parent_id [description]
	 * @return [type]             [description]
	 */
	private function _buildFolderTree($folders, $parent_id = 0)
	{
		$branch = array();
		foreach ($folders as $folder)
		{
			if ($folder['parent'] == $parent_id)
			{
				$children = $this->_buildFolderTree($folders, $folder['id']);
				if ($children)
				{
					$folder['children'] = $children;
				}
				$branch[] = $folder;
			}
		}
		return $branch;
	}

	/**
	 * Build Folder tree in html ul list form
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeHtml($tree)
	{
		$base = substr(PATH_APP, strlen(PATH_ROOT));

		$html = '<ul>';
		foreach ($tree as $treeLevel)
		{
			$folder       = str_replace($base . '/site/groups/' . $this->group->get('gidNumber'), '', $treeLevel['relname']);
			$nodeToggle   = '<span class="tree-folder-toggle-spacer"></span>';
			$childrenHtml = '';

			if (@is_array($treeLevel['children']))
			{
				$nodeToggle   = '<a class="tree-folder-toggle" href="javascript:void(0);"></a>';
				$childrenHtml = $this->_buildFolderTreeHtml($treeLevel['children']);
			}

			$html .= '<li>';
			$html .= $nodeToggle . '<a data-folder="'.$folder.'" href="javascript:void(0);" class="tree-folder">' . $treeLevel['name'].'</a>';
			$html .= $childrenHtml;
			$html .= '</li>';
		}
		$html .= '</ul>';

		return $html;
	}

	/**
	 * Build Folder tree in select list form
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeSelect($tree)
	{
		$html  = '<select class="" name="folder">';
		if ($this->group->get('type') == 3)
		{
			$html .= '<option value="/">(root)</option>';
		}
		$html .= $this->_buildFolderTreeSelectOptionList($tree);
		$html .= '</select>';

		return $html;
	}

	/**
	 * Recursive function to create options for select list
	 *
	 * @param  [type] $tree [description]
	 * @return [type]       [description]
	 */
	private function _buildFolderTreeSelectOptionList($tree)
	{
		$base = substr(PATH_APP, strlen(PATH_ROOT));

		$options = '';
		foreach ($tree as $treeLevel)
		{
			$value = str_replace($base . '/site/groups/' . $this->group->get('gidNumber'), '', $treeLevel['relname']);
			$text  = str_repeat('&lfloor;', substr_count($value, '/'));
			$parts = explode('/', $value);
			$text .= ' ' . array_pop($parts);

			$options .= '<option value="'.$value.'">' . $text.'</option>';
			if (@is_array($treeLevel['children']))
			{
				$options .= $this->_buildFolderTreeSelectOptionList($treeLevel['children']);
			}
		}

		return $options;
	}
}

