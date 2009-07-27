<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!class_exists('modFeaturedquestion')) {
	class modFeaturedquestion
	{
		private $params;

		//-----------

		public function __construct( $params ) 
		{
			$this->params = $params;
		}

		//-----------

		private function niceidformat($someid) 
		{
			while (strlen($someid) < 5) 
			{
				$someid = 0 . "$someid";
			}
			return $someid;
		}

		//-----------

		private function shortenText($text, $chars=300, $p=1) 
		{
			$text = strip_tags($text);
			$text = trim($text);

			if (strlen($text) > $chars) {
				$text = $text.' ';
				$text = substr($text,0,$chars);
				$text = substr($text,0,strrpos($text,' '));
				$text = $text.' &#8230;';
			}

			if ($text == '') {
				$text = '&#8230;';
			}

			if ($p) {
				$text = '<p>'.$text.'</p>';
			}

			return $text;
		}

		//-----------

		private function encode_html($str, $quotes=1)
		{
			$str = $this->ampersands($str);

			$a = array(
				//'&' => '&#38;',
				'<' => '&#60;',
				'>' => '&#62;',
			);
			if ($quotes) $a = $a + array(
				"'" => '&#39;',
				'"' => '&#34;',
			);

			return strtr($str, $a);
		}

		//-----------

		private function ampersands( $str ) 
		{
			$str = stripslashes($str);
			$str = str_replace('&#','*-*', $str);
			$str = str_replace('&amp;','&',$str);
			$str = str_replace('&','&amp;',$str);
			$str = str_replace('*-*','&#', $str);
			return $str;
		}

		//-----------

		private function mkt($stime)
		{
			if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
				$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
			}
			return $stime;
		}

		//-----------

		private function timeAgoo($timestamp)
		{
			// Store the current time
			$current_time = time();

			// Determine the difference, between the time now and the timestamp
			$difference = $current_time - $timestamp;

			// Set the periods of time
			$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

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
			if ($number != 1) $periods[$val].= 's';

			// Return text
			$text = sprintf("%d %s ", $number, $periods[$val]);

			// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
			if (($val >= 1) && (($current_time - $new_time) > 0)) {
				$text .= modFeaturedquestion::timeAgoo($new_time);
			}

			return $text;
		}

		//-----------

		private function timeAgo($timestamp) 
		{
			$text = $this->timeAgoo($timestamp);

			$parts = explode(' ',$text);

			$text  = $parts[0].' '.$parts[1];
			$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
			return $text;
		}

		//-----------

		public function display() 
		{
			ximport('featurehistory');
			
			if (!class_exists('FeatureHistory')) {
				return JText::_('Error: Missing FeatureHistory class.');
			}
			
			$database =& JFactory::getDBO();

			$params =& $this->params;
			
			$filters = array();
			$filters['limit'] = 1;
			
			$cls = trim($params->get( 'moduleclass_sfx' ));
			$txt_length = trim($params->get( 'txt_length' ));
			
			$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
			$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";
			
			$row = null;
			
			$fh = new FeatureHistory( $database );

			// Load some needed libraries
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
			
			// Check the feature history for today's feature
			$fh->loadActive($start, 'answers');
			
			// Did we find a feature for today?
			if ($fh->id && $fh->tbl == 'answers') {
				// Yes - load the member profile
				$row = new AnswersQuestion( $database );
				$row->load( $fh->objectid );
				
				$ar = new AnswersResponse( $database );
				$row->rcount = count($ar->getIds( $row->id ));
			} else {
				// No - so we need to randomly choose one
				$filters['start'] = 0;
				$filters['sortby'] = 'random';
				$filters['tag'] = '';
				$filters['filterby'] = 'open';
				$filters['created_before'] = date('Y-m-d', mktime(0,0,0,date('m'),(date('d')+7), date('Y')))." 00:00:00";
				
				$mp = new AnswersQuestion( $database );

				$rows = $mp->getResults( $filters );
				if (count($rows) > 0) {
					$row = $rows[0];
				}
			}

			$html = '';

			// Did we have a result to display?
			if ($row) {
				$config =& JComponentHelper::getParams( 'com_answers' );
					
				// Check if this has been saved in the feature history
				if (!$fh->id) {
					$fh->featured = $start;
					$fh->objectid = $row->id;
					$fh->tbl = 'answers';
					$fh->store();
				}
				
				$thumb = trim($params->get( 'defaultpic' ));
				
				$name = JText::_('ANONYMOUS');
				if ($row->anonymous == 0) {
					$juser =& JUser::getInstance( $row->created_by );
					if (is_object($juser)) {
						$name = $juser->get('name');
					}
				}
				
				$row->created = $this->mkt($row->created);
				$when = $this->timeAgo($row->created);
				
				// Build the HTML
				$html .= '<div class="'.$cls.'">'."\n";
				//$html .= '<h3><a href="'.JRoute::_('index.php?option=com_answers').'">'.JText::_('Featured Question').'</a></h3>'."\n";
				$html .= '<h3>'.JText::_('Featured Question').'</h3>'."\n";
				if (is_file(JPATH_ROOT.$thumb)) {
					$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'"><img width="50" height="50" src="'.$thumb.'" alt="" /></a></p>'."\n";
				}
				$html .= '<p><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'">'.stripslashes($row->subject).'</a>'."\n";
				if ($row->question) {
					$html .= ': '.$this->shortenText($this->encode_html(strip_tags($row->question)), $txt_length, 0)."\n";
				}
				$html .= '<br /><span>'.JText::sprintf('ASKED_BY', $name).'</span> - <span>'.$when.' ago</span> - <span>';
				$html .= ($row->rcount == 1) ? JText::sprintf('RESPONSE', $row->rcount) : JText::sprintf('RESPONSES', $row->rcount);
				$html .= '</span></p>'."\n";
				$html .= '</div>'."\n";
			}

			// Output HTML
			return $html;
		}
	}
}

//-------------------------------------------------------------

$modfeaturedquestion = new modFeaturedquestion( $params );

require( JModuleHelper::getLayoutPath('mod_featuredquestion') );
