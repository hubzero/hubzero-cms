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

class modMyQuestions
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

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

		if (count($tags) > 0) {
			$out .= '<span class="taggi">'."\n";
			$counter = 0;
			
			for ($i=0; $i< count($tags); $i++) 
			{
				$counter = $counter + strlen(stripslashes($tags[$i]));	
				if ($counter > $max) {
					$num = $num - 1;
				}
				if ($i < $num) {
					// display tag
					$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $tags[$i]);
					$normalized = strtolower($normalized);
					$out .= "\t".'<a href="'.JRoute::_('index.php?option=com_tags&tag='.$normalized).'">'.stripslashes($tags[$i]).'</a> '."\n";
				}
			}
			if ($i > $num) {
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
		$juser =& JFactory::getUser();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
		
		// Get tags of interest
		$mt = new MembersTags( $database );
		if ($cloud) {
			$tags = $mt->get_tag_cloud(0,0,$juser->get('id'));
		} else {
			$tags = $mt->get_tag_string($juser->get('id'));
		}
		
		return $tags;
	}
	
	//-----------

	private function getQuestions($kind='open', $interests=array())
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();		
		
		// Get some classes we need
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'question.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'response.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'log.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'questionslog.php' );
				
		$aq = new AnswersQuestion( $database );		
		if ($this->banking) {
			$AE = new AnswersEconomy( $database );
			$BT = new BankTransaction( $database );
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
		
		switch ( $kind ) 
		{
			case 'mine':
				$filters['mine'] = 1;
				$filters['sortby'] = 'responses';    
		    break;
			
			case 'assigned': 
				$filters['mine'] = 0;
				require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.author.php' );
			
				$TA = new ToolAuthor($database); 
				$tools = $TA->getToolContributions($juser->get('id'));
				if ($tools) {
					foreach ($tools as $tool) 
					{
						$filters['tag'] .= 'tool'.$tool->toolname.',';
					}
				}			
				if (!$filters['tag']) { 
					$filters['filterby'] = 'none';
				}	   
		    break;
			
			case 'interest':
				$filters['mine'] = 0;
				$interests = (count($interests) <= 0) ? $this->getInterests() : $interests;
				$filters['filterby'] = (!$interests) ? 'none' : 'open';
				$filters['tag'] = $interests;		    
		    break;
		}
		
		$results = $aq->getResults( $filters );
	 	if ($this->banking && $results) {
	 		$awards = array();
			
			foreach ($results as $result) 
			{
				// Calculate max award
				$result->marketvalue = round($AE->calculate_marketvalue($result->id, 'maxaward'));
				$result->maxaward = round(2*(($result->marketvalue)/3));
				if ($kind != 'mine') {
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
		//$xhub =& XFactory::getHub();
		
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$this->banking = $upconfig->get('bankAccounts');
		if ($this->banking) {
			ximport( 'bankaccount' );
		}
			
		//$juser =& JFactory::getUser();
	
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_myquestions');
		
		// show assigned?
		$show_assigned = intval( $this->params->get( 'show_assigned' ) );
		$show_assigned = $show_assigned ? $show_assigned : 0;
		$this->show_assigned = $show_assigned;
		
		// show interests?
		$show_interests = intval( $this->params->get( 'show_interests' ) );
		$show_interests = $show_interests ? $show_interests : 0;
		$this->show_interests = $show_interests;
		
		// max num of questions
		$max = intval( $this->params->get( 'max_questions' ) );
		$max= $max ? $max : 12;
		$c = 1;
		
		// Build the HTML
		//$foundresults = false;
		$assignedcount = 0;
		$othercount = 0;
		
		// Get Open Questions User Asked
		$this->openquestions = $this->getQuestions('mine');
		$opencount = ($this->openquestions) ? count($this->openquestions) : 0;
				
		//$onum  = $opencount.' ';
		//$onum .= ($opencount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		
		// Get Questions related to user contributions
		if ($this->show_assigned) {
			$c++;
			$this->assigned = $this->getQuestions('assigned');
			$assignedcount = ($this->assigned) ? count($this->assigned) : 0; 
				
			//$anum  = $assignedcount.' ';
			//$anum .= ($assignedcount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		}
		
		// Get interest tags
		if ($this->show_interests) {
			$c++;
			$this->interests = $this->getInterests();
			if (!$this->interests) { 
				$this->intext = JText::_('MOD_MYQUESTIONS_NA');
			} else {
				$this->intext = $this->formatTags($this->interests);
			}
				
			// Get questions of interest
			$this->otherquestions = $this->getQuestions("interest", $this->interests);
			$othercount = ($this->otherquestions) ? count($this->otherquestions) : 0; 
				
			//$othnum  = $othercount.' ';
			//$othnum .= ($othercount == 1) ? JText::_('RESULT') : JText::_('RESULTS');
		}
		
		// Limit number of shown questions
		$totalq = $opencount + $assignedcount + $othercount;
		$limit_mine = $max;
		$breaker = $max/$c;
		$this->limit_mine = ($totalq - $opencount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $opencount);
		$this->limit_assigned = ($totalq - $assignedcount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $assignedcount);
		$this->limit_interest = ($totalq - $othercount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $othercount);
	}
}
