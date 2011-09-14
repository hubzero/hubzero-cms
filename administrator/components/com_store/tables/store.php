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

/**
 * Short description for 'Store'
 * 
 * Long description (if any) ...
 */
class Store extends  JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         	= NULL;  // @var int(11) Primary key


	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title    		= NULL;

	/**
	 * Description for 'price'
	 * 
	 * @var unknown
	 */
	var $price    		= NULL;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description    = NULL;

	/**
	 * Description for 'available'
	 * 
	 * @var unknown
	 */
	var $available    	= NULL;

	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	var $published   	= NULL;

	/**
	 * Description for 'featured'
	 * 
	 * @var unknown
	 */
	var $featured   	= NULL;

	/**
	 * Description for 'special'
	 * 
	 * @var unknown
	 */
	var $special   		= NULL;

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category   	= NULL;

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type   		= NULL;

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created  		= NULL;  // @var datetime


	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params 		= NULL;  // @var text

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__store', 'id', $db );
	}

	/**
	 * Short description for 'getInfo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getInfo( $id)
	{
		if ($id == null) {
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE id=".$id;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getItems'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rtrn Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @param      object $config Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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
					$params = new JParameter( $row->params );
					$row->size  = $params->get( 'size', '' );
					$row->color = $params->get( 'color', '' );
				}
			}
		}

		return $result;
	}
}

