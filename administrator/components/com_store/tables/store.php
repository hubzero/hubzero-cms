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


class Store extends  JTable 
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $title    		= NULL; 
	var $price    		= NULL;  
	var $description    = NULL;  
	var $available    	= NULL;  
	var $published   	= NULL;
	var $featured   	= NULL;
	var $special   		= NULL;
	var $category   	= NULL; 
	var $type   		= NULL;  
	var $created  		= NULL;  // @var datetime
	var $params 		= NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__store', 'id', $db );
	}
	
	//-----------
	
	public function getInfo( $id)
	{
		if ($id == null) {
			return false;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE id=".$id;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getItems( $rtrn='count', $filters, $config)
	{
		// build body of query
		$query  = "FROM $this->_tbl AS C WHERE ";
		
		if (isset($filters['filterby'])) {
			switch ($filters['filterby'])
			{
				case 'all': 
					$query .= "1=1";
					break;
				case 'available': 
					$query .= "C.available=1";
					break;
				case 'published': 
					$query .= "C.published=1";
					break;
				default: 
					$query .= "C.published=1";
					break;
			}
		} else {
			$query .= "C.published=1";
		}
	
		switch ($filters['sortby'])
		{
			case 'pricelow':   	$query .= " ORDER BY C.price DESC, C.publish_up ASC"; break;
			case 'pricehigh':   $query .= " ORDER BY C.price ASC, C.publish_up ASC"; break;
			case 'date':   		$query .= " ORDER BY C.created DESC"; break;
			case 'category':   	$query .= " ORDER BY C.category DESC"; break;
			case 'type':   		$query .= " ORDER BY C.type DESC"; break;
			default:       	  	$query .= " ORDER BY C.featured DESC, C.id DESC"; break; // featured and newest first
		}
		
		// build count query (select only ID)
		$query_count  = "SELECT count(*) ";
	
		// build fetch query
		$query_fetch  = "SELECT C.id, C.title, C.description, C.price, C.created, C.available, C.params, C.special, C.featured, C.category, C.type, C.published ";
		$query_fetch .= $query;
		if ($filters['limit'] && $filters['start']) {
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count') {
			$this->_db->setQuery( $query_count );
			$result = $this->_db->loadResult();
		} else {
			$this->_db->setQuery( $query_fetch );
			$result = $this->_db->loadObjectList();
			if ($result) {
				for ($i=0; $i < count($result); $i++) 
				{
					$row = &$result[$i];

					$row->webpath = $config->get('webpath');
					$row->root = JPATH_ROOT;
					
					// Get parameters
					$params =& new JParameter( $row->params );
					$row->size  = $params->get( 'size', '' );
					$row->color = $params->get( 'color', '' );
				}
			}
		}

		return $result;
	}
}
