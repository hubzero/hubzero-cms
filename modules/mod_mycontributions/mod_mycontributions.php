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

class modMyContributions
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
	
	private function getContributions() 
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Container for the various types of contributions
		//$contributions = array();
		
		// Get "published" contributions
		/*$query1 = "SELECT COUNT(*)"
			. " FROM #__resources AS R, #__author_assoc AS AA"
			. " WHERE AA.authorid='". $juser->get('id') ."'"
			. " AND R.id=AA.subid AND AA.subtable='resources' AND R.standalone='1' AND R.published=1";
		$database->setQuery( $query1 );
		$contributions['published'] = $database->loadResult();
		*/
		
		// Get "in progress" contributions
		$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype, 
							AA.subtable, R.created, R.created_by, R.published, R.publish_up, R.standalone, 
							R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle ";
		$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
		$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
		$query .= "WHERE AA.authorid = ". $juser->get('id') ." ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.standalone=1 AND R.type=rt.id AND (R.published=2 OR R.published=3) AND R.type!=7 ";
		$query .= "ORDER BY published ASC, title ASC";

		$database->setQuery($query);
		//$contributions['inprogress'] = $database->loadObjectList(); // not include tools
					
		// Get "pending" contributions
		/*
		$query3 = "SELECT COUNT(*)"
			. " FROM #__resources AS R, #__author_assoc AS AA"
			. " WHERE AA.authorid='". $juser->get('id') ."'"
			. " AND R.id=AA.subid AND AA.subtable='resources' AND R.standalone='1' AND R.published=3";
		$database->setQuery( $query3 );
		$contributions['pending'] = $database->loadResult();
		*/
		
		return $database->loadObjectList();
	}
	
