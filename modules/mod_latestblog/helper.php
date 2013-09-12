<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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

ximport('Hubzero_Module');

/**
 * Module class for displaying the latest blog posts
 */
class modLatestBlog extends Hubzero_Module
{
	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function run()
	{
		$database =& JFactory::getDBO();

		$juser =& JFactory::getUser();

		ximport("Hubzero_Group");

		//get the params
		$this->cls       = $this->params->get('moduleclass_sfx');

		$this->pullout   = $this->params->get('pullout', 'yes');
		$this->limit     = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);

		$this->feedlink  = $this->params->get('feedlink', 'yes');
		$this->morelink  = $this->params->get('morelink', '');

		$include = $this->params->get('blog', 'site');

		$nullDate = $database->getNullDate();
		$date =& JFactory::getDate();
		$now = $date->toMySQL();
		
		$query = "AND (f.publish_up = " . $database->Quote($nullDate) . " OR f.publish_up <= " . $database->Quote($now) . ") 
				AND (f.publish_down = " . $database->Quote($nullDate) . " OR f.publish_down >= " . $database->Quote($now) . ")";

		$site_blog = array();
		if ($include == 'site' || $include == 'both')
		{
			//get all blog posts on site blog
			$database->setQuery("SELECT f.*, u.name FROM #__blog_entries f LEFT JOIN #__users AS u ON u.id=f.created_by WHERE f.group_id='0' AND f.state='1' AND scope='site' $query ORDER BY publish_up DESC LIMIT " . $this->limit);
			$site_blog = $database->loadObjectList();
		}

		$group_blog = array();
		if ($include == 'group' || $include == 'both')
		{
			//get any group posts
			$database->setQuery("SELECT f.*, u.name FROM #__blog_entries f LEFT JOIN #__users AS u ON u.id=f.created_by WHERE f.group_id<>'0' AND f.state='1' AND scope='group' $query ORDER BY publish_up DESC LIMIT " . $this->limit);
			$group_blog = $database->loadObjectList();

			//make sure that the group for each blog post has the right privacy setting
			foreach ($group_blog as $k => $gf) 
			{
				$group = Hubzero_Group::getInstance($gf->group_id);
				if (is_object($group)) 
				{
					ximport('Hubzero_Group_Helper');
					$blog_access = Hubzero_Group_Helper::getPluginAccess($group, 'blog');

					if ($blog_access == 'nobody' 
					 || ($blog_access == 'registered' && $juser->get('guest')) 
					 || ($blog_access == 'members' && !in_array($juser->get('id'), $group->get('members')))) 
					{
						unset($group_blog[$k]);
					}
				} 
				else 
				{
					unset($group_blog[$k]);
				}
			}
		}
		
		//based on param decide what to include
		switch ($include) 
		{
			case 'site':  $posts = $site_blog;  break;
			case 'group': $posts = $group_blog; break;
			case 'both':  
			default:
				$posts = array_merge($site_blog, $group_blog);
			break;
		}

		$this->dateFormat = '%d %b %Y';
		$this->timeFormat = '%I:%M %p';
		$this->yearFormat  = "%Y";
		$this->monthFormat = "%m";
		$this->dayFormat   = "%d";
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'd M Y';
			$this->timeFormat = 'H:i p';
			$this->yearFormat  = "Y";
			$this->monthFormat = "m";
			$this->dayFormat   = "d";
			$this->tz = true;
		}

		//function to sort by created date
		function sortbydate($a, $b)
		{
			$d1 = date("Y-m-d H:i:s", strtotime($a->created));
			$d2 = date("Y-m-d H:i:s", strtotime($b->created));
			
			return ($d1 > $d2) ? -1 : 1;
		}

		//sort using function above - date desc
		usort($posts, 'sortbydate');

		//set posts to view
		$this->posts = $posts;

		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display()
	{
		$juser =& JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 15)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}
}
