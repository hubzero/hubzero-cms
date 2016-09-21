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

namespace Modules\LatestBlog;

use Hubzero\Module\Module;
use Hubzero\User\Group\Helper as GroupHelper;
use Components\Blog\Models\Archive;
use User;
use Lang;


/**
 * Module class for displaying the latest blog posts
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
		Lang::load('com_blog', Component::path('com_blog').'/site');
		include_once(\Component::path('com_blog') . DS . 'models' . DS . 'archive.php');

		$this->pullout   = $this->params->get('pullout', 'yes');
		$this->feedlink  = $this->params->get('feedlink', 'yes');
		$this->limit     = $this->params->get('limit', 5);

		$filters = array(
			'limit'    => $this->params->get('limit', 5),
			'start'    => 0,
			'scope'    => $this->params->get('blog', 'site'),
			'scope_id' => 0,
			'state'    => 1,
			'access'   => User::getAuthorisedViewLevels()
		);
		if ($filters['scope'] == 'both' || $filters['scope'] == 'group')
		{
			$filters['limit'] = ($filters['limit'] * 5);  // Since some groups May have private entries, we need to up the limit to try and catch more
		}
		if ($filters['scope'] == 'both')
		{
			$filters['scope'] = '';
		}

		$archive = new Archive('site', 0);
		$rows = $archive->entries($filters)
			->ordered()
			->rows();

		$posts = array();

		foreach ($rows as $k => $gf)
		{
			if ($this->params->get('blog', 'site') == 'group' || $this->params->get('blog', 'site') == 'both')
			{
				//make sure that the group for each blog post has the right privacy setting
				if (!$gf->get('scope_id'))
				{
					continue;
				}

				$group = $gf->item();
				if (is_object($group))
				{
					$blog_access = GroupHelper::getPluginAccess($group, 'blog');

					if ($blog_access == 'nobody'
					 || ($blog_access == 'registered' && User::isGuest())
					 || ($blog_access == 'members' && !in_array(User::get('id'), $group->get('members'))))
					{
						continue;
					}
				}
				else
				{
					continue;
				}
			}

			$posts[] = $gf;
		}

		$this->posts = $posts;

		require $this->getLayoutPath();
	}

	/**
	 * Display module content
	 * 
	 * @return     void
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
