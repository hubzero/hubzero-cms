<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\System\Models\Mediatracking;
use Components\System\Models\Mediatrackingdetailed;
use App;
use Request;
use User;
use Date;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatracking.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'mediatrackingdetailed.php';

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
		$time       = Request::getInt('time', 0);
		$duration   = Request::getInt('duration', 0);
		$event      = Request::getString('event', 'update');
		$entityId   = Request::getInt('entity_id', 0);
		$entityType = Request::getWord('entity_type', 'resource');
		$detailedId = Request::getInt('detailed_tracking_id', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];

		// Check for entity id
		if (!$entityId)
		{
			echo 'Unable to find entity identifier.';
			return;
		}

		// Load tracking information for user for this entity
		$trackingInformation         = Mediatracking::oneByUserAndObject($entityId, $entityType, User::get('id'));
		$trackingInformationDetailed = Mediatrackingdetailed::oneOrNew($detailedId);

		// Are we creating a new tracking record?
		if (!is_object($trackingInformation) || $trackingInformation->isNew())
		{
			$trackingInformation = Mediatracking::blank();
			$trackingInformation->set(array(
				'user_id'                     => User::get('id'),
				'session_id'                  => $session->getId(),
				'ip_address'                  => $ipAddress,
				'object_id'                   => $entityId,
				'object_type'                 => $entityType,
				'object_duration'             => $duration,
				'current_position'            => $time,
				'farthest_position'           => $time,
				'current_position_timestamp'  => Date::toSql(),
				'farthest_position_timestamp' => Date::toSql(),
				'completed'                   => 0,
				'total_views'                 => 1,
				'total_viewing_time'          => 0
			));
		}
		else
		{
			// Get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->get('current_position');

			// If we have a positive value and its less then our ten second threshold
			// add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->set('total_viewing_time', $trackingInformation->get('total_viewing_time') + $time_viewed);
			}

			// Set the new current position
			$trackingInformation->set('current_position', $time);
			$trackingInformation->set('current_position_timestamp', Date::toSql());

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformation->set('object_duration', $duration);
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformation->current_position > $trackingInformation->farthest_position)
			{
				$trackingInformation->set('farthest_positionset', $time);
				$trackingInformation->set('farthest_position_timestamp', Date::toSql());
			}

			// If event type is start, means we need to increment view count
			if ($event == 'start' || $event == 'replay')
			{
				$trackingInformation->set('total_views', $trackingInformation->get('total_views') + 1);
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformation->set('completed', $trackingInformation->get('completed') + 1);
			}
		}

		// Save detailed tracking info
		if ($event == 'start' || $trackingInformationDetailed->isNew())
		{
			$trackingInformationDetailed->set(array(
				'user_id'                     => User::get('id'),
				'session_id'                  => $session->getId(),
				'ip_address'                  => $ipAddress,
				'object_id'                   => $entityId,
				'object_type'                 => $entityType,
				'object_duration'             => $duration,
				'current_position'            => $time,
				'farthest_position'           => $time,
				'current_position_timestamp'  => Date::toSql(),
				'farthest_position_timestamp' => Date::toSql(),
				'completed'                   => 0
			));
		}
		else
		{
			// Set the new current position
			$trackingInformationDetailed->set('current_position', $time);
			$trackingInformationDetailed->set('current_position_timestamp', Date::toSql());

			// Set the object duration
			if ($duration > 0)
			{
				$trackingInformationDetailed->set('object_duration', $duration);
			}

			// Check to see if we need to set a new farthest position
			if ($trackingInformationDetailed->get('farthest_position')
			 && $trackingInformationDetailed->get('current_position') > $trackingInformationDetailed->get('farthest_position'))
			{
				$trackingInformationDetailed->set('farthest_position', $time);
				$trackingInformationDetailed->set('farthest_position_timestamp', Date::toSql());
			}

			// If event type is end, we need to increment completed count
			if ($event == 'ended')
			{
				$trackingInformationDetailed->set('completed', $trackingInformationDetailed->get('completed') + 1);
			}
		}

		// Save detailed
		$trackingInformationDetailed->save();

		// Save tracking information
		if ($trackingInformation->save())
		{
			$trackingInformation = $trackingInformation->toObject();
			$trackingInformation->detailedId = $trackingInformationDetailed->get('id');
			$this->send($trackingInformation);
		}
	}
}
