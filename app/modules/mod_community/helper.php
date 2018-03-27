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

namespace Modules\Community;

use Hubzero\Module\Module;

/**
 * Mod_Community helper class, used to query stuff
 */
class Helper extends Module
{
	private function getAll()
	{
		// There is a lot of stuff to fetch

		$db = \App::get('db');

		$limit = 24;

		$res = array();
		$now = \Date::toSql();

		// 1. ***************
		// most popular public projects. Public projects with the most activity within last 6 month
		$query = 'SELECT p.id, p.alias, p.title, projectid, COUNT(projectid) activityItems FROM 
			(SELECT * FROM `#__project_activity` a WHERE a.recorded > DATE_SUB(\'' . $now . '\', INTERVAL 6 MONTH)) a 
			LEFT JOIN `#__projects` p ON p.id = a.projectid 
			WHERE p.private = 0 AND p.state = 1 GROUP BY a.projectid ORDER BY activityItems DESC LIMIT ' . $limit;
		$db->setQuery($query);
		$res['project'] = $db->loadObjectList();

		// 2. ***************
		// most popular groups. Open published groups with the most members
		$query = 'SELECT m.gidNumber, g.cn, g.description as title, COUNT(m.gidNumber) members FROM `#__xgroups_members` m LEFT JOIN `#__xgroups` g ON g.gidNumber = m.gidNumber 
		WHERE g.type = 1 AND g.published = 1 AND g.join_policy = 0 GROUP BY m.gidNumber ORDER BY members DESC LIMIT ' . $limit;
		$db->setQuery($query);
		$res['group'] = $db->loadObjectList();

		// 3. ***************
		// latest public blogs (both sitewide and members)
		$query = "SELECT b.id, b.title, b.created as publish_up, b.scope, b.alias, g.cn, b.created_by, IF(b.scope = 'group' AND g.gidNumber IS NULL, 'n', 'y') AS include
					FROM `#__blog_entries` b
					LEFT JOIN `#__xgroups` g ON g.`gidNumber` = b.scope_id AND b.scope='group'
					WHERE state = 1 AND (('" . $now . "' BETWEEN publish_up AND publish_down) OR ('" . $now . "' > publish_up AND publish_down = '0000-00-00 00:00:00')) 
					AND (join_policy = 0 OR join_policy IS NULL) AND (published = 1 OR published IS NULL) HAVING include = 'y'
					ORDER BY publish_up DESC LIMIT " . $limit;
		$db->setQuery($query);
		$res['blog'] = $db->loadObjectList();

		// 4. ***************
		// most recently updated forum discussions from site and open published groups
		// subquery selects newest from each parent that has children (basically newest parent updates)
		$query = "SELECT c.alias AS c_alias, s.alias AS s_alias, p.id, p.title, s.scope, g.cn, COALESCE(pp.replied, p.created) AS publish_up, IF(p.scope = 'group', g.`join_policy`, NULL) AS join_policy, IF(p.scope = 'group', g.`published`, NULL) AS published 
		FROM `#__forum_posts` p 
		LEFT JOIN `#__forum_categories` c ON c.id = p.category_id
LEFT JOIN `#__forum_sections` s ON s.id = c.section_id
		LEFT JOIN (SELECT parent, MAX(created) AS replied FROM `#__forum_posts` WHERE state = 1 AND parent > 0 GROUP BY parent) pp ON p.id = pp.parent
		LEFT JOIN `#__xgroups` g ON g.`gidNumber` = p.scope_id
		WHERE p.parent < 1 AND p.state = 1 AND (join_policy = 0 OR join_policy IS NULL) AND (published = 1 OR published IS NULL) ORDER BY COALESCE(replied, created) DESC LIMIT " . $limit;
		$db->setQuery($query);
		$res['discussion'] = $db->loadObjectList();

		// 5. ***************
		// most followed public user and open published groups collections
		$query = "SELECT f.following_id, c.object_type, c.alias, c.object_id, g.cn as group_alias, COUNT(f.following_id) as followers, IF(c.object_type = 'group', g.`join_policy`, NULL) AS join_policy, IF(c.object_type = 'group', g.`published`, NULL) AS published, c.title, IF(c.object_type = 'group' AND g.gidNumber IS NULL, 'n', 'y') AS include
		FROM `#__collections_following` f 
		LEFT JOIN `#__collections` c ON f.following_id = c.id 
		LEFT JOIN `#__xgroups` g ON g.`gidNumber` = c.object_id
		WHERE f.following_type = 'collection' AND (join_policy = 0 OR join_policy IS NULL) AND (published = 1 OR published IS NULL) AND c.access = 0 GROUP BY f.following_id HAVING include = 'y' LIMIT " . $limit;
		$db->setQuery($query);
		$res['collection'] = $db->loadObjectList();

		// 6. ***************
		// questions with the most recent activity (created or replied)
		$query = "SELECT q.id, q.subject as title, COALESCE(r.replied, q.created) AS publish_up FROM `#__answers_questions` q
		LEFT JOIN (SELECT `question_id`, MAX(created) AS replied FROM `#__answers_responses` WHERE state = 0 GROUP BY question_id) r ON r.question_id = q.id
		WHERE q.state = 0 LIMIT " . $limit;
		$db->setQuery($query);
		$res['question'] = $db->loadObjectList();

		$sourcesQty = 6;

		$featured = array();

		foreach ($res as $key => $val)
		{
			if (is_array($val))
			{
				$chunk = array_splice($val, 0, ($limit / $sourcesQty));
				foreach ($chunk as $row)
				{
					$featured[] = array('type' => $key, 'item' => $row);
				}
			}
			else
			{
				$val = array();
			}
			$res[$key] = $val;
		}

		$stop = false;
		while (sizeof($featured) < $limit && !$stop)
		{
			$emptyContainers = 0;
			foreach ($res as $key => $val)
			{
				if (sizeof($featured) >= $limit)
				{
					break;
				}
				if (is_array($val) && !empty($val))
				{
					$chunk = array_splice($val, 0, 1);
					foreach ($chunk as $row)
					{
						$featured[] = array('type' => $key, 'item' => $row);
					}
					$res[$key] = $val;
				}
				else
				{
					$emptyContainers++;
					if ($emptyContainers >= sizeof($res))
					{
						$stop = true;
					}
				}
			}
		}

		shuffle($featured);
		return $featured;
	}

	/**
	 * Display method
	 * 
	 * @return void
	 */
	public function display()
	{
		$this->css()
		     ->js();

		\Document::addScript('/app/templates' . DS . \App::get('template')->template . DS . 'js' . DS . 'masonry.pkgd.min.js');
		\Document::addScript('/app/templates' . DS . \App::get('template')->template . DS . 'js' . DS . 'fit.js');

		$this->featured = $this->getAll();

		parent::display();
	}
}
