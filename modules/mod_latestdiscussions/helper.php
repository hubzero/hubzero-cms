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

/**
 * Module class for displaying the latest forum posts
 */
class modLatestDiscussions extends \Hubzero\Module\Module
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

		//get the params
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);

		include_once(JPATH_ROOT . '/components/com_forum/models/forum.php');

		$forum = new ForumModel();

		//based on param decide what to include
		switch ($this->params->get('forum', 'both')) 
		{
			case 'site':
				$posts = $forum->posts('list', array(
					'scope'    => 'site',
					'scope_id' => 0,
					'state'    => 1,
					'limit'    => 100,
					'sort'     => 'created',
					'sort_Dir' => 'DESC'
				));
			break;

			case 'group':
				$posts = $forum->posts('list', array(
					'scope'    => 'site',
					'scope_id' => -1,
					'state'    => 1,
					'limit'    => 100,
					'sort'     => 'created',
					'sort_Dir' => 'DESC'
				));
			break;

			case 'both':
			default:
				$posts = $forum->posts('list', array(
					'scope'    => array('site', 'group'),
					'scope_id' => -1,
					'state'    => 1,
					'limit'    => 100,
					'sort'     => 'created',
					'sort_Dir' => 'DESC'
				));
			break;
		}

		//make sure that the group for each forum post has the right privacy setting
		$categories = array();
		$ids = array();
		$threads = array();
		$t = array();

		$p = array();

		// Run through all the posts and collect some data
		foreach ($posts as $k => $post) 
		{
			if ($post->get('scope') == 'group')
			{
				$group = \Hubzero\User\Group::getInstance($post->get('scope_id'));
				if (is_object($group)) 
				{
					$forum_access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'forum');

					if ($forum_access == 'nobody' 
					 || ($forum_access == 'registered' && $juser->get('guest')) 
					 || ($forum_access == 'members' && !in_array($juser->get('id'), $group->get('members')))) 
					{
						$posts->remove($k);
						continue;
					}
				} 
				else 
				{
					$posts->remove($k);
					continue;
				}
				$post->set('group_alias', $group->get('cn'));
				$post->set('group_title', $group->get('description'));
			}

			if ($post->get('parent') == 0)
			{
				$threads[$post->get('id')] = $post->get('title');
			}
			else 
			{
				$threads[$post->get('thread')] = (isset($threads[$post->get('thread')])) ? $threads[$post->get('thread')] : '';
				if (!$threads[$post->get('thread')])
				{
					$t[] = $post->get('thread');
				}
			}
			$ids[] = $post->get('category_id');

			$p[] = $post;
		}

		$this->posts = new \Hubzero\Base\ItemList($p);

		// Get any threads not found above
		if (count($t) > 0)
		{
			$thrds = $forum->posts('list', array(
				'scope'    => array('site', 'group'),
				'scope_id' => -1,
				'state'    => 1,
				'sort'     => 'created',
				'sort_Dir' => 'DESC',
				'id'       => $t
			));
			foreach ($thrds as $thread)
			{
				$threads[$thread->get('id')] = $thread->get('title');
			}
		}

		$database->setQuery("SELECT c.id, c.alias, s.alias as section FROM `#__forum_categories` c LEFT JOIN `#__forum_sections` as s ON s.id=c.section_id WHERE c.id IN (" . implode(',', $ids) . ") AND c.state='1'");
		$cats = $database->loadObjectList();
		if ($cats)
		{
			foreach ($cats as $category)
			{
				$categories[$category->id] = $category;
			}
		}

		//set posts to view
		$this->threads = $threads;
		//$this->posts = $posts;
		$this->categories = $categories;

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
