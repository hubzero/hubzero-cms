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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for citation sponsor
 */
class CitationsSponsor extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = null;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $sponsor = null;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $link = null;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $image = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations_sponsors', 'id', $db);
	}

	/**
	 * Load sponsor(s) in an associative array
	 *
	 * @param      integer $id Sponsor ID
	 * @return     array
	 */
	public function getSponsor($id = '')
	{
		$where = (is_numeric($id)) ? "WHERE id=" . $this->_db->Quote($id) : "";

		$sql = "SELECT * FROM {$this->_tbl} {$where} ORDER BY sponsor";
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}

	/**
	 * Get all the sponsor IDs associated with a citation
	 *
	 * @param      integer $citeid Citation ID
	 * @return     array
	 */
	public function getCitationSponsor($citeid)
	{
		if (!$citeid)
		{
			return;
		}

		$sql = "SELECT sid FROM #__citations_sponsors_assoc WHERE cid=" . $this->_db->Quote($citeid);
		$this->_db->setQuery($sql);
		return $this->_db->loadResultArray();
	}

	public function getSponsorsForCitationWithId( $citeid )
	{
		if (!$citeid)
		{
			return;
		}

		$sql = "SELECT s.id, s.sponsor, s.link, s.image
				FROM #__citations_sponsors AS s, #__citations_sponsors_assoc AS sa
				WHERE sa.cid={$citeid}
				AND s.id=sa.sid";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Add associations to a citation for a list of sponsors
	 *
	 * @param      integer $citeid   Citation ID
	 * @param      array   $sponsors List of sponsor IDs
	 * @return     array
	 */
	public function addSponsors($citeid, $sponsors)
	{
		if (!$citeid)
		{
			return;
		}

		// remove any existing associations
		$sql = "DELETE FROM #__citations_sponsors_assoc WHERE cid=" . $this->_db->Quote($citeid);
		$this->_db->setQuery($sql);
		$this->_db->query();

		//add all new associations
		$sql = "INSERT INTO #__citations_sponsors_assoc(cid, sid) VALUES";
		foreach ($sponsors as $s)
		{
			$sql .= "({$citeid}, {$s}), ";
		}
		$sql = substr($sql, 0, -2) . ";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}
}
