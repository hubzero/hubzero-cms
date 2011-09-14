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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_support_wishlist' );

/**
 * Short description for 'plgSupportWishlist'
 * 
 * Long description (if any) ...
 */
class plgSupportWishlist extends JPlugin
{

	/**
	 * Short description for 'plgSupportWishlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgSupportWishlist(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'getReportedItem'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $refid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $parent Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getParentId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parentid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getParentId( $parentid, $category )
	{
		ximport('Hubzero_Comment');

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

	/**
	 * Short description for 'parent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parentid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function parent($parentid)
	{
		$database =& JFactory::getDBO();
		$parent = new Hubzero_Comment( $database );
		$parent->load( $parentid );

		return $parent;
	}

	/**
	 * Short description for 'getTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      unknown $parentid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'deleteReportedItem'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $referenceid Parameter description (if any) ...
	 * @param      string $parentid Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $message Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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
				ximport('Hubzero_Comment');

				$comment = new Hubzero_Comment( $database );
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
