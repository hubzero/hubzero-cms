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
JPlugin::loadLanguage( 'plg_support_resources' );

//-----------

class plgSupportResources extends JPlugin
{
	function plgSupportResources(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'resources' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function getReportedItem($refid, $category, $parent) 
	{
		if ($category != 'review' && $category != 'reviewcomment') {
			return null;
		}
		
		if ($category == 'review') {
			$query  = "SELECT rr.id, rr.comment as text, rr.user_id as author, NULL as subject";
			$query .= ", 'review' as parent_category, rr.anonymous as anon";
			$query .= " FROM #__resource_ratings AS rr";
			$query .= " WHERE rr.id=".$refid;
		} else if ($category == 'reviewcomment') {
			$query  = "SELECT rr.id, rr.comment as text, rr.added_by as author, NULL as subject";
			$query .= ", rr.category as parent_category, rr.anonymous as anon";
			$query .= " FROM #__comments AS rr";
			$query .= " WHERE rr.id=".$refid;
		}

		$database =& JFactory::getDBO();
		$database->setQuery( $query );
		//return $database->loadObjectList();
		$rows = $database->loadObjectList();
		if ($rows) {
			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_resources'.a.'id='.$parent.a.'active=reviews') : '';
			}
		}
		return $rows;
	}
	
	//-----------
	
	function getParentId( $parentid, $category ) 
	{
		ximport('xcomment');
		
		$database =& JFactory::getDBO();
		$refid = $parentid;
		
		if ($category == 'reviewcomment') {
			$pdata = $this->parent($parentid);
			$category = $pdata->category;
			$refid = $pdata->referenceid;
			
			if ($pdata->category == 'reviewcomment') {
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->category;
				$refid = $pdata->referenceid;

				if ($pdata->category == 'reviewcomment') {
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->category;
					$refid = $pdata->referenceid;
				}
			}
		}
		
		if ($category == 'review') {
			$database->setQuery( "SELECT resource_id FROM #__resource_ratings WHERE id=".$refid);
			$pid = $database->loadResult();
		 	return $pid;
		}
	}
	
	//-----------
	
	function parent($parentid) 
	{
		$database =& JFactory::getDBO();
		$parent = new XComment( $database );
		$parent->load( $parentid );
		
		return $parent;
	}
	
	//-----------
	
	function getTitle($category, $parentid) 
	{
		if ($category != 'review' && $category != 'reviewcomment') {
			return null;
		}
		
		switch ($category) 
		{
			case 'review': 
				return JText::sprintf('Review of resource #%s', $parentid);		
         	break;
			
			case 'reviewcomment': 
				return JText::sprintf('Comment to review of resource #%s', $parentid);
         	break;
		}
	}
	
	//-----------
	
	function deleteReportedItem($referenceid, $parentid, $category, $message) 
	{
		if ($category != 'review' && $category != 'reviewcomment') {
			return null;
		}
		
		$database =& JFactory::getDBO();
		
		switch ($category)
		{
			case 'review': 
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php' );
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.review.php' );
				
				// Delete the review
				$review = new ResourcesReview( $database );
				$review->delete( $referenceid );
		
				// Recalculate the average rating for the parent resource
				$resource = new ResourcesResource( $database );
				$resource->load( $parentid );
				$resource->calculateRating();
				if (!$resource->store()) {
					echo ReportAbuseHtml::alert( $resource->getError() );
					exit();
				}
				
				$message .= JText::sprintf('This is to notify you that your review to resource #%s was removed from the site due to granted complaint received from a user.',$parentid);
			break;
			
			case 'reviewcomment':
				ximport('xcomment');
				
				$comment = new XComment( $database );
				$comment->load( $referenceid );
				$comment->state = 2;
				if (!$comment->store()) {
					echo ReportAbuseHtml::alert( $comment->getError() );
					exit();
				}
				
				$message .= JText::sprintf('This is to notify you that your comment on review for resource #%s was removed from the site due to granted complaint received from a user.', $parentid);
			break;
		}
		
		return $message;
	}
}