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


class WikiPageMath extends JTable 
{
	var $id               = NULL;  // @var int(11) Primary key
	var $inputhash        = NULL;  // @var varbinary(16)
	var $outputhash       = NULL;  // @var varbinary(16)
	var $conservativeness = NULL;  // @var tinyint
	var $html             = NULL;  // @var text
	var $mathml           = NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_math', 'id', $db );
	}
	
	//-----------
	
	public function loadByInput( $inputhash ) 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE inputhash='".addslashes($inputhash)."' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
