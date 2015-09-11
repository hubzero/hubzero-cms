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
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for dipslaying group events
 */
class GroupEventMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Displays group events";
		$txt['html'] = '<p>Displays group events.</p>';
		$txt['html'] = '<p>Examples:</p>
							<ul>
								<li><code>[[Groupevent(number=3)]]</code> - Displays the next three group events</li>
								<li><code>[[Groupevent(title=Group Events, number=2)]]</code> - Adds title above event list. Displays 2 events.</li>
								<li><code>[[Groupevent(id=123)]]</code> - Displays single group event with ID # 123</li>
							</ul>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		//get the args passed in
		$args = explode(',', $this->args);

		//parse each arg into key value pair
		foreach ($args as $a)
		{
			$kv[] = explode('=', trim($a));
		}

		//set final args
		foreach ($kv as $k)
		{
			$arg[$k[0]] = (isset($k[1])) ? $k[1] : $k[0];
		}

		//set a default
		//$default_events = 3;

		//get the user defined # of events
		//$num_events = (isset($arg['number']) && is_numeric($arg['number']) && $arg['number'] > 0) ? $arg['number'] : $default_events;

		//get the group
		$cn = Request::getVar('cn');

		//get the group object based on gid
		$group = \Hubzero\User\Group::getInstance($cn);

		//check to make sure we have a valid group
		if (!is_object($group))
		{
			return '[This macro is designed for Groups only]';
		}

		//array of filters
		$filters = array(
			'id' => (isset($arg['id'])) ? $arg['id'] : null,
			'limit' => (isset($arg['number'])) ? $arg['number'] : 3
		);

		//get group events
		$events =  $this->getGroupEvents( $group, $filters );

		//create the html container
		$html  = '<div class="upcoming_events">';

		//display the title
		$html .= (isset($arg['title']) && $arg['title'] != '') ? '<h3>' . $arg['title'] . '</h3>' : '';

		//render the events
		$html .= $this->renderEvents( $group, $events );

		//close the container
		$html .= '</div>';

		//return rendered events
		return $html;
	}

	private function getGroupEvents( $group, $filters = array() )
	{
		//instantiate database
		$database = App::get('db');

		//build query
		$sql = "SELECT * FROM #__events
				WHERE publish_up >= UTC_TIMESTAMP()
				AND scope=" . $database->quote('group') . "
				AND scope_id=" . $database->Quote($group->get('gidNumber')) . "
				AND state=1";

		//do we have an ID set
		if (isset($filters['id']))
		{
			$sql .= " AND id=" . $database->Quote( $filters['id'] );
		}

		//add ordering
		$sql .= " ORDER BY publish_up ASC";

		//do we have a limit set
		if (isset($filters['number']))
		{
			$sql .= " LIMIT " . $filters['number'];
		}

		//return result
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	/**
	 * Render the events
	 *
	 * @param      array     Array of group events
	 * @return     string
	 */
	private function renderEvents( $group, $events )
	{
		$content = '';
		if (count($events) > 0)
		{
			foreach ($events as $event)
			{
				//build link
				$link = Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=details&event_id=' . $event->id);

				//build date
				$date = '';
				$publishUp   = strtotime($event->publish_up);
				$publishDown = strtotime($event->publish_down);
				if (date("z", $publishUp) == date("z", $publishDown))
				{
					$date  = Date::of($publishUp)->format('m/d/Y @ g:i a');
					$date .= ' &mdash; ' . Date::of($publishDown)->format('g:i a');
				}
				else if (isset($event->publish_down) && $event->publish_down != '' && $event->publish_down != '0000-00-00 00:00:00')
				{
					$date  = Date::of($publishUp)->format('m/d/Y @ g:i a');
					$date .= ' &mdash; <br />&nbsp;&nbsp;&nbsp;' . Date::of($publishDown)->format('m/d/Y @ g:i a');
				}
				else
				{
					$date  = Date::of($publishUp)->format('m/d/Y @ g:i a');
				}

				//shorten content
				$details = nl2br($event->content);
				if (strlen($details) > 150)
				{
					$details = substr($details, 0, 150) . '...';
				}

				//create list
				$content .= '<div class="event">';
				$content .= '<strong><a class=" title" href="' . $link . '">' . stripslashes($event->title) . '</a></strong>';
				$content .= '<br /><span class="date">' . $date . '</span>';
				$content .= '<br /><span class="details">' . $details . '</span>';
				$content .= '</div><br />';
			}
		}
		else
		{
			$content .= '<p>Currently there are no upcoming group events. Add an event by <a href="' . Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=add') . '">clicking here.</a></p>';
		}

		return $content;
	}
}
