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
 *
 */

namespace Components\System\Api\Controllers;

use Hubzero\Component\ApiController;
use App;
use Request;
use User;
use Date;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mediatracking.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'mediatrackingdetailed.php';

/**
 * API controller class for system tasks
 */
class Mediav1_0 extends ApiController
{
	/**
	 * Records media tracking info
	 *
	 * @apiMethod POST
	 * @apiUri    /system/media/tracking
	 * @return    void
	 */
	public function trackingTask()
	{
		// Instantiate objects
		$database = App::get('db');
		$session  = App::get('session');

		// Get request vars
		$time       = Request::getVar('time', 0);
		$duration   = Request::getVar('duration', 0);
		$event      = Request::getVar('event', 'update');
		$entityId   = Request::getVar('entity_id', 0);
		$entityType = Request::getVar('entity_type', 'resource');
		$detailedId = Request::getVar('detailed_tracking_id', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];

		// Check for entity id
		if (!$entityId)
		{
			echo 'Unable to find entity identifier.';
			return;
		}

		// Instantiate new media tracking object
		$mediaTracking         = new \Components\System\Tables\MediaTracking($database);
		$mediaTrackingDetailed = new \Components\System\Tables\MediaTrackingDetailed($database);

		// Load tracking information for user for this entity
		$trackingInformation         = $mediaTracking->getTrackingInformationForUserAndResource(User::get('id'), $entityId, $entityType);
		$trackingInformationDetailed = $mediaTrackingDetailed->loadByDetailId($detailedId);

		// Are we creating a new tracking record?
		if (!is_object($trackingInformation))
		{
			$trackingInformation                              = new \stdClass;
			$trackingInformation->user_id                     = User::get('id');
			$trackingInformation->session_id                  = $session->getId();
			$trackingInformation->ip_address                  = $ipAddress;
			$trackingInformation->object_id                   = $entityId;
			$trackingInformation->object_type                 = $entityType;
			$trackingInformation->object_duration             = $duration;
			$trackingInformation->current_position            = $time;
			$trackingInformation->farthest_position           = $time;
			$trackingInformation->current_position_timestamp  = Date::toSql();
			$trackingInformation->farthest_position_timestamp = Date::toSql();
			$trackingInformation->completed                   = 0;
			$trackingInformation->total_views                 = 1;
			$trackingInformation->total_viewing_time          = 0;
		}
		else
		{
			// Get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->current_position;

			// If we have a positive value and its less then our ten second threshold
			// add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->total_viewing_time += $time_viewed;
			}

			// Set the new current position
			$trackingInformation->current_position           = $time;
			$trackingInformation->current_position_timestamp = Date::toSql();

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformation->object_duration = $duration;
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformation->current_position > $trackingInformation->farthest_position)
			{
				$trackingInformation->farthest_position           = $time;
				$trackingInformation->farthest_position_timestamp = Date::toSql();
			}

			// If event type is start, means we need to increment view count
			if ($event == 'start' || $event == 'replay')
			{
				$trackingInformation->total_views++;
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformation->completed++;
			}
		}

		// Save detailed tracking info
		if ($event == 'start' || !$trackingInformationDetailed)
		{
			$trackingInformationDetailed                              = new \stdClass;
			$trackingInformationDetailed->user_id                     = User::get('id');
			$trackingInformationDetailed->session_id                  = $session->getId();
			$trackingInformationDetailed->ip_address                  = $ipAddress;
			$trackingInformationDetailed->object_id                   = $entityId;
			$trackingInformationDetailed->object_type                 = $entityType;
			$trackingInformationDetailed->object_duration             = $duration;
			$trackingInformationDetailed->current_position            = $time;
			$trackingInformationDetailed->farthest_position           = $time;
			$trackingInformationDetailed->current_position_timestamp  = Date::toSql();
			$trackingInformationDetailed->farthest_position_timestamp = Date::toSql();
			$trackingInformationDetailed->completed                   = 0;
		}
		else
		{
			// Set the new current position
			$trackingInformationDetailed->current_position           = $time;
			$trackingInformationDetailed->current_position_timestamp = Date::toSql();

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformationDetailed->object_duration = $duration;
			}

			// Check to see if we need to set a new farthest position
			if (isset($trackingInformationDetailed->farthest_position) && $trackingInformationDetailed->current_position > $trackingInformationDetailed->farthest_position)
			{
				$trackingInformationDetailed->farthest_position           = $time;
				$trackingInformationDetailed->farthest_position_timestamp = Date::toSql();
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformationDetailed->completed++;
			}
		}

		// Save detailed
		$mediaTrackingDetailed->save($trackingInformationDetailed);

		// Save tracking information
		if ($mediaTracking->save($trackingInformation))
		{
			if (!isset($trackingInformation->id))
			{
				$trackingInformation->id = $mediaTracking->id;
			}
			$trackingInformation->detailedId = $mediaTrackingDetailed->id;
			$this->send($trackingInformation);
		}
	}
}