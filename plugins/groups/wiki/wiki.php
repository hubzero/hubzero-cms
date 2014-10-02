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

/**
 * Groups Plugin class for wiki
 */
class plgGroupsWiki extends \Hubzero\Plugin\Plugin
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
			'name' => 'wiki',
			'title' => JText::_('PLG_GROUPS_WIKI'),
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'editor.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'parser.php');

		$book = new WikiModelBook($group->get('cn'));
		$arr['metadata']['count'] = $book->pages('count');

		if ($arr['metadata']['count'] <= 0)
		{
			if ($result = $book->scribe($option))
			{
				$this->setError($result);
			}

			$arr['metadata']['count'] = $book->pages('count', array(), true);
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html')
		{
			//set group members plugin access level
			$group_plugin_acl = $access[$active];

			//Create user object
			$juser = JFactory::getUser();

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
				$url = $_SERVER['REQUEST_URI'];
				if (!JURI::isInternal($url))
				{
					$url = JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active='.$active);
				}

				$this->redirect(
					JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url)),
					JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
					'warning'
				);
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

			$lang = JFactory::getLanguage();
			$lang->load('com_wiki');

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

			$this->css()
			     ->js();

			// Return the content
			$arr['html'] = $content;
		}

		// Return the output
		return $arr;
	}

	/**
	 * Update wiki pages if a group changes its CN
	 *
	 * @param      object $before Group before changed
	 * @param      object $after  Group after changed
	 */
	public function onGroupAfterSave($before, $after)
	{
		if (!$before->get('cn') || $after->get('cn') == $before->get('cn'))
		{
			return;
		}

		$database = JFactory::getDBO();
		$database->setQuery("UPDATE `#__wiki_page` SET `group_cn`=" . $database->quote($after->get('cn')) . " WHERE `group_cn`=" . $database->quote($before->get('cn')));
		if (!$database->query())
		{
			return;
		}

		$database->setQuery("SELECT id, scope FROM `#__wiki_page` WHERE `group_cn`=" . $database->quote($after->get('cn')));
		if ($results = $database->loadObjectList())
		{
			$pattern = '^' . str_replace(array('-', ':'), array('\-', '\:'), $before->get('cn'));
			foreach ($results as $result)
			{
				$result->scope = preg_replace("/$pattern/i", $after->get('cn'), $result->scope);
				$database->setQuery("UPDATE `#__wiki_page` SET `scope`=" . $database->quote($result->scope) . " WHERE `id`=" . $database->quote($result->id));
				if (!$database->query())
				{
					$this->setError($database->getErrorMsg());
				}
			}
		}
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

		// Instantiate a WikiTablePage object
		$database = JFactory::getDBO();

		// Start the log text
		$log = JText::_('PLG_GROUPS_WIKI_LOG') . ': ';

		if (count($ids) > 0)
		{
			// Loop through all the IDs for pages associated with this group
			foreach ($ids as $id)
			{
				$wp = new WikiTablePage($database);
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
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id FROM `#__wiki_page` AS p WHERE p.group_cn=" . $database->quote($gid));
		return $database->loadObjectList();
	}
}
