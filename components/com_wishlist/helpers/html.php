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

if (!defined('n')) 
{
	/**
	 * Shortcut constant for tabs
	 */
	define('t', "\t");

	/**
	 * Shortcut constant for new lines
	 */
	define('n', "\n");

	/**
	 * Shortcut constant for line returns
	 */
	define('r', "\r");

	/**
	 * Shortcut constant for break tags
	 */
	define('br', '<br />');

	/**
	 * Shortcut constant for space character
	 */
	define('sp', '&#160;');

	/**
	 * Shortcut constant for ampersand
	 */
	define('a', '&amp;');
}

/**
 * Wishlist helper class for misc. HTML
 */
class WishlistHtml
{
	/**
	 * Remove paragraph tags and break tags
	 * 
	 * @param      string $pee Text to unparagraph
	 * @return     string
	 */
	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Clean text of potential XSS and other unwanted items such as
	 * HTML comments and javascrip"\t". Also shortens tex"\t".
	 * 
	 * @param      string  $text    Text to clean
	 * @param      integer $desclen Length to shorten to
	 * @return     string
	 */
	public function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
		$text = str_replace('{mosimage}', '', $text);
		$text = str_replace("\n", ' ', $text);
		$text = str_replace("\r", ' ', $text);
		$text = preg_replace('/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i','\\2', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = strip_tags($text);
		if (strlen($text) > $desclen) 
		{
			$elipse = true;
		}
		$text = substr($text, 0, $desclen);
		if ($elipse) 
		{
			$text .= '...';
		}
		$text = trim($text);

		return $text;
	}

	/**
	 * Generate a select form
	 * 
	 * @param      string $name  Field name
	 * @param      array  $array Data to populate select with
	 * @param      mixed  $value Value to select
	 * @param      string $class Class to add
	 * @return     string HTML
	 */
	public function formSelect($name, $array, $value, $class='')
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
	 * Draw a table row
	 * 
	 * @param      string $h Header cell
	 * @param      string $c Cell content
	 * @return     string HTML
	 */
	public function tableRow($h,$c='')
	{
		$html  = '  <tr>' . "\n";
		$html .= '   <th>' . $h . '</th>' . "\n";
		$html .= '   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>' . "\n";
		$html .= '  </tr>' . "\n";

		return $html;
	}

	/**
	 * Convert a numerical vote value to a readable text value
	 * 
	 * @param      integer $rawnum   Vote value
	 * @param      string  $category Vote type
	 * @param      string  $output   Value to append to
	 * @return     string 
	 */
	public function convertVote($rawnum, $category, $output='')
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
	 * Display a form for setting the ranking of a wish
	 * 
	 * @param      string  $option   Component name
	 * @param      object  $wishlist Current wishlist
	 * @param      string  $task     Component task
	 * @param      object  $myvote   User's ote
	 * @param      integer $admin    User is admin?
	 * @return     string HTML
	 */
	public function rankingForm($option, $wishlist, $task, $myvote, $admin)
	{
		$importance = array(
			''    => JText::_('SELECT_IMP'),
			'0.0' => '0 -' . JText::_('RUBBISH'),
			'1'   => '1 - ' . JText::_('MAYBE'),
			'2'   => '2 - ' . JText::_('INTERESTING'),
			'3'   => '3 - ' . JText::_('GOODIDEA'), 
			'4'   => '4 - ' . JText::_('IMPORTANT'), 
			'5'   => '5 - ' . JText::_('CRITICAL')
		);
		$effort = array(
			''    => JText::_('SELECT_EFFORT'),
			'5'   => JText::_('FOURHOURS'),
			'4'   => JText::_('ONEDAY'),
			'3'   => JText::_('TWODAYS'),
			'2'   => JText::_('ONEWEEK'),
			'1'   => JText::_('TWOWEEKS'),
			'0.0' => JText::_('TWOMONTHS'), 
			'6'   => JText::_('don\'t know')
		);

		$html  = '<form method="post" action="index.php?option=' . $option . '" class="rankingform" id="rankForm">' . "\n";
		$html .= "\t".'<fieldset>' . "\n";
		$html .= "\t\t".'<label>' . "\n";
		$html .= "\t\t\t".WishlistHtml::formSelect('importance', $importance, $myvote->myvote_imp, 'rankchoices');
		$html .= "\t\t".'</label>' . "\n";
		if ($admin == 2) 
		{
			$html .= "\t\t".'<label>' . "\n";
			$html .= "\t\t\t".WishlistHtml::formSelect('effort', $effort, $myvote->myvote_effort, 'rankchoices');
			$html .= "\t\t".'</label>' . "\n";
		} 
		else 
		{
			$html .= "\t\t".'<input type="hidden" name="effort" value="6" />' . "\n";
		}
		$html .= "\t\t".'<input type="hidden" name="task" value="' . $task . '" />' . "\n";
		$html .= "\t\t".'<input type="hidden" name="category" value="' . $wishlist->category . '" />' . "\n";
		$html .= "\t\t".'<input type="hidden" name="rid" value="' . $wishlist->referenceid . '" />' . "\n";
		$html .= "\t\t".'<input type="hidden" name="wishid" value="' . $myvote->id . '" />' . "\n";
		$html .= "\t\t".'<input type="submit"  value="' . JText::_('SAVE') . '" />';
		$html .= "\t".'</fieldset>' . "\n";
		$html .= '</form>' . "\n";

		return $html;
	}

