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

//----------------------------------------------------------
// Wishlist Economy class:
// Stores economy funtions for wishlists
//----------------------------------------------------------

ximport('Hubzero_Bank');

class WishlistEconomy extends JObject
{
	var $_db = NULL;  // Database
	
	//-----------
	
	public function __construct( &$db)
	{
		$this->_db = $db;
	}
	
	//-----------
	
	public function getPayees($wishid) 
	{
		if (!$wishid) {
			return null;
		}
		$sql = "SELECT DISTINCT uid FROM #__users_transactions WHERE category='wish' AND referenceid=$wishid AND type='hold'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getTotalPayment($wishid, $uid) 
	{
		if (!$wishid or !$uid) {
			return null;
		}
		$sql = "SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid='$wishid' AND type='hold' AND uid='$uid'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function cleanupBonus($wishid) 
	{
		if (!$wishid) {
			return null;
		}
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'models'.DS.'wishlist.php' );
		$objWish = new Wish( $this->_db );
		$wish = $objWish->get_wish ($wishid, '', 1);
		
		if ($wish->bonus > 0) {
			// Adjust credits
			$payees = $this->getPayees($wishid);
			if ($payees) {
				foreach ($payees as $p) 
				{
					$BTL = new Hubzero_Bank_Teller( $this->_db , $p->uid );
					$hold = $this->getTotalPayment($wishid, $p->uid);
					if ($hold) {
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $hold;
						$BTL->credit_adjustment($adjusted);
					}
				}
			}
			 // Delete holds
			$BT = new Hubzero_Bank_Transaction( $this->_db  );
			$BT->deleteRecords( 'wish', 'hold', $wishid );	
		}
	}
	
	//-----------
	
	public function distribute_points($wishid, $type='grant', $points=0) 
	{
		if (!$wishid) {
			return null;
		}
					
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'models'.DS.'wishlist.php' );
		$objWish = new Wish( $this->_db );
		$wish = $objWish->get_wish ($wishid);
		
		$points = !$points ? $wish->bonus : $points;
		
		// Points for list owners
		if ($points > 0 && $type!='royalty') {
			// Get the component parameters
			$wconfig =& JComponentHelper::getParams( 'com_wishlist' );
			$admingroup = ($wconfig->get('group')) ? trim($wconfig->get('group')) : 'hubadmin';
			
			// get list owners
			$objOwner = new WishlistOwner(  $this->_db );
			$owners   = $objOwner->get_owners($wish->wishlist, $admingroup, '', 0, $wishid );
			$owners   = $owners['individuals'];
						
			$mainshare = $wish->assigned ?  $points*0.8 : 0; //80%
			$commonshare = $mainshare ? ($points - $mainshare)/count($owners) : $points/count($owners);
						
			// give the remaining 20%
			if ($owners && $commonshare) {
				foreach ($owners as $owner) 
				{
					$BTLO = new Hubzero_Bank_Teller( $this->_db , $owner );
					if ($wish->assigned && $wish->assigned == $owner) {
						//$BTLO->deposit($mainshare, JText::_('Bonus for fulfilling assigned wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
						$mainshare += $commonshare;
					} else {
						$BTLO->deposit($commonshare, JText::_('Bonus for fulfilling wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
					}
				}
			} else {
				$mainshare += $commonshare;
			}
			
			// give main share
			if ($wish->assigned && $mainshare) {
				$BTLM = new Hubzero_Bank_Teller( $this->_db , $wish->assigned );
				$BTLM->deposit($mainshare, JText::_('Bonus for fulfilling assigned wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
			}
				
			// Adjust credits
			$payees = $this->getPayees($wishid);
			if ($payees) {
				foreach ($payees as $p) 
				{
					$BTL = new Hubzero_Bank_Teller( $this->_db , $p->uid );
					$hold = $this->getTotalPayment($wishid, $p->uid);
					if ($hold) {
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $hold;
						$BTL->credit_adjustment($adjusted);
						
						// withdraw bonus amount
						$BTL->withdraw($hold, JText::_('Bonus payment for granted wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
					}
				}
			}
			
			// Remove holds if exist
			if ($wish->bonus) {
				$BT = new Hubzero_Bank_Transaction( $this->_db  );
				$BT->deleteRecords( 'wish', 'hold', $wishid );
			}
		}
		
		// Points for wish author (needs to be granted by another person)
		$juser =& JFactory::getUser();
		if ($wish->ranking > 0 && $wish->proposed_by != $juser->get('id')) {
			$BTLA = new Hubzero_Bank_Teller( $this->_db , $wish->proposed_by );
			$BTLA->deposit($wish->ranking, JText::_('Your wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist.' '.JText::_('was granted'), 'wish', $wishid);
		}
	}
}
