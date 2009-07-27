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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_support_transfer' );

//-----------

class plgSupportTransfer extends JPlugin
{
	function plgSupportTransfer(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'transfer' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		$xhub =& XFactory::getHub();
		$banking = $xhub->getCfg('hubBankAccounts');
		$this->banking = $banking;
		
		if ($banking) {
			ximport( 'bankaccount' );
		}
	}
	
	//-----------
	
	function transferItem($from_type, $from_id, $to_type, $rid=0, $deactivate=1)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		
		if($from_type == NULL or $from_id == NULL or $to_type == NULL) {
			$this->setError( JText::_('Missing required information to complete the transfer.') );
			return false;
		}
		
		if($from_type == $to_type) {		
			$this->setError( JText::_('Cannot proceed with the transfer. Categories need to be different.') );
			return false;
		}
		
		// collectors
		$author 	= '';
		$subject 	= '';
		$body 		= '';
		$tags 		= '';
		$owner		= ''; // name of group owning the item
		$anonymous 	= 0;
		
		
		// get needed scripts
		include_once( JPATH_ROOT.DS.'components'.DS.'com_support'.DS.'support.tags.php' );
		require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.ticket.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.comment.php' ); 
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.wishlist.php' ); 
		
		$wconfig =& JComponentHelper::getParams( 'com_wishlist' );
		$admingroup = $wconfig->get('group') ? $wconfig->get('group') : 'hubadmin'; 
		
		// Get needed scripts & initial data
		switch ( $from_type) 
		{
			// Transfer from a Support Ticket
			case 'ticket':
	
			$row = new SupportTicket( $database );
			$row->load( $from_id );			
			
			if($row->id) {
				$author 	= $row->login;
				$subject 	= $this->shortenText($row->summary, 200); // max 200 characters
				$body 		= $row->summary;
				$owner		= $row->group;
				
				// If we are de-activating original item
				if($deactivate) { 
					$row->status = 2; 
					$row->resolved = 'transfered';
				}
				
				$st = new SupportTags( $database );
				$tags = $st->get_tag_string( $from_id, 0, 0, NULL, 0, 1 );		
			} 
			else {
				$this->setError( JText::_('ERROR: Original item not found.') );
				return false;
			} 	
					
			break;
			
			// Transfer from a Question
			case 'question': 			
			
			$row = new AnswersQuestion( $database );
			$row->load( $from_id );
			
			if($row->id) {
				$author 	= $row->created_by;
				$subject 	= $this->shortenText($row->subject, 200); // max 200 characters
				$body 		= $row->question;
				$anonymous	= $row->anonymous;
				
				// If we are de-activating original item
				if($deactivate) { 
					$row->state = 2; 
					$row->reward = 0;
				}
				
				$tagging = new AnswersTags( $database );
				$tags = $tagging->get_tag_string( $from_id, 0, 0, NULL, 0, 1 );	
							
			} 
			else {
				$this->setError( JText::_('ERROR: Original item not found.') );
				return false;
			} 		  	
					
			break;
			
			// Transfer from a Wish
			case 'wish': 
			
			$row = new Wish( $database );
			$row->load( $from_id );
			
			if($row->id) {
				$author 	= $row->proposed_by;
				$subject 	= $this->shortenText($row->subject, 200); // max 200 characters
				$body 		= $row->about;
				$anonymous	= $row->anonymous;	
				
				// If we are de-activating original item
				if($deactivate) { 
					$row->status = 2; 
					$row->ranking = 0;
					
					// also delete all previous votes for this wish
					$objR = new WishRank( $database );
					$objR->remove_vote($from_id);
				}
				
				// get owner
				ximport('xgroup');
				$objG 	  = new WishlistOwnerGroup( $database );
				$nativegroups = $objG->get_owner_groups($row->wishlist, $admingroup, '',1);
				$owner = (count($nativegroups) > 0 && $nativegroups[0]!= $admingroup) ? $nativegroups[0] : ''; // tool group
				
				$objWishlist = new Wishlist ( $database );
				$wishlist = $objWishlist->get_wishlist($row->wishlist);
				if(isset($wishlist->resource) && isset($wishlist->resource->alias)) {
					$tags  = $wishlist->resource->type==7 ? 'tool:' : 'resource:';
					$tags .= $wishlist->resource->alias ? $wishlist->resource->alias : $wishlist->referenceid ;
				}
						
			} 
			else {
				$this->setError( JText::_('ERROR: Original item not found.') );
				return false;
			} 		
					
			break;
			
		}
		
		// if no author can be found, use current administrator
		$author = & XUser::getInstance($author);
		if (!is_object($author)) {
			$author = & XUser::getInstance($juser->get('id'));
		}
		
