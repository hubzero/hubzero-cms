<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

/**
 * Short description for 'modNotices'
 * 
 * Long description (if any) ...
 */
class modNotices
{

	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	/**
	 * Short description for '_countdown'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $year Parameter description (if any) ...
	 * @param      unknown $month Parameter description (if any) ...
	 * @param      unknown $day Parameter description (if any) ...
	 * @param      unknown $hour Parameter description (if any) ...
	 * @param      unknown $minute Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _countdown($year, $month, $day, $hour, $minute)
	{
		$config = JFactory::getConfig();

		// make a unix timestamp for the given date
		$the_countdown_date = mktime($hour, $minute, 0, $month, $day, $year, -1);

		// get current unix timestamp
		$now = time() + ($config->getValue('config.offset') * 60 * 60);

		$difference = $the_countdown_date - $now;
		if ($difference < 0) $difference = 0;

		$days_left = floor($difference/60/60/24);
		$hours_left = floor(($difference - $days_left*60*60*24)/60/60);
		$minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);

		$left = array($days_left, $hours_left, $minutes_left);
		return $left;
	}

	/**
	 * Short description for '_mkt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	private function _mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}

	/**
	 * Short description for '_convert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _convert($stime)
	{
		$t = array();
		$t['year']   = date('Y', $stime);
		$t['month']  = date('M', $stime);
		$t['day']    = date('jS', $stime);
		$t['hour']   = date('g', $stime);
		$t['minute'] = date('i', $stime);
		$t['ampm']   = date('A', $stime);
		return $t;
	}

	/**
	 * Short description for '_timeto'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $stime Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _timeto($stime)
	{
		if ($stime[0] == 0 && $stime[1] == 0 && $stime[2] == 0) {
			$o  = JText::_('IMMEDIATELY');
		} else {
			$o  = JText::_('IN').' ';
			$o .= ($stime[0] > 0) ? $stime[0] .' '.JText::_('DAYS').', '  : '';
			$o .= ($stime[1] > 0) ? $stime[1] .' '.JText::_('HOURS').', ' : '';
			$o .= ($stime[2] > 0) ? $stime[2] .' '.JText::_('MINUTES')    : '';
		}
		return $o;
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$database =& JFactory::getDBO();

		// Set today's time and date
		$now = date( 'Y-m-d H:i:s', time() );

		// Get some initial parameters
		$params = $this->params;
		$start = $params->get( 'start_publishing' );
		$start = JHTML::_('date',$start,'%Y-%m-%d %H:%M:%S');
		$stop  = $params->get( 'stop_publishing' );
		$stop  = JHTML::_('date',$stop,'%Y-%m-%d %H:%M:%S');

		$this->publish = false;
		if (!$start || $start == '0000-00-00 00:00:00') {
			$this->publish = true;
		} else {
			if ($start <= $now) {
				$this->publish = true;
			} else {
				$this->publish = false;
			}
		}
		if (!$stop || $stop == '0000-00-00 00:00:00') {
			$this->publish = true;
		} else {
			if ($stop >= $now && $this->publish) {
				$this->publish = true;
			} else {
				$this->publish = false;
			}
		}

		// Only do something if the module's time frame hasn't expired
		if ($this->publish) {
			ximport('Hubzero_Document');
			Hubzero_Document::addModuleStylesheet('mod_notices');
			
			// Get some parameters
			$this->moduleid   = $params->get( 'moduleid' );
			$this->alertlevel = $params->get( 'alertlevel' );
			$timezone   = $params->get( 'timezone' );
			$message    = $params->get( 'message' );

			// Convert start time
			$start = $this->_mkt($start);
			$d = $this->_convert($start);
			$time_start = $d['hour'].':'.$d['minute'].' '.$d['ampm'].', '.$d['month'].' '.$d['day'].', '.$d['year'];

			// Convert end time
			$stop = $this->_mkt($stop);
			$u = $this->_convert($stop);
			$time_end = $u['hour'].':'.$u['minute'].' '.$u['ampm'].', '.$u['month'].' '.$u['day'].', '.$u['year'];

			// Convert countdown-to-start time
			$d_month  = date('m', $start);
			$d_day    = date('d', $start);
			$d_hour   = date('H', $start);
			$time_left = $this->_countdown($d['year'], $d_month, $d_day, $d_hour, $d['minute']);
			$time_cd_tostart = $this->_timeto($time_left);

			// Convert countdown-to-return time
			$u_month  = date('m', $stop);
			$u_day    = date('d', $stop);
			$u_hour   = date('H', $stop);
			$time_left = $this->_countdown($u['year'], $u_month, $u_day, $u_hour, $u['minute']);
			$time_cd_toreturn = $this->_timeto($time_left);

			// Parse message for tags
			$message = str_replace('<notice:start>', $time_start, $message);
			$message = str_replace('<notice:end>', $time_end, $message);
			$message = str_replace('<notice:countdowntostart>', $time_cd_tostart, $message);
			$message = str_replace('<notice:countdowntoreturn>', $time_cd_toreturn, $message);
			$message = str_replace('<notice:timezone>', $timezone, $message);

			$this->message = $message;
		}
	}
}
