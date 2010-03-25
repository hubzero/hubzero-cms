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

//----------------------------------------------------------
// Comment database class
//----------------------------------------------------------

class ResourcesRecommendation extends JTable 
{
	var $fromID       = NULL;  // @var int(11) Primary key
	var $toID         = NULL;  // @var int(11)
	var $contentScore = NULL;  // @var float
	var $tagScore     = NULL;  // @var float
	var $titleScore   = NULL;  // @var float
	var $timestamp    = NULL;  // @var datetime (0000-00-00 00:00:00)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__recommendation', 'fromID', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->comment ) == '' or trim( $this->comment ) == JText::_('Enter your comments...')) {
			$this->setError( JText::_('Please provide a comment') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getResults( $filters=array() ) 
	{
		$query = "SELECT *, (10*titleScore + 5*contentScore+2*tagScore)/(10+5+2) AS rec_score 
		FROM #__recommendation AS rec, #__resources AS r 
		WHERE (rec.fromID ='".$filters['id']."' AND r.id = rec.toID AND r.standalone=1) 
		OR (rec.toID ='".$filters['id']."' AND r.id = rec.fromID AND r.standalone=1) having rec_score > ".$filters['threshold']." 
		ORDER BY rec_score DESC LIMIT ".$filters['limit'];
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>