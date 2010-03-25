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

//----------------------------------------------------------
// Resource Audience class
//----------------------------------------------------------

class ResourceAudience extends JTable 
{
	var $id       	= NULL;  // @var int(11) Primary key
	var $rid 		= NULL;  // @var int(11)
	var $versionid 	= NULL;  // @var int(11)
	var $level0 	= NULL;  // @var tinyint
	var $level1 	= NULL;  // @var tinyint
	var $level2 	= NULL;  // @var tinyint
	var $level3 	= NULL;  // @var tinyint
	var $level4 	= NULL;  // @var tinyint	
	var $level5 	= NULL;  // @var tinyint
	var $comments 	= NULL;  // @var varchar(255)
	var $addedBy	= NULL;  // @var int(11)
	var $added		= NULL;  // @var datetime
			
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_taxonomy_audience', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->rid ) == '') {
			$this->setError( JText::_('Missing resource ID') );
			return false;
		}
		return true;
	}
	
	//-----------
	 
	 public function getAudience ($rid, $versionid = 0, $getlabels = 1, $numlevels = 5) {
	 	if ($rid === NULL) {
			return false;
		}
		
		$sql = "SELECT a.* ";
		if($getlabels) {
			$sql.="\n, L0.title as label0, L1.title as label1, L2.title as label2, L3.title as label3, L4.title as label4 ";
			$sql.= $numlevels == 5 ? ", L5.title as label5  " : "";
			$sql.= "\n, L0.description as desc0, L1.description as desc1, L2.description as desc2, L3.description as desc3, L4.description as desc4 ";
			$sql.= $numlevels == 5 ? ", L5.description as desc5  " : "";
		}
		$sql.= " FROM $this->_tbl AS a ";
		if($getlabels) {
			$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L0 on L0.label='level0' ";
			$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L1 on L1.label='level1' ";
			$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L2 on L2.label='level2' ";
			$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L3 on L3.label='level3' ";
			$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L4 on L4.label='level4' ";
			if($numlevels == 5) {
				$sql.= "\n JOIN #__resource_taxonomy_audience_levels AS L5 on L5.label='level5' ";
			}
		}
		$sql.= " WHERE  a.rid=$rid ";
		$sql.= $versionid ? " AND  a.versionid=$versionid " : "";
		$sql.= " LIMIT 1 ";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();	 
	 }
}
//----------------------------------------------------------
// Resource Audience Level class
//----------------------------------------------------------

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
		$sql = "SELECT label, title FROM #__resource_taxonomy_audience_levels ";
		$sql.= $numlevels == 4 ? " WHERE label != 'level5' " : "";
		$sql.= " ORDER BY label ASC";
		
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		if($result) {
			foreach($result as $r) {
				$levels[$r->label] = $r->title;
			}
		}
		return $levels;		
	}
}
?>