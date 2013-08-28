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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for publication audience level
 */
class PublicationAudienceLevel extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       		= NULL;

	/**
	 * varchar(11)
	 * 
	 * @var string
	 */
	var $label 			= NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $title 			= NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $description 	= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_audience_levels', 'id', $db );
	}
	
	/**
	 * Get records to a determined level
	 * 
	 * @param      integer $numlevels 		Number of levels to return
	 * @param      array   $levels    		Array to populate
	 * @param      array   $return_array    Return as array?
	 * @return     array
	 */
	public function getLevels($numlevels = 4, $levels = array(), $return_array = 1)
	{
		$sql  = "SELECT label, title, description FROM $this->_tbl ";
		$sql .= $numlevels == 4 ? " WHERE label != 'level5' " : "";
		$sql .= " ORDER BY label ASC";

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();
		if ($result) 
		{
			foreach ($result as $r)
			{
				$levels[$r->label] = $r->title;
			}
		}
		
		return $return_array ? $levels : $result;
	}
}
