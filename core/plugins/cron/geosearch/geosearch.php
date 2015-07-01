<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	 hubzero-cms
 * @author		Kevin Wojkovic <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license	 http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Cron plugin for support tickets
 */
class plgCronGeosearch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return	array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'	 => 'getLocationData',
				'label'	=> Lang::txt('PLG_CRON_GEOSEARCH_GET_LOCATION_DATA'),
				'params' => ''
			),
			array(
				'name'	 => 'sendGroupAnnouncements',
				'label'	=> Lang::txt('PLG_CRON_GEOSEARCH_UPDATE_LOCATION_DATA'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * populate the geosearch markers table
	 *
	 * @param	 object	 $job	\Components\Cron\Models\Job
	 * @return	boolean
	 */
	public function getLocationData(\Components\Cron\Models\Job $job)
	{
		$this->database = App::get('db');
		require_once(PATH_CORE . DS . 'components' . DS .'com_members' . DS . 'tables' . DS . 'profile.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_geosearch' . DS . 'tables' . DS . 'geosearchmarkers.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_jobs' . DS . 'tables' . DS . 'job.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_events' . DS . 'tables' . DS . 'event.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_members' . DS . 'tables' . DS . 'organization.php');




		// get current markers
		$markers = new \Components\Geosearch\Tables\GeosearchMarkers($this->database);
		$markers = $markers->getMarkers(array(), 'array');

		// user profiles
		$objProfile = new \Components\Members\Tables\Profile($this->database);
		$profiles = $objProfile->selectWhere('uidNumber', 'public=1');

		// jobs
		$objJob = new \Components\Jobs\Tables\Job($this->database);
		$jobs = $objJob->get_openings();

		// events
		$objEvents = new \Components\Events\Tables\Event($this->database);
		$events = $objEvents->getEvents('year', array('year' => date('Y'), 'category' => 0));

		// organizations
		$objOrganizations = new	\Components\Members\Tables\Organization($this->database);
		$organizations = $objOrganizations->find('all');

		// create some containers
		$markerMemberIDs = array();
		$markerJobIDs = array();
		$markerEventIDs = array();
		$markerOrganizationIDs = array();

		//separate IDs by scope
		foreach ($markers as $marker)
		{
			switch ($marker->scope)
			{
				case "member":
					array_push($markerMemberIDs, $marker->scope_id);
				break;

				case "job":
					array_push($markerJobIDs, $marker->scope_id);
				break;

				case "event":
					array_push($markerEventIDs, $marker->scope_id);
				break;

				case "organization":
					array_push($markerOrganizationIDs, $marker->scope_id);
				break;


			}
		}

		// removes existing ids, adds non-existant ids [profile]
		foreach ($profiles as $profile)
		{
			if (($key = array_search($profile->uidNumber, $markerMemberIDs)) !== false)
			{
				unset($markerMemberIDs[$key]);
			}
			else
			{
				array_push($markerMemberIDs, $profile->uidNumber);
			}
		}

		// removes existing ids, adds non-existant ids [job]
		foreach ($jobs as $job)
		{
			if (($key = array_search($job->code, $markerJobIDs)) !== false)
			{
				unset($markerJobIDs[$key]);
			}
			else
			{
				array_push($markerJobIDs, $job->code);
			}
		}

		// removes existing ids, adds non-existant ids [event]
		foreach ($events as $event)
		{
			if (($key = array_search($event->id, $markerEventIDs)) !== false)
			{
				unset($markerEventIDs[$key]);
			}
			else
			{
				array_push($markerEventIDs, $event->id);
			}
		}

		// removes existing ids, adds non-existant ids
		foreach ($organizations as $organization)
		{
			if (($key = array_search($organization->id, $markerOrganizationIDs)) !== false)
			{
				unset($markerOrganizationIDs[$key]);
			}
			else
			{
				array_push($markerOrganizationIDs, $organization->id);
			}
		}


		return true;
	}

	/**
	 * populate the geosearch markers table
	 *
	 * @param	 object	 $job	\Components\Cron\Models\Job
	 * @return	boolean
	 */
	public function updateLocationData(\Components\Cron\Models\Job $job)
	{

	}
}
