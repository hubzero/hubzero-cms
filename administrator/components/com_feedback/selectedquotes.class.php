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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class SelectedQuotes extends JTable 
{
	var $id         		= NULL;  // @var int(11) Primary key
	var $userid	    		= NULL;  // @var int(11)
	var $fullname   		= NULL;  // @var string
	var $org	    		= NULL;  // @var string
	var $short_quote     	= NULL;  // @var text
	var $quote      		= NULL;  // @var text
	var $picture    		= NULL;  // @var string
	var $date	    		= NULL;  // @var datetime	
	var $flash_rotation 	= NULL;  // @var int(1)
	var $notable_quotes 	= NULL;  // @var int(1)
	var $notes 				= NULL;	 // @var string

	function __construct( &$db ) 
	{
		parent::__construct( '#__selected_quotes', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->quote ) == '') {
			$this->setError( JText::_('Quote must contain text.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function buildQuery( $filters ) 
	{
		$query = "FROM $this->_tbl ";
		if ((isset($filters['search']) && $filters['search'] != '') 
		 || (isset($filters['id']) && $filters['id'] != 0)
		 || (isset($filters['notable_quotes']) && $filters['notable_quotes'] != '')) {
			$query .= "WHERE";
		}
		if (isset($filters['search']) && $filters['search'] != '' ) {
			$words = explode(' ', $filters['search']);
			$sqlsearch = "";
			foreach ($words as $word) 
			{
				$sqlsearch .= " (LOWER(fullname) LIKE '%$word%') OR";
			}
			$query .= substr($sqlsearch, 0, -3);
		}
		if (isset($filters['notable_quotes'])) {
			$query .= " notable_quotes=".$filters['notable_quotes'];
		}
		if (isset($filters['id']) && $filters['id'] != 0 ) {
			$query .= " AND id=".$filters['id'];
		}
		if ($filters['sortby'] == '') {
			$filters['sortby'] = 'date';
		}
		$query .= "\n ORDER BY ".$filters['sortby']." DESC";
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != '') {
			$query .= " LIMIT ".$filters['limit'];
		}
		return $query;
	}
	
	//-----------
	
	function getCount( $filters=array() ) 
	{
		$query  = "SELECT COUNT(*) ";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getResults( $filters=array() ) 
	{
		$query  = "SELECT DISTINCT * ";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function deletePicture( $config=null )
	{
		// Load the component config
		if (!$config) {
			$config =& JComponentHelper::getParams( 'com_feedback' );
		}
		
		// Incoming member ID
		if (!$this->id) {
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			return false;
		}
		
		// Incoming file
		if (!$this->picture) {
			return true;
		}
		
		// Build the file path
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $this->id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$this->picture) or !$this->picture) { 
			return true;
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$this->picture)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				return false;
			}
		}
	
		return true;
	}
}


class mosSelectedQuotes extends SelectedQuotes 
{	
}
?>