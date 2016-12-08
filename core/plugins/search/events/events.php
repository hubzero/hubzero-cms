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

use Components\Events\Models\Orm\Event as CalEvent;

require_once Component::path('com_events') . DS . 'models' . DS . 'orm' . DS . 'event.php';

/**
 * Search events
 */
class plgSearchEvents extends \Hubzero\Plugin\Plugin
{
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
		$terms = $request->get_term_ar();
		$weight = 'match(e.title, e.content) against(\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(e.title LIKE '%$mand%' OR e.content LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(e.title NOT LIKE '%$forb%' AND e.content NOT LIKE '%$forb%')";
		}

		// Commenting out Access check as it was never used and column was removed from table
		// during events refactoring
		//
		// @author Chris Smoak
		// @date   4/20/2014
		//
		// $addtl_where[] = '(e.access IN (' . implode(',', User::getAuthorisedViewLevels()) . '))';

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				e.title,
				e.content AS description,
				e.scope,
				e.scope_id,
				concat('index.php?option=com_events&task=details&id=', e.id) AS link,
				$weight AS weight,
				publish_up AS date,
				'Events' AS section
			FROM `#__events` e
			WHERE
				state = 1 AND
				approved AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);

		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}

			// check group perms
			if ($row->scope == 'group')
			{
				// load group
				$group = \Hubzero\User\Group::getInstance($row->scope_id);

				// make sure we found one
				if (!$group)
				{
					continue;
				}

				// get group calendar access
				$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'calendar');

				// is calendar off
				// is calendar for registered users & not logged in
				// is calendar for members only and we are not a member
				if ($access == 'nobody'
					|| ($access == 'registered' && User::isGuest())
					|| ($access == 'members' && !in_array(User::get('id'), $group->get('members'))))
				{
					continue;
				}
			}

			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}

	/**
	 * Short description for 'onBeforeSearchRenderEvents'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $res Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function onBeforeSearchRenderEvents($res)
	{
		$date = $res->get('date');
		return
			'<p class="event-date">
			<span class="month">' . date('M', $date) . '</span>
			<span class="day">' . date('d', $date) . '</span>
			<span class="year">' . date('Y', $date) . '</span>
			</p>';
	}

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
		$hubtype = 'event';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'event')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__events WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the (start) date of the event
				// Format the date for SOLR
				$date = Date::of($row->publish_up)->format('Y-m-d');
				$date .= 'T';
				$date .= Date::of($row->publish_up)->format('h:m:s') . 'Z';

				// Get the name of the author
				$sql1 = "SELECT name FROM #__users WHERE id={$row->created_by};";
				$author = $db->setQuery($sql1)->query()->loadResult();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'events';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				if ($row->scope == 'event')
				{
					$path = '/events/details/' . $row->id;
				}
				elseif ($row->scope == 'group')
				{
					$group = \Hubzero\User\Group::getInstance($row->scope_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/calendar/details/' . $row->id;
					}
					else
					{
						$path = '';
					}
				}

				// Public condition
				if ($row->state == 1 && $row->approved == 1 && $row->scope != 'group')
				{
					$access_level = 'public';
				}
				else
				{
					// Default private
					$access_level = 'private';
				}

				if ($row->scope != 'group')
				{
					$owner_type = 'user';
					$owner = $row->created_by;
				}
				else
				{
					$owner_type = 'group';
					$owner = $row->scope_id;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $row->content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->title = $title;
				$record->description = $description;
				$record->author = array($author);
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__events;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return array($type => $ids);
			}
		}
	}
}

