<?php
/**
 * @package     hubzero-cms
 * @author      HUBzero
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 * All rights reserved.
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
 * Module class for displaying the latest forum posts
 */
class modLatestGroups extends \Hubzero\Module\Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function run()
	{
		$database = JFactory::getDBO();

		$juser = JFactory::getUser();
		$uid = $juser->get('id');

		//get the params
		$this->cls = $this->params->get('moduleclass_sfx');
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);
		$this->feedlink = $this->params->get('feedlink', 'yes');
		$this->morelink = $this->params->get('morelink', '');

		// Get popular groups
		$popularGroups = \Hubzero\User\Group\Helper::getPopularGroups();

		$counter = 0;
		$groupsToDisplay = array();
		foreach ($popularGroups as $g)
		{
			// Get the group
			$group = \Hubzero\User\Group::getInstance($g->gidNumber);

			// Check join policy
			$joinPolicy = $group->get('join_policy');

			// If group is invite only or closed check if th user is a member of the group
			if ($joinPolicy > 1)
			{
				// if not a member do not display the group
				if (!$group->isMember($uid))
				{
					continue;
				}
			}

			$groupsToDisplay[] = $g;

			$counter++;
			if ($counter == $this->limit)
			{
				break;
			}
		}

		//set groups to view
		$this->groups = $groupsToDisplay;

		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Display module
	 *
	 * @return     void
	 */
	public function display()
	{
		// Push the module CSS to the template
		$this->css();

		$debug = (defined('JDEBUG') && JDEBUG ? true : false);

		if (!$debug && intval($this->params->get('cache', 0)))
		{
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}
}
