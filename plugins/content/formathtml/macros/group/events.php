<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once JPATH_ROOT.'/plugins/content/formathtml/macros/group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Events extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group events.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Events()]]</code></li>
								<li><code>[[Group.Events(3)]]</code> - Displays the next 3 group events</li>
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
		// check if we can render
		if (!parent::canRender())
		{
			return \JText::_('[This macro is designed for Groups only]');
		}

		// get args
		$args = $this->getArgs();

		//array of filters
		$filters = array(
			'limit' => (isset($args[0]) && is_numeric($args[0])) ? $args[0] : 3
		);

		//get group events
		$events =  $this->getGroupEvents($this->group, $filters);

		//create the html container
		$html  = '<div class="upcoming_events">';

		//render the events
		$html .= $this->renderEvents($this->group, $events);

		//close the container
		$html .= '</div>';

		//return rendered events
		return $html;
	}

	/**
	 * Get a list of events for a group
	 *
	 * @param      object $group
	 * @param      array  $filters
	 * @return     array
	 */
	private function getGroupEvents($group, $filters = array())
	{
		//instantiate database
		$database = \JFactory::getDBO();

		//build query
		$sql = "SELECT * FROM #__events
				WHERE publish_up >= UTC_TIMESTAMP()
				AND scope=" . $database->quote('group') . "
				AND scope_id=" . $database->Quote($group->get('gidNumber')) . "
				AND state=1";

		//add ordering
		$sql .= " ORDER BY publish_up ASC";

		//do we have a limit set
		if (isset($filters['limit']))
		{
			$sql .= " LIMIT " . $filters['limit'];
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
	private function renderEvents($group, $events)
	{
		$content = '';
		if (count($events) > 0)
		{
			foreach ($events as $event)
			{
				//build link
				$link = \JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=details&event_id=' . $event->id);

				//build date
				$date = '';
				$publishUp   = $event->publish_up;
				$publishDown = $event->publish_down;

				if (date("z", strtotime($publishUp)) == date("z", strtotime($publishDown)))
				{
					$date  = \JHTML::_('date', $publishUp, 'm/d/Y @ g:i a');
					$date .= ' &mdash; ' . \JHTML::_('date', $publishDown, 'g:i a');
				}
				else if (isset($event->publish_down) && $event->publish_down != '' && $event->publish_down != '0000-00-00 00:00:00')
				{
					$date  = \JHTML::_('date', $publishUp, 'm/d/Y @ g:i a');
					$date .= ' &mdash; ' . \JHTML::_('date', $publishDown, 'm/d/Y @ g:i a');
				}
				else
				{
					$date  = \JHTML::_('date', $publishUp, 'm/d/Y @ g:i a');
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
			$content .= '<p>Currently there are no upcoming group events. Add an event by <a href="' . \JRoute::_('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=add') . '">clicking here.</a></p>';
		}

		return $content;
	}
}

