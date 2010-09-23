<?php
/**
 * @package		HUBzero CMS
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


class ResourceAudienceLevel extends JTable 
{
	var $id       		= NULL;  // @var int(11) Primary key
	var $label 			= NULL;  // @var 
	var $title 			= NULL;  // @var
	var $description 	= NULL;  // @var
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_taxonomy_audience_levels', 'id', $db );
	}
	
	//-----------
	 
	public function getLevels ($numlevels = 4, $levels = array()) 
	{	
		$sql  = "SELECT label, title FROM #__resource_taxonomy_audience_levels ";
		$sql .= $numlevels == 4 ? " WHERE label != 'level5' " : "";
		$sql .= " ORDER BY label ASC";
		
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r) 
			{
				$levels[$r->label] = $r->title;
			}
		}
		return $levels;		
	}
}
