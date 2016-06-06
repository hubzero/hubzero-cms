<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author		Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Geocode;

/**
 * Cron plugin for support tickets
 */
class plgCronGeosearch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
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
			)
		);

		return $obj;
	}

	/**
	 * populate the geosearch markers table
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function getLocationData(\Components\Cron\Models\Job $job)
	{
		//setup database object
		$this->database = App::get('db');

		//get the relevant tables
		require_once(PATH_CORE . DS . 'components' . DS .'com_members' . DS . 'models' . DS . 'member.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_members' . DS . 'models' . DS . 'profile' . DS . 'field.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_geosearch' . DS . 'tables' . DS . 'geosearchmarkers.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_jobs' . DS . 'tables' . DS . 'job.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_events' . DS . 'tables' . DS . 'event.php');

		// get current markers
		$markers = new \Components\Geosearch\Tables\GeosearchMarkers($this->database);
		$markers = $markers->getMarkers(array(), 'array');

		// user profiles
		$profiles = \Components\Members\Models\Member::all()
			->select('id')
			->whereEquals('access', 1)
			->rows();

		// jobs
		$objJob = new \Components\Jobs\Tables\Job($this->database);
		$jobs = $objJob->get_openings();

		// events
		$objEvents = new \Components\Events\Tables\Event($this->database);
		$events = $objEvents->getEvents('year', array('year' => date('Y'), 'category' => 0));

		// organizations
		$organizations = array();

		$field = \Components\Members\Models\Profile\Field::all()
			->whereEquals('name', 'organization')
			->row();

		if ($field->get('id'))
		{
			$options = $field->options()->ordered()->rows();

			foreach ($options as $option)
			{
				$organization = new stdClass;
				$organization->id = $option->get('id');
				$organization->organization = $option->get('label');

				$organizations[] = $organization;
			}
		}

		if (count($markers) > 0)
		{
			//separate by scope
			$existingMarkers = $this->_separatebyScope($markers);

			//unique entries
			foreach ($existingMarkers as $class => &$existing)
			{
				switch ($class)
				{
					case 'markerJobIDs':
						$identifier = 'code';
						$all = $jobs;
					break;
					case 'markerMemberIDs':
						$identifier = 'uidNumber';
						$all = $profiles;
					break;
					case 'markerEventIDs':
						$identifier = 'id';
						$all = $events;
					break;
					case 'markerOrganizationIDs':
						$identifier = 'id';
						$all = $organizations;
					break;
					default:
						$identifier = '';
						$all = array();
					break;
				} //end switch
				if ($identifier != '' && count($all) > 0)
				{
					//var_dump($all);
					$existing = $this->_distill($existing, $all, $identifier);
				}
			}

			$markerMemberIDs = $this->_scopify($existingMarkers['markerMemberIDs'], 'member');
			$markerJobIDs = $this->_scopify($existingMarkers['markerJobIDs'], 'job');
			$markerEventIDs = $this->_scopify($existingMarkers['markerEventIDs'], 'event');
			$markerOrganizationIDs = $this->_scopify($existingMarkers['markerOrganizationIDs'], 'organization');

		} //end if (checks for existing markers and filters in new markers)
		elseif (count($markers) == 0) // for unpopulated table
		{
			$markerMemberIDs = array();
			$markerJobIDs = array();
			$markerEventIDs = array();
			$markerOrganizationIDs = array();

			foreach ($profiles as $profile)
			{
				$obj = array();
				$obj['scope'] = 'member';
				$obj['scope_id'] = $profile->get('id');

				array_push($markerMemberIDs, $obj);
			}

			foreach ($jobs as $job)
			{
				$obj = array();
				$obj['scope'] = 'job';
				$obj['scope_id'] = $job->code;

				array_push($markerJobIDs, $obj);
			}

			foreach ($events as $event)
			{
				$obj = array();
				$obj['scope'] = 'event';
				$obj['scope_id'] = $event->id;

				array_push($markerEventIDs, $obj);
			}

			foreach ($organizations as $organization)
			{
				$obj = array();
				$obj['scope'] = 'organization';
				$obj['scope_id'] = $organization->id;;

				array_push($markerOrganizationIDs, $obj);
			}
		}

		//merge into one array
		$newMarkers = $this->_merger($markerMemberIDs, $markerJobIDs, $markerEventIDs, $markerOrganizationIDs);

		$creations = $this->_doGeocode($newMarkers, $objProfile, $objJob, $objEvents, $objOrganizations);

		foreach ($creations as $creation)
		{
			if ($creation->location != '' && $creation->location != NULL)
			{
				$m = new \Components\Geosearch\Tables\GeosearchMarkers($this->database);
				$m->addressLatitude = $creation->location->getLatitude();
				$m->addressLongitude = $creation->location->getLongitude();
				$m->scope_id = $creation->scope_id;
				$m->scope = $creation->scope;
				$m->store(true);
			}
		}

		return true;
	}

	/**
	 * populate the geosearch markers table
	 *
	 * @param   array    $markers  list of markers to geocode
	 * @return  boolean
	 */
	private function _doGeocode($markers, $objProfile, $objJob, $objEvents, $objOrganizations)
	{
		$geocode = new \Hubzero\Geocode\Geocode;
		$createMarkers = array();

		foreach ($markers as $marker)
		{
			switch ($marker['scope'])
			{
				case 'job':
					$object = $objJob->get_opening(0,0,0, $marker['scope_id']);
					$address = $object->companyLocation;
				break;
				case 'member':
					$object = $objProfile->selectWhere('organization' , 'uidNumber = ' . $marker['scope_id']);
					$address = $object[0]->organization;
				break;
				case 'event':
					$db = App::get('db');
					$object= new $objEvents($db);
					$object->load($marker['scope_id']);
					$address = $object->adresse_info;
				break;
				case 'organization':
					$object = $objOrganizations->find('all');
					foreach ($object as $obj)
					{
						if ($marker['scope_id'] == $obj->id)
						{
							$address = $obj->organization;
						}
					}
				break;
			} //end switch


			if ($address != "")
			{
				try
				{
					$location = $geocode->locate($address);
					$createMarker = new stdClass;
					$createMarker->location = $location;
					$createMarker->scope = $marker['scope'];
					$createMarker->scope_id = $marker['scope_id'];
					array_push($createMarkers, $createMarker);
				}
				catch (Exception $e)
				{
					continue; //skip bad locations
				}
			}

		} //end foreach

		return $createMarkers;
	} // end _doGeocode

	/**
	* add the unique IDs with scope for gathering later
	*
	* @param array  $idList   list of unique IDs
	* @param string $scope    the scope of the ID
	* @return array $result   array(['scope'] => $scope, ['scope_id'] = $id)
	*/
	private function _scopify($idList = array(), $scope = '')
	{
		if (count($idList) > 0 && $scope != '')
		{
			foreach ($idList as &$obj)
			{
				$id = $obj;
				$obj = array();
				$obj['scope'] = $scope;
				$obj['scope_id'] = $id;
			}

			return $idList;
		}
		else
		{
			return false;
		}
	}

	/**
	* add the unique IDs with scope for gathering later
	*
	* @param array  $idList   list of unique IDs
	* @param string $scope    the scope of the ID
	* @param string $identifier the name of the ID property for the given object
	* @return array $result   array(['scope'] => $scope, ['scope_id'] = $id)
	*/
	private function _distill($existing = array(), $all = array(), $identifier = '')
	{
		if (count($existing) > 0 && count($all) > 0 && $identifier != '')
		{
			foreach ($all as $a)
			{
				// detect if the identifier is in the list of current markers
				if (($key = array_search($a->$identifier, $existing)) !== false)
				{
					// remove it from the list
					unset($existing[$key]);
				}
				else
				{
					// add new ones
					array_push($existing, $a->$identifier);
				}
			} //end foreach
			return $existing;
		} // end if
		elseif (count($existing) == 0 && count($all) > 0)
		{
			$newMarkers = array();
			foreach ($all as $a)
			{
				array_push($newMarkers, $a->$identifier);
			}

			return $newMarkers;

		} //end elseif
		else
		{
			return false;
		} //end else
	} //end _uniqueEntry

	/**
	* separates existing markers by scope for counting and sorting later.
	*
	* @param array  $markers   array of marker objects
	* @return array  array($markerMemberIDs, $markerJobIDs, $markerEventIDs, $markerOrganizationIDs)
	*/
	private function _separatebyScope($markers = array())
	{
		if (count($markers) > 0)
		{
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

			return array(
				'markerMemberIDs' => $markerMemberIDs,
				'markerJobIDs' => $markerJobIDs,
				'markerEventIDs' => $markerEventIDs,
				'markerOrganizationIDs' => $markerOrganizationIDs
				);
		}
		else
		{
			return false;
		}
	} //end _separateByScope

	/**
	* separates existing markers by scope for counting and sorting later.
	*
	* @param array  $markers   array of marker objects
	* @return array  array($markerMemberIDs, $markerJobIDs, $markerEventIDs, $markerOrganizationIDs)
	*/
	private function _merger()
	{
		$arrays = func_get_args();
		$output = array();
		if (func_num_args() > 0)
		{
			foreach ($arrays as $array)
			{
				if (count($array) > 0 && $array != false)
				{
					foreach ($array as $key => $value)
					{
						array_push($output, $value);
					}
				}
			}
			return $output;
		}
		else
		{
			return false;
		}
	} //end _merger
} //end plgCronGeosearch
