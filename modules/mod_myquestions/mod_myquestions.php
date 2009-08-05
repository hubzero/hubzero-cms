<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
// Joomla module
// "My Questions"
//    This module displays questions submitted by the user,
//    as well as questions he/she can answer based on tags
//-------------------------------------------------------------

class modMyQuestions
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

	private function formatTags($string='', $num=3, $max=25)
	{
		
		$out = '';
		$tags = split(',',$string);

		if(count($tags) > 0) {
			$out .= '<span class="taggi">'."\n";
			$counter = 0;
			
			for($i=0; $i< count($tags); $i++) {
				$counter = $counter + strlen(stripslashes($tags[$i]));	
				if($counter > $max) {
					$num = $num - 1;
				}
				if($i < $num) {
					// display tag
					$normalized = $this->normalize_tag($tags[$i]);
					$out .= "\t".'<a href="'.JRoute::_('index.php?option=com_tags'.a.'tag='.$normalized).'">'.stripslashes($tags[$i]).'</a> '."\n";
				}
				
			}
			if($i > $num) {
				$out .= ' (&#8230;)';
			}
			$out .= '</span>'."\n";
		}
		
		return $out;
	
	}
	
	//-----------

	private function getInterests($cloud=0)
	{
		$database =& JFactory::getDBO();
		$juser 	 =& JFactory::getUser();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
		
		// Get tags of interest
		$mt = new MembersTags( $database );
		if($cloud) {
			$tags = $mt->get_tag_cloud(0,0,$juser->get('id') );
		} else {
			$tags = $mt->get_tag_string( $juser->get('id') );
		}
		
		return $tags;	
	
	}
	
	//-----------

	private function getQuestions($kind="open", $interests=array())
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();		
		
		// Get some classes we need
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
				
		$aq = new AnswersQuestion( $database );		
		if($this->banking) {
			$AE = new AnswersEconomy( $database );
			$BT = new BankTransaction( $database);
		}
			
		$params =& $this->params;
		$moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;		
		
		$filters = array();
		$filters['limit']    = $limit;
		$filters['start']    = 0;
		$filters['tag']		 = '';
		$filters['filterby'] = 'open';
		$filters['sortby']   = 'date';
		
		switch( $kind ) 
		{
			case 'mine':
			$filters['mine']	= 1;
			$filters['sortby']   = 'responses';    
		    break;
			
			case 'assigned': 
			$filters['mine']	= 0;
			require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.author.php' );
			
			$TA = new ToolAuthor($database); 
			$tools = $TA->getToolContributions($juser->get('id'));
			if($tools) {
				foreach($tools as $tool) {
					$filters['tag'] .= 'tool'.$tool->toolname.',';
				}
			}			
			if(!$filters['tag']) { $filters['filterby']   = 'none'; }	   
		    break;
			
			case 'interest':
			$filters['mine']	= 0;
			$interests 	= count($interests) <=0 ? $this->getInterests() : $interests;
			$filters['filterby']   =  (!$interests) ? 'none' : 'open';
			$filters['tag'] = 	$interests;		    
		    break;
			
		}
		
		$results = $aq->getResults( $filters );
	 	if($this->banking && $results) {
	 		$awards = array();
			
			foreach($results as $result) {
				// Calculate max award
				$result->marketvalue = round($AE->calculate_marketvalue($result->id, 'maxaward'));
				$result->maxaward = round(2*(($result->marketvalue)/3));
				if($kind !="mine") {
					$result->maxaward = $result->maxaward + $result->reward;
				}
				$awards[] = ($result->maxaward) ? $result->maxaward : 0;
			}
				
			// re-sort by max reponses
			array_multisort($awards, SORT_DESC, $results);
	 	}
					
		return $results;			
			
	}
	
	
	//-----------
	
	public function display() 
	{
			
		$juser =& JFactory::getUser();			
		$option = 'com_answers';
	
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_myquestions');
		
		// show assigned?
		$show_assigned = intval( $this->params->get( 'show_assigned' ) );
		$show_assigned = $show_assigned ? $show_assigned : 0;
		
		// show interests?
		$show_interests = intval( $this->params->get( 'show_interests' ) );
		$show_interests = $show_interests ? $show_interests : 0;
	
		// max num of questions
		$max = intval( $this->params->get( 'max_questions' ) );
		$max= $max ? $max : 12;
		$c = 1;
		
		// Build the HTML
		$foundresults = false;
		$html = '';
		
		$opencount = 0;
		$assignedcount = 0;
		$othercount = 0;
		
		// Get Open Questions User Asked
		$openquestions = $this->getQuestions("mine");
		$opencount = $openquestions ? count($openquestions) : 0;
				
		$onum  = $opencount.' ';
		$onum .= ($opencount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		
		// Get Questions related to user contributions
		if($show_assigned) {
			$c++;
			$assigned = $this->getQuestions("assigned");
			$assignedcount = $assigned ? count($assigned) : 0; 
				
			$anum  = $assignedcount.' ';
			$anum .= ($assignedcount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		}
		
		
		// Get interest tags
		if($show_interests) {
			$c++;
			$interests = $this->getInterests();
			if(!$interests) { $intext= JText::_('NA'); }
			else {
			$intext = $this->formatTags($interests);
			}
				
			// Get questions of interest
			$otherquestions = $this->getQuestions("interest", $interests);
			$othercount = $otherquestions ? count($otherquestions) : 0; 
				
			$othnum  = $othercount.' ';
			$othnum .= ($othercount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		}
		
		// Limit number of shown questions
		$totalq = $opencount + $assignedcount + $othercount;
		$limit_mine = $max;
		$breaker = $max/$c;
		$limit_mine = ($totalq - $opencount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $opencount);
		$limit_assigned = ($totalq - $assignedcount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $assignedcount);
		$limit_interest = ($totalq - $othercount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $othercount);
			
		
		// Questions I asked
		$html .= '<h4>'.JText::_('OPEN_QUESTIONS').' <small><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'?filterby=open">'.JText::_('VIEW_ALL').'</a></small></h4>'.n;
		
		//$html .= '<div class="category-wrap">'.n;
		
		if ($openquestions) {
			$html .= '<ol class="expandedlist">'.n;			
			for ($i=0; $i < count($openquestions); $i++) 
			{
				if ($i < $limit_mine) {
					$rcount = (isset($openquestions[$i]->rcount)) ?  $openquestions[$i]->rcount : 0;
					$rclass = ($rcount > 0) ?  'yes' : 'no';
					$href = JRoute::_('index.php?option='.$option.a.'task=question'.a.'id='.$openquestions[$i]->id);
					
					$html .= t.'<li class="question">'.n;				
					$html .= t.t.'<span class="q"><a href="'.$href.'">'.$this->shortenText(stripslashes($openquestions[$i]->subject), 60, 0).'</a></span>'.n;
					$html .= t.t.'<span class="extra">'.$rcount.'<span class="responses_'.$rclass.'">&nbsp;</span></span>'.n;
					if ($rcount > 0 && $this->banking) {
						$html .= t.t.'<p class="earnpoints">'.JText::_('CLOSE_THIS_QUESTION').' '.$openquestions[$i]->maxaward.' '.JText::_('POINTS').'</p>';
					}
					$html .= t.'</li>'.n;
				}
			}
			$html .= '</ol>'.n;
		} else {
			$html .= '<p>'. JText::_('NO_QUESTIONS') .'</p>';
		}
		//$html .= '</div>'."\n";
		//$html .= '<p class="more"><a href="'.JRoute::_('index.php?option='.$option.a.'task=new').'">'. JText::_('ADD_QUESTION') .' &raquo;</a></p>';
		$html .= "\t".'<ul class="module-nav">'."\n";
		//$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_support&task=tickets').'">'.JText::_('ALL_TICKETS').'</a></li>'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=new').'">'.JText::_('ADD_QUESTION').'</a></li>'."\n";
		$html .= "\t".'</ul>'."\n";
	
		
		// Questions related to my contributions
		if ($show_assigned) {
			$html .= '<h4>'.JText::_('Open Questions on My Contributions').' <small><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'?filterby=open'.a.'assigned=1">'.JText::_('VIEW_ALL').'</a></small></h4>'.n;
			if ($assigned) {
				$html .= '<p class="incentive"><span>'.strtolower(JText::_('BEST ANSWER MAY EARN')).'</span></p>'.n;
				//$html .= '<div class="category-wrap">'.n;
			
				$html .= '<ol class="expandedlist">'.n;			
				for ($i=0; $i < count($assigned); $i++) 
				{
					if($i < $limit_assigned) {
						$href = JRoute::_('index.php?option='.$option.a.'task=question'.a.'id='.$assigned[$i]->id);
						$html .= t.'<li class="question">'.n;				
						$html .= t.t.'<span class="q"><a href="'.$href.'">'.$this->shortenText(stripslashes($assigned[$i]->subject), 60, 0).'</a></span>'.n;
						if($this->banking) {
							$html .= t.t.'<span class="extra economy">'.$assigned[$i]->maxaward.' <span class="pts">'.strtolower(JText::_('pts')).'</span></span>'.n;
						}			
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				//$html .= '</div>'."\n";
			} else {
				$html .= '<p>'. JText::_('NO_QUESTIONS').'</p>'.n;
			}
		}
		
		// Questions of interest
		if ($show_interests) {
			$html .= '<h4>'.JText::_('Open Questions of Interest').' <small><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'?filterby=open'.a.'interest=1">'.JText::_('VIEW_ALL').'</a></small></h4>'.n;
			$html .= t.'<p class="category-header-details">'.n;
			if ($interests) {
				$html .= t.t.'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('EDIT').'</a>]</span>'.n;
			} else {
				$html .= t.t.'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('ADD_INTERESTS').'</a>]</span>'.n;
			}
			$html .= t.t.'<span class="q">'.JText::_('MY_INTERESTS').': '.$intext.'</span>'.n;
			$html .= t.'</p>'.n;
			if ($otherquestions) {
				$html .= '<p class="incentive"><span>'.strtolower(JText::_('BEST ANSWER MAY EARN')).'</span></p>'.n;
				//$html .= '<div class="category-wrap">'.n;
				$html .= '<ol class="expandedlist">'.n;			
				for ($i=0; $i < count($otherquestions); $i++) 
				{
					if($i < $limit_interest) {
						$href = JRoute::_('index.php?option='.$option.a.'task=question'.a.'id='.$otherquestions[$i]->id);
						$html .= t.'<li class="question">'.n;				
						$html .= t.t.'<span class="q"><a href="'.$href.'">'.$this->shortenText(stripslashes($otherquestions[$i]->subject), 60, 0).'</a></span>'.n;
						if($this->banking) {
							$html .= t.t.'<span class="extra economy">'.$otherquestions[$i]->maxaward.' <span class="pts">'.strtolower(JText::_('pts')).'</span></span>'.n;
						}			
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				//$html .= '</div>'."\n";
			} else {
				$html .= '<p>'. JText::_('NO_QUESTIONS') .' <a href="'.JRoute::_('index.php?option='.$option.a.'task=search').'?filterby=open">'. JText::_('ALL_OPEN_QUESTIONS') .'</a></p>'.n;
			}
		}
		
		// Output the HTML
		return $html;
	}
	
	//-----------

	public function shortenText($text, $chars=300, $p=1) 
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
	
	public function normalize_tag($tag) 
	{		
			$normalized_valid_chars = 'a-zA-Z0-9';
			$normalized_tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);
			return strtolower($normalized_tag);
		
	}
}

//-------------------------------------------------------------

$modmyquestions = new modMyQuestions();
$modmyquestions->params = $params;

$xhub =& XFactory::getHub();
$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
$banking = $upconfig->get('bankAccounts');
$modmyquestions->banking = $banking;

if ($banking) {
	ximport( 'bankaccount' );
}

require( JModuleHelper::getLayoutPath('mod_myquestions') );
?>
