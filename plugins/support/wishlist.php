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
JPlugin::loadLanguage( 'plg_support_wishlist' );

//-----------

class plgSupportWishlist extends JPlugin
{
	public function plgSupportWishlist(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function getReportedItem($refid, $category, $parent) 
	{
		if ($category != 'wish' && $category != 'wishcomment') {
			return null;
		}
		
		if ($category == 'wish') {
			$query  = "SELECT ws.id, ws.about as text, ws.proposed_by as author, ws.subject as subject";
			$query .= ", 'wish' as parent_category, ws.anonymous as anon";
			$query .= " FROM #__wishlist_item AS ws";
			$query .= " WHERE ws.id=".$refid;
		} else if ($category == 'wishcomment') {
			$query  = "SELECT rr.id, rr.comment as text, rr.added_by as author, NULL as subject";
			$query .= ", rr.category as parent_category, rr.anonymous as anon";
			$query .= " FROM #__comments AS rr";
			$query .= " WHERE rr.id=".$refid;
		}

		$database =& JFactory::getDBO();
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($rows) {
			foreach ($rows as $key => $row) 
			{
			
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_wishlist&task=wishlist&id='.$parent) : '';
				if ($rows[$key]->parent_category == 'wishcomment') {
					$rows[$key]->href = JRoute::_('index.php?option=com_wishlist&task=wish&wishid='.$parent);
				}
			}
		}
		return $rows;
	}
	
	//-----------
	
	public function getParentId( $parentid, $category ) 
	{
		ximport('xcomment');
		
		$database =& JFactory::getDBO();
		$refid = $parentid;
		
		if ($category == 'wishcomment') {
			$pdata = $this->parent($parentid);
			$category = $pdata->category;
			$refid = $pdata->referenceid;
			
			if ($pdata->category == 'wishcomment') {
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->category;
				$refid = $pdata->referenceid;

				if ($pdata->category == 'wishcomment') {
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->category;
					$refid = $pdata->referenceid;
				}
			}
		}
		
		if ($category == 'wish') {
			$database->setQuery( "SELECT wishlist FROM #__wishlist_item WHERE id=".$refid);
			$pid = $database->loadResult();
		 	return $pid;
		}
	}
	
	//-----------
	
	public function parent($parentid) 
	{
		$database =& JFactory::getDBO();
		$parent = new XComment( $database );
		$parent->load( $parentid );
		
		return $parent;
	}
	
	//-----------
	
	public function getTitle($category, $parentid) 
	{
		if ($category != 'wish' && $category != 'wishcomment') {
			return null;
		}
		
		switch ($category) 
		{
			case 'wish': 
				return JText::sprintf('Wish from list #%s', $parentid);		
         	break;
			
			case 'wishcomment': 
				return JText::sprintf('Comment to wish  #%s', $parentid);
         	break;
		}
	}
	
	//-----------
	
	public function deleteReportedItem($referenceid, $parentid, $category, $message) 
	{
		if ($category != 'wish' && $category != 'wishcomment') {
			return null;
		}
		
		$database =& JFactory::getDBO();
		
		switch ($category)
		{
			case 'wish': 
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
					
				// Delete the wish
				$wish = new Wish( $database );
				$wish->delete_wish( $referenceid );
		
				// also delete all votes for this wish
				$objR = new WishRank( $database );
				$objR->remove_vote($referenceid);
				
				$message .= JText::sprintf('This is to notify you that your wish on wish list #%s '.$parentid.' was removed from the site due to granted complaint received from a user.',$parentid);
			break;
			
			case 'wishcomment':
				ximport('xcomment');
				
				$comment = new XComment( $database );
				$comment->load( $referenceid );
				$comment->state = 2;
				if (!$comment->store()) {
					echo SupportHtml::alert( $comment->getError() );
					exit();
				}
				
				$message .= JText::sprintf('This is to notify you that your comment on wish #%s '.$parentid.' was removed from the site due to granted complaint received from a user.', $parentid);
			break;
		}
		
		return $message;
	}
}