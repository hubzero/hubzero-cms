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
	
	private function _getContributions() 
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
	
	private function _getToollist($show_questions, $show_wishes, $show_tickets, $limit_tools='40')
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		ximport('Hubzero_Tool');
		// Query filters defaults
		$filters = array();
		$filters['sortby'] = 'f.published DESC';
		$filters['filterby'] = 'all';
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		
		// Create a Tool object
		$rows = Hubzero_Tool::getTools( $filters, false);
		$limit = 100000;
		
		if ($rows) {
			for ($i=0; $i < count($rows); $i++) 
			{
				// what is resource id?
				$rid = Hubzero_Tool::getResourceId($rows[$i]->id);
				$rows[$i]->rid = $rid;
						
				// get questions, wishes and tickets on published tools
				if ($rows[$i]->published == 1 && $i <= $limit_tools) {
					if ($show_questions) {
						// get open questions
						require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'question.php' );
						require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'response.php' );
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
						if ($results) {
							foreach ($results as $r) 
							{
								if ($r->rcount == 0) {
									$unanswered++;
								}
							}
						}

						$rows[$i]->q = count($results);
						$rows[$i]->q_new = $unanswered;
					}
					
					if ($show_wishes) {
						// get open wishes
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
						include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
						require_once( JPATH_ROOT.DS.'components'.DS.'com_wishlist'.DS.'controller.php' );
						
						$objWishlist = new Wishlist( $database );
						$objWish = new Wish( $database );
						$listid = $objWishlist->get_wishlistID($rid, 'resource');
						
						$rows[$i]->w = 0;
						$rows[$i]->w_new = 0;
							
						if ($listid) {
							$filters = WishlistController::getFilters(1);
							$wishes = $objWish->get_wishes($listid, $filters, 1, $juser);
							$unranked = 0;
							if ($wishes) {
								foreach ($wishes as $w) 
								{
									if ($w->ranked == 0) {
										$unranked++;
									}
								}
							}
							
							$rows[$i]->w = count($wishes);
							$rows[$i]->w_new = $unranked;
						}
					}
					
					if ($show_tickets) {
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
						if ($tickets) {
							foreach ($tickets as $t) 
							{
								if ($t->comments == 0 && $t->status==0 && !$t->owner) {
									$unassigned++;
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

	public function getState($int)
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

	public function getType($int)
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
		$xprofile =& Hubzero_Factory::getProfile();
		$juser =& JFactory::getUser();
		$session_quota = $xprofile->get('jobsAllowed');
		$administrator = in_array('middleware', $xprofile->get('admin'));
		
		// show tool contributions separately?
		$show_tools = intval( $this->params->get( 'show_tools' ) );
		$show_tools = $show_tools ? $show_tools : 1;
		$this->show_tools = $show_tools;
		
		// get questions on resources?
		//$show_questions = intval( $this->params->get( 'get_questions' ) );
		//$show_questions = $show_questions ? $show_questions : 1;
		$this->show_questions = 1;
		
		// get wishes on resources?
		//$show_wishes = intval( $this->params->get( 'get_wishes' ) );
		//$show_wishes = $show_wishes ? $show_wishes : 1;
		$this->show_wishes = 1;
		
		// get tickets on resources?
		//$show_tickets = intval( $this->params->get( 'get_tickets' ) );
		//$show_tickets = $show_tickets ? $show_tickets : 1;
		$this->show_tickets = 1;
		
		// how many tools to display?
		$limit_tools = intval( $this->params->get( 'limit_tools' ) );
		$limit_tools = $limit_tools ? $limit_tools : 10;
		$this->limit_tools = $limit_tools;
		
		// how many tools to display?
		$limit_other = intval( $this->params->get( 'limit_other' ) );
		$limit_other = $limit_other ? $limit_other : 5;
		$this->limit_other = $limit_other;
		
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_mycontributions');
		
		// Tools in progress
		$this->tools = ($this->show_tools) ? $this->_getToollist($this->show_questions, $this->show_wishes, $this->show_tickets, $this->limit_tools) : array();

		// Other cotnributions
		$this->contributions = $this->_getContributions();
	}
}
