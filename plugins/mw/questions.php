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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_mw_questions' );
	
//-----------

class plgMwQuestions extends JPlugin
{
	function plgMwQuestions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'mw', 'questions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMwAreas( $resource ) 
	{
		$areas = array(
			'questions' => JText::_('QUESTIONS')
		);
		return $areas;
	}

	//-----------

	function onMw( $toolname, $option, $authorized, $areas )
	{
		$database =& JFactory::getDBO();

		// Get a needed library
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php');

		// Get all the questions for this tool
		$a = new mosAnswersQuestion( $database );
		$rows = $a->getQuestionsByTag( 'tool:'.$toolname );

		// Did we get results back?
		if ($rows) {
			ximport('xdocument');
			ximport('xprofile');
			XDocument::addComponentStylesheet('com_answers');

			// Loop through the results and build the HTML
			$sbjt  = t.t.'<ul class="questions">'.n;
			foreach ($rows as $row) 
			{
				if ($row->anonymous == 0) {
					$xprofile =& XProfile::getInstance( $row->created_by );
					$name = $xprofile->get('name');
				} else {
					$name = JText::_('ANONYMOUS');
				}

				$row->created = $this->mkt($row->created);
				$when = $this->timeAgo($row->created);

				$tags = $this->getTagCloud($row->id);

				$link_on = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);

				$sbjt .= t.t.t.'<h4><a href="'. $link_on .'">'.$row->subject.'</a></h4>'.n;
				$sbjt .= t.t.t.'<p class="snippet">';
				$sbjt .= $this->shortenText($row->question, 100, 0);
				$sbjt .= '</p>'.n;
				$sbjt .= t.t.t.'<p>'.JText::sprintf('ASKED_BY',$name).' - '.JText::sprintf('TIME_AGO',$when).' - ';
				if ($row->rcount == 1) {
					$sbjt .= JText::sprintf('NUM_RESPONSE',$row->rcount);
				} else {
					$sbjt .= JText::sprintf('NUM_RESPONSES',$row->rcount);
				}
				$sbjt .= '</p>'.n;
				$sbjt .= t.t.t.'<p>Tags:</p> '.$tags.n;
			}
			$sbjt .= t.t.'</ul>'.n;
		} else {
			$sbjt  = t.t.'<p>'.JText::_('NO_QUESTIONS_FOUND').'</p>'.n;
		}

		$html  = MwHtml::hed(3,'<a name="questions"></a>'.JText::_('QUESTIONS_AND_ANSWERS')).n;
		$html .= MwHtml::div(
					'<p>'.JText::_('QUESTIONS_EXPLANATION').'</p>'.
					'<p class="add"><a href="/answers/question/new/?tag=tool:'.$toolname.'">'.JText::_('ASK_A_QUESTION_ABOUT_TOOL').'</a></p>', 'aside');
		$html .= MwHtml::div($sbjt,'subject');

		/*
		$metadata  = '<p class="question"><a href="'.$resource->sef.'?active=questions">';
		if (count($rows) == 1) {
			$metadata .= JText::sprintf('NUM_QUESTION',count($rows));
		} else {
			$metadata .= JText::sprintf('NUM_QUESTIONS',count($rows));
		}
		$metadata .= '</a> (<a href="/answers/question/new/?tag=tool:'.$resource->alias.'">'.JText::_('ASK_A_QUESTION').'</a>)</p>'.n;
		*/
		$metadata = '';

		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array('SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR', 'DECADE');
		
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
		if ($number != 1) $periods[$val].= 'S';
		
		// Return text
		$text = sprintf("%d %s ", $number, JText::_($periods[$val]));
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= $this->timeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	function timeAgo($timestamp) 
	{
		$text = $this->timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	
	//-----------

	function shortenText($text, $chars=300, $p=1) 
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

	function getTagCloud($id, $tagger_id=0)
	{
		$database =& JFactory::getDBO();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
		$tagging = new AnswersTags( $database );
		$tags = $tagging->get_tags_on_object($id, 0, 0, $tagger_id);
		
		if (count($tags) > 0) {
			$tagarray = array();
			$tagarray[] = '<ol class="tags">';
			if (!empty($tags))
			foreach ($tags as $tag)
			{
				$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
				$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
				$tagarray[] = ' <li><a href="'.JRoute::_('index.php?option=com_answers&task=tag&tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
			}
			$tagarray[] = '</ol>';

			$alltags = implode( "\n", $tagarray );
		} else {
			$alltags = '&nbsp;';
		}
		return $alltags;
	}
}
