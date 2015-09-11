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

namespace Modules\LatestGroups;

use Hubzero\Module\Module;
use Hubzero\User\Group;
use User;

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
		$database = \App::get('db');

		$uid = User::get('id');

		//get the params
		$this->cls = $this->params->get('moduleclass_sfx');
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);
		$this->feedlink = $this->params->get('feedlink', 'yes');
		$this->morelink = $this->params->get('morelink', '');

		// Get popular groups
		$popularGroups = Group\Helper::getPopularGroups();

		$counter = 0;
		$groupsToDisplay = array();
		foreach ($popularGroups as $g)
		{
			// Get the group
			$group = Group::getInstance($g->gidNumber);

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

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}
