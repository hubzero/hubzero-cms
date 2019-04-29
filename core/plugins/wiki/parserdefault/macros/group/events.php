<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Wiki\Parserdefault\Macros\GroupMacro;

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
			return \Lang::txt('[This macro is designed for Groups only]');
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
		$database = \App::get('db');

		//build query
		$sql = "SELECT * FROM `#__events`
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
	 * @param      array  $group  Array of group events
	 * @param      array  $events Array of events
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
				$link = \Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=details&event_id=' . $event->id);

				//build date
				$date = '';
				$publishUp   = $event->publish_up;
				$publishDown = $event->publish_down;

				if (date("z", strtotime($publishUp)) == date("z", strtotime($publishDown)))
				{
					$date  = \Date::of($publishUp)->toLocal('m/d/Y @ g:i a');
					$date .= ' &mdash; ' . \Date::of($publishDown)->toLocal('g:i a');
				}
				else if (isset($event->publish_down) && $event->publish_down && $event->publish_down != '0000-00-00 00:00:00')
				{
					$date  = \Date::of($publishUp)->toLocal('m/d/Y @ g:i a');
					$date .= ' &mdash; ' . \Date::of($publishDown)->toLocal('m/d/Y @ g:i a');
				}
				else
				{
					$date  = \Date::of($publishUp)->toLocal('m/d/Y @ g:i a');
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
			$content .= '<p>Currently there are no upcoming group events. Add an event by <a href="' . \Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=add') . '">clicking here.</a></p>';
		}

		return $content;
	}
}
