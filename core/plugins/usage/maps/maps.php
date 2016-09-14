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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Usage plugin class for overview
 */
class plgUsageMaps extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return     array
	 */
	public function onUsageAreas()
	{
		return array(
			'maps' => Lang::txt('PLG_USAGE_MAPS')
		);
	}

	/**
	 * Get hosts data
	 *
	 * @param      object &$db      JDatabase
	 * @param      array  $location Longitude/latitude
	 * @return     string
	 */
	private function get_hosts(&$db, $location)
	{
		$query = "SELECT DISTINCT(domain) FROM `#__xsession` WHERE ipLATITUDE = '" . $location['lat'] . "' AND ipLONGITUDE = '" . $location['lng'] . "'";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$info = '';
		if ($rows)
		{
			foreach ($rows as $row)
			{
				$info .= '_b_' . $row->domain . '_bb_' . $this->get_count($db, $row->domain, $location);
			}
		}
		return rtrim($info, '_br_');
	}

	/**
	 * Get a record count
	 *
	 * @param      object &$db      JDatabase
	 * @param      string $domain   Domain
	 * @param      array  $location Longitude/latitude
	 * @return     string
	 */
	private function get_count(&$db, $domain, $location)
	{
		$query = "SELECT COUNT(DISTINCT username) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND guest = '0' AND domain = '" . $domain . "' AND ipLATITUDE = '" . $location['lat'] . "' AND ipLONGITUDE = '" . $location['lng'] . "' LIMIT 1";

		$db->setQuery($query);
		$users = $db->loadResult();

		$info = '';
		if ($users)
		{
			$info .= '_br_ - Users: ' . $users;
		}

		$query = "SELECT COUNT(DISTINCT ip) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND guest = '1' AND domain = '" . $domain . "' AND bot = '0' AND ipLATITUDE = '" . $location['lat']."' AND ipLONGITUDE = '" . $location['lng'] . "' LIMIT 1";

		$db->setQuery($query);
		$guests = $db->loadResult();

		if ($guests)
		{
			$info .= '_br_ - Guests: ' . $guests;
		}

		$query = "SELECT COUNT(DISTINCT ip) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND  guest = '1' AND domain = '" . $domain . "' AND bot = '1' AND ipLATITUDE = '" . $location['lat']."' AND ipLONGITUDE = '" . $location['lng'] . "' LIMIT 1";

		$db->setQuery($query);
		$bots = $db->loadResult();

		if ($bots)
		{
			$info .= '_br_ - Bots: ' . $bots;
		}

		if ($info)
		{
			$info = $info . '_br_';
			return $info;
		}
		else
		{
			return '_br_';
		}
	}

	/**
	 * Check if the location is from a bot
	 *
	 * @param      object &$db      JDatabase
	 * @param      array  $location Longitude/latitude
	 * @return     integer
	 */
	private function checkbot(&$db, $location)
	{
		$query = "SELECT bot FROM #__xsession WHERE ipLATITUDE ='" . $location['lat'] . "' AND ipLONGITUDE = '" . $location['lng'] . "' ORDER BY bot DESC LIMIT 1";

		$db->setQuery($query);
		$bot = $db->loadResult();

		return $bot;
	}

	/**
	 * Get data for a type
	 *
	 * @param      string $type Data type
	 * @return     void
	 */
	private function getData($type)
	{
		$db =& $this->udb;

		$html = '';

		switch ($type)
		{
			case 'locations':
				$query = "SELECT ipLATITUDE, ipLONGITUDE, SUM(hits) as totalhits FROM ipmap GROUP BY ipLATITUDE, ipLONGITUDE ORDER BY totalhits";
				$db->setQuery($query);
				$rows = $db->loadObjectList();

				$html .= '<locations>' . "\n";
				if ($rows)
				{
					foreach ($rows as $row)
					{
						$html .= '<location lat="' . $row->ipLATITUDE . '" lng="' . $row->ipLONGITUDE . '" hits="' . $row->totalhits . '"/>' . "\n";
					}
				}
				$html .= '</locations>' . "\n";
			break;

			case 'markers':
				$date = Request::getVar('period', '2008-03-00');
				$local = Request::getVar('local', '');

				if ($local == 'us')
				{
					$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '" . $date . "' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					//$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					$query = 'SELECT DISTINCT ipLAT, ipLONG, count(*) as ips FROM location WHERE datetime < "' . $date . '" AND (countrySHORT = "US" OR countrySHORT = "PR") GROUP BY ipLAT, ipLONG ORDER BY ips';
				}
				else
				{
					$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '" . $date . "' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					#$sql = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					$query = "SELECT DISTINCT ipLAT, ipLONG, count(*) as ips FROM location WHERE datetime < '" . $date . "' GROUP BY ipLAT, ipLONG ORDER BY ips";
				}

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				$html .= '<markers>' . "\n";
				if ($rows)
				{
					foreach ($rows as $row)
					{
						$html .= '<marker lat="' . $row->ipLAT . '" lng="' . $row->ipLONG . '" type="' . $row->ips . '"/>' . "\n";
					}
				}
				$html .= '</markers>' . "\n";
			break;

			case 'online':
				$html .= '<markers>' . "\n";
				$query = "SELECT DISTINCT ipLATITUDE, ipLONGITUDE, ipCITY, ipREGION, countrySHORT FROM #__xsession WHERE ipLATITUDE <> '' GROUP BY ipLATITUDE, ipLONGITUDE";

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				if ($rows)
				{
					foreach ($rows as $row)
					{
						$location = array();
						$location['lat'] = $row->ipLATITUDE;
						$location['lng'] = $row->ipLONGITUDE;
						$city = '_b_' . $row->ipCITY . ', ' . $row->ipREGION . ', ' . $row->countrySHORT . '_bb_';

						$info = $this->get_hosts($db, $location);
						$bot = $this->checkbot($db, $location);

						$html .= '<marker lat="' . $location['lat'] . '" lng="' . $location['lng'] . '" info = "' . $city . '_hr_' . $info . '" bot = "' . $bot . '"/>' . "\n";
					}
				}
				$html .= '</markers>' . "\n";
			break;
		}

		while (@ob_end_clean());

		// Date in the past
		header("Expires: Mon, 26 Jul 2017 05:00:00 GMT");
		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		// HTTP/1.0
		header("Pragma: no-cache");
		//XML Header
		header("content-type:text/xml");

		echo $html;

		die();
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            JDatabase
	 * @param      array  $months        Month names (Jan -> Dec)
	 * @param      array  $monthsReverse Month names in reverse (Dec -> Jan)
	 * @param      string $enddate       Time period
	 * @return     string HTML
	 */
	public function onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)
	{
		// Check if our task is the area we want to return results for
		if ($task)
		{
			if (!in_array($task, $this->onUsageAreas())
			 && !in_array($task, array_keys($this->onUsageAreas())))
			{
				return '';
			}
		}

		// Incoming
		$lat  = Request::getVar('lat', '35');
		$long = Request::getVar('long', '-90');
		$zoom = Request::getVar('zoom', '');
		if ($lat != '35' && $long != '-90')
		{
			$zoom = '14';
		}
		else
		{
			$zoom = '4';
		}

		$type = Request::getVar('type', 'online');
		$no_html = Request::getVar('no_html', 0);

		$type = str_replace(':', '-', $type);

		if ($no_html)
		{
			$data = Request::getVar('data','');

			if ($data)
			{
				$this->getData($data);
			}
			else
			{
				$config = Component::params($option);

				$key = $config->get('mapsApiKey');
				$mappath = $config->get('maps_path');

				if (is_file(__DIR__ . DS . $type . '.php'))
				{
					include_once(__DIR__ . DS . $type . '.php');
				}
				else
				{
					App::abort(500, Lang::txt('PLG_USAGE_MAPS_TYPE_NOT_FOUND', $type));
					return;
				}

				return $html;
			}
		}

		Pathway::append(Lang::txt('PLG_USAGE_MAPS_' . strtoupper($type)), 'index.php?option=' . $option . '&task=' . $task . '&type=' . $type);

		$html  = '<h3>' . Lang::txt('PLG_USAGE_MAPS_' . strtoupper($type)) . '</h3>' . "\n";
		$html .= '<p><a class="map" href="' . Route::url('index.php?option=' . $option . '&task=maps&type=' . $type) . '">' . Lang::txt('PLG_USAGE_MAPS_RESET') . '</a></p>';
		$html .= '<iframe src="' . Route::url('index.php?option=' . $option . '&task=' . $task . '&type=' . $type . '&no_html=1&lat=' . $lat . '&long=' . $long . '&zoom=' . $zoom) . '" width="100%" height="600px" scrolling="no" frameborder="0" title="' . Lang::txt('PLG_USAGE_MAPS_' . strtoupper($type)) . '"></iframe>' . "\n";

		return $html;
	}
}

