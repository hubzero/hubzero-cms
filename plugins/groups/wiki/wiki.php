<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
ximport('Hubzero_Plugin');

/**
 * Groups Plugin class for wiki
 */
class plgGroupsWiki extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function plgGroupsWiki(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'wiki',
			'title' => JText::_('PLG_GROUPS_WIKI'),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => $this->params->get('display_tab', 1)
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php');

		$page = new WikiPage(JFactory::getDBO());
		$arr['metadata']['count'] = $page->getPagesCount(array(
			'group' => $group->get('cn'),
			'state' => array('0', '1')
		));
		if ($arr['metadata']['count'] <= 0)
		{
			if (!defined('WIKI_SUBPAGE_SEPARATOR'))
			{
				define('WIKI_SUBPAGE_SEPARATOR', '/');
			}
			if (!defined('WIKI_MAX_PAGENAME_LENGTH'))
			{
				define('WIKI_MAX_PAGENAME_LENGTH', 100);
			}

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'setup.php');

			$result = WikiSetup::initialize('com_groups', $group->get('cn'));
			if ($result) 
			{
				$this->setError($result);
			}
			$arr['metadata']['count'] = $page->getPagesCount(array(
				'group' => $group->get('cn'),
				'state' => array('0', '1')
			));
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser =& JFactory::getUser();

			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if ($group_plugin_acl == 'nobody') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
				return $arr;
			}

			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') 
			 && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) 
			{
				$url = JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active);
				$message = JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active));
				$this->redirect( "/login?return=".base64_encode($url), $message, 'warning' );
				return;
			}

			//check to see if user is member and plugin access requires members
			if (!in_array($juser->get('id'), $members) 
			 && $group_plugin_acl == 'members' 
			 && $authorized != 'admin') 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
				return $arr;
			}

			// Set some variables for the wiki
			$scope = trim(JRequest::getVar('scope', ''));
			if (!$scope) 
			{
				JRequest::setVar('scope', $group->get('cn') . DS . $active);
			}

			// Import some needed libraries
			ximport('Hubzero_User_Helper');

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'attachment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'author.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'comment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'log.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'page.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'html.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'setup.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'tags.php');

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
					$controllerName = 'page';
				break;
			}

			$pagename = trim(JRequest::getVar('pagename', ''));

			if (substr(strtolower($pagename), 0, strlen('image:')) == 'image:'
			 || substr(strtolower($pagename), 0, strlen('file:')) == 'file:') 
			{
				$controllerName = 'media';
				$action = 'download';
			}

			JRequest::setVar('task', $action);

			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$lang = JFactory::getLanguage();
				$lang->load('com_wiki');
			}

			//$controllerName = JRequest::getCmd('controller', 'page');
			if (!file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'controllers' . DS . $controllerName . '.php'))
			{
				$controllerName = 'page';
			}
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'controllers' . DS . $controllerName . '.php');
			$controllerName = 'WikiController' . ucfirst($controllerName);

			// Instantiate controller
			$controller = new $controllerName(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_wiki',
				'name'      => 'groups',
				'sub'       => 'wiki',
				'group'     => $group->get('cn')
			));

			// Catch any echoed content with ob
			ob_start();
			$controller->execute();
			$controller->redirect();
			$content = ob_get_contents();
			ob_end_clean();

			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'wiki');

			// Return the content
			$arr['html'] = $content;
		}

		// Return the output
		return $arr;
	}

	/**
	 * Remove any associated resources when group is deleted
	 * 
	 * @param      object $group Group being deleted
	 * @return     string Log of items removed
	 */
	public function onGroupDelete($group)
	{
		// Get all the IDs for pages associated with this group
		$ids = $this->getPageIDs($group->get('cn'));

		// Import needed libraries
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');

		// Instantiate a WikiPage object
		$database =& JFactory::getDBO();

		// Start the log text
		$log = JText::_('PLG_GROUPS_WIKI_LOG') . ': ';

		if (count($ids) > 0) 
		{
			// Loop through all the IDs for pages associated with this group
			foreach ($ids as $id)
			{
				$wp = new WikiPage($database);
				$wp->load($id->id);
				// Delete all items linked to this page
				//$wp->deleteBits($id->id);

				// Delete the wiki page last in case somehting goes wrong
				//$wp->delete($id->id);
				if ($wp->id)
				{
					$wp->state = 2;
					$wp->store();
				}

				// Add the page ID to the log
				$log .= $id->id . ' ' . "\n";
			}
		} 
		else 
		{
			$log .= JText::_('PLG_GROUPS_WIKI_NO_RESULTS_FOUND')."\n";
		}

		// Return the log
		return $log;
	}

	/**
	 * Return a count of items that will be removed when group is deleted
	 * 
	 * @param      object $group Group to delete
	 * @return     string
	 */
	public function onGroupDeleteCount($group)
	{
		return JText::_('PLG_GROUPS_WIKI_LOG') . ': ' . count($this->getPageIDs($group->get('cn')));
	}

	/**
	 * Get a list of page IDs associated with this group
	 * 
	 * @param      string $gid Group alias
	 * @return     array
	 */
	public function getPageIDs($gid=NULL)
	{
		if (!$gid) 
		{
			return array();
		}
		$database =& JFactory::getDBO();
		$database->setQuery("SELECT id FROM #__wiki_page AS p WHERE p.group_cn='" . $gid . "'");
		return $database->loadObjectList();
	}
}
