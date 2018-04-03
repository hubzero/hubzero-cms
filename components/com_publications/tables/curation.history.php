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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication curation history
 */
class CurationHistory extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_curation_history', 'id', $db);
	}

	/**
	 * Get history records
	 *
	 * @param   integer  $vid  Publication Version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getRecords($vid = null, $filters = array())
	{
		if (!intval($vid))
		{
			return false;
		}

		$sortby  = isset($filters['sortby']) && $filters['sortby'] ? $filters['sortby'] : 'created';
		$sortdir = isset($filters['sortdir']) && strtoupper($filters['sortdir']) == 'DESC'  ? 'DESC' : 'ASC';

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);

		if (isset($filters['curator']) && $filters['curator'] == 1)
		{
			$query .= " AND curator=1";
		}

		$query .= " ORDER BY " . $sortby . " " . $sortdir;
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Get last history record
	 *
	 * @param   integer  $vid  Publication Version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getLastRecord($vid = null, $curator = 0)
	{
		if (!intval($vid))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);
		$query.= ($curator == 1) ? " AND curator=1" : " AND curator=0";

		$query .= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}
}
