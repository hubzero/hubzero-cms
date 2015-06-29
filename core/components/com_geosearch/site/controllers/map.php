<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Brandon Beatty, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

/**
 * Display HABRI Members on Google Map
 */
class GeosearchControllerMap extends \Hubzero\Component\SiteController
{
	/**
	 * display
	 */
	public function displayTask()
	{
		$filters          = array();
		$filters['limit'] = 1000; //Request::getInt('limit', 1000, 'request');
		$filters['start'] = 0; //Request::getInt('limitstart', 0, 'request');
		$resources        = Request::getVar('resource', '', 'request');
		$tags             = trim(Request::getString('tags', '', 'request'));
		$distance         = Request::getInt('distance', '', 'request');
		$location         = Request::getVar('location', '', 'request');
		$unit             = Request::getVar('dist_units', '', 'request');

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->display();
	}


	/**
	 * get marker coordinates
	 */
	public function getmarkersTask()
	{
		$checked = Request::getVar('checked', array(), 'request');
		$tags = trim(Request::getString('tags', '', 'request'));
		$resources = Request::get('resources', array());

		$filters = array();
		$filters['scope'] = $resources;

		// get markers object
		$GM = new GeosearchMarkers($this->database);

		echo $GM->getMarkers($filters);
		exit();
	}

	/**
	 * geocode location
	 * string	$location
	 * return 	array lat/lng coordinates
	 */
	public function doGeocode($location = "")
	{
		if ($location != "")
		{
			// geocode address
			$base_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=";
			$url_addy = urlencode($location);
			$request_url = $base_url . $url_addy . "&sensor=false";
			$xml = simplexml_load_file($request_url);
			$status = $xml->status;
			if ($status == "OK")
			{
				// successful geocode
				$lat = $xml->result->geometry->location->lat;
				$lng = $xml->result->geometry->location->lng;
				$latlng = array($lat,$lng);
				return $latlng;
			}
			else
			{
				// failure to geocode
				/*
				echo "Location " . $location . " failed to geocode. ";
				echo "Received status " . $status . "\n";
				*/
				return false;
			}
		}
	}
}
