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
	 * onGetModel 
	 * 
	 * @param string $type 
	 * @access public
	 * @return void
	 */
	public function onGetModel($type = '')
	{
		if ($type == 'event')
		{
			return new CalEvent;
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
	public function onProcessFields($type, $row, &$db)
	{
		if ($type == 'event')
		{
			// Instantiate new $fields object
			$fields = new stdClass;

			// Format the date for SOLR
			$date = Date::of($row->publish_up)->format('Y-m-d');
			$date .= 'T';
			$date .= Date::of($row->publish_up)->format('h:m:s') . 'Z';
			$fields->date = $date;

			// Clean up the title
			$fields->title = $row->title;

			$fields->fulltext = $row->content;
			$fields->location = $row->adress_info;

			// Permissions and other scope-based parameter
			if ($row->scope == 'group')
			{
				$fields->access_level = 'private';
				$fields->owner_type = 'group';
				$fields->owner = $row->scope_id;

				$group = \Hubzero\User\Group::getInstance($row->scope_id);
				if (isset($group) && is_object($group))
				{
					$groupCN = $group->get('cn');
					$fields->url = '/groups/' . $groupCN . '/calendar/details/' . $row->id;
				}
			}
			elseif ($row->scope == 'event' && $row->approved == 1 && $row->state == 1)
			{
				$fields->access_level = 'public';
				$fields->url = '/events/details/' . $row->id;
			}
			else
			{
				$fields->access_level = 'private';
				$fields->owner_type = 'user';
				$fields->owner = $row->created_by;
				$fields->url = '/events/details/' . $row->id;
			}

			return $fields;
		}
	}
}

