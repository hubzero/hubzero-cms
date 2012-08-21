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

if (!defined('n')) 
{

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

/**
 * Support helper class for misc. HTML
 */
class SupportHtml
{
	/**
	 * Short description for 'alert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function alert($msg)
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	/**
	 * Get the status text for a status number
	 * 
	 * @param      integer $int Status number
	 * @return     string 
	 */
	public function getStatus($open=0, $status=0)
	{
		/*switch ($int)
		{
			case 0: $status = JText::_('TICKET_STATUS_NEW');      break;
			case 1: $status = JText::_('TICKET_STATUS_WAITING');  break;
			case 2: $status = JText::_('TICKET_STATUS_RESOLVED'); break;
		}
		return $status;*/
		switch ($open)
		{
			case 1:
				switch ($status)
				{
					case 2:
						$status = JText::_('TICKET_STATUS_WAITING');
					break;
					case 1:
						$status = 'accepted';
					break;
					case 0:
					default:
						$status = JText::_('TICKET_STATUS_NEW');
					break;
				}
			break;
			case 0:
				$status = JText::_('TICKET_STATUS_RESOLVED');
			break;
		}
		return $status;
	}

	/**
	 * Short description for 'selectArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $js Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectArray($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach ($array as $anode)
		{
			$selected = ($anode == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode.'"'.$selected.'>'.stripslashes($anode).'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectObj'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $js Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function selectObj($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->txt == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.stripslashes($anode->txt).'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'collapseFilters'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function collapseFilters($filters)
	{
		$fstring = array();
		foreach ($filters as $key => $val)
		{
			if (substr($key,0,1) != '_' && $key != 'limit' && $key != 'start') 
			{
				if ($val !== '') 
				{
					$fstring[] = $key . ':' . $val;
				}
			}
		}
		$fstring = implode(' ', $fstring);
		return trim($fstring);
	}

	/**
	 * Short description for 'mkt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs)) 
		{
			$stime = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $stime;
	}

	/**
	 * Short description for 'timeAgoo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0) $val = 0;

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val].= "s";

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0))
		{
			$text .= SupportHtml::timeAgoo($new_time);
		}

		return $text;
	}

	/**
	 * Short description for 'timeAgo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $timestamp Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function timeAgo($timestamp)
	{
		$timestamp = SupportHtml::mkt($timestamp);
		$text = SupportHtml::timeAgoo($timestamp);

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];

		return $text;
	}

	/**
	 * Short description for 'getMemberPhoto'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      integer $anonymous Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getMemberPhoto($member, $anonymous=0)
	{
		$config =& JComponentHelper::getParams('com_members');

		if (!$anonymous && $member->get('picture')) 
		{
			$thumb  = DS . trim($config->get('webpath'), DS);
			$thumb .= DS . SupportHtml::niceidformat($member->get('uidNumber')) . DS . $member->get('picture');

			$thumb = SupportHtml::thumbit($thumb);
		} 
		else 
		{
			$thumb = '';
		}

		$dfthumb = DS . ltrim($config->get('defaultpic'), DS);
		$dfthumb = SupportHtml::thumbit($dfthumb);

		if ($thumb && is_file(JPATH_ROOT . $thumb)) 
		{
			return $thumb;
		} 
		else if (is_file(JPATH_ROOT . $dfthumb)) 
		{
			return $dfthumb;
		}
	}

	/**
	 * Short description for 'thumbit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $thumb Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function thumbit($thumb)
	{
		$image = explode('.', $thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.', $image);

		return $thumb;
	}

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
}

