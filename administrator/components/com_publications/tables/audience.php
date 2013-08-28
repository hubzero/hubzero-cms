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
 * Table class for publication audience
 */
class PublicationAudience extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       					= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $publication_id 			= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $publication_version_id 	= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level0 					= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level1 					= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level2 					= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level3 					= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level4 					= NULL;

	/**
	 * tinyint
	 * 
	 * @var integer
	 */
	var $level5 					= NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $comments 					= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by					= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created					= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_audience', 'id', $db );
	}
	
	/**
	 * Load the audience for a publication by version id
	 * 
	 * @param      integer $versionid Pub version ID
	 * @return     mixed False if error, Object on success
	 */	
	public function loadByVersion( $versionid = NULL) 
	{
		if ($versionid === NULL) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE publication_version_id=".$versionid." LIMIT 1");
		
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	/**
	 * Get the audience for a publication
	 * 
	 * @param      integer $pid       Pub ID
	 * @param      integer $versionid Pub version ID
	 * @param      integer $getlabels Get labels or not (1 = yes, 0 = no)
	 * @param      integer $numlevels Number of levels to return
	 * @return     mixed False if error, Object on success
	 */	
	public function getAudience( $pid = NULL, $versionid = 0, $getlabels = 1, $numlevels = 5) 
	{
		if ($pid === NULL) 
		{
			return false;
		}
		
		$sql = "SELECT a.* ";
		if ($getlabels) 
		{
			$sql .="\n, L0.title as label0, L1.title as label1, L2.title as label2, L3.title as label3, L4.title as label4 ";
			$sql .= $numlevels == 5 ? ", L5.title as label5  " : "";
			$sql .= "\n, L0.description as desc0, L1.description as desc1, L2.description as desc2, L3.description as desc3, L4.description as desc4 ";
			$sql .= $numlevels == 5 ? ", L5.description as desc5  " : "";
		}
		$sql .= " FROM $this->_tbl AS a ";
		
		if ($getlabels) 
		{
			$sql .= "\n JOIN #__publication_audience_levels AS L0 on L0.label='level0' ";
			$sql .= "\n JOIN #__publication_audience_levels AS L1 on L1.label='level1' ";
			$sql .= "\n JOIN #__publication_audience_levels AS L2 on L2.label='level2' ";
			$sql .= "\n JOIN #__publication_audience_levels AS L3 on L3.label='level3' ";
			$sql .= "\n JOIN #__publication_audience_levels AS L4 on L4.label='level4' ";
			if ($numlevels == 5) 
			{
				$sql .= "\n JOIN #__publication_audience_levels AS L5 on L5.label='level5' ";
			}
		}
		$sql .= " WHERE  a.publication_id=$pid ";
		$sql .= $versionid ? " AND  a.publication_version_id=$versionid " : "";
		$sql .= " LIMIT 1 ";
		
		$this->_db->setQuery( $sql );		
		$result = $this->_db->loadObjectList();	 
		return $result ? $result[0] : null;
	}
	
	/**
	 * Delete audience for a publication
	 * 
	 * @param      integer $versionid Pub version ID
	 * @return     boolean
	 */	
	public function deleteAudience( $versionid = null ) 
	{
		if ($versionid === NULL) 
		{
			return false;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE publication_version_id = '".$versionid."'";
		$this->_db->setQuery( $query );
		
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}
