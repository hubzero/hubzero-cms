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

class modWishVoters
{
	private $params;

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
	
	private function _list( $rows, $limit)
	{
		if (count($rows) <= 0) {
			$html  = "\t".'<p>'.JText::_('Noone has yet voted for a single wish on this list.').'</p>'."\n";
		} else {
			$html  = "\t".'<ul class="voterslist">'."\n";
			$html .= "\t\t".'<li class="title">'.JText::_('Name (login)').' <span>'.JText::_('wishes ranked').'</span></li>'."\n";
			$k=1;
			foreach ($rows as $row)
			{
				if($k <= $limit) {
					$name = JText::_('UNKNOWN');
					$auser =& JUser::getInstance($row->userid);
					if (is_object($auser)) {
							$name = $auser->get('name');
							$login = $auser->get('username');
					}
					
					$html .= "\t\t".'<li>'."\n";
					$html .= "\t\t".'<span class="lnum">'.$k.'.</span>'."\n";
					$html .= "\t\t\t".$name.' <span class="wlogin">('.$login.')</span>'."\n";
					$html .= "\t\t\t".'<span>'.$row->times.'</span>'."\n";
					$html .= "\t\t".'</li>';
					$k++;
				}
			}
			$html .= "\t".'</ul>'."\n";
		}
		
		return $html;
	}

	//-----------
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
		$objWishlist = new Wishlist ( $database );
		//$objOwner = new WishlistOwner( $database );
		
		// which list is being viewed?
		$listid 	= JRequest::getInt( 'id', 0 );
		$refid		= JRequest::getInt( 'rid', 0 );
		$category 	= JRequest::getVar( 'category', '' );
		
		// figure list id
		if ($category && $refid) {
			$listid = $objWishlist->get_wishlistID($refid, $category);
		}
					
		// cannot rank a wish if list/wish is not found
		if (!$listid) {
			echo JText::_('Cannot locate a wish or a wish list');
			return;
		}	
		
		//$wparams =& JComponentHelper::getParams( 'com_wishlist' );
		//$admingroup = $wparams->get('group');
		
		//$wishlist = $objWishlist->get_wishlist($listid);			
		//$owners = $objOwner->get_owners($listid, $admingroup, $wishlist);

		$database->setQuery( "SELECT DISTINCT v.userid, SUM(v.importance) as imp, COUNT(v.wishid) as times "
			. " FROM #__wishlist_vote as v JOIN #__wishlist_item as w ON w.id=v.wishid WHERE w.wishlist='".$listid."'"
			. " GROUP BY v.userid ORDER BY times DESC, v.voted DESC ");
			//. " LIMIT ".$limit
			//);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_wishvoters');
		
		// Build the HTML
		$html  = '<div';
		$html .= ($moduleclass) ? ' class="'.$moduleclass.'">'."\n" : '>'."\n";
		$html .= "\t".'<h3>Giving the Most Input</h3>'."\n";
		$html .= $this->_list( $rows, $limit);
		$html .= '</div>'."\n";
		
		// Output the HTML
		echo $html;
	}
}