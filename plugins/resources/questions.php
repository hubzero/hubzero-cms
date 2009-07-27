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
JPlugin::loadLanguage( 'plg_resources_questions' );
	
//-----------

class plgResourcesQuestions extends JPlugin
{
	function plgResourcesQuestions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'questions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesAreas( $resource ) 
	{
		if ($resource->type != 7) {
			$areas = array();
		} else {
			$areas = array(
				'questions' => JText::_('QUESTIONS')
			);
		}
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return array('html'=>'','metadata'=>'');
		}

		$database =& JFactory::getDBO();
		
		// Are we banking?
		$xhub =& XFactory::getHub();		
		$banking = $xhub->getCfg('hubBankAccounts');
		$xuser =& XFactory::getUser();
		
		// Info aboit points link
		$aconfig =& JComponentHelper::getParams( 'com_answers' );
		$infolink = $aconfig->get('infolink') ? $aconfig->get('infolink') : '/kb/points/'; 

		// Get a needed library
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php' );

		// Get all the questions for this tool
		$a = new AnswersQuestion( $database );
		//$rows = $a->getQuestionsByTag( 'tool:'.$resource->alias );
		
		$filters = array();
		$filters['limit']    = JRequest::getInt( 'limit', 0 );
		$filters['start']    = JRequest::getInt( 'limitstart', 0 );
		$filters['tag']      = $resource->type== 7 ?  'tool'.$resource->alias : 'resource'.$resource->id;
		$filters['q']        = JRequest::getVar( 'q', '' );
		$filters['filterby'] = JRequest::getVar( 'filterby', '' );
		$filters['sortby']   = JRequest::getVar( 'sortby', 'withinplugin' );
		$rows = $a->getResults( $filters );

		$count = $a->getCount( $filters );
		
		// count questions
		/*
		if ($rows) {
			foreach($rows as $row) {
				$row->reports = $this->get_reports($row->id, 'question');
				if(!$row->reports) {
					$count++;
				}
			}
		}
		*/
		
