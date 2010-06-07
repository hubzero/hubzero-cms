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
JPlugin::loadLanguage( 'plg_support_blog' );

//-----------

class plgSupportBlog extends JPlugin
{
	public function plgSupportBlog(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'support', 'blog' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function getReportedItem($refid, $category, $parent) 
	{
		if ($category != 'blog') {
			return null;
		}
		
		$query  = "SELECT rc.id, rc.entry_id, rc.content as `text`, rc.created_by as author, NULL as subject, rc.anonymous as anon, 'blog' AS parent_category 
					FROM #__blog_comments AS rc 
					WHERE rc.id=".$refid;
		
		$database =& JFactory::getDBO();
		$database->setQuery( $query );

		$rows = $database->loadObjectList();
		if ($rows) {
			ximport('xblog');
			foreach ($rows as $key => $row) 
			{
				$entry = new BlogEntry( $database );
				$entry->load($rows[$key]->entry_id);
				
				$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$entry->created_by.'&active=blog&task='.JHTML::_('date',$entry->publish_up, '%Y', 0).'/'.JHTML::_('date',$entry->publish_up, '%m', 0).'/'.$entry->alias.'#c'.$rows[$key]->id);
			}
		}
		return $rows;
	}
}