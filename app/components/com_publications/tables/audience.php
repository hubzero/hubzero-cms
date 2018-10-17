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
 * Table class for publication audience
 */
class Audience extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_audience', 'id', $db);
	}

	/**
	 * Load the audience for a publication by version id
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function loadByVersion($versionid = null)
	{
		if ($versionid === null)
		{
			return false;
		}

		return parent::load(array(
			'publication_version_id' => (int) $versionid
		));
	}

	/**
	 * Get the audience for a publication
	 *
	 * @param   integer  $pid        Pub ID
	 * @param   integer  $versionid  Pub version ID
	 * @param   integer  $getlabels  Get labels or not (1 = yes, 0 = no)
	 * @param   integer  $numlevels  Number of levels to return
	 * @return  mixed    False if error, Object on success
	 */
	public function getAudience($pid = null, $versionid = 0, $getlabels = 1, $numlevels = 5)
	{
		if ($pid === null)
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
		$sql .= " WHERE  a.publication_id=" . $this->_db->quote($pid);
		$sql .= $versionid ? " AND  a.publication_version_id=" . $this->_db->quote($versionid) : "";
		$sql .= " LIMIT 1 ";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete audience for a publication
	 *
	 * @param   integer  $versionid  Pub version ID
	 * @return  boolean
	 */
	public function deleteAudience($versionid = null)
	{
		if ($versionid === null)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($versionid);
		$this->_db->setQuery($query);

		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