		$html = '';
		if ($rtrn == 'all' || $rtrn == 'html') {
			// Did we get results back?
	
			if ($rows) {
				
				ximport('xdocument');
				XDocument::addComponentStylesheet('com_answers');
				
				$title = JText::_('RECENT_QUESTIONS');
				$limit = $this->_params->get('display_limit');
				$limit = $limit ? $limit : 10;
				if($count > 0 && ($count > $limit)) {
				$tag = $resource->type== 7 ?  'tool'.$resource->alias : 'resource'.$resource->id;
				$title.= ' <span>(<a href="'.JRoute::_('index.php?option=com_answers'.a.'task=search').'?tag='.$tag.'&sortby=withinplugin">'.JText::_('VIEW_ALL') .' '.$count.'</a>)</span>';
				}
				else {
				$title .= ' ('.$count.')';
				}
				$sbjt  = '<div class="answers_plugin">'.n;
				$sbjt .= '<h3>'.$title.'</h3>'.n;
				$sbjt .= '</div>'.n;

				// Loop through the results and build the HTML
				$sbjt .= t.t.'<ul class="questions plugin">'.n;
				
				/*
				foreach ($rows as $row) 
				{
					if ($row->anonymous == 0) {
						$name = JText::_('UNKNOWN');
						$xuser =& XUser::getInstance( $row->created_by );
						if (is_object($xuser)) {
							$name = $xuser->get('name');
						}
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
				
				*/
				$i=1;
				foreach ($rows as $row) 
				{
				$row->reports = $this->get_reports($row->id, 'question');			
				$row->created = $this->mkt($row->created);
				$row->when = $this->timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				//$row->reports = $this->get_reports($row->id, 'question');
				
				if(!$row->reports && $i<= $limit) {
					$i++;	
					$link_on = JRoute::_('index.php?option=com_answers'.a.'task=question'.a.'id='.$row->id);
					$tags    = $this->getTagCloud($row->id, $option);
					$alt_r  = ($row->rcount == 0) ? 'No' : $row->rcount;
					$alt_r .= ' '.JText::_('RESPONSE');
					$alt_r .= ($row->rcount == 1) ? '' : 's';
					$alt_r .= ' '.JText::_('TO_THIS_QUESTION');
					
					$alt_v  = ($row->helpful == 0) ? 'No' : $row->helpful;
					$alt_v .= ' '.JText::_('RECOMMENDATION');
					$alt_v .= ($row->helpful == 1) ? '' : 's';
					$alt_v .= ' '.JText::_('AS_A_GOOD_QUESTION');
					
					// author name
					$name = JText::_('ANONYMOUS');
					if ($row->anonymous == 0) {
						$xuser =& XUser::getInstance( $row->created_by );
						if (is_object($xuser)) {
							$name = $xuser->get('name');
						} else {
							$name = JText::_('Unknown');
						}
					}
			
					
					$sbjt .= t.' <li class="reg';
					$sbjt .= (isset($row->reward) && $row->reward == 1 && $banking) ? ' hasreward' : '';
					$sbjt .= ($row->state == 1) ? ' answered' : '';
					$sbjt .= '"';
					//$html .= ($row->rcount > 0) ? ' hasanswers"' : '"';
					$sbjt .= '>'.n;
					/*
					if ($row->state == 1) {
						$sbjt .= t.t.'<p class="acceptanswer"><span>'.JText::_('ANSWERED').'</span></p>'.n;
					}
					else if (isset($row->reward) && $row->reward == 1 && $banking) {
						$sbjt .= t.t.'<p class="rewardset">+ '.$row->points.' <a href="'.$infolink.'" title="There is a '.$row->points.' point reward for answering this question.">&nbsp;</a></p>'.n;
					}*/
					$sbjt .= t.t.'<div class="ensemble_left">';
					if ($row->question != '') {
						$row->question = stripslashes($row->question);
						$fulltext = htmlspecialchars($this->cleanText($row->question));
					}
					else {
					 	$fulltext = stripslashes($row->subject);
					}
					$sbjt .= t.t.'<h4><a href="'. $link_on .'" title="'.$fulltext.'">'.stripslashes($row->subject).'</a></h4>'.n;
					
					$sbjt .= t.t.'<p class="supplemental">'.JText::_('ASKED_BY').' '.$name;
					$sbjt .= ' - '.$row->when.' ago';
					$sbjt .= '</p>'.n;
					/*
					if($tags) {
					$sbjt .= t.t.'<p>Tags:</p> '.$tags.n;
					}
					*/
					
					$sbjt .= t.t.'</div>';
					$sbjt .= t.t.'<div class="ensemble_right">';
					$sbjt .= t.t.'<div class="statusupdate">'.n;
					
					$sbjt .= t.t.'<p>'.$row->rcount.'<span class="responses_';
					$sbjt .= ($row->rcount == 0) ? 'no' : 'yes';
					$sbjt .= '"><a href="'.$link_on.'#answers" title="'.$alt_r.'">&nbsp;</a></span>';
					$sbjt .= '  '.$row->helpful.' <span class="votes_';
					$sbjt .= ($row->helpful == 0) ? 'no' : 'yes';
					$sbjt .= '"><a href="'.$link_on.'?vote=1" title="'.$alt_v.'">&nbsp;</a></span>';
					$sbjt .= t.t.'</p>';
					//$sbjt .= ($row->state==1) ? '<span class="update_answered">'.JText::_('ANSWERED').'</span>' : '<span class="update_unanswered"><a href="">'.JText::_('Answer this').'</a></span>';
					$sbjt .= ($row->state==1) ? '<span class="update_answered">'.JText::_('ANSWERED').'</span>' : '';
					$sbjt .= t.t.'</div>';
					$sbjt .= t.t.'<div class="rewardarea">'.n;
					if (isset($row->reward) && $row->reward == 1 && $banking) {
						$sbjt .= t.t.'<p>+ '.$row->points.' <a href="'.$infolink.'" title="There is a '.$row->points.' point reward for answering this question.">&nbsp;</a></p>'.n;
					}
					$sbjt .= t.t.'</div>';
					$sbjt .= t.t.'</div>';
					$sbjt .= t.t.'<div style="clear:left"></div>';
					$sbjt .= t.'&nbsp; </li>'.n;
				  }
				  else if($row->reports && $i<= $limit) {
				  // do not display
					$sbjt .= t.' <li class="reg under_review"> ';
					$sbjt .= t.'<h4 class="review">'.JText::_('QUESTION_UNDER_REVIEW').'</h4>'.n;
					$sbjt .= t.t.'<p class="supplemental">'.JText::_('ASKED_BY').' '.$name;
					$sbjt .= ' - '.$row->when.' ago';
					$sbjt .= '</p>'.n;
					$sbjt .= t.'&nbsp; </li>'.n;
				  }
				
				}
				
				$sbjt .= t.t.'</ul>'.n;
			} else {
				$sbjt  = t.t.'<p>'.JText::_('NO_QUESTIONS_FOUND').'</p>'.n;
			}

			$html  = ResourcesHtml::hed(3,'<a name="questions"></a>'.JText::_('QUESTIONS_AND_ANSWERS')).n;
			$html .= ResourcesHtml::aside(
						'<p>'.JText::_('QUESTIONS_EXPLANATION').'</p>'.
						'<p class="add"><a href="/answers/question/new/?tag=tool:'.$resource->alias.'">'.JText::_('ASK_A_QUESTION_ABOUT_TOOL').'</a></p>');
			$html .= ResourcesHtml::subject($sbjt);
		}

		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$metadata  = '<p class="answer"><a href="'.JRoute::_('index.php?option=com_resources'.a.'alias='.$resource->alias.a.'active=questions').'">';
			if ($count == 1) {
				$metadata .= JText::sprintf('NUM_QUESTION',$count);
			} else {
				$metadata .= JText::sprintf('NUM_QUESTIONS',$count);
			}
			$metadata .= '</a> (<a href="/answers/question/new/?tag=tool:'.$resource->alias.'">'.JText::_('ASK_A_QUESTION').'</a>)</p>'.n;
		}

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

	public function shortenText($text, $chars=200) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			//$text = $text.' &#8230;';
		}

		return $text;
	}
	
	//-----------
	
	public function get_reports($id, $cat)
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$filters = array();
		$filters['id']  = $id;
		$filters['category']  = $cat;
		$filters['state']  = 0;
		
		// Check for abuse reports on an item
		$ra = new ReportAbuse( $database );
		
		return $ra->getCount( $filters );
	}
	
	//-----------
	
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
	

	//-----------

	function getTagCloud($id, $tagger_id=0)
	{
		$database =& JFactory::getDBO();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
		$tagging = new AnswersTags( $database );

		return $tagging->get_tag_cloud( 0, 0, $id );
	}
}