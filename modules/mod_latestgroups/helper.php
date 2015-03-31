<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\LatestGroups;

use Hubzero\Module\Module;
use User;
use JFactory;

/**
 * Module class for displaying the latest groups
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		$database = JFactory::getDBO();

		$uid = User::get('id');

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

		require $this->getLayoutPath();
	}

	/**
	 * Display module
	 *
	 * @return  void
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

			// Module time is in seconds, setLifeTime() is in minutes
			// Some module times may have been set in minutes so we
			// need to account for that.
			$ct = intval($this->params->get('cache_time', 900));
			$ct = (!$ct || $ct == 15 ?: $ct / 60);
			$cache->setLifeTime($ct);

			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}
}