		$today = date( 'Y-m-d H:i:s', time() );
		
		// Where do we transfer?
		switch ( $to_type) 
		{
			// Transfer to a Support Ticket
			case 'ticket':
			$newrow = new SupportTicket( $database );
			$newrow->status = 0;
			$newrow->created =  $today;
			$newrow->login = $author->get('login');
			$newrow->severity = 'normal';
			$newrow->summary = $subject;
			$newrow->report = $body ? $body : $subject;
			$newrow->section = 1;
			$newrow->type = 0;
			$newrow->instances = 1;
			$newrow->email = $author->get('email');
			$newrow->name = $author->get('name');
			
			// do we have an owner group?
			$newrow->group = $owner ? $owner : '' ;
			
			break;
			
			case 'question':
			$newrow = new AnswersQuestion( $database );
			$newrow->subject    = $subject;
			$newrow->question   = $body;
			$newrow->created    = $today;
			$newrow->created_by = $author->get('login');
			$newrow->state      = 0;
			$newrow->anonymous  = $anonymous;
			
			break;
			
			case 'wish':
			$newrow = new Wish( $database );
			$newrow->subject     = $subject;
			$newrow->about    	 = $body;
			$newrow->proposed    = $today;
			$newrow->proposed_by = $author->get('uid');
			$newrow->status      = 0;
			$newrow->anonymous  = $anonymous;
			
			// which wishlist?
			$objWishlist = new Wishlist ( $database );
			$mainlist = $objWishlist->get_wishlistID(1, 'general');
			$listid = 0;
			if(!$rid && $owner) {
				$rid = $this->getResourceIdFromGroup($owner);
			}
			
			if($rid) {
				$listid = $objWishlist->get_wishlistID($rid);
			}
			$newrow->wishlist = $listid ? $listid : $mainlist;
			
			break;
			
		}
				
		
		// Save new information
		if (!$newrow->store()) {
					$this->setError( $newrow->getError() );
					exit();
		}
		else {
			// Checkin ticket
			$newrow->checkin();
			//$tags .= ', Transferred from a '.ucfirst($from_type);

			// Extras
			if($newrow->id) {
			
				switch ( $to_type) 
				{
					case 'ticket':
						// Tag new ticket
						if($tags) {
						$st = new SupportTags( $database );
						$st->tag_object( $juser->get('id'), $newrow->id, $tags, 0, 0 );
						}
					break;
					
					case 'question':
						// Tag new question
						if($tags) {
						$tagging = new AnswersTags( $database );			
						$tagging->tag_object($juser->get('id'), $newrow->id, $tags, 0, 0);
						}
						
					break;
				}
			
			
			}

		}
					
		
		// If we are de-activating original item
		if($deactivate) {
			// overwrite old entry
			if (!$row->store()) {
				$this->setError( $row->getError() );
				exit();
			}
				
			// Clean up rewards if banking
			if($this->banking) {
				switch ( $from_type) 
				{
					case 'ticket':
						// no banking yet
					break;
					
					case 'question':
						$BT = new BankTransaction( $database );
						$reward = $BT->getAmount( 'answers', 'hold', $from_id, $author->get('uid')  );
						
						// Remove hold
						if($reward) {
							$BT->deleteRecords( 'answers', 'hold', $from_id );
									
							// Make credit adjustment
							$BTL_Q = new BankTeller( $database, $author->get('uid') );
							$credit = $BTL_Q->credit_summary();
							$adjusted = $credit - $reward;
							$BTL_Q->credit_adjustment($adjusted);
						}
					
					break;
					
					case 'wish':
						$WE = new WishlistEconomy( $database );			
						$WE->cleanupBonus($from_id);
					
					break;
				}
			}
		}
	
		
		
	
		return $newrow->id;
		
		
	}
	
	//-----------
	
	public function getResourceIdFromTag($tag)
	{
		// intended to find a resource from a tag, e.g. tool:cntbands		
		if ($tag=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT t.objectid FROM #__tags_object as t LEFT JOIN #__tags as tt ON tt.id = t.tagid WHERE t.tbl="resources" AND (tt.raw_tag="'.$tag.'" OR tt.tag="'.$tag.'")' );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function getResourceIdFromGroup($groupname)
	{
		// intended to find a resource from the name of owner group, e.g. app-cntbands		
		if ($tag=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT r.id FROM #__resources as r LEFT JOIN #__xgroups as g ON g.cn = r.alias WHERE g.cn="'.$groupname.'"' );
		return $this->_db->loadResult();
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
	
	
}