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
 * Table class for resource audience
 */
class ResourceAudience extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $rid 		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $versionid 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level0 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level1 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level2 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level3 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level4 	= NULL;

	/**
	 * tinyint
	 *
	 * @var integer
	 */
	var $level5 	= NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $comments 	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $addedBy	= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $added		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_taxonomy_audience', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->rid) == '')
		{
			$this->setError(JText::_('Missing resource ID'));
			return false;
		}
		return true;
	}

	/**
	 * Get the audience for a resource
	 *
	 * @param      integer $rid       Resource ID
	 * @param      integer $versionid Resource version ID
	 * @param      integer $getlabels Get labels or not (1 = yes, 0 = no)
	 * @param      integer $numlevels Number of levels to return
	 * @return     mixed False if error, Object on success
	 */
	public function getAudience($rid, $versionid = 0, $getlabels = 1, $numlevels = 5)
	{
		if ($rid === NULL)
		{
			return false;
		}

		$sql = "SELECT a.* ";
		if ($getlabels)
		{
			$sql .=", L0.title as label0, L1.title as label1, L2.title as label2, L3.title as label3, L4.title as label4 ";
			$sql .= $numlevels == 5 ? ", L5.title as label5  " : "";
			$sql .= ", L0.description as desc0, L1.description as desc1, L2.description as desc2, L3.description as desc3, L4.description as desc4 ";
			$sql .= $numlevels == 5 ? ", L5.description as desc5  " : "";
		}
		$sql .= " FROM $this->_tbl AS a ";
		if ($getlabels)
		{
			$sql .= " JOIN #__resource_taxonomy_audience_levels AS L0 on L0.label='level0' ";
			$sql .= " JOIN #__resource_taxonomy_audience_levels AS L1 on L1.label='level1' ";
			$sql .= " JOIN #__resource_taxonomy_audience_levels AS L2 on L2.label='level2' ";
			$sql .= " JOIN #__resource_taxonomy_audience_levels AS L3 on L3.label='level3' ";
			$sql .= " JOIN #__resource_taxonomy_audience_levels AS L4 on L4.label='level4' ";
			if ($numlevels == 5)
			{
				$sql .= " JOIN #__resource_taxonomy_audience_levels AS L5 on L5.label='level5' ";
			}
		}
		$sql .= " WHERE  a.rid=$rid ";
		$sql .= $versionid ? " AND  a.versionid=" . $this->_db->Quote($versionid) : "";
		$sql .= " LIMIT 1 ";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}

