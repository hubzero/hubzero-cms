<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki helper class for determining page authorization
 */
class WikiHelperPage
{
	/**
	 * Get the current page
	 * 
	 * @return     void
	 */
	public static function getPage($config)
	{
		$tbl = new WikiPage(JFactory::getDBO());
		$pagename = trim(JRequest::getVar('pagename', '', 'default', 'none', 2));
		if (substr(strtolower($pagename), 0, strlen('image:')) != 'image:' 
		 && substr(strtolower($pagename), 0, strlen('file:')) != 'file:')
		{
			$pagename = $tbl->normalize($pagename);
		} 
		JRequest::setVar('pagename', $pagename);

		$scope = trim(JRequest::getVar('scope', ''));
		if ($scope)
		{
			// Clean the scope. Since scope is built of a chain of pagenames or groups/groupname/wiki
			// the wiki normalize() should strip any nasty stuff out
			$bits = explode('/', $scope);
			foreach ($bits as $i => $bit)
			{
				$bits[$i] = $tbl->normalize($bit);
			}
			$scope = implode('/', $bits);
			JRequest::setVar('scope', $scope);
		}

		$task = trim(JRequest::getWord('task', ''));

		// No page name given! Default to the home page
		if (!$pagename && $task != 'new')
		{
			$pagename = $config->get('homepage', 'MainPage');
		}

		// Load the page
		//$page = WikiPage::getInstance($pagename, $scope);
		$page = new WikiPage(JFactory::getDBO());
		$page->load($pagename, $scope);

		/*if (in_array(strtolower($page->getNamespace()), array('special'))) 
		{
			$page->load(JRequest::getVar('page', ''), $scope);
		}*/

		if (!$page->exist() 
		 && (strtolower($page->getNamespace()) == 'help')) 
		{
			$page->load($pagename, '');
			$page->scope = $scope;
		}

		$paramClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramClass = 'JRegistry';
		}
		$page->params = new $paramClass($page->params);

		return $page;
	}

	/**
	 * Set permissions for a user
	 * 
	 * @return     void
	 */
	public static function authorize($config, $page)
	{
		$config->set('access-view', true);
		$config->set('access-manage', false);
		$config->set('access-admin', false);
		$config->set('access-create', false);
		$config->set('access-delete', false);
		$config->set('access-edit', false);
		$config->set('access-modify', false);

		$config->set('access-comment-view', false);
		$config->set('access-comment-create', false);
		$config->set('access-comment-delete', false);
		$config->set('access-comment-edit', false);

		$juser =& JFactory::getUser();

		// Check if they are logged in
		if ($juser->get('guest')) 
		{
			// Not logged-in = can only view
			return $config;
		}

		$option = JRequest::getCmd('option', 'com_wiki');

		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($option, 'manage')) 
		{
			$config->set('access-admin', true);
			$config->set('access-manage', true);
			$config->set('access-create', true);
			$config->set('access-delete', true);
			$config->set('access-edit', true);
			$config->set('access-modify', true);

			$config->set('access-comment-view', true);
			$config->set('access-comment-create', true);
			$config->set('access-comment-delete', true);
			$config->set('access-comment-edit', true);

			return $config;
		}

		// Is a group set?
		if (trim($page->group_cn)) 
		{
			ximport('Hubzero_Group');
			$group = Hubzero_Group::getInstance($page->group_cn);

			// Is this a group manager?
			if ($group->is_member_of('managers', $juser->get('id'))) 
			{
				// Allow access to all options
				$config->set('access-manage', true);
				$config->set('access-create', true);
				$config->set('access-delete', true);
				$config->set('access-edit', true);
				$config->set('access-modify', true);
				
				$config->set('access-comment-view', true);
				$config->set('access-comment-create', true);
				$config->set('access-comment-delete', true);
				$config->set('access-comment-edit', true);
			}
			else
			{
				// Check permissions based on the page mode (knol/wiki)
				switch ($page->params->get('mode')) 
				{
					// Knowledge article
					// This means there's a defined set of authors
					case 'knol':
						if ($page->created_by == $juser->get('id')
						 || $page->isAuthor($juser->get('id'))) 
						{
							$config->set('access-create', true);
							$config->set('access-delete', true);
							$config->set('access-edit', true);
							$config->set('access-modify', true);
						}
						else if ($page->params->get('allow_changes'))
						{
							$config->set('access-modify', true); // This allows users to suggest changes
						}

						if ($page->params->get('allow_comments'))
						{
							$config->set('access-comment-view', true);
							$config->set('access-comment-create', true);
						}
					break;

					// Standard wiki
					default:
						// 1 = private to group, 2 = ...um, can't remember
						if ($group->is_member_of('members', $juser->get('id'))) 
						{
							$config->set('access-create', true);
							if ($page->state != 1)
							{
								$config->set('access-delete', true);
								$config->set('access-edit', true);
								$config->set('access-modify', true);
							}

							$config->set('access-comment-view', true);
							$config->set('access-comment-create', true);
						}
					break;
				}
			}
		}
		// No group = Site wiki
		else 
		{
			$config->set('access-create', true);

			// Check permissions based on the page mode (knol/wiki)
			switch ($page->params->get('mode')) 
			{
				// Knowledge article
				// This means there's a defined set of authors
				case 'knol':
					if ($page->created_by == $juser->get('id')
					 || $page->isAuthor($juser->get('id'))) 
					{
						$config->set('access-delete', true);
						$config->set('access-edit', true);
						$config->set('access-modify', true);
					}
					else if ($page->params->get('allow_changes'))
					{
						$config->set('access-modify', true); // This allows users to suggest changes
					}

					if ($page->params->get('allow_comments'))
					{
						$config->set('access-comment-view', true);
						$config->set('access-comment-create', true);
					}
				break;

				// Standard wiki
				default:
					$config->set('access-delete', true);
					$config->set('access-edit', true);
					$config->set('access-modify', true);

					$config->set('access-comment-view', true);
					$config->set('access-comment-create', true);
				break;
			}
		}

		return $config;
	}
}

