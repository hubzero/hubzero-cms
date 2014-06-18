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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wishlist helper class for misc. HTML
 */
class WishlistHtml
{
	/**
	 * Generate a select form
	 *
	 * @param      string $name  Field name
	 * @param      array  $array Data to populate select with
	 * @param      mixed  $value Value to select
	 * @param      string $class Class to add
	 * @return     string HTML
	 */
	public static function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Convert a numerical vote value to a readable text value
	 *
	 * @param      integer $rawnum   Vote value
	 * @param      string  $category Vote type
	 * @param      string  $output   Value to append to
	 * @return     string
	 */
	public static function convertVote($rawnum, $category, $output='')
	{
		$rawnum = round($rawnum);
		if ($category == 'importance')
		{
			switch ($rawnum)
			{
				case 0: $output = JText::_('COM_WISHLIST_RUBBISH');     break;
				case 1: $output = JText::_('COM_WISHLIST_MAYBE');       break;
				case 2: $output = JText::_('COM_WISHLIST_INTERESTING'); break;
				case 3: $output = JText::_('COM_WISHLIST_GOODIDEA');    break;
				case 4: $output = JText::_('COM_WISHLIST_IMPORTANT');   break;
				case 5: $output = JText::_('COM_WISHLIST_CRITICAL');    break;
			}
		}
		else if ($category == 'effort')
		{
			switch ($rawnum)
			{
				case 0: $output = JText::_('COM_WISHLIST_TWOMONTHS');   break;
				case 1: $output = JText::_('COM_WISHLIST_TWOWEEKS');    break;
				case 2: $output = JText::_('COM_WISHLIST_ONEWEEK');     break;
				case 3: $output = JText::_('COM_WISHLIST_TWODAYS');     break;
				case 4: $output = JText::_('COM_WISHLIST_ONEDAY');      break;
				case 5: $output = JText::_('COM_WISHLIST_FOURHOURS');   break;
				case 6: $output = JText::_('COM_WISHLIST_DONT_KNOW'); 	break;
				case 7: $output = JText::_('COM_WISHLIST_NA');         	break;
			}
		}

		return $output;
	}

	/**
	 * Convert a timestamp to a more human readable string such as "3 days ago"
	 *
	 * @param      string $date Timestamp
	 * @return     string
	 */
	public static function nicetime($date)
	{
		if (empty($date))
		{
			return JText::_('No date provided');
		}

		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
		$lengths = array('60', '60', '24', '7', '4.35', '12', '10');

		$now = time();
		$unix_date = strtotime($date);

		   // check validity of date
		if (empty($unix_date))
		{
			return JText::_('Bad date');
		}

		// is it future date or past date
		if ($now > $unix_date)
		{
			$difference = $now - $unix_date;
			$tense = 'ago';

		}
		else
		{
			$difference = $unix_date - $now;
			$tense = '';
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1)
		{
			$periods[$j] .= 's';
		}

		return "$difference $periods[$j] {$tense}";
	}
}

