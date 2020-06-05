<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
