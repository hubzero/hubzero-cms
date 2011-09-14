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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_usage_maps' );

/**
 * Short description for 'plgUsageMaps'
 * 
 * Long description (if any) ...
 */
class plgUsageMaps extends JPlugin
{

	/**
	 * Short description for 'plgUsageMaps'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgUsageMaps(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'usage', 'maps' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onUsageAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function onUsageAreas()
	{
		$areas = array(
			'maps' => JText::_('PLG_USAGE_MAPS')
		);
		return $areas;
	}

	/**
	 * Short description for 'get_hosts'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$db Parameter description (if any) ...
	 * @param      array $location Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function get_hosts(&$db, $location)
	{
		$query = "SELECT DISTINCT(domain) FROM #__xsession WHERE ipLATITUDE = '".$location['lat']."' AND ipLONGITUDE = '".$location['lng']."'";

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$info = '';
		if ($rows) {
			foreach ($rows as $row)
			{
				$info .= "_b_".$row->domain."_bb_".$this->get_count($db, $row->domain, $location);
			}
		}
		return rtrim($info,'_br_');
	}

	/**
	 * Short description for 'get_count'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$db Parameter description (if any) ...
	 * @param      string $domain Parameter description (if any) ...
	 * @param      array $location Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function get_count(&$db, $domain, $location)
	{
		$query = "SELECT COUNT(DISTINCT username) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND guest = '0' AND domain = '".$domain."' AND ipLATITUDE = '".$location['lat']."' AND ipLONGITUDE = '".$location['lng']."' LIMIT 1";

		$db->setQuery( $query );
		$users = $db->loadResult();

		$info = '';
		if ($users) {
			$info .= "_br_ - Users: ".$users;
		}

		$query = "SELECT COUNT(DISTINCT ip) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND guest = '1' AND domain = '".$domain."' AND bot = '0' AND ipLATITUDE = '".$location['lat']."' AND ipLONGITUDE = '".$location['lng']."' LIMIT 1";

		$db->setQuery( $query );
		$guests = $db->loadResult();

		if ($guests) {
			$info .= "_br_ - Guests: ".$guests;
		}

		$query = "SELECT COUNT(DISTINCT ip) FROM #__xsession,#__session WHERE #__xsession.session_id=#__session.session_id AND  guest = '1' AND domain = '".$domain."' AND bot = '1' AND ipLATITUDE = '".$location['lat']."' AND ipLONGITUDE = '".$location['lng']."' LIMIT 1";

		$db->setQuery( $query );
		$bots = $db->loadResult();

		if ($bots) {
			$info .= "_br_ - Bots: ".$bots;
		}

		if ($info) {
			$info = $info."_br_";
			return $info;
		} else {
			return "_br_";
		}
	}

	/**
	 * Short description for 'checkbot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$db Parameter description (if any) ...
	 * @param      array $location Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function checkbot(&$db, $location)
	{
		$query = "SELECT bot FROM #__xsession WHERE ipLATITUDE ='".$location['lat']."' AND ipLONGITUDE = '".$location['lng']."' ORDER BY bot DESC LIMIT 1";

		$db->setQuery( $query );
		$bot = $db->loadResult();

		return $bot;
	}

	/**
	 * Short description for 'getData'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $type Parameter description (if any) ...
	 * @return     void
	 */
	private function getData( $type )
	{
		$db =& $this->udb;

		$html = '';

		switch ($type)
		{
			case 'locations':
				$query = "SELECT ipLATITUDE, ipLONGITUDE, SUM(hits) as totalhits FROM ipmap GROUP BY ipLATITUDE, ipLONGITUDE ORDER BY totalhits";
				$db->setQuery( $query );
				$rows = $db->loadObjectList();

				$html .= '<locations>'."\n";
				if ($rows) {
					foreach ($rows as $row)
					{
						$html .= '<location lat="'.$row->ipLATITUDE.'" lng="'.$row->ipLONGITUDE.'" hits="'.$row->totalhits.'"/>'."\n";
					}
				}
				$html .= '</locations>'."\n";
			break;

			case 'markers':
				$date = JRequest::getVar('period','2008-03-00');
				$local = JRequest::getVar('local','');

				if ($local == 'us') {
					$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					//$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					$query = 'SELECT DISTINCT ipLAT, ipLONG, count(*) as ips FROM location WHERE datetime < "'.$date.'" AND (countrySHORT = "US" OR countrySHORT = "PR") GROUP BY ipLAT, ipLONG ORDER BY ips';
				} else {
					$query = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					#$sql = "SELECT DISTINCT ipLAT, ipLONG, type FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY datetime";
					$query = "SELECT DISTINCT ipLAT, ipLONG, count(*) as ips FROM location WHERE datetime < '".$date."' GROUP BY ipLAT, ipLONG ORDER BY ips";
				}

				$db->setQuery( $query );
				$rows = $db->loadObjectList();

				$html .= '<markers>'."\n";
				if ($rows) {
					foreach ($rows as $row)
					{
						$html .= '<marker lat="'.$row->ipLAT.'" lng="'.$row->ipLONG.'" type="'.$row->ips.'"/>'."\n";
					}
				}
				$html .= '</markers>'."\n";
			break;

			case 'online':
				$html .= '<markers>'."\n";
				$query = "SELECT DISTINCT ipLATITUDE, ipLONGITUDE, ipCITY, ipREGION, countrySHORT FROM #__xsession WHERE ipLATITUDE <> '' GROUP BY ipLATITUDE, ipLONGITUDE";

				$db->setQuery( $query );
				$rows = $db->loadObjectList();

				if ($rows) {
					foreach ($rows as $row)
					{
						$location = array();
						$location['lat'] = $row->ipLATITUDE;
						$location['lng'] = $row->ipLONGITUDE;
						$city = "_b_".$row->ipCITY.", ".$row->ipREGION.", ".$row->countrySHORT."_bb_";

						$info = $this->get_hosts($db, $location);
						$bot = $this->checkbot($db, $location);

						$html .= '<marker lat="'.$location['lat'].'" lng="'.$location['lng'].'" info = "'.$city.'_hr_'.$info.'" bot = "'.$bot.'"/>'."\n";
					}
				}
				$html .= '</markers>'."\n";
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
	 * Short description for 'onUsageDisplay'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @param      unknown $db Parameter description (if any) ...
	 * @param      unknown $months Parameter description (if any) ...
	 * @param      unknown $monthsReverse Parameter description (if any) ...
	 * @param      unknown $enddate Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function onUsageDisplay( $option, $task, $db, $months, $monthsReverse, $enddate )
	{
		// Check if our task is the area we want to return results for
		if ($task) {
			if (!in_array( $task, $this->onUsageAreas() )
			 && !in_array( $task, array_keys( $this->onUsageAreas() ) )) {
				return '';
			}
		}

		// Incoming
		$lat  = JRequest::getVar('lat','35');
		$long = JRequest::getVar('long','-90');
		$zoom = JRequest::getVar('zoom','');
		if ($lat != '35' && $long != '-90') {
		    $zoom = '14';
		} else {
		    $zoom = '4';
		}

		$type = JRequest::getVar('type','online');
		$no_html = JRequest::getVar('no_html',0);

		$type = str_replace(':','-',$type);

		if ($no_html) {
			$data = JRequest::getVar('data','');

			if ($data) {
				$this->getData($data);
			} else {
				$config =& JComponentHelper::getParams( $option );

				$key = $config->get('mapsApiKey');
				$mappath = $config->get('maps_path');

				include_once( JPATH_ROOT.DS.'plugins'.DS.'usage'.DS.'maps'.DS.$type.'.php' );

				return $html;
			}
		}

		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('PLG_USAGE_MAPS_'.strtoupper($type)),'index.php?option='.$option.'&task='.$task.'&type='.$type);

		$html  = '<h3>'.JText::_('PLG_USAGE_MAPS_'.strtoupper($type)).'</h3>'."\n";
		$html .= '<p><a class="map" href="'.JRoute::_('index.php?option='.$option.'&task=maps&type='.$type).'">'.JText::_('Reset map').'</a></p>';
		$html .= '<iframe src="'.JRoute::_('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&lat='.$lat.'&long='.$long.'&zoom='.$zoom).'" width="100%" height="600px" scrolling="no" frameborder="0"></iframe>'."\n";

		return $html;
	}
}