//-----------
	
	private function getToollist($show_questions, $show_wishes, $show_tickets, $limit_tools='40')
	{
		$juser  =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Query filters defaults
		$filters = array();
		$filters['sortby'] = 'f.published DESC';
		$filters['filterby'] = 'all';
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		
		// Create a Tool object
		$obj = new Tool( $database );
		$rows = $obj->getTools( $filters, false);
		$limit = 100000;
		
		
		if ($rows) {
			for ($i=0; $i < count($rows); $i++) 
			{
				// what is resource id?
				$rid = $obj->getResourceId($rows[$i]->id);
				$rows[$i]->rid = $rid;
						
				// get questions, wishes and tickets on published tools
				if($rows[$i]->published == 1 && $i <= $limit_tools) {
										
					if($show_questions) {
						// get open questions
						require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
						$aq = new AnswersQuestion( $database );	
						$filters = array();
						$filters['limit']    = $limit;
						$filters['start']    = 0;
						$filters['filterby'] = 'open';
						$filters['sortby']   = 'date';
						$filters['mine']	 = 0;
						$filters['tag']  	 = 'tool'.$rows[$i]->toolname;
						$results = $aq->getResults( $filters );
						$unanswered = 0;
						if($results) {
							foreach($results as $r) {
								if($r->rcount == 0) {
								 $unanswered ++;
								}
							}
						}

						$rows[$i]->q = count($results);
						$rows[$i]->q_new = $unanswered;
					}
					
					if($show_wishes) {
						// get open wishes
						require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.wishlist.php' );
						require_once( JPATH_ROOT.DS.'components'.DS.'com_wishlist'.DS.'controller.php' );
						
						$objWishlist = new Wishlist( $database );
						$objWish = new Wish( $database );
						$listid = $objWishlist->get_wishlistID($rid, 'resource');
						
						$rows[$i]->w = 0;
						$rows[$i]->w_new = 0;
							
						if($listid) {
							$filters = WishlistController::getFilters(1);
							$wishes = $objWish->get_wishes($listid, $filters, 1, $juser);
							$unranked = 0;
							if($wishes) {
								foreach($wishes as $w) {
									if($w->ranked == 0) {
									 $unranked ++;
									}
								}
							}
							
							$rows[$i]->w = count($wishes);
							$rows[$i]->w_new = $unranked;
						}
					}
					
					if($show_tickets) {
						// get open tickets
						$group = $rows[$i]->devgroup;
						
						// Find support tickets on the user's contributions
						$database->setQuery( "SELECT id, summary, category, status, severity, owner, created, login, name, 
							 (SELECT COUNT(*) FROM #__support_comments as sc WHERE sc.ticket=st.id AND sc.access=0) as comments
							 FROM #__support_tickets as st WHERE (st.status=0 OR st.status=1) AND type=0 AND st.group='$group'
							 ORDER BY created DESC
							 LIMIT $limit"
							);
						$tickets = $database->loadObjectList();
						if ($database->getErrorNum()) {
							echo $database->stderr();
							return false;
						}
						$unassigned = 0;
						if($tickets) {
							foreach($tickets as $t) {
								if($t->comments == 0 && $t->status==0 && !$t->owner) {
								 $unassigned ++;
								}
							}
						}
						
						
						$rows[$i]->s = count($tickets);
						$rows[$i]->s_new = $unassigned;
					}
				}
			}
		}
	
				
		return $rows;
	}
	
	//-----------

	private function getState($int)
	{
		switch ($int)
		{
			case 1: $state = 'registered'; break;
			case 2: $state = 'created';    break;
			case 3: $state = 'uploaded';   break;
			case 4: $state = 'installed';  break;
			case 5: $state = 'updated';    break;
			case 6: $state = 'approved';   break;
			case 7: $state = 'published';  break;
			case 8: $state = 'retired';    break;
			case 9: $state = 'abandoned';  break;
		}
		return $state;
	}
	
	//-----------

	private function getType($int)
	{
		switch ($int)
		{
			case 1:  $type = 'Online Presentation';      break;  // online presentations
			case 3:  $type = 'Publication';       break;  // publications
			case 5:  $type = 'Animation';         break;  // animations
			case 9:  $type = 'Download';          break;  // downloads
			case 39: $type = 'Teaching Material'; break;  // teaching materials
			default: $type = 'Other';             break;
		}
		return $type;
	}
	
	//-----------
	
	public function display()
	{
		// Get the user's profile from LDAP...
		$xuser =& XFactory::getUser();
		$juser =& JFactory::getUser();
		$session_quota = $xuser->get('jobs_allowed');
		$administrator = in_array('middleware', $xuser->get('admin'));
		
		// show tool contributions separately?
		$show_tools = intval( $this->params->get( 'show_tools' ) );
		$show_tools = $show_tools ? $show_tools : 1;
		
		// get questions on resources?
		//$show_questions = intval( $this->params->get( 'get_questions' ) );
		//$show_questions = $show_questions ? $show_questions : 1;
		$show_questions = 1;
		
		// get wishes on resources?
		//$show_wishes = intval( $this->params->get( 'get_wishes' ) );
		//$show_wishes = $show_wishes ? $show_wishes : 1;
		$show_wishes = 1;
		
		// get tickets on resources?
		//$show_tickets = intval( $this->params->get( 'get_tickets' ) );
		//$show_tickets = $show_tickets ? $show_tickets : 1;
		$show_tickets = 1;
		
		// how many tools to display?
		$limit_tools = intval( $this->params->get( 'limit_tools' ) );
		$limit_tools = $limit_tools ? $limit_tools : 10;
		
		// how many tools to display?
		$limit_other = intval( $this->params->get( 'limit_other' ) );
		$limit_other = $limit_other ? $limit_other : 5;
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_mycontributions');
		
		// Tools in progress
		$tools = $show_tools ? $this->getToollist($show_questions, $show_wishes, $show_tickets, $limit_tools) : array();

		// Other cotnributions
		$contributions = $this->getContributions();
		
		// Build the HTML
		$html = '';
		if ($show_tools && $tools) {
			$html .= '<h4>'.JText::_('Tools').' ';
			if (count($tools) > $limit_tools)  {
				$html .= '<small><a href="'.DS.'contribtool">'.JText::_('View all').' '.count($tools).'</a></small>';
			}
			$html .= '</h4>'."\n";
			//$html .= '<div class="category-wrap">'.n;
			$html .= '<ul class="expandedlist">'."\n";			
			for ($i=0; $i < count($tools); $i++) 
			{
				if ($i <= $limit_tools) {
					$class =  $tools[$i]->published ? 'published' : 'draft';
					$urgency = ($this->getState($tools[$i]->state) == 'installed' or $this->getState($tools[$i]->state)=='created') ? ' '.JText::_('and requires your action') : '' ;
					$href = JRoute::_('index.php?option=com_contribtool'.a.'task=status'.a.'toolid='.$tools[$i]->id);
					
					$html .= t.'<li class="'.$class.'">'."\n";
					$html .= t.t.'<a href="'.$href.'">'.stripslashes($tools[$i]->toolname).'</a>'.n;
					$html .= t.t.'<span class="statusnote">'.JText::_('Status').': <span class="status_'.$this->getState($tools[$i]->state).'"><a href="'.$href.'" title="'.JText::_('This tool is now in').' '.$this->getState($tools[$i]->state).' '.JText::_('status').$urgency.'">'.$this->getState($tools[$i]->state).'</a></span></span>'."\n";
					if ($tools[$i]->published) {
						$html .= t.t.'<span class="extra">'."\n";
						$html .= !$show_wishes ? '<span class="item_empty ">&nbsp;</span>' : '';
						$html .= !$show_tickets ? '<span class="item_empty ">&nbsp;</span>' : '';
						if ($show_questions) {
							$html .= t.t.t.'<span class="item_q"><a href="'.JRoute::_('index.php?option=com_answers'.a.'task=myquestions').'?filterby=open'.a.'assigned=1'.a.'tag=tool'.$tools[$i]->toolname.'" ';
							$html .= ' title="'.JText::_('There').' ';
							$html .= $tools[$i]->q == 1 ? strtolower(JText::_('is')) : strtolower(JText::_('are'));
							$html .= ' '.$tools[$i]->q.' '.JText::_('open').' ';
							$html .= $tools[$i]->q == 1 ? strtolower(JText::_('question')) : strtolower(JText::_('questions'));
							$html .= ' '.strtolower(JText::_('for this tool')).' ('.$tools[$i]->q_new.' '.JText::_('unanswered').')">';
							$html .= $tools[$i]->q.'</a>';
							if ($tools[$i]->q_new > 0) {
								$html .='<br /><span class="item_new">+ '.$tools[$i]->q_new.'</span>';
							}
							$html .= '</span>'."\n";
						} else {
							$html .= t.t.t.'<span class="item_empty">&nbsp;</span>';
						}
						if ($show_wishes) {
							$html .= t.t.t.'<span class="item_w"><a href="'.JRoute::_('index.php?option=com_wishlist'.a.'task=wishlist'.a.'category=resource'.a.'rid='.$tools[$i]->rid).'"';								$html .= ' title="'.JText::_('There').' ';
							$html .= $tools[$i]->w == 1 ? strtolower(JText::_('is')) : strtolower(JText::_('are'));
							$html .= ' '.$tools[$i]->w.' '.JText::_('pending').' ';
							$html .= $tools[$i]->w == 1 ? strtolower(JText::_('wish')) : strtolower(JText::_('wishes'));
							$html .= ' '.strtolower(JText::_('for this tool')).' ('.$tools[$i]->w_new.' '.JText::_('unranked').')"';
							$html .= '>'.$tools[$i]->w.'</a>';
							if ($tools[$i]->w_new > 0) {
								$html .='<br /><span class="item_new">+ '.$tools[$i]->w_new.'</span>';
							}						
							$html .='</span>'."\n";
						}
						if ($show_tickets) {
							$html .= t.t.t.'<span class="item_s"><a href="'.JRoute::_('index.php?option=com_support&task=tickets').'?find=group:'.$tools[$i]->devgroup.'"';
							$html .= ' title="'.JText::_('There').' ';
							$html .= $tools[$i]->s == 1 ? strtolower(JText::_('is')) : strtolower(JText::_('are'));
							$html .= ' '.$tools[$i]->s.' '.JText::_('open').' ';
							$html .= $tools[$i]->s == 1 ? strtolower(JText::_('ticket')) : strtolower(JText::_('tickets'));
							$html .= ' '.strtolower(JText::_('for this tool')).' ('.$tools[$i]->s_new.' '.JText::_('unassigned').')"';
							$html .= '>'.$tools[$i]->s.'</a></span>'."\n";
							if ($tools[$i]->s_new > 0) {
								$html .='<br /><span class="item_new">+ '.$tools[$i]->s_new.'</span>';
							}
							$html .= t.t.'</span>'."\n";
						}
					}		
					$html .= t.'</li>'."\n";
				}
			}
			$html .= '</ul>'."\n";
			//$html .= '</div>'.n;
			$html .= '<h4>'.JText::_('Other Contributions in Progress');
			if ($contributions && count($contributions) > $limit_other)  {
				$html .= ' <small><a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id')).DS.'contributions">'.JText::_('View all').'</a></small>'.n;
			}
			$html .= '</h4>'."\n";
		}
		
		if (!$contributions) {
			$html .= '<p>'.JText::_('No contributions found.').'</p>'."\n";
		} else {
			//$html .= '<div class="category-wrap">'."\n";
			$html .= '<ul class="expandedlist">'."\n";
			for ($i=0; $i < count($contributions); $i++) 
			{
				if ($i < $limit_other) {
					// Determine css class
					switch ($contributions[$i]->published)
					{
						case 1:  $class = 'published';  break;  // published
						case 2:  $class = 'draft';      break;  // draft
						case 3:  $class = 'pending';    break;  // pending
					}
					
					// get author login
					$author_login = JText::_('unknown');
					$author =& XUser::getInstance( $contributions[$i]->created_by );
					if (is_object($author)) {
						$author_login = $author->get('login');
					}
					$href = '/contribute/?step=1&amp;id='.$contributions[$i]->id;
					
					$html .= "\t".'<li class="'.$class.'">'."\n";
					$html .= "\t\t".'<a href="'.$href.'">'.$this->shortenText(stripslashes($contributions[$i]->title), 40, 0).'</a>'."\n";
					$html .= "\t\t".'<span>'.JText::_('Type').': '.$this->getType($contributions[$i]->type).' - '.JText::sprintf('Submitted by %s',$author_login).'</span>'."\n";
					$html .= "\t".'</li>'."\n";
				}
			}
			$html .= '</ul>'."\n";
			//$html .= '</div>'."\n";
		}

		$html .= "\t\t".'<p class="more"><a href="/contribute/?task=start">'.JText::_('Start a new contribution').' &raquo;</a></p>'."\n";
		
		// Output final HTML
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
}

//----------------------------------------------------------

$modmycontributions = new modMyContributions();
$modmycontributions->params = $params;

require( JModuleHelper::getLayoutPath('mod_mycontributions') );
?>
