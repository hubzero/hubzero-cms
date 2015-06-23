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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Brandon Beatty
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

		/*profiles = new MembersProfile($this->database);
		//getRecords()
		$profiles = $profiles->getRecords(array('sortby' => 'uidNumber', 'show'=>''));



		foreach ($profiles as $profile)
		{
			if ($profile->organization == 'Purdue')
			{
			}
			elseif ($profile->organization == "")
			{
			}
		}
		*/
		// get resources, set to all if none selected
		if (empty($resources))
		{
			$resources = array('members','jobs','events','organizations');
		}


		$this->view->resources = $resources;
		// keep search inputs
		$this->view->distance = $distance;
		$this->view->location = $location;
		$this->view->unit = $unit;

		/*
		// Tag search
		if ($tags != '')
		{
			$tags = explode(",",$tags);
			$HT = new GeosearchTags($this->database);
			$uids = $HT->searchTagsMems($tags, $filters);
			$eids = $HT->searchTagsEvents($tags, $filters);
			$oids = $HT->searchTagsOrgs($tags, $filters);
			$this->view->uids = $uids;
			$this->view->eids = $eids;
			$this->view->jids = 0;
			$this->view->oids = $oids;

			// keep tags
			$this->view->stags = $tags;
		}
		else
		{
			$this->view->uids = array();
		} */

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}

		$this->view->display();
	}

	/**
	 * Set breadcrumbs
	 * @return
	 */
	private function _pathway()
	{
		//add 'groups' item to pathway
		if (count(Pathway::count()) <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
	}

	/**
	 * Set Title
	 * @return
	 */
	private function _title()
	{
		//set title of browser window
		Document::setTitle(Lang::txt(strtoupper($this->_option)));
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
		//$filter['types'] = $resources;

		// get markers object
		$GM = new GeosearchMarkers($this->database);

		echo $GM->getMarkers($filters);
		exit();
	}

	/**
	 * get marker infowindow contents
	 */
	public function getaddyxmlTask()
	{
		// get id and type
		$id = Request::getInt('uid', 0, 'request');
		$type = Request::getVar('type', 0, 'request');

		// start XML
		$dom = new DOMDocument("1.0");
		$node = $dom->createElement("profiles");
		$parnode = $dom->appendChild($node);

		// add to XML document node
		$node = $dom->createElement("profile");
		$newnode = $parnode->appendChild($node);

		switch ($type)
		{
			case "member":
				// check for logged in user
				if (User::get('id'))
				{
					$newnode->setAttribute("jid", User::get('id'));
				}

				// get profile object
				$user = \Hubzero\User\Profile::getInstance($id);

				// add attributes
				$newnode->setAttribute("org", $user->get('organization'));
				$newnode->setAttribute("url", $user->get('url'));

				if ($user->get('surname'))
				{
					$name = $user->get('surname').", ".$user->get('givenName');
				}
				else
				{
					$name = $user->get('name');
				}

				$newnode->setAttribute("name", $name);

				// get photo
				$newnode->setAttribute("photo", \Hubzero\User\Profile\Helper::getMemberPhoto($id, 0));

				// get bio
				if ($user->get('bio'))
				{
					$bio = $user->getBio('parsed');
					$bio = \Hubzero\Utility\String::truncate($bio, 200);
					$newnode->setAttribute("bio", $bio);
				}

				// link
				$profileLink = Route::url('index.php?option=com_members&id=' . $user->get('uidNumber'));
				$newnode->setAttribute("profilelink", $profileLink);

				$messageLink = Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $user->get('uidNumber'));
				if ($juser->get('guest'))
				{
					$messageLink = '/login?return' . base64_encode($messageLink);
				}
				$newnode->setAttribute("messagelink", $messageLink);
				break;
			case "event":
				$event = new EventsEvent($this->database);
				$event->load($id);
				$newnode->setAttribute("url", $event->extra_info);
				$newnode->setAttribute("name", $event->title);
				if ($event->content)
				{
					$desc = \Hubzero\Utility\String::truncate(stripslashes($event->content), 200);
					$newnode->setAttribute("bio", $desc);
				}

				// format dates
				$start = Date::of($event->publish_up)->toLocal('l, F j, Y g:i a');
				$end   = Date::of($event->publish_down)->toLocal('l, F j, Y g:i a');
				$newnode->setAttribute("start", $start);
				$newnode->setAttribute("end", $end);
				$newnode->setAttribute("tz", $event->time_zone);

				$link = Route::url('index.php?option=com_events&task=details&id=' . $event->id);
				$newnode->setAttribute("link", $link);
				break;
			case "job":
				$J = new \Components\Jobs\Tables\Job($this->database);
				$job = $J->get_opening($id);
				$newnode->setAttribute("url", '');
				$newnode->setAttribute("code", $job->code);
				$newnode->setAttribute("name", $job->title);
				$newnode->setAttribute("org", $job->companyName);
				if ($job->description)
				{
					$jobsModelJob = new \Components\Jobs\Models\Job($job->id);
					$desc = $jobsModelJob->content('parsed');
					$desc = \Hubzero\Utility\String::truncate($desc, 290);
					$newnode->setAttribute("bio", $desc);
				}
				$link = Route::url('index.php?option=com_jobs&task=job&id=' . $job->code);
				$newnode->setAttribute("link", $link);
				$newnode->setAttribute("jobtype", $job->typename);
				break;
			case "org":
				$RR = new \Components\Resources\Tables\Resource($this->database);
				$RR->load($id);

				// get url, location data
				$data = $this->getResourceData($RR->fulltxt);

				// get location xml
				$location = "<location>{$data['citations']}</location>";
				$locxml = simplexml_load_string($location);
				$newnode->setAttribute("url", $data['sponsoredby']);
				$newnode->setAttribute("name", $RR->title);
				$newnode->setAttribute("org", $locxml->value);

				// description
				$bio = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $RR->fulltxt);
				$bio = trim($bio);
				$bio = \Hubzero\Utility\String::truncate(stripslashes($bio), 200);
				$newnode->setAttribute("bio", $bio);

				$link = Route::url('index.php?option=com_resources&id=' . $RR->id);
				$newnode->setAttribute("link", $link);
				break;
		}
		echo $dom->saveXML();
		exit;
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

	/**
	 * pull data from resource description
	 * string	$fulltxt
	 * return 	array
	 */
	public function getResourceData($fulltxt = "")
	{
		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $fulltxt, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = $match[2];
			}
		}
		return $data;
	}
}
