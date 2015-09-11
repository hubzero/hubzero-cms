<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Tables;

/**
 * Table class for citation sponsor
 */
class Sponsor extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations_sponsors', 'id', $db);
	}

	/**
	 * Load sponsor(s) in an associative array
	 *
	 * @param   integer  $id  Sponsor ID
	 * @return  array
	 */
	public function getSponsor($id = '')
	{
		$where = (is_numeric($id)) ? "WHERE id=" . $this->_db->quote($id) : "";

		$sql = "SELECT * FROM {$this->_tbl} {$where} ORDER BY sponsor";
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}

	/**
	 * Get all the sponsor IDs associated with a citation
	 *
	 * @param   integer  $citeid  Citation ID
	 * @return  array
	 */
	public function getCitationSponsor($citeid)
	{
		if (!$citeid)
		{
			return;
		}

		$sql = "SELECT sid FROM `#__citations_sponsors_assoc` WHERE cid=" . $this->_db->quote($citeid);
		$this->_db->setQuery($sql);
		return $this->_db->loadColumn();
	}

	/**
	 * Get all the sponsor associated with a citation
	 *
	 * @param   integer  $citeid  Citation ID
	 * @return  array
	 */
	public function getSponsorsForCitationWithId($citeid)
	{
		if (!$citeid)
		{
			return;
		}

		$sql = "SELECT s.id, s.sponsor, s.link, s.image
				FROM `#__citations_sponsors` AS s, `#__citations_sponsors_assoc` AS sa
				WHERE sa.cid={$citeid}
				AND s.id=sa.sid";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Add associations to a citation for a list of sponsors
	 *
	 * @param   integer  $citeid    Citation ID
	 * @param   array    $sponsors  List of sponsor IDs
	 * @return  array
	 */
	public function addSponsors($citeid, $sponsors)
	{
		if (!$citeid)
		{
			return;
		}

		// remove any existing associations
		$sql = "DELETE FROM `#__citations_sponsors_assoc` WHERE cid=" . $this->_db->quote($citeid);
		$this->_db->setQuery($sql);
		$this->_db->query();

		// add all new associations
		$sql = "INSERT INTO `#__citations_sponsors_assoc` (cid, sid) VALUES";
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
