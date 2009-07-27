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

//-------------------------------------------------------------

class modRecentQuestions
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
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
			$text .= modRecentQuestions::timeAgoo($new_time);
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

	private function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	private function getTagCloud($id, $tagger_id=0)
	{
		require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
		
		$database =& JFactory::getDBO();
		
		$tagging = new AnswersTags( $database );

		return $tagging->get_tag_cloud(0, 0, $id);
	}
	
	//-----------

	public function display()
	{
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$state = $params->get( 'state' );
		$limit = intval( $params->get( 'limit' ) );
		
		switch ($state) 
		{
			case 'open': $st = "a.state=0"; break;
			case 'closed': $st = "a.state=1"; break;
			case 'both':
			default: $st = ""; break;
		}
		
		$tag = JRequest::getVar( 'tag', '', 'get' );
		$style = JRequest::getVar( 'style', '', 'get' );
		if ($tag) {
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount"
				."\n FROM #__answers_questions AS a, #__tags_object AS t, #__tags AS tg"
				."\n WHERE a.id=t.questionid AND tg.id=t.tagid AND t.tbl='answers' AND (tg.tag='".$tag."' OR tg.raw_tag='".$tag."' OR tg.alias='".$tag."')";
			if ($st) {
				$query .= " AND ".$st;
			}
		} else {
			$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount"
				."\n FROM #__answers_questions AS a";
			if ($st) {
				$query .= "WHERE ".$st;
			}
		}
		$query .= "\n ORDER BY a.created DESC";
		$query .= ($limit) ? "\n LIMIT ".$limit : "";
		
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		if (count($rows) > 0) {
			$html  = "\t\t".'<ul class="questions">'."\n";
			foreach ($rows as $row) 
			{
				$name = JText::_('ANONYMOUS');
				if ($row->anonymous == 0) {
					$juser =& JUser::getInstance( $row->created_by );
					if (is_object($juser)) {
						$name = $juser->get('name');
					}
				}
				
				$link_on = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);

				$row->created = $this->mkt($row->created);
				$when = $this->timeAgo($row->created);
				
				$tags = $this->getTagCloud($row->id);
				
				$html .= "\t\t".' <li>'."\n";
				if ($style == 'compact') {
					$html .= "\t\t\t".'<a href="'. $link_on .'">'.$row->subject.'</a>'."\n";
					$html .= '<span> - ';
					$html .= ($row->rcount == 1) ? JText::sprintf('RESPONSE', $row->rcount) : JText::sprintf('RESPONSES', $row->rcount);
					$html .= '</span>';
				} else {
					$html .= "\t\t\t".'<h4><a href="'. $link_on .'">'.$row->subject.'</a></h4>'."\n";
					$html .= "\t\t\t".'<p class="snippet">';
					$html .= $this->shortenText($row->question, 100, 0);
					$html .= '</p>'."\n";
					$html .= "\t\t\t".'<p>'.JText::sprintf('ASKED_BY', $name).' - '.$when.' ago - ';
					$html .= ($row->rcount == 1) ? JText::sprintf('RESPONSE', $row->rcount) : JText::sprintf('RESPONSES', $row->rcount);
					$html .= '</p>'."\n";
					$html .= "\t\t\t".'<p>'.JText::_('TAGS').':</p> '.$tags."\n";
				}
				$html .= "\t\t".' </li>'."\n";
			}
			$html .= "\t\t".'</ul>'."\n";
		} else {
			$html  = "\t\t".'<p>'.JText::_('NO_RESULTS').'</p>'."\n";
		}

		echo $html;
	}
}

//-------------------------------------------------------------

$modrecentquestions = new modRecentQuestions();
$modrecentquestions->params = $params;

require( JModuleHelper::getLayoutPath('mod_recentquestions') );
?>
