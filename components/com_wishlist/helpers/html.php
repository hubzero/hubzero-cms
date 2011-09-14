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
defined('_JEXEC') or die( 'Restricted access' );

if (!defined('n')) {

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''br''
 */
	define('br','<br />');

/**
 * Description for ''sp''
 */
	define('sp','&#160;');

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

/**
 * Short description for 'WishlistHtml'
 * 
 * Long description (if any) ...
 */
class WishlistHtml
{

	/**
	 * Short description for 'txt_unpee'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $pee Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
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
	 * Short description for 'cleanText'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $text Parameter description (if any) ...
	 * @param      integer $desclen Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = str_replace( '{mosimage}', '', $text );
		$text = str_replace( "\n", ' ', $text );
		$text = str_replace( "\r", ' ', $text );
		$text = preg_replace( '/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i','\\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text);
		$text = preg_replace( '/{.+?}/', '', $text);
		$text = strip_tags( $text );
		if (strlen($text) > $desclen) $elipse = true;
		$text = substr( $text, 0, $desclen );
		if ($elipse) $text .= '...';
		$text = trim($text);

		return $text;
	}

	/**
	 * Short description for 'formSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}

	/**
	 * Short description for 'tableRow'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $h Parameter description (if any) ...
	 * @param      string $c Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function tableRow($h,$c='')
	{
		$html  = t.'  <tr>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>'.n;
		$html .= t.'  </tr>'.n;

		return $html;
	}

	/**
	 * Short description for 'convertVote'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $rawnum Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $output Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function convertVote($rawnum, $category, $output='')
	{
		$rawnum = round($rawnum);
		if ($category == 'importance') {
			switch ($rawnum)
			{
				case '0': $output = JText::_('RUBBISH');     break;
				case '1': $output = JText::_('MAYBE');       break;
				case '2': $output = JText::_('INTERESTING'); break;
				case '3': $output = JText::_('GOODIDEA');    break;
				case '4': $output = JText::_('IMPORTANT');   break;
				case '5': $output = JText::_('CRITICAL');    break;
			}
		} else if ($category == 'effort') {
			switch ($rawnum)
			{
				case '0': $output = JText::_('TWOMONTHS');   break;
				case '1': $output = JText::_('TWOWEEKS');    break;
				case '2': $output = JText::_('ONEWEEK');     break;
				case '3': $output = JText::_('TWODAYS');     break;
				case '4': $output = JText::_('ONEDAY');      break;
				case '5': $output = JText::_('FOURHOURS');   break;
				case '6': $output = JText::_('don\'t know'); break;
				case '7': $output = JText::_('N/A');         break;
			}
		}

		return $output;
	}

	/**
	 * Short description for 'rankingForm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @param      mixed $wishlist Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @param      mixed $myvote Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function rankingForm($option, $wishlist, $task, $myvote, $admin)
	{
		$importance = array(''=>JText::_('SELECT_IMP'),'0.0'=>'0 -'.JText::_('RUBBISH'),'1'=>'1 - '.JText::_('MAYBE'),'2'=>'2 - '.JText::_('INTERESTING'),
		'3'=>'3 - '.JText::_('GOODIDEA'), '4'=>'4 - '.JText::_('IMPORTANT'), '5'=>'5 - '.JText::_('CRITICAL'));
		$effort = array(''=>JText::_('SELECT_EFFORT'),'5'=>JText::_('FOURHOURS'),'4'=>JText::_('ONEDAY'),
		'3'=>JText::_('TWODAYS'),'2'=>JText::_('ONEWEEK'),'1'=>JText::_('TWOWEEKS'),'0.0'=>JText::_('TWOMONTHS'), '6'=>JText::_('don\'t know'));

		$html  = '<form method="post" action="index.php?option='.$option.'" class="rankingform" id="rankForm">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.WishlistHtml::formSelect('importance', $importance, $myvote->myvote_imp, 'rankchoices');
		$html .= t.t.'</label>'.n;
		if ($admin == 2) {
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.WishlistHtml::formSelect('effort', $effort, $myvote->myvote_effort, 'rankchoices');
			$html .= t.t.'</label>'.n;
		} else {
			$html .= t.t.'<input type="hidden" name="effort" value="6" />'.n;
		}
		$html .= t.t.'<input type="hidden" name="task" value="'.$task.'" />'.n;
		$html .= t.t.'<input type="hidden" name="category" value="'.$wishlist->category.'" />'.n;
		$html .= t.t.'<input type="hidden" name="rid" value="'.$wishlist->referenceid.'" />'.n;
		$html .= t.t.'<input type="hidden" name="wishid" value="'.$myvote->id.'" />'.n;
		$html .= t.t.'<input type="submit"  value="'.JText::_('SAVE').'" />';
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;

		return $html;
	}
	//-----------


	/**
	 * Short description for 'browseForm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      unknown $total Parameter description (if any) ...
	 * @param      object $wishlist Parameter description (if any) ...
	 * @param      unknown $pageNav Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function browseForm($option, $filters, $admin, $id, $total, $wishlist, $pageNav)
	{
		$sortbys = array();
		if ($admin) {
			$sortbys['ranking']=JText::_('RANKING');
		}
		$sortbys['date'] = JText::_('DATE');
		$sortbys['feedback'] = JText::_('FEEDBACK');

		if ($wishlist->banking) {
			$sortbys['bonus']=JText::_('BONUS_AND_POPULARITY');
		}
		$filterbys = array('all'=>JText::_('ALL_WISHES_ON_THIS_LIST'),'open'=>JText::_('ACTIVE'),'granted'=>JText::_('GRANTED'), 'accepted'=>JText::_('WISH_STATUS_ACCEPTED'), 'rejected'=>JText::_('WISH_STATUS_REJECTED'));

		if ($admin == 1 or $admin == 2) { // a few extra options
			$filterbys['private'] = JText::_('PRIVATE');
			$filterbys['public'] = JText::_('PUBLIC');
			if ($admin == 2) {
				$filterbys['mine'] = JText::_('MSG_ASSIGNED_TO_ME');
			}
		}
		$html = '';
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.'<label class="tagdisplay">'.JText::_('WISH_FIND_BY_TAGS').': '.n;

		JPluginHelper::importPlugin( 'hubzero' );
		$dispatcher =& JDispatcher::getInstance();
		$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$filters['tag'])) );

		if (count($tf) > 0) {
			$html .= $tf[0];
		} else {
			$html .= t.t.t.'<input type="text" name="tags" id="tags-men" value="'.$filters['tag'].'" />'.n;
		}
		$html .= '</label>';
		$html .= t.t.t.'<label >'.JText::_('SHOW').': '.n;
		$html .= WishlistHtml::formSelect('filterby', $filterbys, $filters['filterby'], '', '');
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.' &nbsp; <label> '.JText::_('SORTBY').':'.n;
		$html .= WishlistHtml::formSelect('sortby', $sortbys, $filters['sortby'], '', '');
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'<input type="hidden" name="newsearch" value="1" />'.n;
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.'</fieldset>'.n;

		return $html;
	}

	/**
	 * Short description for 'nicetime'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $date Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function nicetime($date)
	{
		if (empty($date)) {
			return "No date provided";
		}

		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();
		$unix_date = strtotime($date);

		   // check validity of date
		if (empty($unix_date)) {
			return "Bad date";
		}

		// is it future date or past date
		if ($now > $unix_date) {
			$difference = $now - $unix_date;
			$tense = "ago";

		} else {
			$difference = $unix_date - $now;
			//$tense = "from now";
			$tense = "";
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1) {
			$periods[$j].= "s";
		}

		return "$difference $periods[$j] {$tense}";
	}
}

