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

namespace Modules\QubesEvents;

use Hubzero\Module\Module;

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/**
 * Mod_Qubes_Events helper class
 */
class Helper extends Module
{
	/**
	 * Get the list of events
	 *
	 * @return retrieved rows
	 */
	private function _getList()
	{
		$db = \App::get('db');

		$events = array();
		$eventsRaw = array();

		$limit = $this->params->get('limit');

		/* Events from a category


		include_once(JPATH_BASE . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'event.php');

		$appEnv = JFactory::getConfig()->get('application_env');
		$filters['catid'] = '22';
		if ($appEnv == 'development')
		{
			$filters['catid'] = '18';
		}
		$filters['scope'] = 'event';

		// Retrieve records
		$ee = new EventsEvent($db);
		$response = @$ee->getRecords($filters);
		//print_r($response); die;

		*/

		include_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'tags.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'event.php');

		// Get all events tagged as needed

		\Plugin::import('tags', 'events');

		include_once(PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');
		$tagobj = \Components\Tags\Models\Tag::oneByTag('qubeshappenings');
		$tags = array($tagobj);

		$response = \Event::trigger('tags.onTagView', array($tags, 0, 0, '', 'events'));
		$response = $response[0]['results'];

		// order by time
		usort($response, array($this, 'date_compare'));

		//print_r($response); die;

		foreach ($response as $event)
		{
			//print_r($event);
			//die;
		}

		foreach ($response as $event)
		{
			//$cloud = new \Components\Events\Models\Tags($res->id);
			$eventValid = true;

			// skip events in the past
			if(isset($event->publish_down))
			{
				$checkDate = strtotime($event->publish_down);
			}
			else {
				$checkDate = strtotime($event->publish_up);
			}
			if(time() > $checkDate) {
				continue;
			}

			$cloud = new \Components\Events\Models\Tags($event->id);
			$listoftags = $cloud->tags();

			// Get the website
			$eme = \Components\Events\Models\Event::getInstance($event->id);
			$event->applyUrl = $eme->get('extra_info');

			foreach($listoftags as $tag) {

				$rt = $tag->get('raw_tag');
				//echo $rt; echo ' ';
				if (substr($rt, 0, 6) == 'group-')
				{
					$group = substr($rt, 6);
					$group = \Hubzero\User\Group::getInstance($group);

					if (empty($group))
					{
						$eventValid = false;
					}
					else
					{
						$g = new \stdClass();
						$g->cn = $group->get('cn');
						$g->gidNumber = $group->get('gidNumber');
						$g->logo = $group->getLogo();
						//print_r($g); die;

						$event->group = $g;
					}
				}
				elseif (substr($rt, 0, 6) == 'event-')
				{
					$event->type = substr($rt, 6);

					$event->type_name = ucfirst($event->type);
					if ($event->type == 'fmn')
					{
						$event->type_name = 'Faculty Mentoring Network';
					}
				}
				elseif (substr($rt, 0, 7) == 'status-')
				{
					$event->status = substr($rt, 7);
				}
			}

			if (!empty($event->type) && $eventValid)
			{
				// Check if it is happening now
				if(time() > strtotime($event->publish_up) && time() < strtotime($event->publish_down)) {
					$event->status = 'now';
				}

				if (empty($event->status))
				{
					$event->status = 'open';
				}

				$events[$event->type][$event->status][] = $event;
				$eventsRaw[] = $event;
			}
		}

		// Put events in proper order (manual)
		$eventsOrdered = array();
		$idsUsed = array();

		// do the most important

		//(i)  Show Workshops happening now
		if (!empty($events['workshop']['now'])) {
			$priorityGroup = $events['workshop']['now'];

			foreach($priorityGroup as $e) {
				$eventsOrdered[] = $e;
				$idsUsed[] = $e->id;
			}
		}
		//(ii)  Show Faculty Mentoring Networks that have open registration
		if (!empty($events['fmn']['open'])) {
			$priorityGroup = $events['fmn']['open'];
			foreach($priorityGroup as $e) {
				$eventsOrdered[] = $e;
				$idsUsed[] = $e->id;
			}
		}
		//(iii)  Show Workshops that have open registration
		if (!empty($events['workshop']['open'])) {
			$priorityGroup = $events['workshop']['open'];
			foreach($priorityGroup as $e) {
				$eventsOrdered[] = $e;
				$idsUsed[] = $e->id;
			}
		}
		//(iv)  Show Faculty Mentoring Networks happening now
		if (!empty($events['fmn']['now'])) {
			$priorityGroup = $events['fmn']['now'];
			foreach($priorityGroup as $e) {
				$eventsOrdered[] = $e;
				$idsUsed[] = $e->id;
			}
		}

		// do the rest
		foreach ($eventsRaw as $e)
		{
			if (!in_array($e->id, $idsUsed))
			{
				$eventsOrdered[] = $e;
			}
		}

		//print_r($eventsOrdered); die;

		if(!empty($limit) && is_numeric($limit))
		{
			$eventsOrdered = array_slice($eventsOrdered, 0, $limit);
		}

		return $eventsOrdered;
	}

	/**
	 * Display method
	 * Used to add CSS for each slide as well as the javascript file(s) and the parameterized function
	 *
	 * @return void
	 */
	public function display()
	{
		$this->css();


		// Get the billboard slides
		$this->events = $this->_getList();

		parent::display();
	}

	private function date_compare($a, $b)
	{
		$t1 = strtotime($a->publish_up);
		$t2 = strtotime($b->publish_up);
		return $t1 - $t2;
	}
}
