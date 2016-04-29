<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

namespace Modules\Notices;

use Hubzero\Module\Module;
use Request;
use Config;
use Lang;
use Date;

/**
 * Module class for displaying site wide notices
 */
class Helper extends Module
{
	/**
	 * Calculate the time left from a date time
	 *
	 * @param   integer  $year    Year
	 * @param   integer  $month   Month
	 * @param   integer  $day     Day
	 * @param   integer  $hour    Hour
	 * @param   integer  $minute  Minute
	 * @return  array
	 */
	private function _countdown($year, $month, $day, $hour, $minute)
	{
		// Make a unix timestamp for the given date
		$the_countdown_date = mktime($hour, $minute, 0, $month, $day, $year, -1);

		// Get current unix timestamp
		$now = time() + (Config::get('offset') * 60 * 60);

		$difference = $the_countdown_date - $now;
		if ($difference < 0)
		{
			$difference = 0;
		}

		$days_left    = floor($difference/60/60/24);
		$hours_left   = floor(($difference - $days_left*60*60*24)/60/60);
		$minutes_left = floor(($difference - $days_left*60*60*24 - $hours_left*60*60)/60);

		$left = array($days_left, $hours_left, $minutes_left);
		return $left;
	}

	/**
	 * Turn datetime 0000-00-00 00:00:00 to time
	 *
	 * @param   string   $stime  Datetime to convert
	 * @return  integer
	 */
	private function _mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs))
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Break a timestamp into its parts
	 *
	 * @param   integer  $stime  Timestamp
	 * @return  array
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
	 * Show the amount of time left
	 *
	 * @param   array   $stime  Timestamp
	 * @return  string
	 */
	private function _timeto($stime)
	{
		if ($stime[0] == 0 && $stime[1] == 0 && $stime[2] == 0)
		{
			$o  = Lang::txt('MOD_NOTICES_IMMEDIATELY');
		}
		else
		{
			$o  = Lang::txt('MOD_NOTICES_IN') . ' ';
			$o .= ($stime[0] > 0) ? $stime[0] . ' ' . Lang::txt('MOD_NOTICES_DAYS') . ', '  : '';
			$o .= ($stime[1] > 0) ? $stime[1] . ' ' . Lang::txt('MOD_NOTICES_HOURS') . ', ' : '';
			$o .= ($stime[2] > 0) ? $stime[2] . ' ' . Lang::txt('MOD_NOTICES_MINUTES')      : '';
		}
		return $o;
	}

	/**
	 * Auto Link Text
	 *
	 * @param   string  $text  Text to look for links
	 * @return  string
	 */
	private static function _autoLinkText($text)
	{
		// Replace email links
		$text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})/', '<a href="mailto:$1">$1</a>', $text);

		// Replace url links
		$text = preg_replace('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#', '<a class="ext-link" rel="external" href="$1">$1</a>', $text);

		// Return auto-linked text
		return $text;
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$database = \App::get('db');

		// Set today's time and date
		$now = Date::toSql();

		$database->setQuery("SELECT publish_up, publish_down FROM `#__modules` WHERE id=" . $this->module->id);
		$item = $database->loadObject();
		$this->module->publish_up   = (isset($item->publish_up))   ? $item->publish_up   : null;
		$this->module->publish_down = (isset($item->publish_down)) ? $item->publish_down : null;

		// Get some initial parameters
		$start = $this->params->get('start_publishing', $this->module->publish_up);
		//$start = Date::of($start)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		$stop  = $this->params->get('stop_publishing', $this->module->publish_down);
		//$stop  = Date::of($stop)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

		$this->publish = false;
		if (!$start || $start == $database->getNullDate())
		{
			$this->publish = true;
		}
		else
		{
			if ($start <= $now)
			{
				$this->publish = true;
			}
			else
			{
				$this->publish = false;
			}
		}
		if (!$stop || $stop == $database->getNullDate())
		{
			$this->publish = true;
		}
		else
		{
			if ($stop >= $now && $this->publish)
			{
				$this->publish = true;
			}
			else
			{
				$this->publish = false;
			}
		}

		$hide = '';
		if ($this->publish && $this->params->get('allowClose', 1))
		{
			// Figure out days left

			// make a unix timestamp for the given date
			$the_countdown_date = $this->_mkt($stop);

			// get current unix timestamp
			$now = time() + (Config::get('offset') * 60 * 60);

			$difference = $the_countdown_date - $now;
			if ($difference < 0)
			{
				$difference = 0;
			}

			$this->days_left = floor($difference/60/60/24);
			$this->days_left = ($this->days_left ? $this->days_left : 7);

			$expires = $now + 60*60*24*$this->days_left;

			$hide = Request::getVar($this->params->get('moduleid', 'sitenotice'), '', 'cookie');

			if (!$hide && Request::getVar($this->params->get('moduleid', 'sitenotice'), '', 'get'))
			{
				setcookie($this->params->get('moduleid', 'sitenotice'), 'closed', $expires);
			}
		}

		// Only do something if the module's time frame hasn't expired
		if ($this->publish && !$hide)
		{
			// Get some parameters
			$this->moduleid   = $this->params->get('moduleid', 'sitenotice');
			$this->alertlevel = $this->params->get('alertlevel', 'medium');
			$timezone         = $this->params->get('timezone');
			$message          = $this->params->get('message');

			// Convert start time
			$start = $this->_mkt($start);
			$d = $this->_convert($start);
			$time_start = $d['hour'] . ':' . $d['minute'] . ' ' . $d['ampm'] . ', ' . $d['month'] . ' ' . $d['day'] . ', ' . $d['year'];

			// Convert end time
			$stop = $this->_mkt($stop);
			$u = $this->_convert($stop);
			$time_end  = $u['hour'] . ':' . $u['minute'] . ' ' . $u['ampm'] . ', ' . $u['month'] . ' ' . $u['day'] . ', ' . $u['year'];

			// Convert countdown-to-start time
			$d_month   = date('m', $start);
			$d_day     = date('d', $start);
			$d_hour    = date('H', $start);
			$time_left = $this->_countdown($d['year'], $d_month, $d_day, $d_hour, $d['minute']);
			$time_cd_tostart = $this->_timeto($time_left);

			// Convert countdown-to-return time
			$u_month   = date('m', $stop);
			$u_day     = date('d', $stop);
			$u_hour    = date('H', $stop);
			$time_left = $this->_countdown($u['year'], $u_month, $u_day, $u_hour, $u['minute']);
			$time_cd_toreturn = $this->_timeto($time_left);

			// Parse message for tags
			$message = str_replace('<notice:start>', $time_start, $message);
			$message = str_replace('<notice:end>', $time_end, $message);
			$message = str_replace('<notice:countdowntostart>', $time_cd_tostart, $message);
			$message = str_replace('<notice:countdowntoreturn>', $time_cd_toreturn, $message);
			$message = str_replace('<notice:timezone>', $timezone, $message);

			// auto link?
			if ($this->params->get('autolink', 1))
			{
				$message = self::_autoLinkText($message);
			}

			if (!trim($message))
			{
				$this->publish = false;
			}

			$this->message = $message;

			require $this->getLayoutPath();
		}
	}
}
