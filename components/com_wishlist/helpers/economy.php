<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Wishlist Economy class:
// Stores economy funtions for wishlists
//----------------------------------------------------------

ximport('Hubzero_Bank');

/**
 * Short description for 'WishlistEconomy'
 * 
 * Long description (if any) ...
 */
class WishlistEconomy extends JObject
{

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	var $_db = NULL;  // Database

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db)
	{
		$this->_db = $db;
	}

	/**
	 * Short description for 'getPayees'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $wishid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getPayees($wishid)
	{
		if (!$wishid) {
			return null;
		}
		$sql = "SELECT DISTINCT uid FROM #__users_transactions WHERE category='wish' AND referenceid=$wishid AND type='hold'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getTotalPayment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $wishid Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getTotalPayment($wishid, $uid)
	{
		if (!$wishid or !$uid) {
			return null;
		}
		$sql = "SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid='$wishid' AND type='hold' AND uid='$uid'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'cleanupBonus'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $wishid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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

	/**
	 * Short description for 'distribute_points'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $wishid Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      number $points Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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
					$o =& JUser::getInstance( $owner );
					if (!is_object($o) || !$o->get('id')) {
						continue;
					}
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
				$o =& JUser::getInstance( $wish->assigned );
				if (is_object($o) && $o->get('id')) {
					$BTLM = new Hubzero_Bank_Teller( $this->_db , $wish->assigned );
					$BTLM->deposit($mainshare, JText::_('Bonus for fulfilling assigned wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
				}
			}

			// Adjust credits
			$payees = $this->getPayees($wishid);
			if ($payees) {
				foreach ($payees as $p)
				{
					$o =& JUser::getInstance( $p->uid );
					if (!is_object($o) || !$o->get('id')) {
						continue;
					}
					
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
		if ($wish->ranking > 0 && $wish->proposed_by != $juser->get('id') && $wish->proposed_by) {
			
			$o =& JUser::getInstance( $wish->proposed_by );
			if (is_object($o) && $o->get('id')) {
				$BTLA = new Hubzero_Bank_Teller( $this->_db , $wish->proposed_by );
				$BTLA->deposit($wish->ranking, JText::_('Your wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist.' '.JText::_('was granted'), 'wish', $wishid);
			}
		}
	}
}

