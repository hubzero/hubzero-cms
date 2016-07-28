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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Blog\Models\Entry;
use Hubzero\User\Group;

require_once PATH_CORE . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'entry.php';

/**
 * Search blog entries
 */
class plgSearchBlogs extends \Hubzero\Plugin\Plugin
{
	/**
	 * Description for 'IRST_CLASS_CHILDREN'
	 */
	const FIRST_CLASS_CHILDREN = false;

	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$now = Date::toSql();

		$terms = $request->get_term_ar();
		$weight = '(match(be.title, be.content) against (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(be.title LIKE '%$mand%' OR be.content LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(be.title NOT LIKE '%$forb%' AND be.content NOT LIKE '%$forb%')";
		}
		$addtl_where[] = "(be.publish_up <= '$now')";
		$addtl_where[] = "(be.publish_down = '0000-00-00 00:00:00' OR (be.publish_down != '0000-00-00 00:00:00' AND be.publish_down > '$now'))";
		$addtl_where[] = '(be.access IN (0,' . implode(',', User::getAuthorisedViewLevels()) . '))';

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				be.id,
				be.title,
				be.content AS description,
				(CASE WHEN be.scope_id > 0 AND be.scope='group' THEN
					concat('index.php?option=com_groups&cn=', g.cn, '&active=blog&scope=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias)
				WHEN be.scope='member' AND be.scope_id > 0 THEN
					concat('index.php?option=com_members&id=', be.created_by, '&active=blog&task=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias)
				ELSE
					concat('index.php?option=com_blog&year=', extract(year from be.created), '&month=', extract(month from be.created), '&alias=', be.alias)
				END) AS link,
				$weight AS weight,
				'Blog Entry' AS section,
				be.created AS date,
				u.name AS contributors,
				be.created_by AS contributor_ids
			FROM `#__blog_entries` be
			INNER JOIN `#__users` u ON u.id = be.created_by
			LEFT JOIN `#__xgroups` AS g ON g.gidNumber=be.scope_id AND be.scope='group'
			WHERE
				be.state=1 AND
				$weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);

		if (($rows = $rows->to_associative()) instanceof \Components\Search\Models\Basic\Result\Blank)
		{
			return;
		}

		$id_map = array();
		foreach ($rows as $idx => $row)
		{
			$id_map[$row->get('id')] = $idx;
		}

		$comments = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
		 	CASE WHEN bc.anonymous THEN 'Anonymous Comment' ELSE concat('Comment by ', u.name) END AS title,
			bc.content AS description,
			concat('index.php?option=com_members&id=', be.created_by, '&active=blog&task=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias) AS link,
			bc.created AS date,
			'Comments' AS section,
			bc.entry_id
			FROM `#__blog_comments` bc
			INNER JOIN `#__blog_entries` be
				ON be.id = bc.entry_id
			INNER JOIN `#__users` u
				ON u.id = bc.created_by
			WHERE bc.entry_id IN (" . implode(',', array_keys($id_map)) . ")
			ORDER BY bc.created"
		);
		foreach ($comments->to_associative() as $comment)
		{
			$rows->at($id_map[$comment->get('entry_id')])->add_child($comment);
		}

		$results->add($rows);
	}

/************************************************
 *
 * HubSearch Required Methods
 * @author Kevin Wojkovich <kevinw@purdue.edu>
 *
 ***********************************************/

	/****************************
	Query-time / General Methods
	****************************/

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'blog-entry';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	public function onGetModel($type = '')
	{
		if ($type == 'blog-entry')
		{
			return new Entry;
		}
	}
	/*********************
		Index-time methods
	*********************/
	/**
	 * onProcessFields - Set SearchDocument fields which have conditional processing
	 *
	 * @param mixed $type 
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
	public function onProcessFields($type, $row)
	{
		if ($type == 'blog-entry')
		{
			// Determine the author of the Entry
			$user = User::getInstance($row->created_by);
			$authorArr = array();
			array_push($authorArr, $user->name);

			// Instantiate new $fields object
			$fields = new stdClass;

			// Calculate Permissions
			// Public condition
			if ($row->state == 1 && $row->access == 1)
			{
				$fields->access_level = 'public';
			}
			// Registered condition
			elseif ($row->state == 1 && $row->access == 2)
			{
				$fields->access_level = 'registered';
			}
			// Default private
			else
			{
				$fields->access_level = 'private';
			}

			if ($row->scope != 'group')
			{
				$fields->owner_type = 'user';
				$fields->owner = $row->created_by;
			}
			else
			{
				$fields->owner_type = 'group';
				$fields->owner = $row->scope_id;
			}

			// Build out path
			$year = Date::of(strtotime($row->publish_up))->toLocal('Y');
			$month = Date::of(strtotime($row->publish_up))->toLocal('m');
			$alias = $row->alias;

			if ($row->scope == 'site')
			{
				$path = '/blog/' . $year . '/' . $month . '/' . $alias;
			}
			elseif ($row->scope == 'member')
			{
				$path = '/members/'. $row->scope_id  . '/blog/' . $year . '/' . $month . '/' . $alias;
			}
			elseif ($row->scope == 'group')
			{
				$group = Group::getInstance($row->scope_id);

				// Make sure group is valid.
				if (is_object($cn))
				{
					$cn = $group->get('cn');
					$path = '/groups/'. $cn . '/blog/' . $year . '/' . $month . '/' . $alias;
				}
			}

			$fields->url = $path;
			$fields->author = $authorArr;
			$fields->tags = $row->tags('array');

			$fields->title = $row->title;
			$fields->alias = $row->alias;

			// Monkeying around to adapt for console application
			$content = $row->get('content');
			$content = html_entity_decode($content);
			$content = strip_tags($content);
			$fields->fulltext = $content;

			// Format the date for SOLR
			$date = Date::of($row->publish_up)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->publish_up)->format('h:m:s') . 'Z';
			$fields->date = $date;

			$fields->description = html_entity_decode($row->description);

			return $fields;
		}
	}
}
