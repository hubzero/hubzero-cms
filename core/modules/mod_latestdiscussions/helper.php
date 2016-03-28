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

namespace Modules\LatestDiscussions;

use Hubzero\Module\Module;
use Components\Forum\Models\Manager;
use Components\Forum\Models\Post;
use Hubzero\User\Group;
use Component;
use User;

/**
 * Module class for displaying the latest forum posts
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

		//get the params
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);

		include_once(Component::path('com_forum') . DS . 'models' . DS . 'manager.php');

		$forum = new Manager();

		//based on param decide what to include
		switch ($this->params->get('forum', 'both'))
		{
			case 'site':
				$posts = $forum->posts(array(
						'scope'    => 'site',
						'scope_id' => 0,
						'state'    => Post::STATE_PUBLISHED,
						'access'   => User::getAuthorisedViewLevels()
					))
					->order('created', 'desc')
					->limit(100)
					->rows();
			break;

			case 'group':
				$posts = $forum->posts(array(
						'scope'    => 'group',
						'scope_id' => -1,
						'state'    => Post::STATE_PUBLISHED,
						'access'   => User::getAuthorisedViewLevels()
					))
					->order('created', 'desc')
					->limit(100)
					->rows();
			break;

			case 'both':
			default:
				$posts = $forum->posts(array(
						'scope'    => array('site', 'group'),
						'scope_id' => -1,
						'state'    => Post::STATE_PUBLISHED,
						'access'   => User::getAuthorisedViewLevels()
					))
					->order('created', 'desc')
					->limit(100)
					->rows();
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
				$group = Group::getInstance($post->get('scope_id'));
				if (is_object($group))
				{
					$forum_access = Group\Helper::getPluginAccess($group, 'forum');

					if ($forum_access == 'nobody'
					 || ($forum_access == 'registered' && User::isGuest())
					 || ($forum_access == 'members' && !in_array(User::get('id'), $group->get('members'))))
					{
						//$posts->remove($k);
						continue;
					}
				}
				else
				{
					//$posts->remove($k);
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

		$this->posts = $p;

		// Get any threads not found above
		if (count($t) > 0)
		{
			$thrds = $forum->posts(array(
					'scope'    => array('site', 'group'),
					'scope_id' => -1,
					'state'    => Post::STATE_PUBLISHED,
					'access'   => User::getAuthorisedViewLevels()
				))
				->whereIn('id', $t)
				->order('created', 'desc')
				->limit(100)
				->rows();
			foreach ($thrds as $thread)
			{
				$threads[$thread->get('id')] = $thread->get('title');
			}
		}

		if (count($ids) > 0)
		{
			$database->setQuery("SELECT c.id, c.alias, s.alias as section FROM `#__forum_categories` c LEFT JOIN `#__forum_sections` as s ON s.id=c.section_id WHERE c.id IN (" . implode(',', $ids) . ") AND c.state='1'");
			$cats = $database->loadObjectList();
			if ($cats)
			{
				foreach ($cats as $category)
				{
					$categories[$category->id] = $category;
				}
			}
		}

		//set posts to view
		$this->threads = $threads;
		//$this->posts = $posts;
		$this->categories = $categories;

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
