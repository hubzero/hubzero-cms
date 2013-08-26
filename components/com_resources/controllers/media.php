<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Resources controller class
 */
class ResourcesControllerMedia extends Hubzero_Controller
{
	
	public function trackingTask()
	{
		//include need media tracking library
		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.php';
		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'media.tracking.detailed.php';
		
		//instantiate objects
		$juser    =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$session  =& JFactory::getSession();
		
		//get request vars
		$time       = JRequest::getVar('time', 0);
		$duration   = JRequest::getVar('duration', 0);
		$event      = JRequest::getVar('event', 'update');
		$resourceid = JRequest::getVar('resourceid', 0);
		$detailedId = JRequest::getVar('detailedTrackingId', 0);
		$ipAddress  = $_SERVER['REMOTE_ADDR'];
		
		//check for resource id
		if(!$resourceid)
		{
			echo 'Unable to find resource identifier.';
			return;
		}
		
		//instantiate new media tracking object
		$mediaTracking         = new ResourceMediaTracking( $database );
		$mediaTrackingDetailed = new ResourceMediaTrackingDetailed( $database );
		
		//load tracking information for user for this resource
		$trackingInformation         = $mediaTracking->getTrackingInformationForUserAndResource( $juser->get('id'), $resourceid );
		$trackingInformationDetailed = $mediaTrackingDetailed->loadByDetailId( $detailedId );
		
		//are we creating a new tracking record
		if(!is_object($trackingInformation))
		{
			$trackingInformation                              = new stdClass;
			$trackingInformation->user_id                     = $juser->get('id');
			$trackingInformation->session_id                  = $session->getId();
			$trackingInformation->ip_address                  = $ipAddress; 
			$trackingInformation->object_id                   = $resourceid;
			$trackingInformation->object_type                 = 'resource';
			$trackingInformation->object_duration             = $duration;
			$trackingInformation->current_position            = $time;
			$trackingInformation->farthest_position           = $time;
			$trackingInformation->current_position_timestamp  = date('Y-m-d H:i:s');
			$trackingInformation->farthest_position_timestamp = date('Y-m-d H:i:s');
			$trackingInformation->completed                   = 0;
			$trackingInformation->total_views                 = 1;
			$trackingInformation->total_viewing_time          = 0;
		}
		else
		{
			//get the amount of video watched from last tracking event
			$time_viewed = (int)$time - (int)$trackingInformation->current_position;
			
			//if we have a positive value and its less then our ten second threshold
			//add viewing time to total watched time
			if ($time_viewed < 10 && $time_viewed > 0)
			{
				$trackingInformation->total_viewing_time += $time_viewed;
			}
			
			//set the new current position
			$trackingInformation->current_position           = $time;
			$trackingInformation->current_position_timestamp = date('Y-m-d H:i:s');
			
			//set the object duration
			if($duration > 0)
			{
				$trackingInformation->object_duration = $duration;
			}
			
			//check to see if we need to set a new farthest position
			if($trackingInformation->current_position > $trackingInformation->farthest_position)
			{
				$trackingInformation->farthest_position           = $time;
				$trackingInformation->farthest_position_timestamp = date('Y-m-d H:i:s');
			}
			
			//if event type is start, means we need to increment view count
			if($event == 'start' || $event == 'replay')
			{
				$trackingInformation->total_views++;
			}
			
			//if event type is end, we need to increment completed count
			if($event == 'ended')
			{
				$trackingInformation->completed++;
			}
		}
		
		// save detailed tracking info
		if($event == 'start')
		{
			$trackingInformationDetailed                              = new stdClass;
			$trackingInformationDetailed->user_id                     = $juser->get('id');
			$trackingInformationDetailed->session_id                  = $session->getId();
			$trackingInformationDetailed->ip_address                  = $ipAddress; 
			$trackingInformationDetailed->object_id                   = $resourceid;
			$trackingInformationDetailed->object_type                 = 'resource';
			$trackingInformationDetailed->object_duration             = $duration;
			$trackingInformationDetailed->current_position            = $time;
			$trackingInformationDetailed->farthest_position           = $time;
			$trackingInformationDetailed->current_position_timestamp  = date('Y-m-d H:i:s');
			$trackingInformationDetailed->farthest_position_timestamp = date('Y-m-d H:i:s');
			$trackingInformationDetailed->completed                   = 0;
		}
		else
		{
			//set the new current position
			$trackingInformationDetailed->current_position           = $time;
			$trackingInformationDetailed->current_position_timestamp = date('Y-m-d H:i:s');
			
			//set the object duration
			if($duration > 0)
			{
				$trackingInformationDetailed->object_duration = $duration;
			}
			
			//check to see if we need to set a new farthest position
			if($trackingInformationDetailed->current_position > $trackingInformationDetailed->farthest_position)
			{
				$trackingInformationDetailed->farthest_position           = $time;
				$trackingInformationDetailed->farthest_position_timestamp = date('Y-m-d H:i:s');
			}
			
			//if event type is end, we need to increment completed count
			if($event == 'ended')
			{
				$trackingInformationDetailed->completed++;
			}
		}
		
		//save detailed
		$mediaTrackingDetailed->save( $trackingInformationDetailed );
		
		//save tracking information
		if( $mediaTracking->save($trackingInformation) )
		{
			if(!isset($trackingInformation->id))
			{
				$trackingInformation->id = $mediaTracking->id;
			}
			$trackingInformation->detailedId = $mediaTrackingDetailed->id;
			echo json_encode( $trackingInformation );
		}
	}
}