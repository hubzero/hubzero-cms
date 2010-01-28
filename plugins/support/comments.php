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
JPlugin::loadLanguage( 'plg_support_comments' );

//-----------

class plgSupportComments extends JPlugin
{
	public function plgSupportComments(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'comments' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function getReportedItem($refid, $category, $parent) 
	{
		if ($category != 'comment') {
			return null;
		}
		
		$query  = "SELECT rc.id, rc.comment as text, rc.added_by as author, NULL as subject, rc.anonymous as anon";
		$query .= ", CASE rc.category WHEN 'reviewcomment' THEN 'reviewcomment' WHEN 'review' THEN 'reviewcomment' WHEN 'answer' THEN 'answercomment' WHEN 'answercomment' THEN 'answercomment' WHEN 'wishcomment' THEN 'wishcomment' WHEN 'wish' THEN 'wishcomment' END AS parent_category";
		$query .= " FROM #__comments AS rc";
		$query .= " WHERE rc.id=".$refid;
		
		$database =& JFactory::getDBO();
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		if ($rows) {
			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_resources&id='.$parent.'&active=reviews') : '';
				if ($rows[$key]->parent_category == 'answercomment') {
					$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id='.$parent);
				}
				if ($rows[$key]->parent_category == 'wishcomment') {
					$rows[$key]->href = JRoute::_('index.php?option=com_wishlist&task=wish&wishid='.$parent);
				}
			}
		}
		return $rows;
	}
}