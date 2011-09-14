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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourceAudience'
 * 
 * Long description (if any) ...
 */
class ResourceAudience extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       	= NULL;  // @var int(11) Primary key


	/**
	 * Description for 'rid'
	 * 
	 * @var unknown
	 */
	var $rid 		= NULL;  // @var int(11)


	/**
	 * Description for 'versionid'
	 * 
	 * @var unknown
	 */
	var $versionid 	= NULL;  // @var int(11)


	/**
	 * Description for 'level0'
	 * 
	 * @var unknown
	 */
	var $level0 	= NULL;  // @var tinyint


	/**
	 * Description for 'level1'
	 * 
	 * @var unknown
	 */
	var $level1 	= NULL;  // @var tinyint


	/**
	 * Description for 'level2'
	 * 
	 * @var unknown
	 */
	var $level2 	= NULL;  // @var tinyint


	/**
	 * Description for 'level3'
	 * 
	 * @var unknown
	 */
	var $level3 	= NULL;  // @var tinyint


	/**
	 * Description for 'level4'
	 * 
	 * @var unknown
	 */
	var $level4 	= NULL;  // @var tinyint	


	/**
	 * Description for 'level5'
	 * 
	 * @var unknown
	 */
	var $level5 	= NULL;  // @var tinyint


	/**
	 * Description for 'comments'
	 * 
	 * @var unknown
	 */
	var $comments 	= NULL;  // @var varchar(255)


	/**
	 * Description for 'addedBy'
	 * 
	 * @var unknown
	 */
	var $addedBy	= NULL;  // @var int(11)


	/**
	 * Description for 'added'
	 * 
	 * @var unknown
	 */
	var $added		= NULL;  // @var datetime

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
		parent::__construct( '#__resource_taxonomy_audience', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->rid ) == '') {
			$this->setError( JText::_('Missing resource ID') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getAudience'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $rid Parameter description (if any) ...
	 * @param      mixed $versionid Parameter description (if any) ...
	 * @param      integer $getlabels Parameter description (if any) ...
	 * @param      mixed $numlevels Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getAudience($rid, $versionid = 0, $getlabels = 1, $numlevels = 5)
	{
		if ($rid === NULL) {
			return false;
		}

		$sql = "SELECT a.* ";
		if ($getlabels) {
			$sql .="\n, L0.title as label0, L1.title as label1, L2.title as label2, L3.title as label3, L4.title as label4 ";
			$sql .= $numlevels == 5 ? ", L5.title as label5  " : "";
			$sql .= "\n, L0.description as desc0, L1.description as desc1, L2.description as desc2, L3.description as desc3, L4.description as desc4 ";
			$sql .= $numlevels == 5 ? ", L5.description as desc5  " : "";
		}
		$sql .= " FROM $this->_tbl AS a ";
		if ($getlabels) {
			$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L0 on L0.label='level0' ";
			$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L1 on L1.label='level1' ";
			$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L2 on L2.label='level2' ";
			$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L3 on L3.label='level3' ";
			$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L4 on L4.label='level4' ";
			if ($numlevels == 5) {
				$sql .= "\n JOIN #__resource_taxonomy_audience_levels AS L5 on L5.label='level5' ";
			}
		}
		$sql .= " WHERE  a.rid=$rid ";
		$sql .= $versionid ? " AND  a.versionid=$versionid " : "";
		$sql .= " LIMIT 1 ";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
}