	/**
	 * Display a form for browsing wishes
	 * 
	 * @param      string  $option   Component name
	 * @param      array   $filters  Search filters
	 * @param      integer $admin    User is admin?
	 * @param      integer $id       An... id? @NOTE: What is the purpose of this?
	 * @param      integer $total    Record total
	 * @param      object  $wishlist Current wishlist
	 * @param      object  $pageNav  Pagination
	 * @return     string HTML
	 */
	public function browseForm($option, $filters, $admin, $id, $total, $wishlist, $pageNav)
	{
		$sortbys = array();
		if ($admin) 
		{
			$sortbys['ranking']=JText::_('RANKING');
		}
		$sortbys['date'] = JText::_('DATE');
		$sortbys['feedback'] = JText::_('FEEDBACK');

		if ($wishlist->banking) 
		{
			$sortbys['bonus'] = JText::_('BONUS_AND_POPULARITY');
		}
		$filterbys = array(
			'all'      => JText::_('ALL_WISHES_ON_THIS_LIST'),
			'open'     => JText::_('ACTIVE'),
			'granted'  => JText::_('GRANTED'), 
			'accepted' => JText::_('WISH_STATUS_ACCEPTED'), 
			'rejected' => JText::_('WISH_STATUS_REJECTED')
		);

		if ($admin == 1 or $admin == 2) 
		{ // a few extra options
			$filterbys['private'] = JText::_('PRIVATE');
			$filterbys['public'] = JText::_('PUBLIC');
			if ($admin == 2) 
			{
				$filterbys['mine'] = JText::_('MSG_ASSIGNED_TO_ME');
			}
		}
		$html  = '<fieldset>' . "\n";
		$html .= "\t" . '<label class="tagdisplay">' . JText::_('WISH_FIND_BY_TAGS') . ': ' . "\n";

		JPluginHelper::importPlugin('hubzero');
		$dispatcher =& JDispatcher::getInstance();
		$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $filters['tag'])));

		if (count($tf) > 0) 
		{
			$html .= $tf[0];
		} 
		else 
		{
			$html .= "\t\t" . '<input type="text" name="tags" id="tags-men" value="' . $filters['tag'] . '" />' . "\n";
		}
		$html .= "\t" . '</label>' . "\n";
		$html .= "\t" . '<label>' . JText::_('SHOW') . ': ' . "\n";
		$html .= WishlistHtml::formSelect('filterby', $filterbys, $filters['filterby'], '', '');
		$html .= "\t" . '</label>' . "\n";
		$html .= "\t" . ' &nbsp; <label> ' . JText::_('SORTBY') . ':' . "\n";
		$html .= WishlistHtml::formSelect('sortby', $sortbys, $filters['sortby'], '', '');
		$html .= "\t" . '</label>' . "\n";
		$html .= "\t" . '<input type="hidden" name="newsearch" value="1" />' . "\n";
		$html .= "\t" . '<input type="submit" value="' . JText::_('GO') . '" />' . "\n";
		$html .= '</fieldset>' . "\n";

		return $html;
	}

	/**
	 * Convert a timestamp to a more human readable string such as "3 days ago"
	 * 
	 * @param      string $date Timestamp
	 * @return     string
	 */
	public function nicetime($date)
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

